<?php
// Inclui o arquivo de configuração da conexão com o banco de dados
require_once "../config/conexao.php";

// Inicia a sessão para armazenar dados do usuário logado
session_start();

/**
 * Função para verificar credenciais de login
 * 
 * @param PDO $pdo Conexão com o banco de dados
 * @param string $nome Nome do profissional
 * @param string $senha Senha em texto puro
 * @return void Não retorna valor, mas redireciona ou exibe mensagens
 */
function verificaLogin($pdo, $nome, $senha)
{
    try {
        // Query para buscar profissional pelo nome
        $sql = "SELECT * FROM profissionais WHERE nome_profissional = :nome_profissional";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":nome_profissional", $nome, PDO::PARAM_STR);
        $stmt->execute();
        
        // Obtém o usuário como array associativo
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se usuário existe e a senha está correta
        // password_verify() compara a senha em texto puro com o hash armazenado
        if ($usuario && password_verify($senha, $usuario['senha'])) {

            // SESSÕES CORRETAS - Armazena dados do usuário na sessão
            $_SESSION['nome_logado'] = $usuario['nome_profissional'];
            $_SESSION['cargo_logado']   = $usuario['cargo_profissional'];
            $_SESSION['id_logado']   = $usuario['id_profissional'];
            
            // Sugestão: adicionar cargo também na sessão para uso futuro
            // $_SESSION['cargo_logado'] = $usuario['cargo_profissional'];

            // REDIRECIONAMENTO baseado no cargo
            if ($usuario['cargo_profissional'] == "coordenador") {
                // Coordenador vai para página de todos os usuários
                header("Location: ../usuarios.php");
                exit();
            } else {
                // Outros profissionais vão para sua página de gerenciamento
                header("Location: ../gerenciarUsuario.php");
                exit();
            }
        } else {
            // Credenciais inválidas - usa JavaScript para alerta e redirecionamento
            echo "<script>
                alert('Usuário ou senha inválidos');
                window.location.href = '../index.php';
            </script>";
            exit();
        }
    } catch (Exception $e) {
        // Em caso de erro no banco
        $_SESSION['erro_login'] = "Erro ao logar usuário";
        echo "Erro ao fazer login: " . $e->getMessage();
        exit();
    }
}

// Verifica se a requisição é do tipo POST (formulário enviado)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Obtém dados do formulário com operador de coalescência nula (??)
    $nome = $_POST['nome_profissional'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Validação básica dos campos
    if (empty($nome) || empty($senha)) {
        $_SESSION['erro_login'] = "Preencha todos os campos";
        header("Location: ../index.php");
        exit();
    }

    // Chama a função de verificação de login
    verificaLogin($pdo, $nome, $senha);
}
?>