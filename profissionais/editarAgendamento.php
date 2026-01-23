<?php
// Inclui o arquivo de configuração de conexão com o banco de dados
require_once "../config/conexao.php";

// Verifica se a requisição foi feita através do método POST
// Se não foi POST, mostra mensagem de erro e termina execução
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido");
}

// Define uma função para verificar se um usuário já tem agendamento em um determinado horário
function usuarioComAgendamento($pdo, $nome_usuario, $data, $hora, $id_agendamento_atual = null)
{
    // Query base para contar agendamentos com mesmo usuário, data e hora
    $sql = "SELECT COUNT(*) FROM agendamentos 
            WHERE nome_usuario = :nome_usuario 
            AND data = :data 
            AND hora = :hora";
    
    // Se estiver editando um agendamento existente, exclui o próprio agendamento da verificação
    if ($id_agendamento_atual) {
        $sql .= " AND id_agendamento != :id_agendamento";
    }
    
    // Prepara a query SQL
    $stmt = $pdo->prepare($sql);
    
    // Vincula os parâmetros à query
    $stmt->bindValue(":nome_usuario", $nome_usuario);
    $stmt->bindValue(":data", $data);
    $stmt->bindValue(":hora", $hora);
    
    // Se houver um id_agendamento_atual, vincula este também
    if ($id_agendamento_atual) {
        $stmt->bindValue(":id_agendamento", $id_agendamento_atual);
    }
    
    // Executa a query
    $stmt->execute();
    
    // Retorna true se encontrar algum agendamento, false caso contrário
    // fetchColumn() retorna o valor da primeira coluna do resultado
    return $stmt->fetchColumn() > 0;
}

// Obtém os dados do agendamento enviados via POST
// Usa operador de coalescência nula para definir valores padrão caso não existam

// ID do agendamento a ser editado
$id_agendamento = $_POST['id_agendamento'] ?? 0;

// Tenta obter o ID do profissional da URL (referer)
$id_profissional = 0;

// Verifica se há um referer (página anterior) e tenta extrair o ID da URL
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    
    // Tenta extrair o id_profissional da URL usando regex
    if (preg_match('/[?&]id=(\d+)/', $referer, $matches)) {
        $id_profissional = intval($matches[1]);
    }
}

// Se não encontrou no referer, tenta buscar do próprio agendamento no banco
if ($id_profissional <= 0 && $id_agendamento > 0) {
    try {
        $stmt_ag = $pdo->prepare("SELECT id_profissional FROM agendamentos WHERE id_agendamento = :id_agendamento");
        $stmt_ag->execute([':id_agendamento' => $id_agendamento]);
        $agendamento = $stmt_ag->fetch(PDO::FETCH_ASSOC);
        
        if ($agendamento && isset($agendamento['id_profissional'])) {
            $id_profissional = intval($agendamento['id_profissional']);
        }
    } catch (Exception $e) {
        // Ignora erro, continua com validação abaixo
    }
}

// Data do agendamento
$data = $_POST['data'] ?? '';

// Hora do agendamento
$hora = $_POST['hora'] ?? '';

// Nome do usuário/paciente
$nome_usuario = $_POST['nome_usuario'] ?? '';

// Status do agendamento
$status_agendamento = $_POST['status_agendamento'] ?? '';

// Validação básica dos dados recebidos

// Verifica se o ID do agendamento é válido
if ($id_agendamento <= 0) {
    die("<script>alert('ID do agendamento inválido'); window.history.back();</script>");
}

// Verifica se o ID do profissional é válido
if ($id_profissional <= 0) {
    die("<script>alert('ID do profissional inválido ou não encontrado'); window.history.back();</script>");
}

// Verifica se os campos obrigatórios estão preenchidos
if (empty($data) || empty($hora)) {
    die("<script>alert('Preencha todos os campos obrigatórios'); window.history.back();</script>");
}

// Inicia bloco try-catch para tratamento de exceções
try {
    // 1. Verifica se o profissional existe no banco de dados
    $stmt_prof = $pdo->prepare("SELECT id_profissional FROM profissionais WHERE id_profissional = :id_profissional");
    $stmt_prof->execute([':id_profissional' => $id_profissional]);
    $profissional = $stmt_prof->fetch(PDO::FETCH_ASSOC);

    // Verifica se encontrou o profissional
    if (!$profissional) {
        die("<script>alert('Profissional não encontrado'); window.history.back();</script>");
    }

    // 2. Verifica se o usuário já tem agendamento no mesmo horário
    // Chama a função usuarioComAgendamento passando o agendamento atual para exclusão da verificação
    if (usuarioComAgendamento($pdo, $nome_usuario, $data, $hora, $id_agendamento)) {
        die("<script>
                alert('Este usuário JÁ TEM um agendamento para $data às $hora!');
                window.history.back();
            </script>");
    }

    // 3. Atualiza o AGENDAMENTO no banco de dados
    $query = "UPDATE agendamentos SET
                nome_usuario = :nome_usuario,
                data = :data,
                hora = :hora,
                status_agendamento = :status_agendamento
            WHERE id_agendamento = :id_agendamento";

    // Prepara a query de atualização
    $stmt = $pdo->prepare($query);

    // Executa a atualização com os valores dos parâmetros
    $stmt->execute([
        ':nome_usuario' => $nome_usuario,
        ':data' => $data,
        ':hora' => $hora,
        ':status_agendamento' => $status_agendamento,
        ':id_agendamento' => $id_agendamento
    ]);

    // Verifica se alguma linha foi atualizada (rowCount() retorna o número de linhas afetadas)
    if ($stmt->rowCount() > 0) {
        // 4. Redirecionamento após sucesso
        
        // Redireciona para a página de gerenciamento com o ID do profissional
        $redirectUrl = '../gerenciarAgendamento.php?id=' . $id_profissional;

        // Mostra alerta de sucesso e redireciona
        echo "<script>
                alert('Agendamento atualizado com sucesso!');
                window.location.href = '$redirectUrl';
            </script>";
    } else {
        // Se nenhuma linha foi atualizada, mostra mensagem de erro
        echo "<script>
                alert('Nenhum agendamento foi atualizado. Verifique o ID.');
                window.history.back();
            </script>";
    }

} catch (PDOException $e) {
    // Captura exceções do tipo PDOException (erros relacionados ao banco de dados)
    // Mostra alerta com a mensagem de erro (addslashes para evitar problemas com aspas no JavaScript)
    // Também registra o erro no console do navegador
    echo "<script>
            alert('Erro ao atualizar agendamento: " . addslashes($e->getMessage()) . "');
            console.error('Erro: " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
}
?>