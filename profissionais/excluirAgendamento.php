<?php
// Inicia ou resume uma sessão PHP
// As sessões permitem armazenar informações entre diferentes páginas
session_start();

// Inclui o arquivo de configuração de conexão com o banco de dados
require_once "../config/conexao.php";

// Obtém os dados enviados via formulário POST
// Usa operador de coalescência nula (??) para definir valores padrão caso não existam

// ID do agendamento a ser excluído
$id_agendamento = $_POST['id_agendamento'] ?? '';

// ID do profissional (usado para redirecionamento)
$id_profissional = $_POST['id_profissional'] ?? '';

// Inicializa variáveis para mensagem e ícone de feedback
$mensagem = '';
$icone = '';

// Inicia bloco try-catch para tratamento de exceções
try {
    // Query SQL para excluir um agendamento da tabela 'agendamentos'
    // A exclusão é baseada no id_agendamento
    $sql = "DELETE FROM agendamentos WHERE id_agendamento = :id_agendamento";
    
    // Prepara a query SQL para execução
    $stmt = $pdo->prepare($sql);
    
    // Associa o valor do parâmetro :id_agendamento à variável $id_agendamento
    $stmt->bindValue(':id_agendamento', $id_agendamento);

    // Executa a query e verifica se foi bem-sucedida E se alguma linha foi afetada
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        // Se a exclusão foi bem-sucedida e pelo menos uma linha foi excluída
        $mensagem = 'Agendamento excluído com sucesso!';
        $icone = 'success'; // Ícone/tipo de mensagem de sucesso
    } else {
        // Se nenhuma linha foi afetada (agendamento não encontrado ou já excluído)
        $mensagem = 'Agendamento não encontrado ou já foi excluído';
        $icone = 'warning'; // Ícone/tipo de mensagem de aviso
    }
} catch (PDOException $e) {
    // Captura exceções do tipo PDOException (erros relacionados ao banco de dados)
    $mensagem = 'Erro ao excluir agendamento';
    $icone = 'error'; // Ícone/tipo de mensagem de erro
}

// Determina a URL de redirecionamento após a exclusão
// Usa operador ternário para decidir a URL baseada no id_profissional
$redirect = empty($id_profissional) 
    ? '../gerenciarAgendamento.php?id=' . $id_profissional  // Se não houver id_profissional, redireciona para página geral
    : '../gerenciarAgendamento.php?id=' . $id_profissional; // Se houver, adiciona como parâmetro na URL

// Armazena a mensagem e o tipo de mensagem na sessão PHP
// Isso permite que a próxima página acesse essas informações
$_SESSION['mensagem'] = $mensagem;
$_SESSION['tipo_mensagem'] = $icone;

// Exibe um alerta JavaScript com a mensagem e redireciona para a página especificada
echo "<script>
    alert('" . addslashes($mensagem) . "');
    window.location.href = '$redirect';
</script>";

// Termina a execução do script
exit();
?>