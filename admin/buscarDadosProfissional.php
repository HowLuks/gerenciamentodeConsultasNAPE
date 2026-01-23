<?php
// Inclui o arquivo de configuração de conexão com o banco de dados
require_once "./config/conexao.php";
if ($_SESSION['id_logado'] == 1 or $_SESSION['cargo_logado'] == "coordenador") {
    // Coordenador master (ID 1) pode acessar qualquer perfil
    $id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id_logado'];
} else {
    // Outros usuários só podem acessar seu próprio perfil
    $id = intval($_SESSION['id_logado']);
}

/**
 * Função para buscar todos os dados do profissional na
 * Página: 'dados_pessoais.php' 
 * 
 * @param PDO $pdo Objeto de conexão PDO com o banco de dados
 * @param mixed $id ID do profissional a ser buscado
 * @return array|false Retorna um array associativo com os dados do profissional 
 *                    ou false em caso de erro
 */
function buscarDadosProfissional(PDO $pdo, int $id)
{
    try {
        // Query SQL para selecionar todos os campos do profissional com o ID especificado
        $sql = "SELECT * FROM profissionais WHERE id_profissional = :id_profissional";
        
        // Prepara a consulta SQL para execução
        $stmt = $pdo->prepare($sql);
        
        // Vincula o parâmetro :id_profissional ao valor da variável $id
        // PDO::PARAM_INT especifica que o valor é um inteiro, prevenindo SQL injection
        $stmt->bindValue(":id_profissional", $id, PDO::PARAM_INT);
        
        // Executa a consulta preparada
        $stmt->execute();
        
        // Retorna todos os resultados como um array associativo
        // PDO::FETCH_ASSOC retorna apenas os nomes das colunas como chaves do array
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultado ?: null; //Retorna null se não encontrar

    } catch (PDOException $e) {
        // Captura exceções específicas do PDO (erros de banco de dados)
        // Exibe uma mensagem de erro na tela (em produção, isto deveria ser logado)
        echo "Erro ao buscar dados do usuário: " . $e->getMessage();
        
        // Retorna false para indicar que a operação falhou
        return false;
    }
}

$dadosProf = buscarDadosProfissional($pdo, $id);
?>