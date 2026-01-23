<?php
// Define o cabeçalho HTTP para indicar que a resposta será em formato JSON
header('Content-Type: application/json');

// Configura o PHP para reportar todos os tipos de erros
error_reporting(E_ALL);

// Configura o PHP para exibir os erros na saída (útil para debugging)
ini_set('display_errors', 1);

// Obtém o parâmetro 'id_usuario' da URL, converte para inteiro
// Se não existir, atribui 0 como valor padrão
$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;

// Verifica se o ID do usuário é válido (maior que zero)
if ($id_usuario <= 0) {
    // Retorna erro em JSON se o ID for inválido
    echo json_encode(['erro' => 'ID não enviado ou inválido']);
    // Termina a execução do script
    exit;
}

// Inicia bloco try-catch para tratamento de exceções
try {
    // Inclui o arquivo de configuração de conexão com o banco de dados
    // __DIR__ representa o diretório atual do arquivo
    require_once __DIR__ . '/../config/conexao.php';
    
    // **VERIFICA SE EXISTE A COLUNA id_profissional NA TABELA usuarios**
    // Executa uma query que mostra as colunas da tabela 'usuarios'
    // onde o nome da coluna é igual a 'id_profissional'
    $stmt = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'id_profissional'");
    
    // Se houver pelo menos uma linha no resultado, significa que a coluna existe
    $tem_id_profissional = $stmt->rowCount() > 0;
    
    // Verifica se a coluna id_profissional existe na tabela
    if ($tem_id_profissional) {
        // Se a coluna existe, faz um JOIN com a tabela profissionais
        $query = "SELECT 
                    u.id_usuario,
                    u.nome_usuario,
                    u.numero_prontuario,
                    u.contato_usuario,
                    u.situacao,
                    u.laudado,
                    u.diagnostico,
                    u.quantidade_terapias,
                    u.informacao_adicional
                  FROM usuarios u
                  LEFT JOIN profissionais p ON u.id_profissional = p.id_profissional
                  WHERE u.id_usuario = :id_usuario";
    } else {
        // Se a coluna não existe, busca apenas os dados da tabela usuarios
        // Assume que há uma coluna 'nome_profissional' na própria tabela usuarios
        $query = "SELECT 
                    id_usuario,
                    nome_usuario,
                    numero_prontuario,
                    contato_usuario,
                    situacao,
                    laudado,
                    diagnostico,
                    quantidade_terapias,
                    informacao_adicional
                  FROM usuarios 
                  WHERE id_usuario = :id_usuario";
    }
    
    // Prepara a query SQL para execução
    $stmt = $pdo->prepare($query);
    
    // Associa o parâmetro :id_usuario com a variável $id_usuario
    // PDO::PARAM_INT garante que o valor seja tratado como inteiro
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    
    // Executa a query preparada
    $stmt->execute();
    
    // Busca o resultado como um array associativo
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verifica se algum dado foi encontrado
    if ($dados) {
        // Retorna os dados formatados em JSON
        // Usa operador de coalescência nula (??) para valores padrão se a chave não existir
        echo json_encode([
            'nome_usuario' => $dados['nome_usuario'] ?? '',
            'id_usuario' => $dados['id_usuario'] ?? '',
            'prontuario' => $dados['numero_prontuario'] ?? '',
            'contato' => $dados['contato_usuario'] ?? '',
            'situacao' => $dados['situacao'] ?? '',
            'laudado' => $dados['laudado'] ?? '',
            'diagnostico' => $dados['diagnostico'] ?? '',
            'qtd_terapias' => $dados['quantidade_terapias'] ?? '',
            'info_adicional' => $dados['informacao_adicional'] ?? ''
        ]);
    } else {
        // Retorna erro se usuário não for encontrado
        echo json_encode(['erro' => 'Usuário não encontrado']);
    }
    
} catch (PDOException $e) {
    // Captura exceções do PDO (erros de banco de dados)
    // Retorna erro detalhado em JSON (incluindo a query que falhou)
    echo json_encode([
        'erro' => 'Erro no banco de dados',
        'mensagem' => $e->getMessage(),
        'query' => $query ?? ''
    ]);
}
?>