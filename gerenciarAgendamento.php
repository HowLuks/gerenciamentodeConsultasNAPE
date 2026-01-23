<?php
require_once "./config/conexao.php";
require_once "./admin/buscarProfissional.php";
require_once "./admin/consultarProfissionais.php";
require_once "./admin/consultarIdProfissional.php";
require_once "./admin/consultarUsuarios.php";
require_once "./admin/buscarIdUsuario.php";
require_once "./admin/buscarIdAgendamento.php";

if ($_SESSION['id_logado'] == 1 or $_SESSION['cargo_logado'] == "coordenador") {
  // Coordenador master (ID 1) pode acessar qualquer perfil
  $id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id_logado'];
} else {
  // Outros usuários só podem acessar seu próprio perfil
  $id = intval($_SESSION['id_logado']);
}
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

  <div id="overlay2" class="overlay" style="display: none;"></div>

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

      <div class="container">
        <div class="info">
          <img class="icon-logo" src="imgs/icones/icons8-calendário-100 (1).png" alt="Ícone usuário" />
        </div>
        <div class="header-text">
          <h1>Agendamentos</h1>
          <p><?php echo strtoupper($profissional['cargo_profissional']) ?? 'Cargo não definido' ?>:
            <?php echo $profissional['nome_profissional'] ?? 'Nome não definido' ?>
          </p>
        </div>
      </div>

      <div class="parteCima">
        <div class="opcoes">
          <div class="agendamentos">
            <button class="botao-agendamento" id="openModal">
              <img src="imgs/icones/icons8-mais-calendário-100.png" width="40" />
              <h2 style="margin: 7px">Novo agendamento</h2>
            </button>
          </div>
        </div>
      </div>

      <div class="container-agenda">
        <div class="opcoes">
          <div class="agendamentos">
            <h1 style="margin: 7px">Agendamentos</h1>
          </div>
        </div>

        <div class="conteudoBaixo">
          <table class="table">
            <thead>
              <tr>
                <th>Nº Prontuário</th>
                <th>Usuário</th>
                <th>Laudado</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($consultarTabelaA)): ?>
                <?php foreach ($consultarTabelaA as $row): ?>
                  <tr>
                    <td data-label="Nº Prontuário">
                      <?= $row['numero_prontuario'] ? htmlspecialchars($row['numero_prontuario']) : "sem dado" ?>
                    </td>
                    <td data-label="Usuário">
                      <?= $row['nome_usuario'] ? htmlspecialchars($row['nome_usuario']) : 'sem dado' ?>
                    </td>
                    <td data-label="Laudado"><?= $row['laudado'] ? htmlspecialchars($row['laudado']) : 'sem dado' ?></td>
                    <td data-label="Data"><?= $row['data'] ? htmlspecialchars($row['data']) : "00/00/00" ?></td>
                    <td data-label="Hora"><?= $row['hora'] ? htmlspecialchars($row['hora']) : '00:00' ?></td>
                    <td data-label="Status">
                      <?= $row['status_agendamento'] ? htmlspecialchars($row['status_agendamento']) : "" ?>
                    </td>
                    <td data-label="Ações">
                      <div class="acoes-topo" style="display: flex">
                        <button class="botao-agendamento openEdit" data-user-id="<?= $row['id_agendamento'] ?>"
                          data-prof-id="<?= $row['id_agendamento'] ?? '' ?>">
                          Editar
                        </button>
                        <form method="POST" action="./profissionais/excluirAgendamento.php" style="display: inline">
                          <input type="hidden" name="id_agendamento" value="<?= $row['id_agendamento'] ?>" />
                          <input type="hidden" name="id_profissional" value="" />
                          <!-- Preencha com o ID do profissional se necessário -->
                          <button class="botao-agendamento" type="submit" name="acao" value="excluir"
                            onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">
                            Excluir
                          </button>
                        </form>
                        <button class="botao-agendamento btn-analisar"
                          data-id="<?= $row['id_agendamento'] ?>">Analisar</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align:center;">Nenhum agendamento encontrado.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- MODAL NOVO USUÁRIO -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <div class="header-popup">
        <h2>Novo Agendamento</h2>
      </div>

      <div class="modal-body">
        <div class="modal-info">
          <div class="coluna">
            <form id="addForm" method="post" action="profissionais/novoAgendamento.php" enctype="multipart/form-data">

              <input type="hidden" name="id_profissional" value="<?= $profissional['id_profissional'] ?>">
              <!-- 2. SELECT DE USUÁRIOS DESSE PROFISSIONAL -->
              <div class="conjunto-info">
                <label for="nome_usuario" class="labelCadastro">Selecione o Usuário:</label>
                <select name="nome_usuario" id="selectUsuarios" class="inputCadastro" required>
                  <option value="">-- Selecione um Usuario --</option>
                  <?php
                  // Buscar usuários desse profissional
                  if (!empty($profissional['id_profissional'])) {
                    $sqlUsuarios = "SELECT u.id_usuario, u.nome_usuario, u.numero_prontuario 
                    FROM usuario_profissional up
                    JOIN usuarios u ON u.id_usuario = up.id_usuario 
                    WHERE up.id_profissional = :id_profissional";

                    $stmtUsuarios = $pdo->prepare($sqlUsuarios);
                    $stmtUsuarios->bindValue(":id_profissional", $profissional['id_profissional']);
                    $stmtUsuarios->execute();
                    $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($usuarios as $usuario) {
                      $prontuario = !empty($usuario['numero_prontuario']) ?
                        htmlspecialchars($usuario['numero_prontuario']) :
                        'Sem prontuário';
                      $nome = htmlspecialchars($usuario['nome_usuario']);

                      // CORREÇÃO: use id_usuario ao invés de id
                      // E o VALUE deve ser o NOME do usuário, não o ID
                      echo "<option value=\"" . htmlspecialchars($usuario['nome_usuario']) . "\">" .
                        $prontuario . " - " . $nome . "</option>";
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="nome">Data:</label>
                <input class="inputCadastro" type="date" name="data" placeholder="Data..." required />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="deficiencia">Hora:</label>
                <input class="inputCadastro" type="time" name="hora" placeholder="Hora" required />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro" for="terapias">Status agendamento:</label>
                <select name="status_agendamento" class="inputCadastro" required>
                  <option value="agendado">Agendado</option>
                  <option value="confirmado">Confirmado</option>
                  <option value="cancelado">Falta</option>
                  <option value="realizado">Justificado</option>
                </select>
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
            <form id="editForm" method="post" action="profissionais/editarAgendamento.php"
              enctype="multipart/form-data">
              <!-- Campo hidden para ID do agendamento -->
              <input type="hidden" name="id_agendamento" id="id_agendamento" value="">

              <!-- Campo hidden para ID do usuário -->
              <input type="hidden" name="id_usuario" id="id_usuario" value="<?= $usuario['id_usuario'] ?? '' ?>">

              <!-- 2. NOME DO USUÁRIO -->
              <div class="conjunto-info">
                <label class="labelCadastro">Nome do usuario:</label>
                <input type="text" class="inputCadastro" id="edit_nome_usuario" name="nome_usuario"
                  value="<?= htmlspecialchars($usuario['nome_usuario'] ?? '') ?>" readonly>
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro">Digite a data:</label>
                <input class="inputCadastro" type="date" name="data" id="edit_data"
                  value="<?= htmlspecialchars($usuario['data'] ?? '') ?>" placeholder="data" required />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro">Digite a hora:</label>
                <input class="inputCadastro" type="time" name="hora" id="edit_hora"
                  value="<?= htmlspecialchars($usuario['hora'] ?? '') ?>" placeholder="hora" required />
              </div>

              <div class="conjunto-info">
                <label class="labelCadastro">Status agendamento:</label>
                <input type="text" class="inputCadastro" name="status_agendamento" id="edit_status"
                  placeholder="Digite o Status do agendamento"
                  value="<?= htmlspecialchars($usuario['status_agendamento'] ?? '') ?>">
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button type="submit" class="botao-agendamento" form="editForm">Salvar</button>
        <button type="button" class="botao-agendamento" id="closeEdit">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- OVERLAY E PUMP LATERAL -->

  <div id="pumpPaciente" class="pump">
    <div class="pump-content">
      <span id="closePump" class="close">&times;</span>

      <div class="pump-header">

        <h2 id="pumpNomeAgendamento">Carregando...</h2>

        <p>
          <strong>ID:</strong> <span id="pumpIdAgendamento"></span> |
          <strong>Nº Prontuário:</strong> <span id="pumpProntuarioAgendamento"></span>
        </p>
      </div>

      <hr />

      <div class="pump-info">

        <div class="campo">
          <p><strong>Contato:</strong></p>
          <p id="pumpContatoAgendamento"></p>
        </div>

        <div class="campo">
          <p><strong>Situação:</strong></p>
          <p id="pumpSituacaoAgendamento"></p>
        </div>

        <div class="campo">
          <p><strong>Diagnóstico:</strong></p>
          <p id="pumpDiagnosticoAgendamento"></p>
        </div>

        <div class="campo">
          <p><strong>Qtd. Terapias:</strong></p>
          <p id="pumpTerapiasAgendamento"></p>
        </div>

        <div class="campo">
          <p><strong>Info. Adicional:</strong></p>
          <p id="pumpInfoAdicionalAgendamento"></p>
        </div>

        <div class="campo">
          <p><strong>Possui Laudo:</strong></p>
          <p id="pumpLaudado"></p>
        </div>
        <div class="campo">
          <p><strong>Hora:</strong></p>
          <p id="pumpHora"></p>
        </div>

        <div class="campo">
          <p><strong>Data:</strong></p>
          <p id="pumpData"></p>
        </div>

        <div class="campo">
          <p><strong>Status Agendamento</strong></p>
          <p id="pumpStatusAgendamento"></p>
        </div>

        <hr>
        <div class="profissional">
          <div class="text-header">
            <h2>Informações do Profissional acompanhado</h2>
          </div>
          <div class="campo">
            <p><strong>Acompanhado pelo Profissional:</strong></p>
            <?php
            $sql = "SELECT p.nome_profissional, p.cargo_profissional
            FROM usuario_profissional up 
            JOIN profissionais p ON p.id_profissional = up.id_profissional 
            JOIN usuarios u ON up.id_usuario = u.id_usuario
            WHERE u.id_usuario = :id_usuario";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id_usuario", $usuario['id_usuario']);
            $stmt->execute();
            $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Usando foreach para percorrer todos os profissionais
            foreach ($profissionais as $profissional) {
              echo "<p>" . $profissional['nome_profissional'] . " - " . $profissional['cargo_profissional'] . "</p>";
            }
            ?>
          </div>
        </div>

      </div>

      <!-- Botão dentro do pump -->
      <!-- Botão dentro do pump -->
      <a href="#" id="btnGerarPDF" style="text-decoration: none;" class="botao-edicao" onclick="gerarPDFAgendamento()">
        GERAR PDF
      </a>
    </div>
  </div>


  <script src="js/script.js"></script>
</body>

</html>