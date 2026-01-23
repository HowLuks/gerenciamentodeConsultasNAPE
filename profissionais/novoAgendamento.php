<?php
require_once "../config/conexao.php";

function adicionarAgendamento($pdo, $dados)
{
    try {
        // VALIDAÇÃO
        if (
            empty($dados['nome_usuario']) ||
            empty($dados['hora']) ||
            empty($dados['data']) ||
            empty($dados['status_agendamento']) ||
            empty($dados['id_profissional'])
        ) {
            $id_profissional = $dados['id_profissional'] ?? '';
            $url_redirect = '../gerenciarAgendamento.php' .
                (!empty($id_profissional) ? '?id=' . $id_profissional : '');

            echo "<script>
                    alert('Preencha todos os campos obrigatórios!');
                    window.location.href = '" . $url_redirect . "';
                  </script>";
            return false;
        }

        // Verificar se o usuário existe
        $sqlVerifica = "SELECT id_usuario FROM usuarios WHERE nome_usuario = :nome_usuario";
        $stmtVerifica = $pdo->prepare($sqlVerifica);
        $stmtVerifica->bindValue(":nome_usuario", $dados["nome_usuario"]);
        $stmtVerifica->execute();

        if ($stmtVerifica->rowCount() === 0) {
            $url_redirect = '../gerenciarAgendamento.php?id=' . $dados['id_profissional'];
            echo "<script>
                    alert('Usuário não encontrado no sistema!');
                    window.location.href = '" . $url_redirect . "';
                  </script>";
            return false;
        }

        // Verificar se já existe agendamento no mesmo horário para este profissional
        $sqlVerificaHorario = "SELECT a.id_agendamento 
                              FROM agendamentos a
                              JOIN agendamento_profissional ap ON a.id_agendamento = ap.id_agendamento
                              WHERE ap.id_profissional = :id_profissional 
                              AND a.data = :data 
                              AND a.hora = :hora";
        
        $stmtVerificaH = $pdo->prepare($sqlVerificaHorario);
        $stmtVerificaH->bindValue(":id_profissional", $dados["id_profissional"], PDO::PARAM_INT);
        $stmtVerificaH->bindValue(":data", $dados["data"]);
        $stmtVerificaH->bindValue(":hora", $dados["hora"]);
        $stmtVerificaH->execute();
        
        if ($stmtVerificaH->rowCount() > 0) {
            $url_redirect = '../gerenciarAgendamento.php?id=' . $dados['id_profissional'];
            echo "<script>
                    alert('Já existe um agendamento para este profissional no mesmo horário!');
                    window.location.href = '" . $url_redirect . "';
                  </script>";
            return false;
        }

        // INICIAR TRANSAÇÃO
        $pdo->beginTransaction();

        // 1. INSERIR NA TABELA agendamentos
        $sqlAgendamento = "INSERT INTO agendamentos (
                nome_usuario,
                data,
                hora,
                status_agendamento
            ) VALUES (
                :nome_usuario,
                :data,
                :hora,
                :status_agendamento
            )";

        $stmtAgendamento = $pdo->prepare($sqlAgendamento);
        $stmtAgendamento->bindValue(":nome_usuario", $dados["nome_usuario"]);
        $stmtAgendamento->bindValue(":data", $dados["data"]);
        $stmtAgendamento->bindValue(":hora", $dados["hora"]);
        $stmtAgendamento->bindValue(":status_agendamento", $dados["status_agendamento"]);
        
        if (!$stmtAgendamento->execute()) {
            $pdo->rollBack();
            throw new Exception("Erro ao inserir agendamento");
        }

        // 2. PEGAR O ID DO AGENDAMENTO INSERIDO
        $id_agendamento = $pdo->lastInsertId();

        // 3. VERIFICAR SE O RELACIONAMENTO JÁ EXISTE
        $sqlVerificaRelacao = "SELECT COUNT(*) as total 
                             FROM agendamento_profissional 
                             WHERE id_agendamento = :id_agendamento 
                             AND id_profissional = :id_profissional";

        $stmtVerificaRel = $pdo->prepare($sqlVerificaRelacao);
        $stmtVerificaRel->bindValue(":id_agendamento", $id_agendamento, PDO::PARAM_INT);
        $stmtVerificaRel->bindValue(":id_profissional", $dados["id_profissional"], PDO::PARAM_INT);
        $stmtVerificaRel->execute();
        $relacaoExistente = $stmtVerificaRel->fetch(PDO::FETCH_ASSOC);

        // 4. INSERIR NA TABELA agendamento_profissional (apenas se não existir)
        if ($relacaoExistente['total'] == 0) {
            $sqlAgendamentoProfissional = "INSERT IGNORE INTO agendamento_profissional (
                    id_agendamento,
                    id_profissional
                ) VALUES (
                    :id_agendamento,
                    :id_profissional
                )";

            $stmtRelacao = $pdo->prepare($sqlAgendamentoProfissional);
            $stmtRelacao->bindValue(":id_agendamento", $id_agendamento, PDO::PARAM_INT);
            $stmtRelacao->bindValue(":id_profissional", $dados["id_profissional"], PDO::PARAM_INT);
            
            if (!$stmtRelacao->execute()) {
                $pdo->rollBack();
                throw new Exception("Erro ao vincular agendamento ao profissional");
            }
        } else {
            // Relação já existe, pode continuar (não é um erro)
            error_log("Relacionamento agendamento_profissional já existe para id_agendamento=$id_agendamento e id_profissional=" . $dados["id_profissional"]);
        }

        // CONFIRMAR TRANSAÇÃO
        $pdo->commit();

        // SUCESSO
        $url_redirect = '../gerenciarAgendamento.php?id=' . $dados['id_profissional'];
        echo "<script>
                alert('Agendamento feito com sucesso!');
                window.location.href = '" . $url_redirect . "';
              </script>";
        return true;

    } catch (Exception $e) {
        // Cancelar transação em caso de erro
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        $id_profissional = $dados['id_profissional'] ?? '';
        $url_redirect = '../gerenciarAgendamento.php' .
            (!empty($id_profissional) ? '?id=' . $id_profissional : '');

        // CORREÇÃO AQUI: Removido addslashes() problemático
        $mensagem_erro = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        
        echo "<script>
                alert('Erro no sistema: " . $mensagem_erro . "');
                window.location.href = '" . $url_redirect . "';
              </script>";
        error_log("Erro ao adicionar agendamento: " . $e->getMessage());
        return false;
    }
}

