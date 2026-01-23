<?php
require_once "./config/conexao.php";
require_once "./admin/buscarProfissional.php";
require_once "./admin/consultarProfissionais.php";
require_once "./admin/consultarIdProfissional.php";
require_once "./admin/consultarUsuarios.php";
require_once "./admin/buscarIdUsuario.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>USUÁRIOS - SISTEMA NAPE</title>
  <link rel="stylesheet" href="css/stylesGU.css">
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
          <li><a href="usuariosGerais.php">Usuários Cadastrados
            </a></li>
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
    <main class="tela-agendamento">
      <div class="cardsSuperiores">
        <div class="container1">
          <div class="info">
            <h1>TOTAL DE USUÁRIOS</h1>
          </div>
          <div class="text">
            <h3><?php echo htmlspecialchars($usuariosGerais) ?></h3>
          </div>
        </div>
        <div class="container1">
          <div class="info">
            <h1>USUÁRIOS LAUDADOS</h1>
          </div>
          <div class="text">
            <h3><?php echo htmlspecialchars($laudadosGerais) ?></h3>
          </div>
        </div>
        <div class="container1">
          <div class="info">
            <h1>USUÁRIOS SEM LAUDOS</h1>
          </div>
          <div class="text">
            <h3><?php echo htmlspecialchars($naoLaudadosGerais) ?></h3>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="info">
          <img class="icon-logo" src="imgs/icones/usuario.png" alt="Ícone usuário" />
        </div>
        <div class="header-text">
          <h1>Usuários Cadastrados</h1>
        </div>
      </div>

      <div class="parteCima">
        <div class="opcoes">
          <div class="agendamentos">
            <button class="botao-agendamento" id="openModal">
              <img src="imgs/icones/icons8-add-user-male-96.png" width="40" />
              <h2 style="margin: 7px">Novo Usuário</h2>
            </button>
          </div>

          <div class="agendamentos">
            <button class="botao-agendamento" id="openModalProf">
              <img src="imgs/icones/icons8-add-user-male-96.png" width="40" />
              <h2 style="margin: 7px">Cadastrar para profissional</h2>
            </button>
          </div>
        </div>
      </div>
      <div class="container-agenda">
        <div class="opcoes">
          <div class="agendamentos">
            <h1 style="margin: 7px">Usuários Cadastrados</h1>
          </div>
        </div>

        <div class="conteudoBaixo">
          <table class="table">
            <thead>
              <tr>
                <th>Nº Prontuário</th>
                <th>Usuário</th>
                <th>Contato</th>
                <th>Situação</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($consultarUsuarios)): ?>
                <?php foreach ($consultarUsuarios as $row): ?>
                  <tr>
                    <td data-label="Nº Prontuário"><?= htmlspecialchars($row['numero_prontuario'] ?? 'sem dado') ?></td>
                    <td data-label="Usuário"><?= htmlspecialchars($row['nome_usuario'] ?? 'sem dado') ?></td>
                    <td data-label="Contato"><?= htmlspecialchars($row['contato_usuario'] ?? 'sem dado') ?></td>
                    <td data-label="Situação"><?= htmlspecialchars($row['situacao'] ?? 'sem dado') ?></td>
                    <td data-label="Ações">
                      <div class="acoes-topo" style="display: flex; gap: 5px;">

                        <!-- Botão Editar - adicione data-prof-id -->
                        <button class="botao-agendamento openEdit" data-user-id="<?= $row['id_usuario'] ?>"
                          data-prof-id="<?= $row['id_profissional'] ?? '' ?>">
                          Editar
                        </button>
                        <!-- FORMULÁRIO DE EXCLUIR - ATUALIZADO -->
                        <form method="POST" action="profissionais/excluirUsuariosGerais.php"
                          onsubmit="return confirmExclusao(this)" style="display: inline">
                          <input type="hidden" name="id" value="<?= $row['id_usuario'] ?>" />
                          <!-- Campo hidden para o ID do profissional -->
                          <input type="hidden" name="id_profissional" id="profId_<?= $row['id_usuario'] ?>" value="" />
                          <button class="botao-agendamento" onclick="return confirm('Deseja excluir esse usuário?')"
                            type="submit" name="acao" value="excluir">
                            Excluir
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align:center;">Nenhum usuário encontrado.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- MODAL CADASTRAR PARA PROFISSIONAL -->
  <div id="cadastroProf" class="cadastroProf">
    <div class="modal-content">
      <div class="header-popup">
        <h2>Cadastrar para profissional</h2>
      </div>

      <div class="modal-body">
        <div class="modal-info">
          <div class="coluna">
            <form id="formCadastroProf" method="post" action="profissionais/adicionarProfissionalParaUsuario.php">

              <div class="conjunto-info">
                <label class="labelCadastro" for="id_usuario">Selecione o usuário:</label>
                <select name="id_usuario" class="inputCadastro" required>
                  <option value="">--- Selecione o Usuário ---</option>
                  <?php
                  // Consulta corrigida para pegar o id_usuario
                  $sqlUsuarios = "SELECT id_usuario, numero_prontuario, nome_usuario FROM usuarios";
                  $stmtUsuarios = $pdo->prepare($sqlUsuarios);
                  $stmtUsuarios->execute();
                  $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

                  foreach ($usuarios as $usuario) {
                    $prontuario = !empty($usuario['numero_prontuario']) ? $usuario['numero_prontuario'] : 'Sem prontuário';
                    $nome = htmlspecialchars($usuario['nome_usuario']);
                    $idUsuario = htmlspecialchars($usuario['id_usuario']);

                    echo "<option value=\"{$idUsuario}\">{$prontuario} - {$nome}</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="id_profissional">Selecione o profissional:</label>
                <select name="id_profissional" class="inputCadastro" required>
                  <option value="">- Selecione o Profissional Acompanhado -</option>
                  <?php
                  // Consulta corrigida para pegar o id_profissional
                  $sqlProfissional = "SELECT id_profissional, nome_profissional, cargo_profissional FROM profissionais WHERE cargo_profissional != 'coordenador'";
                  $stmt = $pdo->prepare($sqlProfissional);
                  $stmt->execute();
                  $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  foreach ($profissionais as $prof) {
                    $nome = htmlspecialchars($prof['nome_profissional']);
                    $cargo = htmlspecialchars($prof['cargo_profissional']);
                    $idProfissional = htmlspecialchars($prof['id_profissional']);

                    echo "<option value=\"{$idProfissional}\">{$nome} - {$cargo}</option>";
                  }
                  ?>
                </select>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button type="submit" class="botao-agendamento" form="formCadastroProf">Cadastrar</button>
        <button class="botao-agendamento" id="closeCadastroProf">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- MODAL NOVO USUARIO -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <div class="header-popup">
        <h2>Novo Usuário</h2>
      </div>

      <div class="modal-body">
        <div class="modal-info">
          <div class="coluna">
            <form id="addForm" method="post" action="profissionais/novoUsuario.php">

              <div class="conjunto-info">
                <label class="labelCadastro" for="nome">Digite o nome do Paciente:</label>
                <input class="inputCadastro" type="text" name="nome_usuario" placeholder="Digite o nome..." required />
              </div>
              
            <div class="conjunto-info">
                <label class="labelCadastro" for="nprontuario">Digite o número de Prontuário:</label>
                <input class="inputCadastro" type="number" name="numero_prontuario" placeholder="Digite o número..."
                  required />
              </div>
              
              <div class="conjunto-info">
                <label class="labelCadastro" for="situacao">Qual situação encontra-se o paciente:</label>
                <select name="situacao" class="inputCadastro">
                  <option value="vinculado">Vinculado</option>
                  <option value="encerrado">Encerrado</option>
                </select>
              </div>
              
              <div class="conjunto-info">
                <label class="labelCadastro" for="nome">Possui multiprofissionais:</label>
                <input class="inputCadastro" type="text" name="multiprofissionais"
                  placeholder="Digite o multiprofissionais..."/>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="telefone">Digite o número de telefone do paciente:</label>
                <input class="inputCadastro" type="tel" name="contato_usuario" placeholder="Telefone"/>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="deficiencia">Digite a deficiência dele:</label>
                <input class="inputCadastro" type="text" name="diagnostico" placeholder="Deficiência"/>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="terapias">Quantidade de terapias mensais:</label>
                <input class="inputCadastro" type="number" name="quantidade_terapias" placeholder="Quantidade"
                 />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="laudo">Possui laudo:</label>
                <select class="inputCadastro" name="laudado">
                  <option value="" disabled selected>-- Selecione --</option>
                  <option value="sim">Sim</option>
                  <option value="nao">Não</option>
                </select>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="info_adicional">Informações Adicionais:</label>
                <input type="text" class="inputCadastro" name="informacao_adicional"
                  placeholder="Informação adicionais">
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button type="submit" class="botao-agendamento" form="addForm">Salvar</button>
        <button class="botao-agendamento" id="closeModal">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR USUÁRIO -->
  <div id="edit" class="modal">
    <div class="modal-content">
      <div class="header-popup">
        <h2>Editar Informações</h2>
      </div>

      <div class="modal-body">
        <div class="modal-info">
          <div class="coluna">
            <form id="editForm" method="post" action="profissionais/editarUsuariosGerais.php"
              enctype="multipart/form-data">
              <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?? '' ?>" />

              <div class="conjunto-info">
                <label class="labelCadastro" for="nome">Digite o nome do Paciente:</label>
                <input class="inputCadastro" type="text" name="nome"
                  value="<?= htmlspecialchars($usuario['nome_usuario'] ?? '') ?>" placeholder="Digite o nome..."
                  required />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="numero_prontuario">Digite o número de Prontuário:</label>
                <input class="inputCadastro" type="number" name="numero_prontuario"
                  value="<?= htmlspecialchars($usuario['numero_prontuario'] ?? '') ?>" placeholder="Digite o número..."
                  required />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="contato_usuario">Digite o número de telefone do paciente:</label>
                <input class="inputCadastro" type="tel" name="contato_usuario"
                  value="<?= htmlspecialchars($usuario['contato_usuario'] ?? '') ?>" placeholder="Telefone"/>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="situacao">Qual situação encontra-se o paciente:</label>
                <select name="situacao" class="inputCadastro">
                  <option value="vinculado" <?= ($usuario['situacao'] ?? '') == 'vinculado' ? 'selected' : '' ?>>Vinculado
                  </option>
                  <option value="encerrado" <?= ($usuario['situacao'] ?? '') == 'encerrado' ? 'selected' : '' ?>>Encerrado
                  </option>
                </select>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="diagnostico">Digite a deficiência dele:</label>
                <input class="inputCadastro" type="text" name="diagnostico"
                  value="<?= htmlspecialchars($usuario['diagnostico'] ?? '') ?>" placeholder="Deficiência"/>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="quantidade_terapias">Quantidade de terapias mensais:</label>
                <input class="inputCadastro" type="number" name="quantidade_terapias"
                  value="<?= htmlspecialchars($usuario['quantidade_terapias'] ?? '') ?>" placeholder="Quantidade"
                  />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="laudo">Possui laudo:</label>
                <select class="inputCadastro" name="laudado">
                  <option value="" disabled <?= !isset($usuario['laudado']) ? 'selected' : '' ?>>-- Selecione --</option>
                  <option value="sim" <?= isset($usuario['laudado']) && $usuario['laudado'] == 'sim' ? 'selected' : '' ?>
                    >Sim</option>
                  <option value="nao" <?= isset($usuario['laudado']) && $usuario['laudado'] == 'nao' ? 'selected' : '' ?>
                    >Não</option>
                </select>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="informacao_adicional">Informações Adicionais:</label>
                <input type="text" class="inputCadastro" name="informacao_adicional" placeholder="Informação"
                  value="<?= htmlspecialchars($usuario['informacao_adicional'] ?? '') ?>">
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button type="submit" class="botao-agendamento" form="editForm">Salvar</button>
        <button class="botao-agendamento" id="closeEdit">Cancelar</button>
      </div>
    </div>
  </div>

  <script src="js/script.js"></script>
</body>

</html>