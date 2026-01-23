<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="admin/adicionarProfissional.php" method="post">
        <label for="text">Nome:</label>
        <input type="text" name="nome_profissional">
        <br>
        <label for="text">email:</label>
        <input type="text" name="email">
        <br>
        <label for="text">senha:</label>
        <input type="text" name="senha">
        <br>
        <label for="text">cargo:</label>
        <input type="text" name="cargo_profissional">
        <br>
        <label for="text">endereco:</label>
        <input type="text" name="endereco">
        <br>
        <label for="text">vinculo:</label>
        <input type="text" name="vinculo">
        <br>
        <label for="text">cidade:</label>
        <input type="text" name="cidade">
        <br>
        <label for="text">data_nascimento:</label>
        <input type="date" name="data_nascimento">
        <br>
        <label for="text">cpf:</label>
        <input type="text" name="cpf">
        <br>
        <button type="submit">ADICIONAR</button>
    </form>
</body>
</html>