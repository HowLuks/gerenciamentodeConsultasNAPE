<?php
session_start();
require_once "./config/conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['id_logado'])) {
  header("Location: index.php");
  exit();
}

// Busca dados do profissional logado
$id_profissional = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM profissionais WHERE id_profissional = :id_profissional");
$stmt->execute([$id_profissional]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Variáveis de sessão
$nome = $_SESSION['nome'] ?? '';
$cargo = $_SESSION['cargo'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Editar Dados do Profissional</title>
<link rel="stylesheet" href="./css/editarProfissional.css">
</head>

<body>
  <!-- MENU LATERAL -->
  <aside id="sidebar" class="sidebar active">
    <div class="sidebar-header">
      <img style="align-items: center; margin-top: 20px;" src="imgs/icones/2.png" width="140">
    </div>
    <ul>
      <div class="aba">
        <img class="icone-aba" src="imgs/icones/badge_200dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.png">
        <li><a href="dados-pessoais.php?id=<?= $_SESSION['id_logado'] ?>">Detalhes Pessoais</a></li>
      </div>
      <?php if (strtoupper($cargo) !== "COORDENADOR"): ?>
        <div class="aba">
          <img class="icone-aba" src="imgs/icones/calendar_month_200dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.png">
          <li><a href="gerenciarAgendamento.php">Agendamentos</a></li>
        </div>
        <div class="aba">
          <img class="icone-aba" src="imgs/icones/group_100dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.png">
          <li><a href="gerenciarUsuario.php">Usuários</a></li>
        </div>
        <div class="aba">
          <img class="icone-aba" src="imgs/icones/group_100dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.png">
          <li><a href="usuariosGerais.php">Usuários Cadastrados</a></li>
        </div>
      <?php endif; ?>

      <?php if (strtoupper($cargo) == "COORDENADOR"): ?>
        <div class="aba">
          <img class="icone-aba" src="imgs/icones/calendar_month_200dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.png">
          <li><a href="agendamento.php">Agendamentos</a></li>
        </div>
        <div class="aba">
          <img class="icone-aba" src="imgs/icones/group_100dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.png">
          <li><a href="usuarios.php">Usuários</a></li>
        </div>
        <div class="aba">
          <img class="icone-aba" src="imgs/icones/user.png">
          <li><a href="usuariosGerais.php">Usuários Cadastrados</a></li>
        </div>
        <div class="aba">
          <img class="icone-aba" src="imgs/icones/equipe.png">
          <li><a href="equipeNAPE.php">Equipe NAPE</a></li>
        </div>
      <?php endif; ?>
      <hr>
      <div class="aba">
        <img class="icone-aba" src="imgs/icones/login_200dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.png">
        <li><a href="./logout/logout.php">Sair</a></li>
      </div>
    </ul>
  </aside>

  <div id="overlay"></div>

  <!-- CONTEÚDO DO SITE -->

  <!-- Main Content - Centralizado -->
  <div class="conteudo-main">
    <div class="form-container">
      <div class="form-card">
        <div class="form-header">
          <h3>Formulário de Edição</h3>
          <p>Preencha os campos abaixo para atualizar as informações</p>
        </div>

        <form id="editForm" method="post" action="profissionais/editarProfissional.php" class="edit-form">
          <!-- Campo hidden para ID -->
          <input type="hidden" name="id_profissional" id="id_profissional" value="<?= $row['id_profissional'] ?? '' ?>">

          <!-- PRIMEIRA LINHA: 2 campos lado a lado -->
          <div class="form-row">
            <div class="form-group">
              <label for="nome_profissional">Nome Completo *</label>
              <div class="input-wrapper">
                <input type="text" id="nome_profissional" name="nome_profissional"
                  value="<?= htmlspecialchars($row['nome_profissional'] ?? '') ?>"
                  placeholder="Digite o nome completo..." required>
              </div>
            </div>

            <div class="form-group">
              <label for="cargo_profissional">Cargo *</label>
              <div class="input-wrapper">
                <input type="text" id="cargo_profissional" name="cargo_profissional"
                  value="<?= htmlspecialchars($row['cargo_profissional'] ?? '') ?>" placeholder="Cargo do profissional"
                  required>
              </div>
            </div>
          </div>

          <!-- SEGUNDA LINHA: 2 campos lado a lado -->
          <div class="form-row">
            <div class="form-group">
              <label for="email">Email *</label>
              <div class="input-wrapper with-icon">
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($row['email'] ?? '') ?>"
                  placeholder="email@exemplo.com" required>
              </div>
            </div>

            <div class="form-group">
              <label for="cpf">CPF</label>
              <div class="input-wrapper">
                <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($row['cpf'] ?? '') ?>"
                  placeholder="000.000.000-00" maxlength="14">
              </div>
            </div>
          </div>

          <!-- TERCEIRA LINHA: 1 campo (senha) -->
          <div class="form-group full-width">
            <label for="senha">Nova Senha (opcional)</label>
            <div class="input-wrapper with-icon">
              <div class="input-icon">
                <img src="imgs/icones/icons8-locked-padlock-78.png" width="20" alt="Senha">
              </div>
              <input type="password" id="senha" name="senha" placeholder="Deixe em branco para manter a atual"
                autocomplete="new-password">
            </div>
            <small class="form-text">Preencha apenas se deseja alterar a senha</small>
          </div>

          <!-- QUARTA LINHA: 3 campos lado a lado (Vínculo, Endereço, Data Nascimento) -->
          <div class="form-row-3">
            <div class="form-group">
              <label for="vinculo">Vínculo</label>
              <div class="input-wrapper">
                <input type="text" id="vinculo" name="vinculo" value="<?= htmlspecialchars($row['vinculo'] ?? '') ?>"
                  placeholder="Tipo de vínculo">
              </div>
            </div>

            <div class="form-group">
              <label for="endereco">Endereço</label>
              <div class="input-wrapper">
                <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($row['endereco'] ?? '') ?>"
                  placeholder="Rua, número, bairro">
              </div>
            </div>

            <div class="form-group">
              <label for="data_nascimento">Data Nascimento</label>
              <div class="input-wrapper">
                <input type="date" id="data_nascimento" name="data_nascimento" 
                  value="<?= htmlspecialchars($row['data_nascimento'] ?? '') ?>">
              </div>
            </div>
          </div>

          <!-- QUINTA LINHA: 2 campos lado a lado (Cidade e Telefone/Celular) -->
          <div class="form-row">
            <div class="form-group">
              <label for="cidade">Cidade</label>
              <div class="input-wrapper">
                <input type="text" id="cidade" name="cidade" value="<?= htmlspecialchars($row['cidade'] ?? '') ?>"
                  placeholder="Nome da cidade">
              </div>
            </div>

            <div class="form-group">
              <label for="telefone">Contato</label>
              <div class="input-wrapper">
                <input type="tel" id="contato" name="contato" 
                  value="<?= htmlspecialchars($row['contato'] ?? 'sem dado') ?>"
                  placeholder="(00) 00000-0000" maxlength="15">
              </div>
            </div>
          </div>
          <!-- Botões posicionados corretamente -->
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancelBtn">Voltar</button>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<script src="js/form.js"></script>

<script>
  // Máscara para telefones
  document.addEventListener('DOMContentLoaded', function() {
    // Máscara para celular (11 dígitos)
    const telefoneInput = document.getElementById('telefone');
    const whatsappInput = document.getElementById('whatsapp');
    
    // Máscara para telefone residencial (10 dígitos)
    const telefoneResidencialInput = document.getElementById('telefone_residencial');
    
    // Máscara para CPF
    const cpfInput = document.getElementById('cpf');
    
    // Função para máscara de telefone
    function maskPhone(value) {
      value = value.replace(/\D/g, '');
      
      if (value.length <= 10) {
        // Formato: (00) 0000-0000
        return value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
      } else {
        // Formato: (00) 00000-0000
        return value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
      }
    }
    
    // Função para máscara de CPF
    function maskCPF(value) {
      return value.replace(/\D/g, '')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})/, '$1-$2')
        .replace(/(-\d{2})\d+?$/, '$1');
    }
    
    // Aplicar máscaras nos inputs
    if (telefoneInput) {
      telefoneInput.addEventListener('input', function(e) {
        e.target.value = maskPhone(e.target.value);
      });
    }
    
    if (whatsappInput) {
      whatsappInput.addEventListener('input', function(e) {
        e.target.value = maskPhone(e.target.value);
      });
    }
    
    if (telefoneResidencialInput) {
      telefoneResidencialInput.addEventListener('input', function(e) {
        e.target.value = maskPhone(e.target.value);
      });
    }
    
    if (cpfInput) {
      cpfInput.addEventListener('input', function(e) {
        e.target.value = maskCPF(e.target.value);
      });
    }
    
    // Script para o botão voltar
    document.getElementById('cancelBtn').addEventListener('click', function() {
      window.history.back();
    });
  });
</script>

</body>

</html>