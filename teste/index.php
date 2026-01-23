<?php
// ==================== SISTEMA COMPLETO ====================
session_start();

// Verificar ação
$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? 0;
$id_usuario = $_GET['id_usuario'] ?? 0;
$id_profissional = $_GET['id_profissional'] ?? 0;

// Conexão com banco - AJUSTE ESTES VALORES!
$host = 'localhost';
$dbname = 'nape_teste';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=3308;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'salvar_usuario':
                salvarUsuario($pdo);
                break;
            case 'salvar_profissional':
                salvarProfissional($pdo);
                break;
            case 'vincular':
                vincularProfissional($pdo);
                break;
        }
    }
}

// Funções do sistema
function salvarUsuario($pdo) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $id = $_POST['id'] ?? 0;
    
    if ($id > 0) {
        $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $id]);
        $_SESSION['msg'] = "Usuário atualizado com sucesso!";
    } else {
        $sql = "INSERT INTO usuarios (nome, email) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email]);
        $_SESSION['msg'] = "Usuário criado com sucesso!";
    }
    header("Location: ?acao=listar");
    exit();
}

function salvarProfissional($pdo) {
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];
    $id = $_POST['id'] ?? 0;
    
    if ($id > 0) {
        $sql = "UPDATE profissionais SET nome_profissional = ?, cargo_profissional = ? WHERE id_profissional = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $especialidade, $id]);
        $_SESSION['msg'] = "Profissional atualizado com sucesso!";
    } else {
        $sql = "INSERT INTO profissionais (nome_profissional, cargo_profissional) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $especialidade]);
        $_SESSION['msg'] = "Profissional criado com sucesso!";
    }
    header("Location: ?acao=listar_profissionais");
    exit();
}

function vincularProfissional($pdo) {
    $id_usuario = $_POST['id_usuario'];
    $id_profissional = $_POST['id_profissional'];
    
    $sql = "INSERT IGNORE INTO usuario_profissional (id_usuario, id_profissional) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario, $id_profissional]);
    
    $_SESSION['msg'] = "Profissional vinculado com sucesso!";
    header("Location: ?acao=editar_usuario&id=$id_usuario");
    exit();
}

function removerVinculo($pdo, $id_usuario, $id_profissional) {
    $sql = "DELETE FROM usuario_profissional WHERE id_usuario = ? AND id_profissional = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario, $id_profissional]);
    
    $_SESSION['msg'] = "Vínculo removido com sucesso!";
    header("Location: ?acao=editar_usuario&id=$id_usuario");
    exit();
}

// Remover vínculo via GET
if ($acao == 'remover_vinculo' && $id_usuario && $id_profissional) {
    removerVinculo($pdo, $id_usuario, $id_profissional);
}

