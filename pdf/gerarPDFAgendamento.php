<?php
require '../vendor/autoload.php';
require_once "../config/conexao.php";

use Dompdf\Dompdf;

// Verifica se veio o ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("ID do agendamento não especificado");
}

$id_agendamento = intval($_GET['id']); // MUDEI O NOME DA VARIÁVEL

// Define fuso horário para America/São_Paulo
date_default_timezone_set('America/Sao_Paulo');
$dataAtual = date('d/m/Y H:i:s');

// **QUERY ATUALIZADA PARA INCLUIR DADOS DO AGENDAMENTO**
try {
  $query = "SELECT 
                a.id_agendamento,
                a.data as data_agendamento,
                a.hora as hora_agendamento,
                a.status_agendamento as status,
                a.nome_usuario as nome_agendado,
                u.id_usuario,
                u.nome_usuario as nome_paciente,
                u.numero_prontuario,
                u.contato_usuario,
                u.situacao,
                u.quantidade_terapias,
                u.diagnostico,
                u.informacao_adicional,
                u.laudado,
                u.multiprofissionais
            FROM agendamentos a 
            LEFT JOIN usuarios u ON u.nome_usuario = a.nome_usuario
            WHERE a.id_agendamento = :id_agendamento"; // MUDEI O PARÂMETRO

  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT); // MUDEI AQUI
  $stmt->execute();

  $dados = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$dados) {
    die("Agendamento não encontrado (ID: $id_agendamento)");
  }

  // **QUERY DOS PROFISSIONAIS - ADICIONADA AQUI**
  if (!empty($dados['id_usuario'])) {
    $sqlProf = "SELECT p.nome_profissional, p.cargo_profissional
                FROM usuario_profissional up 
                JOIN profissionais p ON p.id_profissional = up.id_profissional 
                JOIN usuarios u ON up.id_usuario = u.id_usuario
                WHERE u.id_usuario = :id_usuario";
    
    $stmtProf = $pdo->prepare($sqlProf);
    $stmtProf->bindParam(":id_usuario", $dados['id_usuario'], PDO::PARAM_INT);
    $stmtProf->execute();
    $profissionais = $stmtProf->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $profissionais = [];
  }

} catch (PDOException $e) {
  die("Erro ao buscar dados: " . $e->getMessage());
}

// Formatar data do agendamento
$dataAgendamento = !empty($dados['data_agendamento']) ?
  date('d/m/Y', strtotime($dados['data_agendamento'])) :
  'Não agendado';

$horaAgendamento = !empty($dados['hora_agendamento']) ?
  $dados['hora_agendamento'] :
  'Não definida';

// Preparar HTML dos profissionais
$htmlProfissionais = "";
if (!empty($profissionais)) {
  foreach ($profissionais as $profissional) {
    $nome = htmlspecialchars($profissional['nome_profissional'] ?? '');
    $cargo = htmlspecialchars($profissional['cargo_profissional'] ?? '');
    $htmlProfissionais .= "<p>$nome - $cargo</p>";
  }
} else {
  $htmlProfissionais = "<p>Nenhum profissional vinculado</p>";
}

// HTML do PDF corrigido
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Ficha do Usuário - ' . htmlspecialchars($dados['nome_paciente'] ?? '') . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 10px;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #32a560;
        }
      h1 {
        color: #32a560;
        margin: 0;
      }
      .subtitle {
        color: #666;
        font-size: 16px;
      }
      .info-section {
        margin-bottom: 25px;
      }
      .info-title {
        background-color: #f2f2f2;
        padding: 8px 15px;
        font-weight: bold;
        color: #32a560;
        border-left: 4px solid #32a560;
        margin-bottom: 10px;
      }
      .info-content {
        padding: 0 20px;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
      }
      th {
        background-color: #32a560;
        color: white;
        padding: 10px;
        text-align: left;
        font-size: 15px;
        width: 19%;
      }
      td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
      }
      .footer {
        margin-top: 40px;
        text-align: center;
        color: #666;
        font-size: 12px;
        border-top: 1px solid #ddd;
        padding-top: 20px;
      }
    </style>
</head>
<body>
    <div class="header">
      <h1>FICHA DO USUÁRIO</h1>
      <p class="subtitle">Sistema NAPE - ' . $dataAtual . '</p>
    </div>

    <div class="info-section">
      <div class="info-title">DADOS PESSOAIS</div>
      <div class="info-content">
        <table>
          <tr>
            <th>Nº do Prontuário</th>
            <td>' . htmlspecialchars($dados['numero_prontuario'] ?? 'N/A') . '</td>
            <th>Nome Do Usuário</th>
            <td>' . htmlspecialchars($dados['nome_paciente'] ?? 'N/A') . '</td>
          </tr>
          <tr>
            <th>Contato</th>
            <td>' . htmlspecialchars($dados['contato_usuario'] ?? 'N/A') . '</td>
            <th>Situação</th>
            <td>' . htmlspecialchars($dados['situacao'] ?? 'Sem dados') . '</td>
          </tr>
        </table>
      </div>
    </div>

    <div class="info-section">
      <div class="info-title">INFORMAÇÕES CONFIDENCIAIS</div>
      <div class="info-content">
        <table>
          <tr>
            <th>Diagnóstico</th>
            <td>' . htmlspecialchars($dados['diagnostico'] ?? 'Sem dados') . '</td>
            <th>Quantidade de Terapias</th>
            <td>' . htmlspecialchars($dados['quantidade_terapias'] ?? 'Sem dados') . '</td>
          </tr>
          <tr>
            <th>Informações Adicionais</th>
            <td>' . htmlspecialchars($dados['informacao_adicional'] ?? 'Sem dados') . '</td>
            <th>Status do agendamento</th>
            <td>' . htmlspecialchars($dados['status'] ?? 'Não agendado') . '</td>
            <th>Possui laudo</th>
            <td>' . htmlspecialchars($dados['laudado'] ?? 'Sem dados') . '</td>
          </tr>
          <tr>
            <th>Data do agendamento</th>
            <td>' . $dataAgendamento . '</td>
            <th>Hora</th>
            <td>' . $horaAgendamento . '</td>
          </tr>
        </table>
      </div>
    </div>

    <div class="info-section">
      <div class="info-title">PROFISSIONAL RESPONSÁVEL</div>
      <div class="info-content">
        <table>
          <tr>
            <th>Profissionais Acompanhantes</th>
            <td>' . $htmlProfissionais . '</td>
          </tr>
        </table>
      </div>
    </div>

    <div class="footer">
      <p>Documento gerado automaticamente pelo Sistema NAPE</p>
      <p>Este documento é confidencial e destinado exclusivamente para uso interno</p>
    </div>
</body>
</html>';

// Gerar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nome do arquivo
$nomeArquivo = "agendamento_" . $id_agendamento . "_" .
  preg_replace('/[^a-zA-Z0-9_\-]/', '_', $dados['nome_paciente'] ?? '') .
  ".pdf";

// Forçar download
$dompdf->stream($nomeArquivo, [
  "Attachment" => true
]);

exit;
?>