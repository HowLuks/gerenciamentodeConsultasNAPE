<?php
// profissionais/adicionarProfissionalParaUsuario.php
require_once "../config/conexao.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Método de requisição inválido');</script>";
    echo "<script>window.history.back();</script>";
    exit;
}

$id_usuario = $_POST['id_usuario'] ?? null;
$id_profissional = $_POST['id_profissional'] ?? null;

if (empty($id_usuario) || empty($id_profissional)) {
    echo "<script>alert('Selecione um usuário e um profissional');</script>";
    echo "<script>window.history.back();</script>";
    exit;
}

try {
    // Inserir na tabela de relacionamento
    $sqlInsert = "INSERT INTO usuario_profissional (id_usuario, id_profissional) 
                  VALUES (?, ?)";
    $stmtInsert = $pdo->prepare($sqlInsert);

    if ($stmtInsert->execute([$id_usuario, $id_profissional])) {
        echo "<script>alert('Usuário vinculado ao profissional com sucesso!');</script>";
        echo "<script>window.location.href = '../usuariosGerais.php';</script>";
        exit;
    } else {
        echo "<script>alert('Erro ao cadastrar. Tente novamente.');</script>";
        echo "<script>window.history.back();</script>";
        exit;
    }
    
} catch (PDOException $e) {
    // Verifica se é erro de duplicidade
    if ($e->getCode() == 23000) {
        echo "<script>alert('Este usuário já está vinculado a este profissional.');</script>";
        echo "<script>window.history.back();</script>";
        exit;
    } else {
        // Verifica se é erro de tabela não existe
        if (strpos($e->getMessage(), 'usuario_profissional') !== false) {
            echo "<script>alert('Erro: Tabela de relacionamento não encontrada.');</script>";
        } else {
            echo "<script>alert('Ocorreu um erro no sistema. Por favor, contate o administrador.');</script>";
        }
        echo "<script>window.history.back();</script>";
        exit;
    }
}