// CSS do sistema
$css = '
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
.container { max-width: 1200px; margin: 0 auto; }
.header { background: #2c3e50; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
.header h1 { margin-bottom: 10px; }
.nav { display: flex; gap: 10px; margin-bottom: 20px; }
.btn { display: inline-block; padding: 10px 15px; background: #3498db; color: white; 
       text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
.btn:hover { background: #2980b9; }
.btn-success { background: #27ae60; }
.btn-success:hover { background: #219653; }
.btn-danger { background: #e74c3c; }
.btn-danger:hover { background: #c0392b; }
.msg { padding: 10px; margin: 10px 0; background: #d4edda; color: #155724; 
       border-radius: 4px; border: 1px solid #c3e6cb; }
.table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; }
.table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
.table th { background: #34495e; color: white; }
.table tr:hover { background: #f9f9f9; }
.form-container { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
.form-group input, .form-group select, .form-group textarea { 
    width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
}
.grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
.card { background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; }
.card h3 { margin-bottom: 10px; color: #2c3e50; }
.badge { display: inline-block; padding: 3px 8px; background: #95a5a6; 
         color: white; border-radius: 3px; font-size: 12px; margin-right: 5px; }
.actions { display: flex; gap: 5px; }
</style>
';

// Menu de navegação
$menu = '
<div class="nav">
    <a href="?acao=listar" class="btn">Usuários</a>
    <a href="?acao=listar_profissionais" class="btn">Profissionais</a>
    <a href="?acao=novo_usuario" class="btn btn-success">Novo Usuário</a>
    <a href="?acao=novo_profissional" class="btn btn-success">Novo Profissional</a>
</div>
';

// Exibir mensagem
if (isset($_SESSION['msg'])) {
    $msg = '<div class="msg">' . $_SESSION['msg'] . '</div>';
    unset($_SESSION['msg']);
} else {
    $msg = '';
}

// Conteúdo principal baseado na ação
$conteudo = '';

switch ($acao) {
    case 'listar':
        $conteudo = listarUsuarios($pdo);
        break;
    case 'listar_profissionais':
        $conteudo = listarProfissionais($pdo);
        break;
    case 'novo_usuario':
        $conteudo = formUsuario($pdo);
        break;
    case 'editar_usuario':
        $conteudo = formUsuario($pdo, $id);
        break;
    case 'novo_profissional':
        $conteudo = formProfissional($pdo);
        break;
    case 'editar_profissional':
        $conteudo = formProfissional($pdo, $id);
        break;
    case 'vincular_profissional':
        $conteudo = formVincular($pdo, $id);
        break;
    default:
        $conteudo = listarUsuarios($pdo);
}

// Funções de exibição
function listarUsuarios($pdo) {
    $sql = "SELECT * FROM usuarios ORDER BY nome";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '<h2>Lista de Usuários</h2>';
    
    if (empty($usuarios)) {
        $html .= '<p>Nenhum usuário cadastrado.</p>';
        return $html;
    }
    
    $html .= '<table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Data de Criação</th>
                        <th>Profissionais</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($usuarios as $usuario) {
        // Buscar profissionais vinculados
        $sql = "SELECT p.* FROM profissionais p
                INNER JOIN usuario_profissional up ON p.id_profissional = up.id_profissional
                WHERE up.id_usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario['id']]);
        $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $profissionais_html = '';
        foreach ($profissionais as $prof) {
            $profissionais_html .= '<span class="badge">' . htmlspecialchars($prof['nome_profissional']) . '</span>';
        }
        if (empty($profissionais)) {
            $profissionais_html = '<em>Nenhum profissional</em>';
        }
        
        $html .= '<tr>
                    <td>' . $usuario['id'] . '</td>
                    <td>' . htmlspecialchars($usuario['nome']) . '</td>
                    <td>' . htmlspecialchars($usuario['email']) . '</td>
                    <td>' . $usuario['criado_em'] . '</td>
                    <td>' . $profissionais_html . '</td>
                    <td class="actions">
                        <a href="?acao=editar_usuario&id=' . $usuario['id'] . '" class="btn">Editar</a>
                        <a href="?acao=vincular_profissional&id=' . $usuario['id'] . '" class="btn btn-success">+ Profissional</a>
                    </td>
                  </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function listarProfissionais($pdo) {
    $sql = "SELECT * FROM profissionais ORDER BY nome_profissional";
    $stmt = $pdo->query($sql);
    $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '<h2>Lista de Profissionais</h2>';
    
    if (empty($profissionais)) {
        $html .= '<p>Nenhum profissional cadastrado.</p>';
        return $html;
    }
    
    $html .= '<table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Cargo/Especialidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($profissionais as $prof) {
        $html .= '<tr>
                    <td>' . $prof['id_profissional'] . '</td>
                    <td>' . htmlspecialchars($prof['nome_profissional']) . '</td>
                    <td>' . htmlspecialchars($prof['cargo_profissional']) . '</td>
                    <td class="actions">
                        <a href="?acao=editar_profissional&id=' . $prof['id_profissional'] . '" class="btn">Editar</a>
                    </td>
                  </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function formUsuario($pdo, $id = 0) {
    $usuario = ['id' => 0, 'nome' => '', 'email' => ''];
    $titulo = 'Novo Usuário';
    
    if ($id > 0) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $titulo = 'Editar Usuário';
    }
    
    $html = '<h2>' . $titulo . '</h2>
            <div class="grid">
                <div class="card">
                    <h3>Dados do Usuário</h3>
                    <form method="POST" class="form-container">
                        <input type="hidden" name="acao" value="salvar_usuario">
                        <input type="hidden" name="id" value="' . $usuario['id'] . '">
                        
                        <div class="form-group">
                            <label>Nome:</label>
                            <input type="text" name="nome" value="' . htmlspecialchars($usuario['nome']) . '" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" value="' . htmlspecialchars($usuario['email']) . '" required>
                        </div>
                        
                        <button type="submit" class="btn">Salvar</button>
                        <a href="?acao=listar" class="btn">Cancelar</a>
                    </form>
                </div>';
    
    if ($id > 0) {
        // Listar profissionais vinculados
        $sql = "SELECT p.* FROM profissionais p
                INNER JOIN usuario_profissional up ON p.id_profissional = up.id_profissional
                WHERE up.id_usuario = ?
                ORDER BY p.nome_profissional";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $profissionais_vinculados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $html .= '<div class="card">
                    <h3>Profissionais Vinculados</h3>';
        
        if (empty($profissionais_vinculados)) {
            $html .= '<p>Nenhum profissional vinculado.</p>';
        } else {
            $html .= '<ul>';
            foreach ($profissionais_vinculados as $prof) {
                $html .= '<li style="margin-bottom: 10px; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                            <strong>' . htmlspecialchars($prof['nome_profissional']) . '</strong>
                            <br><small>' . htmlspecialchars($prof['cargo_profissional']) . '</small>
                            <div style="margin-top: 5px;">
                                <a href="?acao=remover_vinculo&id_usuario=' . $id . '&id_profissional=' . $prof['id_profissional'] . '" 
                                   class="btn btn-danger" 
                                   onclick="return confirm(\'Remover este vínculo?\')">
                                    Remover
                                </a>
                            </div>
                          </li>';
            }
            $html .= '</ul>';
        }
        
        $html .= '<div style="margin-top: 15px;">
                    <a href="?acao=vincular_profissional&id=' . $id . '" class="btn btn-success">
                        + Vincular Novo Profissional
                    </a>
                  </div>
                </div>';
    }
    
    $html .= '</div>';
    return $html;
}

function formProfissional($pdo, $id = 0) {
    $profissional = ['id_profissional' => 0, 'nome_profissional' => '', 'cargo_profissional' => ''];
    $titulo = 'Novo Profissional';
    
    if ($id > 0) {
        $sql = "SELECT * FROM profissionais WHERE id_profissional = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $profissional = $stmt->fetch(PDO::FETCH_ASSOC);
        $titulo = 'Editar Profissional';
    }
    
    $html = '<h2>' . $titulo . '</h2>
            <div class="form-container">
                <form method="POST">
                    <input type="hidden" name="acao" value="salvar_profissional">
                    <input type="hidden" name="id" value="' . $profissional['id_profissional'] . '">
                    
                    <div class="form-group">
                        <label>Nome:</label>
                        <input type="text" name="nome" value="' . htmlspecialchars($profissional['nome_profissional']) . '" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Cargo/Especialidade:</label>
                        <input type="text" name="especialidade" value="' . htmlspecialchars($profissional['cargo_profissional']) . '">
                    </div>
                    
                    <button type="submit" class="btn">Salvar</button>
                    <a href="?acao=listar_profissionais" class="btn">Cancelar</a>
                </form>
            </div>';
    
    return $html;
}

function formVincular($pdo, $id_usuario) {
    // Buscar dados do usuário
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        return '<p>Usuário não encontrado.</p>';
    }
    
    // Buscar profissionais disponíveis (não vinculados)
    $sql = "SELECT * FROM profissionais 
            WHERE id_profissional NOT IN (
                SELECT id_profissional 
                FROM usuario_profissional 
                WHERE id_usuario = ?
            )
            ORDER BY nome_profissional";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario]);
    $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '<h2>Vincular Profissional ao Usuário</h2>
            <div class="card">
                <h3>Usuário: ' . htmlspecialchars($usuario['nome']) . '</h3>
                
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="acao" value="vincular">
                        <input type="hidden" name="id_usuario" value="' . $id_usuario . '">
                        
                        <div class="form-group">
                            <label>Selecione o Profissional:</label>
                            <select name="id_profissional" required>';
    
    if (empty($profissionais)) {
        $html .= '<option value="">Todos os profissionais já estão vinculados</option>';
    } else {
        $html .= '<option value="">Selecione um profissional...</option>';
        foreach ($profissionais as $prof) {
            $html .= '<option value="' . $prof['id_profissional'] . '">' 
                   . htmlspecialchars($prof['nome_profissional']) . ' - ' 
                   . htmlspecialchars($prof['cargo_profissional']) 
                   . '</option>';
        }
    }
    
    $html .= '</select>
                        </div>
                        
                        <button type="submit" class="btn" ' . (empty($profissionais) ? 'disabled' : '') . '>
                            Vincular
                        </button>
                        <a href="?acao=editar_usuario&id=' . $id_usuario . '" class="btn">Voltar</a>
                    </form>
                </div>';
    
    if (empty($profissionais)) {
        $html .= '<div class="msg" style="margin-top: 15px;">
                    Todos os profissionais já estão vinculados a este usuário!
                  </div>';
    }
    
    $html .= '</div>';
    return $html;
}

// Renderizar página completa
echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Usuários e Profissionais</title>
    ' . $css . '
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Sistema de Usuários e Profissionais</h1>
            <p>Relação N:N - Um usuário pode ter vários profissionais</p>
        </div>
        
        ' . $msg . '
        ' . $menu . '
        ' . $conteudo . '
    </div>
    
    <script>
    // Confirmação para ações de remoção
    document.addEventListener("DOMContentLoaded", function() {
        var links = document.querySelectorAll("a.btn-danger");
        links.forEach(function(link) {
            link.addEventListener("click", function(e) {
                if (!confirm("Tem certeza que deseja realizar esta ação?")) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</body>
</html>';
?>