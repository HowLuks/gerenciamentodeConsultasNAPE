<?php
// Inclui o arquivo de configuração de conexão com o banco de dados
require_once "../config/conexao.php";

// Verifica se a requisição foi feita através do método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido");
}

// Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtém os dados do formulário enviados via POST
$id_usuario = $_POST['id_usuario'] ?? 0;
$nome = $_POST['nome'] ?? '';
$numero_prontuario = $_POST['numero_prontuario'] ?? '';
$contato_usuario = $_POST['contato_usuario'] ?? '';
$situacao = $_POST['situacao'] ?? 'vinculado';
$diagnostico = $_POST['diagnostico'] ?? '';
$quantidade_terapias = $_POST['quantidade_terapias'] ?? 0;
$informacao_adicional = $_POST['informacao_adicional'] ?? '';

// **Tenta obter o id_profissional de várias fontes diferentes:**
$id_profissional = 0;

// 1. Tenta obter do POST (campo hidden do formulário)
if (isset($_POST['id_profissional']) && $_POST['id_profissional'] > 0) {
    $id_profissional = intval($_POST['id_profissional']);
}
// 2. Tenta obter da sessão
elseif (isset($_SESSION['id_profissional'])) {
    $id_profissional = intval($_SESSION['id_profissional']);
}
// 3. Tenta obter do GET (referência via URL)
elseif (isset($_GET['prof_id'])) {
    $id_profissional = intval($_GET['prof_id']);
}

// Validação: verifica se o ID do usuário é válido
if ($id_usuario <= 0) {
    die("<script>alert('ID do usuário inválido'); window.history.back();</script>");
}

try {
    // Query SQL para atualizar os dados do usuário
    $query = "UPDATE usuarios SET 
                nome_usuario = :nome,
                numero_prontuario = :numero_prontuario,
                contato_usuario = :contato_usuario,
                situacao = :situacao,
                diagnostico = :diagnostico,
                quantidade_terapias = :quantidade_terapias,
                informacao_adicional = :informacao_adicional
              WHERE id_usuario = :id_usuario";

    $stmt = $pdo->prepare($query);
    
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':nome' => $nome,
        ':numero_prontuario' => $numero_prontuario,
        ':contato_usuario' => $contato_usuario,
        ':situacao' => $situacao,
        ':diagnostico' => $diagnostico,
        ':quantidade_terapias' => $quantidade_terapias,
        ':informacao_adicional' => $informacao_adicional
    ]);

    // **REDIRECIONAMENTO PARA gerenciarUsuario.php COM id_profissional**
    $redirectUrl = '../gerenciarUsuario.php?id=' . $id_profissional;
    
    // Verifica se tem id_profissional para adicionar à URL
    if ($id_profissional > 0) {
        $redirectUrl;
    }
    
    echo "<script>
            alert('Usuário atualizado com sucesso!');
            window.location.href = '" . $redirectUrl . "';
          </script>";
    exit();

} catch (PDOException $e) {
    echo "<script>
            alert('Erro ao atualizar: " . addslashes($e->getMessage()) . "');
            window.history.back();
          </script>";
    exit();
}
?>