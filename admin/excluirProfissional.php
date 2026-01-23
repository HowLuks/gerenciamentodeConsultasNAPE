<?php
// Inicia a sessão para acessar as variáveis de sessão
session_start();

// Inclui o arquivo de configuração da conexão com o banco de dados
require_once "../config/conexao.php";

// Obtém o ID do profissional a ser excluído a partir do formulário POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

/**
 * Função para excluir um profissional do banco de dados
 */
function excluirProf($id, $pdo)
{
    try {
        // Verifica se o usuário está tentando excluir a si mesmo
        if ($id == $_SESSION['id_logado']) {
            echo "<script>alert('Não pode excluir seu proprio perfil');
            window.location.href = '../equipeNAPE.php';</script>";
            exit();
        } else {
            // Query SQL para deletar um profissional pelo ID
            $sql = "DELETE FROM profissionais WHERE id_profissional = :id_profissional";

            // Prepara a query para execução
            $stmt = $pdo->prepare($sql);

            // Vincula o parâmetro ID como inteiro para segurança
            $stmt->bindValue(":id_profissional", $id, PDO::PARAM_INT);

            // Executa a query e verifica se foi bem-sucedida
            if ($stmt->execute()) {
                // Se a exclusão for bem-sucedida, redireciona para a página da equipe
                header("Location: ../equipeNAPE.php");
                exit;
            } else {
                echo "Erro ao deletar profissional.";
            }
        }
    } catch (PDOException $e) {
        echo "Erro ao deletar profissional: " . $e->getMessage();
    }
}

// Chama a função para excluir o profissional
excluirProf($id, $pdo);

?>