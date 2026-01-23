<?php
$pdo = new PDO("mysql:host=localhost;dbname=nape;port=3308;", "root", "");

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM teste WHERE nome = :nome AND senha = :senha";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
    $stmt->bindParam(":senha", $senha, PDO::PARAM_STR);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {  
        echo "Usuário não encontrado";
        exit; // para o código aqui
    }

    // Se encontrar, vai para outra página
    header("Location: desfds.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="post">
    <input type="text" name="nome">
    <input type="text" name="senha">
    <button type="submit">ENTRAR</button>
    </form>
</body>

</html>