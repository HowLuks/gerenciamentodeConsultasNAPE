<?php
require '../vendor/autoload.php';
require_once "../config/conexao.php";

use Dompdf\Dompdf;

// Verifica se veio o ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do usuário não especificado");
}

$id_usuario = intval($_GET['id']);

// Define fuso horário para America/São_Paulo
date_default_timezone_set('America/Sao_Paulo');
$dataAtual = date('d/m/Y H:i:s');

// **QUERY PRINCIPAL DO USUÁRIO**
try {
    $query = "SELECT 
                id_usuario,
                nome_usuario,
                numero_prontuario,
                contato_usuario,
                situacao,
                laudado,
                diagnostico,
                quantidade_terapias,
                informacao_adicional
              FROM usuarios
              WHERE id_usuario = :id_usuario";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuário não encontrado (ID: $id_usuario)");
    }

    // **QUERY DOS PROFISSIONAIS - AGORA SIM!**
    $sqlProf = "SELECT p.nome_profissional, p.cargo_profissional
                FROM usuario_profissional up 
                JOIN profissionais p ON p.id_profissional = up.id_profissional 
                JOIN usuarios u ON up.id_usuario = u.id_usuario
                WHERE u.id_usuario = :id_usuario";

    $stmtProf = $pdo->prepare($sqlProf);
    $stmtProf->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
    $stmtProf->execute();
    $profissionais = $stmtProf->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

// HTML do PDF
$html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Ficha do Usuário - {$usuario['nome_usuario']}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 15px;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #32a560;
        }
        h1 { 
            color: #32a560; 
            margin: 0;
            font-size: 24px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .tabela-container {
            margin-bottom: 20px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .tabela-titulo {
            background-color: #32a560;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 16px;
        }
        .tabela-conteudo {
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tr:nth-child(odd) {
            background-color: #ffffff;
        }
        th {
            background-color: #4cc381;
            color: white;
            padding: 8px 12px;
            text-align: left;
            font-size: 13px;
            width: 30%;
            border-right: 1px solid #3da76b;
        }
        td {
            padding: 8px 12px;
            font-size: 13px;
            width: 70%;
        }
        .profissionais-lista {
            margin: 5px 0;
            padding: 0;
        }
        .profissionais-lista p {
            margin: 4px 0;
            padding: 2px 0;
            font-size: 13px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 11px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class='header'>
        <h1>FICHA DO USUÁRIO</h1>
        <p class='subtitle'>Sistema NAPE - $dataAtual</p>
    </div>
    
    <div class='tabela-container'>
        <div class='tabela-titulo'>DADOS PESSOAIS</div>
        <div class='tabela-conteudo'>
            <table>
                <tr>
                    <th>Nº do Prontuário</th>
                    <td>" . htmlspecialchars($usuario['numero_prontuario'] ?? 'N/A') . "</td>
                </tr>
                <tr>
                    <th>Nome Completo</th>
                    <td>" . htmlspecialchars($usuario['nome_usuario'] ?? 'N/A') . "</td>
                </tr>
                <tr>
                    <th>Contato</th>
                    <td>" . htmlspecialchars($usuario['contato_usuario'] ?? 'N/A') . "</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class='tabela-container'>
        <div class='tabela-titulo'>INFORMAÇÕES CONFIDENCIAIS</div>
        <div class='tabela-conteudo'>
            <table>
                <tr>
                    <th>Situação</th>
                    <td>" . htmlspecialchars($usuario['situacao'] ?? 'Sem dados') . "</td>
                </tr>
                <tr>
                    <th>Diagnóstico</th>
                    <td>" . htmlspecialchars($usuario['diagnostico'] ?? 'Sem dados') . "</td>
                </tr>
                <tr>
                    <th>Quantidade de Terapias</th>
                    <td>" . htmlspecialchars($usuario['quantidade_terapias'] ?? 'Sem dados') . "</td>
                </tr>
                <tr>
                    <th>Possui Laudo?</th>
                    <td>" . htmlspecialchars($usuario['laudado'] ?? 'Sem dados') . "</td>
                </tr>
                <tr>
                    <th>Informações Adicionais</th>
                    <td>" . htmlspecialchars($usuario['informacao_adicional'] ?? 'Sem dados') . "</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class='tabela-container'>
        <div class='tabela-titulo'>PROFISSIONAL RESPONSÁVEL</div>
        <div class='tabela-conteudo'>
            <table>
                <tr>
                    <th>Profissionais Acompanhantes</th>
                    <td>
                        <div class='profissionais-lista'>";

// Adiciona os profissionais diretamente no HTML
if (!empty($profissionais)) {
    foreach ($profissionais as $prof) {
        $nome = htmlspecialchars($prof['nome_profissional'] ?? '');
        $cargo = htmlspecialchars($prof['cargo_profissional'] ?? '');
        $html .= "<p>• $nome - $cargo</p>";
    }
} else {
    $html .= "<p>Nenhum profissional vinculado</p>";
}

$html .= "
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class='footer'>
        <p>Documento gerado automaticamente pelo Sistema NAPE</p>
        <p>Este documento é confidencial e destinado exclusivamente para uso interno</p>
    </div>
</body>
</html>
";

// Gerar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nome do arquivo
$nomeArquivo = "usuario_" . ($usuario['id_usuario'] ?? '') . "_" . ($usuario['nome_usuario'] ?? '') . ".pdf";
$nomeArquivo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $nomeArquivo);

// Forçar download
$dompdf->stream($nomeArquivo, [
    "Attachment" => true
]);

exit;
?>