// Inicia sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // DEBUG: Verificar dados recebidos
    error_log("Dados POST recebidos: " . print_r($_POST, true));
    
    $dados = [
        "nome_usuario" => $_POST['nome_usuario'] ?? '',
        "data" => $_POST['data'] ?? '',
        "hora" => $_POST['hora'] ?? '',
        "status_agendamento" => $_POST['status_agendamento'] ?? '',
        "id_profissional" => $_POST['id_profissional'] ?? ''
    ];

    // Verificar se o id_profissional foi enviado
    if (empty($dados['id_profissional'])) {
        echo "<script>
                alert('ID do profissional não informado!');
                window.location.href = '../gerenciarAgendamento.php';
              </script>";
        exit();
    }

    // Verificar se nome_usuario é um ID (se for numérico) ou nome
    if (is_numeric($dados['nome_usuario'])) {
        // Se for numérico, buscar o nome do usuário pelo ID
        $sqlBuscaNome = "SELECT nome_usuario FROM usuarios WHERE id_usuario = :id_usuario";
        $stmtBusca = $pdo->prepare($sqlBuscaNome);
        $stmtBusca->bindValue(":id_usuario", $dados['nome_usuario'], PDO::PARAM_INT);
        $stmtBusca->execute();
        
        if ($usuario = $stmtBusca->fetch(PDO::FETCH_ASSOC)) {
            $dados['nome_usuario'] = $usuario['nome_usuario'];
        } else {
            echo "<script>
                    alert('Usuário não encontrado!');
                    window.location.href = '../gerenciarAgendamento.php?id=" . $dados['id_profissional'] . "';
                  </script>";
            exit();
        }
    }

    // Tenta cadastrar
    adicionarAgendamento($pdo, $dados);
} else {
    // Se acessar diretamente o arquivo sem POST, redireciona
    echo "<script>
            window.location.href = '../gerenciarAgendamento.php';
          </script>";
    exit();
}