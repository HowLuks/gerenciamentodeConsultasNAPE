<?php
require_once "./admin/buscarProfissional.php";
require_once "./admin/consultarProfissionais.php";
require_once "./admin/consultarIdProfissional.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TELA DE LOGIN</title>
  <link rel="stylesheet" href="./css/stylesAg.css">
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
          <li><a href="usuariosGerais.php">Usuários Gerais</a></li>
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
          <li><a href="usuariosGerais.php">Usuários Gerais</a></li>
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
  <div class="content" id="content">
    <header>
      <nav class="faixa">
        <div class="barraDados">
          <img id="menu-btn" style="height: 45px; cursor: pointer" class="icon-logo"
            src="imgs/icones/icons8-cardápio-64.png" alt="icone-Nape" />
          <div class="titulos-barra">
            <h1>Sejam Bem-vindo(a), <?php echo $nome ?></h1>
            <h2><?php echo strtoupper($cargo) ?></h2>
          </div>
        </div>
      </nav>
    </header>
    <hr />
  </div>
  <div class="conteudo-main">
    <main class="tela-agendamento">
      <div class="container">
        <div class="info">
          <img class="icon-logo" src="imgs/icones/usuario.png" alt="" />
          <h1>Usuários</h1>
        </div>
        <div class="text">
          <h3>Gerencia todos os usuários</h3>
        </div>
      </div>
      <!--fecha: container-->
    </main>

    <section class="cards-section">
      <?php
      if (count($consultar) > 0):
        foreach ($consultar as $row): ?>
          <div class="card">
            <div class="card-text">
              <a href="gerenciarUsuario.php?id=<?= $row['id_profissional'] ?>">
                <h3><?= htmlspecialchars(strtoupper($row['nome_profissional'])) ?></h3>
                <p><?= htmlspecialchars(strtoupper($row['cargo_profissional'])) ?></p>
              </a>
            </div>
          </div>
          <?php
        endforeach; ?>
      <?php else: ?>
        <p>Nenhum profissional cadastrado.</p>
      <?php endif ?>
      <!-- Adicione mais cards se quiser -->
    </section>
  </div>

  <!-- MODAL ADICIONAR PROFISSIONAL -->
  <div id="modalForm" class="modal">
    <div class="modal-content">
      <div class="header-popup">
        <h2>Adicionar novo Profissional</h2>
      </div>
      <div id="mensagem" class="mensagem"></div>

      <div class="modal-body">
        <div class="modal-info">
          <div class="coluna">
            <form id="addProfissionalForm" method="post" action="admin/adicionarProfissional.php">
              <div class="conjunto-info">
                <label for="nome_profissional" class="labelCadastro">Digite o nome do
                  Profissional:</label>
                <input type="text" class="inputCadastro" name="nome_profissional" placeholder="Digite o nome" required>
              </div>

              <div class="conjunto-info">
                <label for="email" class="labelCadastro">Digite o email do Profissional:</label>
                <input type="email" class="inputCadastro" name="email" placeholder="Digite o email" required>
              </div>

              <div class="conjunto-info">
                <label for="senha" class="labelCadastro">Digite a senha do Profissional:</label>
                <input type="password" class="inputCadastro" name="senha" placeholder="Digite a senha" required>
              </div>

              <div class="conjunto-info">
                <label for="cargo_profissional" class="labelCadastro">Digite o cargo do
                  Profissional:</label>
                <input type="text" class="inputCadastro" name="cargo_profissional" placeholder="Digite o cargo"
                  required>
              </div>

              <div class="conjunto-info">
                <label for="endereco" class="labelCadastro">Digite o endereço do Profissional:</label>
                <input type="text" class="inputCadastro" name="endereco" placeholder="Digite o endereço" required>
              </div>

              <div class="conjunto-info">
                <label for="vinculo" class="labelCadastro">Digite o vínculo do Profissional:</label>
                <input type="text" class="inputCadastro" name="vinculo" placeholder="Digite o vínculo" required>
              </div>

              <div class="conjunto-info">
                <label for="cidade" class="labelCadastro">Digite a cidade do Profissional:</label>
                <input type="text" class="inputCadastro" name="cidade" placeholder="Digite a cidade" required>
              </div>

              <div class="conjunto-info">
                <label for="data_nascimento" class="labelCadastro">Data de nascimento do
                  Profissional:</label>
                <input type="date" class="inputCadastro" name="data_nascimento" required>
              </div>

              <div class="conjunto-info">
                <label for="cpf" class="labelCadastro">Digite o CPF do Profissional:</label>
                <input type="text" class="inputCadastro" name="cpf" placeholder="Digite o CPF" required>
              </div>
              <div class="modal-actions">
                <button type="submit" class="botao-agendamento" form="addForm">Salvar</button>
                <button class="botao-agendamento" id="cancelProfissional">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
  <script src="./js/script.js"></script>
</body>

</html>