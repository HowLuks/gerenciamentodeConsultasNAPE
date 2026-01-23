<?php
require_once "./config/conexao.php";
require_once "./admin/buscarProfissional.php";
require_once "./admin/consultarProfissionais.php";
require_once "./admin/consultarIdProfissional.php";
require_once "./admin/buscarDadosProfissional.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DADOS PESSOAIS</title>
  <link rel="stylesheet" href="css/dados-pessoais.css">
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
            <h1>Sejam Bem-vindo(a), <?php echo htmlspecialchars($nome) ?></h1>
            <h2><?php echo strtoupper($cargo) ?></h2>
          </div>
        </div>
      </nav>
    </header>
    <hr />
  </div>

  <div class="conteudo-main">
    <!-- Header com informações do usuário -->
    <div class="dados-header">
      <div class="header-info">
        <img style="margin-right: 15px" src="imgs/icones/usuario.png" width="50" alt="" />
        <div class="header-text">
          <h1>Dados Pessoais</h1>
          <p><?php echo strtoupper($profissional['cargo_profissional']) ?? 'Cargo não definido' ?>:
            <?php echo $profissional['nome_profissional'] ?? 'Nome não definido' ?>
          </p>
        </div>
      </div>
    </div>
    <?php
    foreach ($dadosProf as $row):
      ?>
      <!-- Container principal com os dados -->
      <div class="dados-container">
        <div class="dados-grid">
          <div class="campo-grupo campo-destaque">
            <div class="campo-label">Nome Completo:</div>
            <div class="campo-valor">
              <?= $row['nome_profissional'] ? htmlspecialchars($row['nome_profissional']) : "sem dado"; ?>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo-label">Email:</div>
            <div class="campo-valor"> <?= $row['email'] ? htmlspecialchars($row['email']) : "sem dado"; ?></div>
          </div>

          <div class="campo-grupo">
            <div class="campo-label">CPF:</div>
            <div class="campo-valor"><?= $row['cpf'] ? htmlspecialchars($row['cpf']) : 00; ?></div>
          </div>

          <div class="campo-grupo campo-destaque">
            <div class="campo-label">Cargo:</div>
            <div class="campo-valor">
              <?= $row['cargo_profissional'] ? htmlspecialchars($row['cargo_profissional']) : "sem dado"; ?>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo-label">Endereço:</div>
            <div class="campo-valor"><?= $row['endereco'] ? htmlspecialchars($row['endereco']) : "sem dado"; ?></div>
          </div>

          <div class="campo-grupo">
            <div class="campo-label">Vínculo:</div>
            <div class="campo-valor"><?= $row['vinculo'] ? htmlspecialchars($row['vinculo']) : "sem dado"; ?></div>
          </div>

          <div class="campo-grupo">
            <div class="campo-label">Cidade:</div>
            <div class="campo-valor"><?= $row['cidade'] ? htmlspecialchars($row['cidade']) : "sem dado"; ?></div>
          </div>

          <div class="campo-grupo">
            <div class="campo-label">Data de nascimento:</div>
            <div class="campo-valor"><?= $row['data_nascimento'] ? htmlspecialchars($row['data_nascimento']) : "sem dado"; ?></div>
          </div>

          <div class="campo-grupo">
            <div class="campo-label">Contato:</div>
            <div class="campo-valor"><?= $row['contato'] ? htmlspecialchars($row['contato']) : "sem dado"; ?></div>
          </div>
        </div>
      </div>

        <div class="acoes">
          <?php if($cargo == 'coordenador'):?>
      <div class="acoes">
        <form method="POST" action="formulario.php?id=<?=$row['id_profissional'] ?>">
          <input type="hidden" name="id" value="<?=$row['id_profissional'] ?>" />
          <button type="submit" class="botao-edicao">EDITAR</button>
        </form>
        
          <form action="admin/excluirProfissional.php" method="post">
            <input type="hidden" name="id" value="<?= $row['id_profissional'] ?>">
            <button class="botao-excluir excluir" type="submit"
              onclick="return confirm('Deseja excluir esse profissional?')">
              EXCLUIR
            </button>
          </form>
        </div>
        <?php else:?>
          <div class="acoes">
            <form method="POST" style="display: inline">
              <input type="hidden" name="id" value="<?= $row['id_profissional'] ?>" />
              <a style="text-decoration: none;" href="formulario.php?id= <?= $row['id_profissional'] ?>" class="botao-edicao">EDITAR</a>
            </form>
            </div>
            <?php endif;?>

        </div>
    </div>
  <?php endforeach; ?>
  </div>
  </div>
  <script src="js/script.js"></script>
</body>

</html>