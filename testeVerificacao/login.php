<?php 
$pdo = new PDO("mysql:host=localhost;dbname=nape;port=3308;", "root","");
if($_SERVER['REQUEST_METHOD'] === "POST"){
    $senha = $_POST['senha'];
    $numero = $_POST['numero'];

    $sql = "SELECT * FROM teste WHERE numero_verificacao = :numero_verificacao";
    $stmt =  $pdo->prepare($sql);
    $stmt->bindParam(":numero_verificacao",$numero,PDO::PARAM_INT);
    $stmt->execute();
    
    if($stmt->rowCount() == 0){
        echo "Numero não encontrado";
        return false;
    }
    
    $sql2 = "UPDATE teste SET senha = :senha WHERE numero_verificacao = :numero_verificacao";
    $stmt =  $pdo->prepare($sql2);
    $stmt->bindParam(":senha",$senha,PDO::PARAM_STR);
    $stmt->bindParam(":numero_verificacao",$numero,PDO::PARAM_INT);
    $stmt->execute();
    
    header("location: senha.php");
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
        <input type="text" name="numero">
        <input type="text" name="senha">
        <button type="submit">Verificar</button>
    </form>
</body>
</html>