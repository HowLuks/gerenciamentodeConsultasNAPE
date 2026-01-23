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
$laudado = $_POST['laudado'] ?? '';
$quantidade_terapias = $_POST['quantidade_terapias'] ?? 0;
$informacao_adicional = $_POST['informacao_adicional'] ?? '';

// **Tenta obter o id_profissional de várias fontes diferentes:**
$id_profissional = 0;

// 1. PRIMEIRO: Tenta obter do POST (campo hidden do formulário AJAX)
if (isset($_POST['id_profissional']) && $_POST['id_profissional'] > 0) {
    $id_profissional = intval($_POST['id_profissional']);
    error_log("id_profissional do POST: " . $id_profissional);
}
// 2. SEGUNDO: Tenta obter do referer (URL anterior - gerenciarUsuario.php?id=X)
elseif (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (preg_match('/[?&]id=(\d+)/', $referer, $matches)) {
        $id_profissional = intval($matches[1]);
        error_log("id_profissional do referer: " . $id_profissional);
    }
}
// 3. TERCEIRO: Tenta obter da sessão
elseif (isset($_SESSION['id_profissional'])) {
    $id_profissional = intval($_SESSION['id_profissional']);
    error_log("id_profissional da sessão: " . $id_profissional);
}
// 4. QUARTO: Tenta buscar do próprio usuário no banco
elseif ($id_usuario > 0) {
    try {
        $stmt = $pdo->prepare("SELECT id_profissional FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->execute([':id_usuario' => $id_usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['id_profissional']) && $result['id_profissional'] > 0) {
            $id_profissional = intval($result['id_profissional']);
            error_log("id_profissional do banco: " . $id_profissional);
        }
    } catch (Exception $e) {
        error_log("Erro ao buscar id_profissional do banco: " . $e->getMessage());
    }
}

// Se ainda não tem id_profissional, tenta padrão 1 ou mostra erro
if ($id_profissional <= 0) {
    // Tenta um valor padrão (ajuste conforme sua necessidade)
    $id_profissional = 1; // ou null, dependendo do seu sistema
    error_log("id_profissional definido como padrão: " . $id_profissional);
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
                laudado = :laudado,
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
        ':laudado' => $laudado,
        ':situacao' => $situacao,
        ':diagnostico' => $diagnostico,
        ':quantidade_terapias' => $quantidade_terapias,
        ':informacao_adicional' => $informacao_adicional
    ]);

    // **VERIFICA SE FOI ATUALIZADO COM SUCESSO**
    if ($stmt->rowCount() > 0) {
        // **REDIRECIONAMENTO DINÂMICO**
        $redirectUrl = '../usuariosGerais.php';
        
        // Adiciona o id_profissional se for válido
        if ($id_profissional > 0) {
            $redirectUrl;
        }
        
        echo "<script>
                alert('Usuário atualizado com sucesso!');
                window.location.href = '" . $redirectUrl . "';
              </script>";
    } else {
        echo "<script>
                alert('Nenhuma alteração foi realizada ou usuário não encontrado.');
                window.history.back();
              </script>";
    }
    exit();

} catch (PDOException $e) {
    echo "<script>
            alert('Erro ao atualizar: " . addslashes($e->getMessage()) . "');
            window.history.back();
          </script>";
    exit();
}
?>