<?php
// Inclui o arquivo de configuração da conexão com o banco de dados
require_once "./config/conexao.php";

// Obtém o ID do profissional a partir do parâmetro GET 'id'
// Se não existir ou não for um número válido, define como 0
// intval() converte o valor para inteiro (0 se não for numérico)
if ($_SESSION['id_logado'] == 1 or $_SESSION['cargo_logado'] == "coordenador") {
    // Coordenador master (ID 1) pode acessar qualquer perfil
    $id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id_logado'];
} else {
    // Outros usuários só podem acessar seu próprio perfil
    $id = intval($_SESSION['id_logado']);
}
/**
 * Busca um profissional pelo ID - função para obter o nome do profissional
 */
function buscarProfissionalCompleto(PDO $pdo, int $id)
{
    try {
        // Query para buscar dados completos do profissional
        $sql = "SELECT p.id_profissional, p.nome_profissional, p.cargo_profissional 
                FROM profissionais p
                WHERE p.id_profissional = :id_profissional";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id_profissional", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Erro ao buscar profissional: " . $e->getMessage();
        return false;
    }
}

// Chama a função atualizada
$profissional = buscarProfissionalCompleto($pdo, $id);


/**
 * Função para consultar dados de usuários relacionados a um profissional
 * Realiza um JOIN entre as tabelas usuarios e profissionais
 */
function consultarTabelaProf($pdo, $id)
{
    // Query que busca todos os dados de usuários que têm o mesmo nome do profissional
    // JOIN conecta a tabela usuarios com profissionais pelo nome_profissional
    $sql = "SELECT nome_profissional, cargo_profissional, u.*
        FROM usuarios u 
        JOIN usuario_profissional up ON u.id_usuario = up.id_usuario
        JOIN profissionais p ON up.id_profissional = p.id_profissional WHERE p.id_profissional = :id_profissional";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":id_profissional", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Chama a função para consultar a tabela de usuários relacionados
$consultarTabela = consultarTabelaProf($pdo, $id);


/**
 * Função para consultar agendamentos relacionados a um profissional
 * Realiza múltiplos JOINs entre agendamentos, profissionais e usuarios
 */
function consultarTabelaProfA($pdo, $id)
{
    // Query que busca agendamentos junto com informações do profissional e usuário
    // JOIN triplo: agendamentos → agendamento_profissional → usuarios
    $sql = "SELECT a.*, u.numero_prontuario, u.laudado, p.nome_profissional, p.cargo_profissional
            FROM agendamentos a
            JOIN agendamento_profissional ag ON a.id_agendamento = ag.id_agendamento 
            JOIN profissionais p ON ag.id_profissional = p.id_profissional JOIN usuarios u on u.nome_usuario = a.nome_usuario WHERE p.id_profissional = :id_profissional";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":id_profissional", $id, PDO::PARAM_INT);
    $stmt->execute();

    // Retorna TODOS os agendamentos encontrados
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Chama a função para consultar a tabela de agendamentos
$consultarTabelaA = consultarTabelaProfA($pdo, $id);


/**
 * Função para obter todos os dados de um profissional pelo ID
 */
function pegaIdProf($id, $pdo)
{
    // Query para selecionar TODOS os campos do profissional
    $sql = "SELECT * FROM profissionais WHERE id_profissional = :id_profissional";
    $stmt = $pdo->prepare($sql);
    // bindParam() em vez de bindValue() - liga a variável em si (por referência)
    // Isso significa que se $id mudar depois, a query usará o novo valor
    $stmt->bindParam(":id_profissional", $id, PDO::PARAM_INT);
    $stmt->execute();
    // Retorna um array associativo com todos os dados do profissional
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Chama a função para pegar todos os dados do profissional
// Nota: A ordem dos parâmetros está invertida em relação às outras funções ($id primeiro)
$profissional = pegaIdProf($id, $pdo);

function consultarUsuario($pdo, $id)
{
    // Query que busca todos os dados de usuários que têm o mesmo nome do profissional
    // JOIN conecta a tabela usuarios com profissionais pelo nome_profissional
    $sql = "SELECT * FROM usuarios ORDER BY numero_prontuario ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Retorna TODAS as linhas encontradas (fetchAll)
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Chama a função para consultar a tabela de usuários relacionados
$consultarUsuarios = consultarUsuario($pdo, $id);
?>