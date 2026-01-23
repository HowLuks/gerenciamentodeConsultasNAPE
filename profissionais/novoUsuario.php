<?php
require_once "../config/conexao.php";

// Função para verificar se já existe nome ou prontuário
function usuarioExistente($pdo, $nome, $prontuario, $id_usuario = null)
{
    $sql = "SELECT COUNT(*) FROM usuarios 
            WHERE (nome_usuario = :nome_usuario OR numero_prontuario = :numero_prontuario)";
    
    if ($id_usuario) {
        $sql .= " AND id_usuario != :id_usuario";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":nome_usuario", $nome);
    $stmt->bindValue(":numero_prontuario", $prontuario);
    
    if ($id_usuario) {
        $stmt->bindValue(":id_usuario", $id_usuario);
    }
    
    $stmt->execute();

    return $stmt->fetchColumn() > 0; // retorna true se existe
}

function adicionarUsuario($pdo, $dados)
{
    try {
        // VALIDAÇÃO CORRETA
        if (
            empty($dados['nome_usuario']) ||
            empty($dados['numero_prontuario'])
        ) {
            // Redireciona para gerenciarUsuario com mensagem de erro
            echo "<script>
                    alert('Preencha todos os campos obrigatórios!');
                    window.location.href = '../usuariosGerais.php';
                  </script>";
            return false;
        }

        // VERIFICA SE JÁ EXISTE NOME OU PRONTUÁRIO
        if (usuarioExistente($pdo, $dados['nome_usuario'], $dados['numero_prontuario'])) {
            // Redireciona para gerenciarUsuario com mensagem de erro
            echo "<script>
                    alert('Usuário ou prontuário já cadastrado no sistema!');
                    window.location.href = '../usuariosGerais.php';
                  </script>";
            return false;
        }

        $sql = "INSERT INTO usuarios (
                nome_usuario,
                numero_prontuario,
                laudado,
                contato_usuario,
                situacao,
                quantidade_terapias,
                multiprofissionais,
                diagnostico,
                informacao_adicional
            ) VALUES (
                :nome_usuario,
                :numero_prontuario,
                :laudado,
                :contato_usuario,
                :situacao,
                :quantidade_terapias,
                :multiprofissionais,
                :diagnostico,
                :informacao_adicional
            )";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":nome_usuario", $dados["nome_usuario"]);
        $stmt->bindValue(":numero_prontuario", $dados["numero_prontuario"]);
        $stmt->bindValue(":contato_usuario", $dados["contato_usuario"]);
        $stmt->bindValue(":situacao", $dados["situacao"]);
        $stmt->bindValue(":diagnostico", $dados["diagnostico"]);
        $stmt->bindValue(":quantidade_terapias", $dados["quantidade_terapias"]);
        $stmt->bindValue(":laudado", $dados["laudado"]);
        $stmt->bindValue(":multiprofissionais", $dados["multiprofissionais"]);
        $stmt->bindValue(":informacao_adicional", $dados["informacao_adicional"]);

        if ($stmt->execute()) {
            // Sucesso - redireciona para gerenciarUsuario com mensagem de sucesso
            echo "<script>
                    alert('Usuário cadastrado com sucesso!');
                    window.location.href = '../usuariosGerais.php';
                  </script>";
            return true;
        } else {
            // Falha na execução
            echo "<script>
                    alert('Erro ao cadastrar usuário!');
                    window.location.href = '../usuariosGerais.php';
                  </script>";
            return false;
        }

    } catch (PDOException $e) {
        // Erro de banco de dados - redireciona para gerenciarUsuario com mensagem de erro
        echo "<script>
                alert('Erro no banco de dados: " . addslashes($e->getMessage()) . "');
                window.location.href = '../usuariosGerais.php';
              </script>";
        return false;
    }
}

// Inicia sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dados = [
        "nome_usuario" => $_POST['nome_usuario'] ?? '',
        "numero_prontuario" => $_POST['numero_prontuario'] ?? '',
        "laudado" => $_POST['laudado'] ?? '',
        "contato_usuario" => $_POST['contato_usuario'] ?? '',
        "situacao" => $_POST['situacao'] ?? '',
        "quantidade_terapias" => $_POST['quantidade_terapias'] ?? '',
        "multiprofissionais" => $_POST['multiprofissionais'] ?? '',
        "diagnostico" => $_POST['diagnostico'] ?? '',
        "informacao_adicional" => $_POST['informacao_adicional'] ?? '',
    ];

    // Tenta cadastrar
    adicionarUsuario($pdo, $dados);
} else {
    // Se acessar diretamente o arquivo sem POST, redireciona
    echo "<script>
            window.location.href = '../gerenciarUsuario.php';
          </script>";
    exit();
}