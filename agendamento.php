<?php
require_once "./admin/buscarProfissional.php";
require_once "./admin/consultarProfissionais.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TELA DE LOGIN</title>
  <link rel="stylesheet" href="css/stylesAg.css" />
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
          <img class="icon-logo" src="imgs/icones/icons8-calendário-100 (1).png" alt="" />
          <h1>AGENDAMENTOS</h1>
        </div>
        <div class="text">
          <h3>Gerencia todos os agendamentos</h3>
        </div>
      </div> <!--fecha: container-->
    </main>
    <section class="cards-section">
      <?php
      if (count($consultar) > 0):
        foreach ($consultar as $row): ?>
          <div class="card">
            <div class="card-text">
              <a href="gerenciarAgendamento.php?id=<?= $row['id_profissional']?>">
                <h3><?= htmlspecialchars(strtoupper($row['nome_profissional'])) ?></h3>
                <p><?= htmlspecialchars(strtoupper($row['cargo_profissional'])) ?></p>
              </a>
            </div>
          </div>
          <?php
        endforeach; ?>
      <?php else: ?>
        <p>Nenhum profissional encontrado.</p>
      <?php endif ?>
      <!-- Adicione mais cards se quiser -->
    </section>
  </div>
  <script src="js/script.js"></script>
</body>

</html>