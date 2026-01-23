<?php
// Inclui o arquivo de conexão com o banco de dados
require_once "../config/conexao.php";

/**
 * Função para buscar um usuário pelo ID
 * @param PDO $pdo Instância de conexão ao banco de dados
 * @param int $id - ID do usuário que queremos buscar
 * @return array|null - retorna um array associativo com os dados do usuário ou null se não encontrado
 */
function buscarIdUsuarios($pdo, $id) {
    // Cria a query SQL para selecionar todos os campos do usuário pelo ID
    $sql = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
    
    // Prepara a query para execução segura (evitando SQL Injection)
    $stm = $pdo->prepare($sql);
    
    // Vincula o parâmetro :id_usuario com a variável $id (como inteiro)
    $stm->bindParam(":id_usuario", $id, PDO::PARAM_INT);
    
    // Executa a query no banco
    $stm->execute();
    
    // Retorna o resultado como array associativo
    // Se não encontrar nenhum registro, retorna null
    return $stm->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Chama a função para buscar o usuário pelo ID
// OBS: $id precisa estar definido antes desta linha (por exemplo vindo de $_GET['id'])
$usuario = buscarIdUsuarios($pdo,$id);