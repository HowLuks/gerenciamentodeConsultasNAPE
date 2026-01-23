<?php
// Inclui o arquivo de configuração de conexão com o banco de dados
require_once "../config/conexao.php";

// Obtém o parâmetro 'id_agendamento' da URL (via GET) ou retorna string vazia se não existir
$id_agendamento = $_GET['id_agendamento'] ?? '';

// Verifica se o ID do agendamento está vazio
if (empty($id_agendamento)) {
    // Retorna uma mensagem de erro em formato JSON
    echo json_encode(['erro' => 'ID do agendamento não informado']);
    // Encerra a execução do script
    exit;
}

// Inicia um bloco try-catch para tratamento de exceções
try {
    // Define a consulta SQL para buscar informações do agendamento e do usuário
    $sql = "SELECT u.*, a.nome_profissional 
            FROM agendamentos a
            INNER JOIN usuarios u ON a.nome_usuario = u.nome_usuario
            WHERE a.id_agendamento = :id_agendamento";
    
    // Prepara a consulta SQL para execução (prevenção contra SQL injection)
    $stmt = $pdo->prepare($sql);
    
    // Associa o valor do parâmetro :id_agendamento à variável $id_agendamento
    $stmt->bindValue(':id_agendamento', $id_agendamento);
    
    // Executa a consulta preparada
    $stmt->execute();
    
    // Recupera os resultados como um array associativo
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verifica se foram encontrados resultados
    if ($paciente) {
        // Retorna os dados do paciente em formato JSON
        echo json_encode($paciente);
    } else {
        // Retorna mensagem de erro se paciente não for encontrado
        echo json_encode(['erro' => 'Paciente não encontrado para este agendamento']);
    }
} catch (PDOException $e) {
    // Captura exceções relacionadas ao banco de dados
    // Retorna mensagem de erro com detalhes da exceção
    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>