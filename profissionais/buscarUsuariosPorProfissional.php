<?php
// Inclui o arquivo de configuração de conexão com o banco de dados
// O caminho "../config/conexao.php" significa: subir um nível e acessar a pasta config
require_once "../config/conexao.php";

// Define o cabeçalho HTTP para indicar que a resposta será em formato JSON
header('Content-Type: application/json');

// Obtém o parâmetro 'profissional' da URL (via método GET)
// Se o parâmetro não existir, usa string vazia como valor padrão
$nomeProfissional = $_GET['profissional'] ?? '';

// Verifica se o nome do profissional está vazio
if (empty($nomeProfissional)) {
    // Retorna um array vazio em formato JSON
    echo json_encode([]);
    // Termina a execução do script
    exit;
}

// Inicia bloco try-catch para tratamento de exceções
try {
    // Define a consulta SQL para buscar usuários
    // Seleciona ID, nome, número do prontuário e situação
    $query = "SELECT 
                u.id_usuario,
                u.nome_usuario,
                u.numero_prontuario,
                u.situacao
              FROM usuarios u
              WHERE u.nome_profissional = :nome_profissional
              ORDER BY u.nome_usuario";  // Ordena os resultados pelo nome do usuário
    
    // Prepara a consulta SQL para execução
    $stmt = $pdo->prepare($query);
    
    // Associa o parâmetro nomeado :nome_profissional com a variável $nomeProfissional
    // PDO::PARAM_STR garante que o valor seja tratado como string
    $stmt->bindParam(':nome_profissional', $nomeProfissional, PDO::PARAM_STR);
    
    // Executa a consulta preparada
    $stmt->execute();
    
    // Busca TODOS os resultados como um array de arrays associativos
    // FETCH_ASSOC retorna apenas os nomes das colunas como chaves
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Converte o array de usuários para formato JSON e envia como resposta
    echo json_encode($usuarios);
    
} catch (PDOException $e) {
    // Captura exceções do tipo PDOException (erros relacionados ao banco de dados)
    // Retorna uma mensagem de erro em formato JSON contendo a mensagem da exceção
    echo json_encode(['error' => $e->getMessage()]);
}
?>