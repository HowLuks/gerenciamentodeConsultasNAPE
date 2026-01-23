<?php 
$pdo = new PDO("mysql:host=localhost;dbname=nape;port=3308;", "root","");

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $nome = $_POST['nome'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $numero = rand(1000,9999);

    $sql = "INSERT INTO teste (nome,senha,numero_verificacao) values (:nome,:senha,:numero_verificacao)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":nome",$nome,PDO::PARAM_STR);
    $stmt->bindParam(":senha",$senha,PDO::PARAM_STR);
    $stmt->bindParam(":numero_verificacao",$numero,PDO::PARAM_INT);
    $stmt->execute();

    echo "alert('USUARIO CADASRADO')";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="nome">
        <input type="text" name="senha">
        <button type="submit">CADASTRAR</button>
        <a href="login.php">Esqueci a senha</a>
    </form>
</body>
</html>