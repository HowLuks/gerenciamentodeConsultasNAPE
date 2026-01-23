<?php
// Inclui o arquivo de configuração da conexão com o banco de dados
require_once "./config/conexao.php";

/**
 * Função para consultar profissionais excluindo coordenadores
 * 
 * @param PDO $pdo Objeto de conexão PDO com o banco de dados
 * @return array|false Retorna array associativo com os dados ou false em caso de erro
 */
function consultarProfissionais($pdo)
{
    try {
        // Query SQL para buscar profissionais que NÃO são coordenadores
        // Seleciona apenas ID, nome e cargo dos profissionais
        // Condição: cargo_profissional != 'coordenador' (diferente de coordenador)
        // Ordena por ID em ordem ascendente (do menor para o maior)
        $sql = "SELECT p.id_profissional, p.nome_profissional, p.cargo_profissional 
                FROM profissionais p 
                WHERE p.cargo_profissional != 'coordenador' 
                ORDER BY id_profissional ASC";
        
        // Prepara a query para execução
        $stmt = $pdo->prepare($sql);
        
        // Executa a query (não há parâmetros para bind)
        $stmt->execute();
        
        // Retorna TODOS os resultados como array associativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        // Captura exceção e exibe mensagem de erro
        echo "Erro ao consultar profissionais " . $e->getMessage();
        
        // Retorna false para indicar falha na operação
        return false;
    }
}

// Chama a função para consultar profissionais (exceto coordenadores)
// Armazena o resultado na variável $consultar
$consultar = consultarProfissionais($pdo);


/**
 * Função para consultar TODOS os profissionais sem exceções
 * 
 * @param PDO $pdo Objeto de conexão PDO com o banco de dados
 * @return array|false Retorna array associativo com todos os profissionais ou false em caso de erro
 */
function consultarTodosProfissionais($pdo){
    try{
        // Query SQL para buscar TODOS os profissionais
        // Seleciona ID, nome e cargo de TODOS os registros
        // Não há cláusula WHERE (traz todos os registros)
        // Ordena por ID em ordem ascendente
        $sql = "SELECT p.id_profissional, p.nome_profissional, p.cargo_profissional 
                FROM profissionais p 
                ORDER BY id_profissional ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        // Retorna TODOS os resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }catch (Exception $e) {
        // Captura exceção e exibe mensagem de erro
        echo "Erro ao consultar profissionais: " . $e->getMessage();
        
        // Retorna false para indicar falha
        return false;
    }
}

// Chama a função para consultar TODOS os profissionais
// Armazena o resultado na variável $todosProfissionais
$todosProfissionais = consultarTodosProfissionais($pdo);

?>