<?php
// Inclui o arquivo de configuração de conexão com o banco de dados
require_once "../config/conexao.php";

// Verifica se a requisição foi feita através do método POST
// Se não foi POST, mostra mensagem de erro e termina execução
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido");
}

// Recebe e valida os dados do formulário usando filter_input
// FILTER_VALIDATE_INT valida se o valor é um inteiro válido

// Obtém e valida o ID do usuário a ser excluído
// Se não for um inteiro válido, atribui 0
$id_usuario = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;

// Obtém e valida o ID do profissional (usado para redirecionamento)
$id_profissional = filter_input(INPUT_POST, 'id_profissional', FILTER_VALIDATE_INT) ?: 0;

// Validação: verifica se o ID do usuário é válido (maior que zero)
if ($id_usuario <= 0) {
    // Exibe alerta JavaScript e retorna à página anterior
    die("<script>
            alert('ID do usuário inválido');
            window.history.back();
         </script>");
}

// Inicia bloco try-catch para tratamento de exceções
try {
    // Query SQL para excluir o usuário da tabela 'usuarios'
    // A exclusão é baseada no id_usuario
    $query = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
    
    // Prepara a query SQL para execução
    $stmt = $pdo->prepare($query);
    
    // Associa o parâmetro :id_usuario à variável $id_usuario
    // PDO::PARAM_INT garante que o valor seja tratado como inteiro
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    
    // Executa a query de exclusão
    $stmt->execute();
    
    // Define a URL de redirecionamento padrão
    $redirectUrl = '../gerenciarUsuario.php?id=' . $id_profissional;
    
    // Se houver um ID de profissional válido, adiciona como parâmetro na URL
    if ($id_profissional > 0) {
        $redirectUrl;
    }
    
    // Verifica se alguma linha foi afetada pela operação DELETE
    if ($stmt->rowCount() > 0) {
        // Se houve exclusão (pelo menos uma linha excluída)
        echo "<script>
                alert('Usuário excluído com sucesso!');
                window.location.href = '$redirectUrl';
              </script>";
    } else {
        // Se nenhuma linha foi excluída (usuário não encontrado ou já excluído)
        echo "<script>
                alert('Usuário não encontrado ou já foi excluído');
                window.location.href = '$redirectUrl';
              </script>";
    }
    
} catch (PDOException $e) {
    // Captura exceções do tipo PDOException (erros relacionados ao banco de dados)
    
    // Verifica se o erro é relacionado a restrição de chave estrangeira
    // strpos procura a string 'foreign key constraint' na mensagem de erro
    if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        // Se for erro de chave estrangeira, significa que há registros relacionados ao usuário
        echo "<script>
                alert('Não é possível excluir este usuário porque existem registros relacionados.');
                window.history.back();
              </script>";
    } else {
        // Se for outro tipo de erro, mostra a mensagem de erro genérica
        // addslashes é usado para escapar caracteres especiais no JavaScript
        echo "<script>
                alert('Erro ao excluir usuário: " . $e->getMessage() . "');
                window.history.back();
              </script>";
    }
}
?>