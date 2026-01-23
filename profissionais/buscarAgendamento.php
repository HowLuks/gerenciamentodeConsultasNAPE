<?php
// Inclui o arquivo de configuração da conexão com o banco de dados
require_once "../config/conexao.php";

// Obtém o ID do agendamento a partir do parâmetro GET
// Usa o operador de coalescência nula (??) para evitar erro se não existir
$id = $_GET['id'] ?? '';

// Valida se o ID foi informado
if (empty($id)) {
    // Retorna erro em formato JSON
    echo json_encode(['success' => false, 'erro' => 'ID não informado']);
    exit; // Encerra a execução do script
}

try {
    // QUERY SQL para buscar detalhes de um agendamento específico
    // Observação: O comentário menciona buscar pelo id_agendamento ao invés de id_usuario
    
    $sql = "SELECT 
                a.id_agendamento,                    
                a.data as data_agendamento,          
                DATE_FORMAT(a.data, '%d/%m/%Y') as data_formatada, 
                a.hora as hora_agendamento,          
                a.status_agendamento as status,      
                a.nome_usuario,                      
                u.id_usuario,                        
                u.nome_usuario as nome_paciente,     
                u.numero_prontuario,                 
                u.contato_usuario as contato_paciente, 
                u.situacao,                        
                u.quantidade_terapias,               
                u.diagnostico,                      
                u.informacao_adicional,             
                u.laudado,                         
                u.multiprofissionais                
            FROM agendamentos a 
            LEFT JOIN usuarios u ON u.nome_usuario = a.nome_usuario
            WHERE a.id_agendamento = :id_agendamento";  // Filtra pelo ID do agendamento

    // Prepara a query para execução
    $stmt = $pdo->prepare($sql);
    
    // Vincula o parâmetro :id_agendamento ao valor de $id
    // Nota: Não está especificando o tipo (deveria ser PDO::PARAM_INT)
    $stmt->bindValue(':id_agendamento', $id);
    
    // Executa a query
    $stmt->execute();

    // Busca apenas uma linha (fetch) pois espera-se um único agendamento
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se encontrou o agendamento
    if ($agendamento) {
        // Adiciona a chave 'success' ao array com valor true
        $agendamento['success'] = true;
        
        // Converte o array para JSON e envia como resposta
        echo json_encode($agendamento);
    } else {
        // Retorna erro se não encontrou o agendamento
        echo json_encode(['success' => false, 'erro' => 'Agendamento não encontrado']);
    }
    
} catch (PDOException $e) {
    // Captura exceções específicas do PDO (erros de banco de dados)
    // Retorna erro detalhado em formato JSON
    echo json_encode([
        'success' => false, 
        'erro' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
}
?>