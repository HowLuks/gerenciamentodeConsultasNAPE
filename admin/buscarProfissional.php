<?php
//Pega a configuração do banco de dados
require_once "./config/conexao.php";
//Inicia uma session
session_start();

//Se não tiver a session 'id_logado' vai ser redirecionado para 'index.php
//Se não existir a session 'id_logado
if (!isset($_SESSION['id_logado'])) {
    //Redireciona para 'index.php'
    header("location: ./index.php");
    exit(); //Encerra a execução do script
}

//Pega o nome da session
$nome = $_SESSION['nome_logado'];
//Pega o ID da session
$id = $_SESSION['id_logado'];

/**
 * Função para buscar o Cargo do Profissional
 * @param PDO $pdo Instancia do banco de dados
 * @param mixed $id Id do usuario
 */
function buscarCargoProfissional($pdo, $id)
{
    try {
        //Query de buscar o cargo do profissional pelo id dele
        $sql = "SELECT p.cargo_profissional FROM profissionais p WHERE p.id_profissional = :id_profissional";
        //Prepara a query
        $stmt = $pdo->prepare($sql);
        //Vincula os paramentros a query de inserção
        $stmt->bindValue(":id_profissional", $id);
        //Executa a inserção
        $stmt->execute();
        
        //Retorna em um array associativo
        //PDO:FETCH_ASSOC => Retorna apenas os nomes das colunas como chave de array
        $cargo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Retorna o cargo do profissional e se não encontrar vai ser null
        return $cargo ? $cargo['cargo_profissional'] : null;
    } catch (Exception $e) {
        // Exibe uma mensangem se não encontrar o cargo do profissional
        echo "Erro ao encontrar cargo ".$e->getMessage();
        // Retorna false para indicar que a operação falhou
        return false;
    }
}

// Chama a função para buscar o cargo do profissional
$cargo = buscarCargoProfissional($pdo, $id);

/**
 * Função para buscar o nome de um profissional pelo ID
 * 
 * @param PDO $pdo Objeto de conexão PDO com o banco de dados
 * @param mixed $id ID do profissional a ser buscado
 * @return string|null Retorna o nome do profissional ou null se não encontrado
 */
function buscarNomeProfissional($pdo, $id)
{
    try {
        // Query SQL para selecionar apenas o nome_profissional da tabela profissionais
        // 'p' é um alias para a tabela profissionais
        $sql = "SELECT p.nome_profissional FROM profissionais p WHERE p.id_profissional = :id_profissional";
        
        // Prepara a consulta SQL para execução
        $stmt = $pdo->prepare($sql);
        
        // Vincula o parâmetro :id_profissional ao valor da variável $id
        // Nota: Não está especificado o tipo do parâmetro (ex: PDO::PARAM_INT)
        $stmt->bindValue(":id_profissional", $id);
        
        // Executa a consulta preparada
        $stmt->execute();

        // Busca apenas uma linha (fetch) como array associativo
        // Como estamos buscando por ID, esperamos no máximo um resultado
        $cargo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Operador ternário: se $cargo não for vazio, retorna o valor de 'nome_profissional'
        // Caso contrário, retorna null
        return $cargo ? $cargo['nome_profissional'] : null;
        
    } catch (Exception $e) {
        // Captura qualquer exceção genérica
        // Exibe uma mensagem de erro simplificada na tela
        // Problema: não está retornando nada em caso de erro
        echo "Erro ao encontrar nome";
        // Falta um return aqui - a função não retorna nada em caso de exceção
    }
}

// Chamada da função para buscar o nome do profissional
// ATENÇÃO: A variável $id precisa estar definida anteriormente
// Isso pode causar um erro se $id não existir ou se $pdo não estiver conectado
$nome = buscarNomeProfissional($pdo, $id);

// $nome conterá:
// - string com o nome do profissional (se encontrado)
// - null (se não encontrado)
// - null (se ocorrer erro - devido ao catch não ter return)
// - undefined (se $pdo ou $id não estiverem definidos)
?>