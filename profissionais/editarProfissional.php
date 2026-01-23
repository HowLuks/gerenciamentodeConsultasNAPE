<?php
// Inclui o arquivo de configuração de conexão com o banco de dados
// O caminho "../config/conexao.php" significa: subir um nível e acessar a pasta config
require_once "../config/conexao.php";

// Verifica se é uma requisição AJAX
// Verifica se existe o parâmetro 'ajax' no POST e se seu valor é 'true'
$isAjax = isset($_POST['ajax']) && $_POST['ajax'] === 'true';

// Se não for uma requisição AJAX, executa o fluxo normal de edição
if (!$isAjax) {
    // Verifica se o método da requisição é POST
    // Se não for POST, mostra mensagem de erro e termina execução
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Acesso inválido");
    }
    
    // Coleta os dados enviados via formulário POST
    // Usa operador de coalescência nula (??) para definir valores padrão caso não existam
    
    // Tenta obter o ID do profissional de 'id_profissional' ou 'id_usuario' (fallback)
    // Se nenhum existir, atribui 0 como valor padrão
    $id_profissional = $_POST['id_profissional'] ?? $_POST['id_usuario'] ?? 0;
    
    // Obtém o nome do profissional
    $nome_profissional = $_POST['nome_profissional'] ?? '';
    
    // Obtém o email
    $email = $_POST['email'] ?? '';
    
    // Obtém a senha (pode estar vazia se não for alterada)
    $senha = $_POST['senha'] ?? '';
    
    // Obtém o cargo do profissional
    $cargo_profissional = $_POST['cargo_profissional'] ?? '';
    
    // Obtém o vínculo do profissional
    $vinculo = $_POST['vinculo'] ?? '';
    
    // Obtém a cidade
    $cidade = $_POST['cidade'] ?? '';
    
    // Obtém o endereço
    $endereco = $_POST['endereco'] ?? '';
    
    // Obtém o CPF
    $cpf = $_POST['cpf'] ?? '';
    
    // Obtém a data de nascimento
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    
    // Obtém o contato (telefone)
    $contato = $_POST['contato'] ?? '';

    // Validação básica: verifica se o ID do profissional é válido (maior que zero)
    if ($id_profissional <= 0) {
        // Exibe alerta JavaScript e retorna à página anterior
        echo "<script>
                alert('ID inválido');
                window.history.back();
              </script>";
        // Termina a execução do script
        exit();
    }

    // Inicia bloco try-catch para tratamento de exceções
    try {
        // Verifica se a senha foi fornecida (não está vazia)
        if (!empty($senha)) {
            // Se a senha foi fornecida, aplica hash de segurança
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Query SQL para atualizar o profissional incluindo a senha
            $query = "UPDATE profissionais SET 
                      nome_profissional = :nome_profissional,
                      email = :email,
                      senha = :senha,
                      cargo_profissional = :cargo_profissional,
                      vinculo = :vinculo,
                      cidade = :cidade,
                      endereco = :endereco,
                      cpf = :cpf,
                      contato = :contato,
                      data_nascimento = :data_nascimento
                      WHERE id_profissional = :id_profissional";

            // Prepara a query SQL
            $stmt = $pdo->prepare($query);
            
            // Executa a query com os parâmetros
            $stmt->execute([
                ':nome_profissional' => $nome_profissional,
                ':email' => $email,
                ':senha' => $senha_hash,
                ':cargo_profissional' => $cargo_profissional,
                ':vinculo' => $vinculo,
                ':cidade' => $cidade,
                ':endereco' => $endereco,
                ':cpf' => $cpf,
                ':contato' => $contato,
                ':data_nascimento' => $data_nascimento,
                ':id_profissional' => $id_profissional
            ]);
        } else {
            // Se a senha NÃO foi fornecida, atualiza sem alterar a senha
            $query = "UPDATE profissionais SET 
                      nome_profissional = :nome_profissional,
                      email = :email,
                      cargo_profissional = :cargo_profissional,
                      vinculo = :vinculo,
                      cidade = :cidade,
                      endereco = :endereco,
                      cpf = :cpf,
                      contato = :contato,
                      data_nascimento = :data_nascimento
                      WHERE id_profissional = :id_profissional";

            // Prepara a query SQL
            $stmt = $pdo->prepare($query);
            
            // Executa a query com os parâmetros (sem a senha)
            $stmt->execute([
                ':nome_profissional' => $nome_profissional,
                ':email' => $email,
                ':cargo_profissional' => $cargo_profissional,
                ':vinculo' => $vinculo,
                ':cidade' => $cidade,
                ':endereco' => $endereco,
                ':cpf' => $cpf,
                ':contato' => $contato,
                ':data_nascimento' => $data_nascimento,
                ':id_profissional' => $id_profissional
            ]);
        }

        // Verifica se alguma linha foi atualizada no banco de dados
        if ($stmt->rowCount() > 0) {
            // Se houve atualização, mostra alerta de sucesso e redireciona
            echo "<script>
                    alert('Usuário atualizado com sucesso!');
                    window.location.href = '../formulario.php?id=" . $id_profissional . "';
                  </script>";
        } else {
            // Se nenhuma linha foi atualizada, mostra mensagem informativa
            echo "<script>
                    alert('Nenhuma alteração foi realizada');
                    window.history.back();
                  </script>";
        }

    } catch (PDOException $e) {
        // Captura exceções do tipo PDOException (erros relacionados ao banco de dados)
        // Mostra alerta com a mensagem de erro (addslashes para escapar caracteres especiais)
        echo "<script>
                alert('Erro ao atualizar profissional: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
    
    // Termina a execução do script (apenas para requisições não-AJAX)
    exit;
}