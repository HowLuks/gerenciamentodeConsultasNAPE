<?php
// Inclui o arquivo de conexão com o banco de dados localizado no diretório config
// O caminho "../config/conexao.php" significa: subir um nível e acessar a pasta config
require_once "../config/conexao.php";

// Verifica se o parâmetro 'id' foi enviado via GET e se não está vazio
// A condição usa isset() para verificar existência e empty() para verificar se tem valor
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Retorna uma resposta JSON indicando falha com mensagem de erro
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    // Termina a execução do script
    exit;
}

// Converte o valor do parâmetro 'id' para inteiro usando intval()
// Isso garante que o valor seja numérico e ajuda na segurança
$id = intval($_GET['id']);

// Inicia um bloco try-catch para tratamento de exceções
try {
    // Define a consulta SQL para selecionar todos os campos da tabela 'profissionais'
    // onde o 'id_profissional' corresponde ao parâmetro fornecido
    $sql = "SELECT * FROM profissionais WHERE id_profissional = :id_profissional";
    
    // Prepara a consulta SQL para execução usando PDO
    // A preparação ajuda a prevenir ataques de SQL injection
    $stmt = $pdo->prepare($sql);
    
    // Associa o parâmetro nomeado :id_profissional à variável $id
    // PDO::PARAM_INT especifica que o valor deve ser tratado como inteiro
    $stmt->bindParam(":id_profissional",$id, PDO::PARAM_INT);
    
    // Executa a consulta preparada
    $stmt->execute();
    
    // Busca o resultado como um array associativo (chave-valor)
    // FETCH_ASSOC retorna apenas os nomes das colunas como chaves
    $profissional = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verifica se algum registro foi encontrado
    if ($profissional) {
        // Remove o campo 'senha' do array de resultados por motivos de segurança
        // Isso evita que a senha seja enviada ao cliente
        unset($profissional['senha']);
        
        // Retorna uma resposta JSON com sucesso=true e todos os dados do profissional
        // Usa o operador spread (...) para desestruturar o array $profissional
        echo json_encode(['success' => true, ...$profissional]);
    } else {
        // Retorna uma resposta JSON indicando que o profissional não foi encontrado
        echo json_encode(['success' => false, 'message' => 'Profissional não encontrado']);
    }
} catch (PDOException $e) {
    // Captura exceções do tipo PDOException (erros relacionados ao banco de dados)
    // Retorna uma resposta JSON com mensagem de erro contendo a mensagem da exceção
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>