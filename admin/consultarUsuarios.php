<?php
// Inclui o arquivo de configuração da conexão com o banco de dados
require_once "./config/conexao.php";

/**
 * Função para contar usuários com laudo (laudado = 'sim')
 * associados a um profissional específico
 * 
 * @param PDO $pdo Conexão com o banco de dados
 * @param int $id ID do profissional
 * @return int Número de usuários laudados (0 em caso de erro)
 */
function usuariosLaudado($pdo, $id)
{
    try {
        // Query para contar usuários:
        // - JOIN entre usuarios e profissionais pelo nome_profissional
        // - Filtra por ID do profissional
        // - Apenas usuários com status = 1 (ativo)
        // - Apenas usuários com laudado = 'sim'
        $sql = "SELECT COUNT(*)
                FROM usuarios u 
                JOIN usuario_profissional up ON up.id_usuario = u.id_usuario
                WHERE up.id_profissional = :id_profissional
                AND u.laudado = 'sim'";

        $stmt = $pdo->prepare($sql);
        // bindParam() liga a variável por referência
        $stmt->bindParam(":id_profissional", $id, PDO::PARAM_INT);
        $stmt->execute();

        // fetchColumn() retorna apenas a primeira coluna do resultado
        // COUNT(*) retorna um número inteiro
        return $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        // Em produção, usar error_log() é melhor que exibir na tela
        error_log("Erro ao buscar laudados: " . $e->getMessage());
        // Retorna 0 em caso de erro para não quebrar a lógica
        return 0;
    }
}

// Chama a função para contar usuários laudados
// NOTA: A variável $id precisa estar definida anteriormente
$laudados = usuariosLaudado($pdo, $id);


/**
 * Função para contar usuários SEM laudo (laudado = 'não')
 * associados a um profissional específico
 * 
 * @param PDO $pdo Conexão com o banco de dados
 * @param int $id ID do profissional
 * @return int|false Número de usuários não laudados ou false em caso de erro
 */
function usuariosNaoLaudados($pdo, $id)
{
    try {
        // Query similar à anterior, mas com laudado = 'não'
        $sql = "SELECT COUNT(*)
                FROM usuarios u 
                JOIN usuario_profissional up ON up.id_usuario = u.id_usuario
                WHERE up.id_profissional = :id_profissional 
                AND u.laudado = 'não'";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_profissional", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
        
    } catch (Exception $e) {
        // Problema: mistura de tratamentos de erro (echo vs error_log)
        echo "Erro ao buscar não laudados " . $e->getMessage();
        // Retorna false, que é diferente de 0 - pode causar problemas em comparações
        return false;
    }
}

// Chama a função para contar usuários não laudados
$naoLaudados = usuariosNaoLaudados($pdo, $id);


/**
 * Função para contar TODOS os usuários associados a um profissional
 * independente de terem laudo ou não
 * 
 * @param int $id ID do profissional
 * @param PDO $pdo Conexão com o banco de dados
 * @return int|false Número total de usuários ou false em caso de erro
 */
function contarUsuarios($id, $pdo)
{
    try {
        // Conta todos os usuários do profissional
        // Sem filtro de 'laudado' ou 'status'
        $sql = "SELECT COUNT(*)
                FROM usuarios u 
                JOIN usuario_profissional up ON up.id_usuario = u.id_usuario
                WHERE up.id_profissional = :id_profissional";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_profissional", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
        
    } catch (Exception $e) {
        echo "Erro ao buscar todos os usuários " . $e->getMessage();
        // Retorna false - inconsistente com usuariosLaudado() que retorna 0
        return false;
    }
}

// Chama a função para contar os usuários Gerais
// Nota: Ordem dos parâmetros diferente das outras funções
$usuarios = contarUsuarios($id, $pdo);
function contarUsuariosGerais($id, $pdo)
{
    try {
        // Conta todos os usuários do profissional
        // Sem filtro de 'laudado' ou 'status'
        $sql = "SELECT COUNT(*) FROM usuarios";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
        
    } catch (Exception $e) {
        echo "Erro ao buscar todos os usuários " . $e->getMessage();
        // Retorna false - inconsistente com usuariosLaudado() que retorna 0
        return false;
    }
}

// Chama a função para contar todos os usuários
// Nota: Ordem dos parâmetros diferente das outras funções
$usuariosGerais = contarUsuariosGerais($id, $pdo);

/**
 * Função para contar usuários com laudo (laudado = 'sim')
 * associados a um profissional específico
 * 
 * @param PDO $pdo Conexão com o banco de dados
 * @param int $id ID do profissional
 * @return int Número de usuários laudados (0 em caso de erro)
 */
function usuariosGeraisLaudado($pdo, $id)
{
    try {
        // Query para contar usuários:
        // - JOIN entre usuarios e profissionais pelo nome_profissional
        // - Filtra por ID do profissional
        // - Apenas usuários com status = 1 (ativo)
        // - Apenas usuários com laudado = 'sim'
        $sql = "SELECT COUNT(*)
                FROM usuarios u
                AND u.laudado = 'sim'";

        $stmt = $pdo->prepare($sql);
        // bindParam() liga a variável por referência
        $stmt->execute();

        // fetchColumn() retorna apenas a primeira coluna do resultado
        // COUNT(*) retorna um número inteiro
        return $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        // Em produção, usar error_log() é melhor que exibir na tela
        error_log("Erro ao buscar laudados: " . $e->getMessage());
        // Retorna 0 em caso de erro para não quebrar a lógica
        return 0;
    }
}

// Chama a função para contar usuários laudados
// NOTA: A variável $id precisa estar definida anteriormente
$laudadosGerais = usuariosGeraisLaudado($pdo, $id);


/**
 * Função para contar usuários SEM laudo (laudado = 'não')
 * associados a um profissional específico
 * 
 * @param PDO $pdo Conexão com o banco de dados
 * @param int $id ID do profissional
 * @return int|false Número de usuários não laudados ou false em caso de erro
 */
function usuariosGeraisNaoLaudados($pdo, $id)
{
    try {
        // Query similar à anterior, mas com laudado = 'não'
        $sql = "SELECT COUNT(*)
                FROM usuarios u 
                WHERE u.laudado = 'não'";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
        
    } catch (Exception $e) {
        // Problema: mistura de tratamentos de erro (echo vs error_log)
        echo "Erro ao buscar não laudados " . $e->getMessage();
        // Retorna false, que é diferente de 0 - pode causar problemas em comparações
        return false;
    }
}

// Chama a função para contar usuários não laudados
$naoLaudadosGerais = usuariosGeraisNaoLaudados($pdo, $id);
?>