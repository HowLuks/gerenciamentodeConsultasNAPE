<?php
require_once "../config/conexao.php";

function adicionarProfissional($pdo, $nome, $email, $senha, $cargo, $endereco, $vinculo, $cidade, $cpf, $data_nascimento, $contato)
{
    try {
        // Verifica duplicidade de nome ou email
        $verificar = "SELECT * FROM profissionais WHERE nome_profissional = :nome_profissional OR email = :email";
        $st = $pdo->prepare($verificar);
        $st->bindValue(":email", $email, PDO::PARAM_STR);
        $st->bindValue(":nome_profissional", $nome, PDO::PARAM_STR);
        $st->execute();

        if ($st->rowCount() > 0) {
            echo "<script>alert('Nome ou email já cadastrado!'); window.history.back();</script>";
            return false;
        }

        // Verifica duplicidade de CPF
        $verificarCpf = "SELECT * FROM profissionais WHERE cpf = :cpf";
        $stCpf = $pdo->prepare($verificarCpf);
        $stCpf->bindValue(":cpf", $cpf, PDO::PARAM_INT);
        $stCpf->execute();

        if ($stCpf->rowCount() > 0) {
            echo "<script>alert('CPF já cadastrado no sistema!'); window.history.back();</script>";
            return false;
        }

        // Verifica duplicidade de contato (telefone)
        $verificarContato = "SELECT * FROM profissionais WHERE contato = :contato";
        $stContato = $pdo->prepare($verificarContato);
        $stContato->bindValue(":contato", $contato, PDO::PARAM_INT);
        $stContato->execute();

        if ($stContato->rowCount() > 0) {
            echo "<script>alert('Número de contato já cadastrado no sistema!'); window.history.back();</script>";
            return false;
        }

        // Se chegou aqui, não há duplicidades
        $sql = "INSERT INTO profissionais 
                (nome_profissional, email, senha, cargo_profissional, endereco, vinculo, cidade, cpf, data_nascimento, contato) 
                VALUES (:nome_profissional, :email, :senha, :cargo_profissional, :endereco, :vinculo, :cidade, :cpf, :data_nascimento, :contato)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":nome_profissional", $nome);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":senha", $senha);
        $stmt->bindValue(":cargo_profissional", $cargo);
        $stmt->bindValue(":endereco", $endereco);
        $stmt->bindValue(":vinculo", $vinculo);
        $stmt->bindValue(":cidade", $cidade);
        $stmt->bindValue(":cpf", $cpf);
        $stmt->bindValue(":data_nascimento", $data_nascimento);
        $stmt->bindValue(":contato", $contato);

        $stmt->execute();

        echo "<script>
                alert('Usuário adicionado com sucesso!');
                window.location.href = '../equipeNAPE.php';
              </script>";
        return true;

    } catch (Exception $e) {
        echo "<script>alert('Erro ao adicionar profissional: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    try {
        $nome = $_POST['nome_profissional'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $cargo = $_POST['cargo_profissional'];
        $endereco = $_POST['endereco'];
        $vinculo = $_POST['vinculo'];
        $cidade = $_POST['cidade'];
        $contato = $_POST['contato'];
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
        $data_nascimento = $_POST['data_nascimento'];
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Validações básicas
        if (empty($nome) || empty($email) || empty($senha) || empty($cargo) || empty($vinculo)) {
            echo "<script>alert('Preencha todos os campos obrigatórios!'); window.history.back();</script>";
            exit;
        }

        // Validação do CPF (11 dígitos)
        if (strlen($cpf) != 11) {
            echo "<script>alert('CPF inválido! Deve conter 11 dígitos.'); window.history.back();</script>";
            exit;
        }

        adicionarProfissional($pdo, $nome, $email, $senhaHash, $cargo, $endereco, $vinculo, $cidade, $cpf, $data_nascimento, $contato);

    } catch (Exception $e) {
        echo "<script>alert('Erro interno: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}