document.addEventListener("DOMContentLoaded", function () {
  // ========== DETECÇÃO DA PÁGINA ==========
  const currentPage = window.location.pathname;
  const isUsuarioPage =
    currentPage.includes("gerenciarUsuario") ||
    currentPage.includes("gerenciarUsuarios") ||
    currentPage.includes("usuarios.php");
  const isAgendamentoPage =
    currentPage.includes("gerenciarAgendamento") ||
    currentPage.includes("agendamento") ||
    currentPage.includes("agendamento.php");

  // Elementos principais
  const sidebar = document.getElementById("sidebar");
  const menuBtn = document.getElementById("menu-btn");
  const overlay = document.getElementById("overlay");
  const overlay2 = document.getElementById("overlay2");

  // Modais
  const modal = document.getElementById("modal");
  const edit = document.getElementById("edit");
  const pump = document.getElementById("pumpPaciente");
  const modalForm = document.getElementById("modalForm");
  const cadastroProf = document.getElementById("cadastroProf");

  // Botões de abertura
  const openModal = document.getElementById("openModal");
  const openEditBtns = document.querySelectorAll(".openEdit");
  const analisarBtns = document.querySelectorAll(".btn-analisar");
  const bttAdd = document.getElementById("bttAdd");
  const openModalProf = document.getElementById("openModalProf");

  // Botões de fechamento
  const closeModal = document.getElementById("closeModal");
  const closeEdit = document.getElementById("closeEdit");
  const closePump = document.getElementById("closePump");
  const closeCadastroProf = document.getElementById("closeCadastroProf");
  const cancelProfissional = document.getElementById("cancelProfissional");
  const addProfissionalForm = document.getElementById("addProfissionalForm");

  // ========== MODAL ADICIONAR PROFISSIONAL ==========
  if (bttAdd && modalForm) {
    bttAdd.addEventListener("click", () => {
      modalForm.style.display = "flex";
      const mensagem = document.getElementById("mensagem");
      if (mensagem) mensagem.style.display = "none";
    });
  }

  // ADICIONAR EVENT LISTENER PARA O BOTÃO CANCELAR
  if (cancelProfissional && modalForm) {
    cancelProfissional.addEventListener("click", () => {
      modalForm.style.display = "none";
      if (addProfissionalForm) {
        addProfissionalForm.reset();
      }
    });
  }

  // ========== SIDEBAR RESPONSIVA ==========
  function handleSidebar() {
    if (!sidebar) return;

    if (window.innerWidth >= 1024) {
      sidebar.classList.add("active");
      if (overlay) overlay.classList.remove("active");
    } else {
      sidebar.classList.remove("active");
      if (overlay) overlay.classList.remove("active");
    }
  }

  // Inicializar sidebar
  if (sidebar) {
    handleSidebar();
    window.addEventListener("resize", handleSidebar);
  }

  // ========== HAMBURGER MENU ==========
  if (menuBtn && sidebar) {
    menuBtn.addEventListener("click", () => {
      if (pump && pump.classList.contains("active")) {
        return;
      }

      sidebar.classList.toggle("active");
      if (window.innerWidth < 1024 && overlay) {
        overlay.classList.toggle("active");
      }
    });
  }

  // ========== OVERLAY PRINCIPAL ==========
  if (overlay) {
    overlay.addEventListener("click", () => {
      if (
        (pump && pump.classList.contains("active")) ||
        window.innerWidth >= 1024
      ) {
        return;
      }

      if (sidebar) sidebar.classList.remove("active");
      overlay.classList.remove("active");
    });
  }

  // ========== MODAL NOVO USUÁRIO ==========
  if (openModal && modal) {
    openModal.addEventListener("click", () => {
      modal.style.display = "flex";
    });
  }

  if (closeModal && modal) {
    closeModal.addEventListener("click", () => {
      modal.style.display = "none";
      document.getElementById("addForm")?.reset();
    });
  }

  // ========== MODAL CADASTRAR PARA PROFISSIONAL ==========
  if (openModalProf && cadastroProf) {
    openModalProf.addEventListener("click", () => {
      
      cadastroProf.style.display = "flex";
    });
  }

  if (closeCadastroProf && cadastroProf) {
    closeCadastroProf.addEventListener("click", () => {
      cadastroProf.style.display = "none";
    });
  }

  // ========== MODAL EDITAR USUÁRIO ==========
  if (openEditBtns.length && edit) {
    openEditBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        edit.style.display = "flex";
      });
    });
  }

  if (closeEdit && edit) {
    closeEdit.addEventListener("click", () => {
      edit.style.display = "none";
    });
  }

  // ========== PUMP LATERAL (ANALISAR) ==========
  function fecharPump() {
    if (pump) pump.classList.remove("active");
    if (overlay2) overlay2.style.display = "none";
  }

  // ============================================
  // CONFIGURAÇÃO DOS BOTÕES "ANALISAR" - DIFERENCIADO POR PÁGINA
  // ============================================

  if (analisarBtns.length > 0) {
    analisarBtns.forEach((btn) => {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        // VERIFICAR QUAL TIPO DE PÁGINA PARA SABER QUE DADOS BUSCAR
        if (isUsuarioPage) {
          // ========== PÁGINA DE USUÁRIOS ==========
          const userId = this.getAttribute("data-user-id");

          // 1. Mostra o pump
          if (pump) pump.classList.add("active");
          if (overlay2) overlay2.style.display = "block";

          // 2. Define "Carregando..." enquanto busca
          const pumpNome = document.getElementById("pumpNome");
          if (pumpNome) pumpNome.textContent = "Carregando...";

          const url = "profissionais/buscarUsuario.php?id_usuario=" + userId;

          // 3. Faz a requisição AJAX para buscar USUÁRIO
          fetch(url)
            .then((response) => {
              if (!response.ok) {
                throw new Error(`Erro HTTP ${response.status}`);
              }
              return response.json();
            })
            .then((dados) => {
              // Se a API retornar erro
              if (dados.erro) {
                console.error("Erro da API:", dados.erro);
                if (pumpNome) pumpNome.textContent = "Erro: " + dados.erro;
                return;
              }

              // PREENCHE TODOS OS CAMPOS DO USUÁRIO
              if (pumpNome)
                pumpNome.textContent = dados.nome_usuario || "Sem dados";

              const pumpId = document.getElementById("pumpId");
              if (pumpId) pumpId.textContent = dados.id_usuario || "Sem dados";

              const pumpProntuario = document.getElementById("pumpProntuario");
              if (pumpProntuario)
                pumpProntuario.textContent = dados.prontuario || "Sem dados";

              const pumpContato = document.getElementById("pumpContato");
              if (pumpContato)
                pumpContato.textContent = dados.contato || "Sem dados";

              const pumpSituacao = document.getElementById("pumpSituacao");
              if (pumpSituacao)
                pumpSituacao.textContent = dados.situacao || "Sem dados";

              const pumpDiagnostico =
                document.getElementById("pumpDiagnostico");
              if (pumpDiagnostico)
                pumpDiagnostico.textContent = dados.diagnostico || "Sem dados";

              const pumpTerapias = document.getElementById("pumpTerapias");
              if (pumpTerapias)
                pumpTerapias.textContent = dados.qtd_terapias || "Sem dados";

              const pumpInfoAdicional =
                document.getElementById("pumpInfoAdicional");
              if (pumpInfoAdicional)
                pumpInfoAdicional.textContent =
                  dados.info_adicional || "Sem dados";

              const pumpLaudado = document.getElementById("pumpLaudado");
              if (pumpLaudado)
                pumpLaudado.textContent = dados.laudado || "Sem dados";

              const pumpProfissional =
                document.getElementById("pumpProfissional");
              if (pumpProfissional)
                pumpProfissional.textContent =
                  dados.profissional || "Sem dados";

              console.log("Pump do usuário preenchido com sucesso!");
            })
            .catch((error) => {
              console.error("Erro na requisição:", error.message);

              // Mostra erro no pump
              if (pumpNome) pumpNome.textContent = "Erro ao carregar";
              const pumpId = document.getElementById("pumpId");
              if (pumpId) pumpId.textContent = "Erro: " + error.message;
            });

          // NO BLOCO DA PÁGINA DE AGENDAMENTOS (dentro do if (isAgendamentoPage)):
        } else if (isAgendamentoPage) {
          // ========== PÁGINA DE AGENDAMENTOS ==========

          // JEITO SIMPLES: Pega o ID direto do botão
          const idAgendamento = this.getAttribute("data-id");

          // Se não tem data-id, tenta data-user-id (como o botão Editar)
          if (!idAgendamento) {
            const idAgendamento = this.getAttribute("data-user-id");
          }

          // Se ainda não tem, pega do botão "Editar" na mesma linha
          if (!idAgendamento) {
            const linha = this.closest("tr");
            const botaoEditar = linha.querySelector(".openEdit");
            if (botaoEditar) {
              const idAgendamento = botaoEditar.getAttribute("data-user-id");
            }
          }

          // Última tentativa: pega da primeira coluna da tabela
          if (!idAgendamento) {
            const linha = this.closest("tr");
            const primeiraColuna = linha.querySelector("td:first-child");
            if (primeiraColuna) {
              const idAgendamento = primeiraColuna.textContent.trim();
            }
          }
          if (!idAgendamento) {
            alert("Erro: Não foi possível encontrar o ID do agendamento");
            return;
          }

          // ABRE O PUMP
          const pump = document.getElementById("pumpPaciente");
          const overlay2 = document.getElementById("overlay2");

          if (pump) pump.classList.add("active");
          if (overlay2) overlay2.style.display = "block";

          // MOSTRA "CARREGANDO..."
          const pumpNomeAgendamento = document.getElementById(
            "pumpNomeAgendamento"
          );
          if (pumpNomeAgendamento)
            pumpNomeAgendamento.textContent = "Carregando...";

          // PREENCHE O ID NO PUMP (visualmente)
          const pumpIdAgendamento =
            document.getElementById("pumpIdAgendamento");
          if (pumpIdAgendamento) pumpIdAgendamento.textContent = idAgendamento;

          // FAZ A REQUISIÇÃO
          fetch(`./profissionais/buscarAgendamento.php?id=${idAgendamento}`)
            .then((response) => response.json())
            .then((dados) => {
              if (dados.success === true) {
                // PREENCHE TODOS OS CAMPOS DE UMA VEZ
                const campos = {
                  pumpNomeAgendamento:
                    dados.nome_paciente || dados.nome_usuario || "Sem dados",
                  pumpIdAgendamento:
                    dados.id_agendamento || idAgendamento || "Sem dados",
                  pumpProntuarioAgendamento:
                    dados.numero_prontuario || "Sem dados",
                  pumpContatoAgendamento: dados.contato_paciente || "Sem dados",
                  pumpSituacaoAgendamento: dados.situacao || "Sem dados",
                  pumpDiagnosticoAgendamento: dados.diagnostico || "Sem dados",
                  pumpLaudado: dados.laudado || "Sem dados",
                  pumpTerapiasAgendamento:
                    dados.quantidade_terapias || "Sem dados",
                  pumpInfoAdicionalAgendamento:
                    dados.informacao_adicional || "Sem dados",
                  pumpProfissionalAgendamento:
                    dados.nome_profissional || "Sem dados",
                  pumpHora: dados.hora_agendamento || "Sem dados",
                  pumpData:
                    dados.data_formatada ||
                    dados.data_agendamento ||
                    "Sem dados",
                  pumpStatusAgendamento: dados.status || "Sem dados",
                };

                // Preenche cada campo
                Object.keys(campos).forEach((idCampo) => {
                  const elemento = document.getElementById(idCampo);
                  if (elemento) {
                    elemento.textContent = campos[idCampo];
                  }
                });
              } else {
                console.error("ERRO NA API:", dados.erro);
                alert("Erro: " + (dados.erro || "Agendamento não encontrado"));
              }
            })
            .catch((erro) => {
              console.error("🔥 ERRO NA REQUISIÇÃO:", erro);
              alert("Erro ao buscar dados: " + erro.message);
            });
        }
      });
    });
  }

  // ============================================
  // FUNÇÃO PARA  R PDF - DIFERENCIADA
  // ============================================

  function gerarPDFDoPump() {
    // Diferencia entre página de usuários e agendamentos
    if (isUsuarioPage) {
      // Pega os dados que estão VISÍVEIS no pump de USUÁRIO
      const dados = {
        id_usuario:
          document.getElementById("pumpId")?.textContent?.trim() || "Sem dados",
        nome_usuario:
          document.getElementById("pumpNome")?.textContent?.trim() ||
          "Sem dados",
        prontuario:
          document.getElementById("pumpProntuario")?.textContent?.trim() ||
          "Sem dados",
        contato:
          document.getElementById("pumpContato")?.textContent?.trim() ||
          "Sem dados",
        situacao:
          document.getElementById("pumpSituacao")?.textContent?.trim() ||
          "Sem dados",
        diagnostico:
          document.getElementById("pumpDiagnostico")?.textContent?.trim() ||
          "Sem dados",
        qtd_terapias:
          document.getElementById("pumpTerapias")?.textContent?.trim() ||
          "Sem dados",
        info_adicional:
          document.getElementById("pumpInfoAdicional")?.textContent?.trim() ||
          "Sem dados",
        profissional:
          document.getElementById("pumpProfissional")?.textContent?.trim() ||
          "Sem dados",
      };

      // Verifica se tem dados básicos
      if (
        !dados.id_usuario ||
        dados.id_usuario === "N/A" ||
        dados.id_usuario === ""
      ) {
        return;
      }

      // Abre PDF para usuário
      window.open(`./pdf/gerarPDFUsuario.php?id=${dados.id_usuario}`, "_blank");
    } else if (isAgendamentoPage) {
      // Lógica para PDF de agendamento
      const idAgendamento = document
        .getElementById("pumpIdAgendamento")
        ?.textContent?.trim();
      if (
        idAgendamento &&
        idAgendamento !== "Sem dados" &&
        idAgendamento !== ""
      ) {
        window.open(
          `./pdf/gerarPDFAgendamento.php?id=${idAgendamento}`,
          "_blank"
        );
      }
    }
  }

  // ============================================
  // CONFIGURA O BOTÃO QUANDO O PUMP ABRIR
  // ============================================

  // Observa quando o pump abre para configurar o botão
  document.addEventListener("click", function (e) {
    // Se clicou em um botão "Analisar"
    if (e.target.classList.contains("btn-analisar")) {
      // Depois de 100ms (quando o pump já abriu), configura o botão PDF
      setTimeout(() => {
        const btnPDF = document.getElementById("btnGerarPDF");
        if (btnPDF) {
          btnPDF.onclick = gerarPDFDoPump;
        }
      }, 100);
    }
  });

  // Configura o botão PDF quando o DOM carregar
  const btnPDF = document.getElementById("btnGerarPDF");
  if (btnPDF) {
    btnPDF.onclick = gerarPDFDoPump;
  }

  // Adiciona fechamento do pump
  if (closePump && pump && overlay2) {
    closePump.addEventListener("click", function () {
      pump.classList.remove("active");
      overlay2.style.display = "none";
    });
  }

  if (closePump && overlay2) {
    closePump.addEventListener("click", fecharPump);
  }

  if (overlay2) {
    overlay2.addEventListener("click", fecharPump);
  }

  // ========== FECHAR MODAIS CLICANDO FORA ==========
  window.addEventListener("click", (e) => {

    // Fechar modal editar usuário
    if (e.target === edit) {
      edit.style.display = "none";
    }

    // Fechar modal adicionar profissional
    if (e.target === modalForm) {
      modalForm.style.display = "none";
      if (addProfissionalForm) addProfissionalForm.reset();
    }

    // Fechar sidebar no mobile/tablet
    if (
      sidebar &&
      sidebar.classList.contains("active") &&
      !sidebar.contains(e.target) &&
      e.target !== menuBtn &&
      window.innerWidth < 1024
    ) {
      if (pump && pump.classList.contains("active")) {
        return;
      }

      sidebar.classList.remove("active");
      if (overlay) overlay.classList.remove("active");
    }
  });

  // ============================================
  // BOTÃO "EDITAR" - DIFERENCIADO POR PÁGINA
  // ============================================

  // Primeiro, seleciona os elementos
  const editBtns = document.querySelectorAll(".openEdit");
  const editModal = document.getElementById("edit");
  const editOverlay =
    document.getElementById("overlayEdit") ||
    document.getElementById("overlay2");
  const closeEditBtn = document.getElementById("closeEdit");

  // Configura botões "Editar" com base no tipo de página
  if (editBtns.length && editModal) {
    editBtns.forEach((btn) => {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        console.log("🖱️ Botão Editar clicado");

        // ========== PÁGINA DE USUÁRIOS ==========
        if (isUsuarioPage) {
          const userId = this.getAttribute("data-user-id");
          console.log("📋 Editando USUÁRIO com ID:", userId);

          // 1. Pega o ID do profissional da URL atual
          const urlParams = new URLSearchParams(window.location.search);
          const profId = urlParams.get("id");

          // 2. Abre o modal - CORREÇÃO AQUI: mudar para "flex"
          editModal.style.display = "flex"; // ← MUDANÇA DE "block" PARA "flex"
          editModal.classList.add("active");
          if (editOverlay) {
            editOverlay.style.display = "block";
          }

          // 3. VERIFICA e PREENCHE o campo hidden id_profissional
          let profIdInput = document.getElementById("inputProfId");

          // Se o campo não existe, cria ele
          if (!profIdInput) {
            profIdInput = document.createElement("input");
            profIdInput.type = "hidden";
            profIdInput.name = "id_profissional";
            profIdInput.id = "inputProfId";
            document.getElementById("editForm").appendChild(profIdInput);
          }

          // Preenche com o ID do profissional
          profIdInput.value = profId || "";
          console.log(
            "✅ Campo id_profissional preenchido com:",
            profIdInput.value
          );

          // 4. Busca os dados do USUÁRIO via AJAX
          fetch(`profissionais/buscarUsuario.php?id_usuario=${userId}`)
            .then((response) => {
              if (!response.ok) throw new Error("Erro na requisição");
              return response.json();
            })
            .then((dados) => {
              console.log("✅ Dados do USUÁRIO recebidos para edição:", dados);

              // 5. Preenche o formulário com os dados do USUÁRIO
              const campos = {
                id_usuario: dados.id_usuario || "",
                nome: dados.nome_usuario || "",
                numero_prontuario: dados.prontuario || "",
                contato_usuario: dados.contato || "",
                diagnostico: dados.diagnostico || "",
                cpf: dados.cpf || "",
                laudado: dados.laudado || "",
                quantidade_terapias: dados.qtd_terapias || "",
                informacao_adicional: dados.info_adicional || "",
              };

              // Preenche cada campo
              for (const [name, value] of Object.entries(campos)) {
                const campo = editModal.querySelector(`[name="${name}"]`);
                if (campo) {
                  campo.value = value;
                  console.log(`   ${name}: ${value}`);
                } else {
                  console.warn(`Campo não encontrado: ${name}`);
                }
              }

              // Select precisa tratamento especial
              const situacaoSelect = editModal.querySelector(
                'select[name="situacao"]'
              );
              if (situacaoSelect && dados.situacao) {
                situacaoSelect.value = dados.situacao;
                console.log(`   situacao: ${dados.situacao}`);
              }

              console.log("✅ Formulário de USUÁRIO preenchido com sucesso!");
            })
            .catch((error) => {
              console.error("❌ Erro ao buscar dados do usuário:", error);
              alert("Erro ao carregar dados do usuário. Tente novamente.");
            });
        }
        // ========== PÁGINA DE AGENDAMENTOS ==========
        else if (isAgendamentoPage) {
          // Pega o ID do agendamento
          let idAgendamento =
            this.getAttribute("data-user-id") ||
            this.closest("tr")
              .querySelector("td:first-child")
              .textContent.trim();

          console.log("Editando AGENDAMENTO com ID:", idAgendamento);

          if (editModal) {
            editModal.style.display = "flex";

            // Faz a requisição AJAX para buscar os dados do AGENDAMENTO
            fetch(`./profissionais/buscarAgendamento.php?id=${idAgendamento}`)
              .then((response) => {
                if (!response.ok)
                  throw new Error(`Erro HTTP: ${response.status}`);
                return response.json();
              })
              .then((dados) => {
                console.log(
                  "Dados do AGENDAMENTO recebidos para edição:",
                  dados
                );

                if (dados.success === true) {
                  // 1. Profissional (input text)
                  const nomeProfInput =
                    document.getElementById("edit_nome_profissional") ||
                    document.querySelector(
                      '#edit input[name="nome_profissional"]'
                    );
                  if (nomeProfInput && dados.nome_profissional) {
                    nomeProfInput.value = dados.nome_profissional;
                  }

                  // 2. Nome do usuário (input text)
                  const nomeUsuarioInput =
                    document.getElementById("edit_nome_usuario") ||
                    document.querySelector('#edit input[name="nome_usuario"]');
                  if (nomeUsuarioInput && dados.nome_paciente) {
                    nomeUsuarioInput.value = dados.nome_paciente;
                  }

                  // 3. Campo hidden com ID do usuário
                  const idUsuarioInput =
                    editModal.querySelector('input[name="id_usuario"]') ||
                    document.getElementById("id_usuario");
                  if (idUsuarioInput && dados.id_usuario) {
                    idUsuarioInput.value = dados.id_usuario;
                  }

                  // 4. Data
                  const dataInput =
                    document.getElementById("edit_data") ||
                    editModal.querySelector('input[name="data"]');
                  if (dataInput && dados.data_agendamento) {
                    const dataObj = new Date(dados.data_agendamento);
                    const dataFormatada = dataObj.toISOString().split("T")[0];
                    dataInput.value = dataFormatada;
                  }

                  // 5. Hora
                  const horaInput =
                    document.getElementById("edit_hora") ||
                    editModal.querySelector('input[name="hora"]');
                  if (horaInput && dados.hora_agendamento) {
                    horaInput.value = dados.hora_agendamento;
                  }

                  // 6. Status
                  const statusInput =
                    document.getElementById("edit_status") ||
                    editModal.querySelector('input[name="status_agendamento"]');
                  if (statusInput && dados.status) {
                    statusInput.value = dados.status;
                  }

                  // 7. Campo hidden com ID do agendamento
                  let idAgendamentoInput =
                    document.getElementById("id_agendamento") ||
                    editModal.querySelector('input[name="id_agendamento"]');
                  if (!idAgendamentoInput) {
                    idAgendamentoInput = document.createElement("input");
                    idAgendamentoInput.type = "hidden";
                    idAgendamentoInput.name = "id_agendamento";
                    idAgendamentoInput.id = "id_agendamento";
                    editModal
                      .querySelector("form")
                      .appendChild(idAgendamentoInput);
                  }
                  idAgendamentoInput.value =
                    dados.id_agendamento || idAgendamento;

                  console.log(
                    "✅ Formulário de AGENDAMENTO preenchido com sucesso!"
                  );
                } else {
                  console.error("Erro nos dados do agendamento:", dados.erro);
                  alert(
                    "Erro ao carregar dados: " +
                      (dados.erro || "Dados não encontrados")
                  );
                }
              })
              .catch((erro) => {
                console.error("Erro na requisição do agendamento:", erro);
                alert("Erro ao buscar dados: " + erro.message);
              });
          }
        }
      });
    });
  }

  // ============================================
  // FECHAR MODAL DE EDIÇÃO
  // ============================================

  // Botão "Cancelar" ou "X" para fechar
  if (closeEditBtn && editModal) {
    closeEditBtn.addEventListener("click", function () {
      editModal.style.display = "none";
      editModal.classList.remove("active");
      if (editOverlay) {
        editOverlay.style.display = "none";
      }
    });
  }

  // ============================================
  // FUNÇÃO PARA PREENCHER ID DO PROFISSIONAL NOS FORMULÁRIOS
  // ============================================

  function preencherIdProfissionalNosForms() {
    // Pega o ID do profissional da URL
    const urlParams = new URLSearchParams(window.location.search);
    const profId = urlParams.get("id");

    if (profId) {
      // Preenche todos os campos hidden com nome "id_profissional"
      const profIdInputs = document.querySelectorAll(
        'input[name="id_profissional"]'
      );
      profIdInputs.forEach((input) => {
        input.value = profId;
      });
    }
  }

  // Executa quando a página carrega
  preencherIdProfissionalNosForms();

  // ============================================
  // FUNÇÃO DE CONFIRMAÇÃO PARA EXCLUIR
  // ============================================

  function confirmExclusao(form) {
    // Primeiro preenche o id_profissional (caso não esteja preenchido)
    const urlParams = new URLSearchParams(window.location.search);
    const profId = urlParams.get("id");

    if (profId) {
      const profIdInput = form.querySelector('input[name="id_profissional"]');
      if (profIdInput) {
        profIdInput.value = profId;
      }
    }

    // Mostra confirmação
    return confirm(
      "Tem certeza que deseja excluir este " +
        (isUsuarioPage ? "usuário" : "agendamento") +
        "?\nEsta ação não pode ser desfeita!"
    );
  }

  // Função para carregar usuários do profissional selecionado
  function carregarUsuariosDoProfissional(nomeProfissional) {
    if (!nomeProfissional || nomeProfissional.trim() === "") {
      const selectUsuarios = document.getElementById("selectUsuarios");
      if (selectUsuarios) {
        selectUsuarios.innerHTML =
          '<option value="">-- Primeiro selecione um profissional --</option>';
      }
      return;
    }

    // Mostra loading
    const select = document.getElementById("selectUsuarios");
    if (!select) return;

    select.innerHTML = '<option value="">Carregando usuários...</option>';
    select.disabled = true;

    // Faz requisição AJAX para buscar usuários
    fetch(
      `profissionais/buscarUsuariosPorProfissional.php?profissional=${encodeURIComponent(
        nomeProfissional
      )}`
    )
      .then((response) => {
        if (!response.ok) throw new Error("Erro na requisição");
        return response.json();
      })
      .then((usuarios) => {
        // Preenche o select
        let options = '<option value="">-- Selecione um usuário --</option>';

        if (usuarios.length > 0) {
          usuarios.forEach((user) => {
            options += `<option value="${user.id_usuario}">${user.nome_usuario} (Pront: ${user.numero_prontuario})</option>`;
          });
        } else {
          options =
            '<option value="">Nenhum usuário encontrado para este profissional</option>';
        }

        select.innerHTML = options;
        select.disabled = false;

        // Se estiver editando, seleciona o usuário atual
        const userIdAtual = document.querySelector(
          'input[name="id_usuario"]'
        )?.value;
        if (userIdAtual) {
          select.value = userIdAtual;
        }
      })
      .catch((error) => {
        console.error("Erro ao buscar usuários:", error);
        select.innerHTML =
          '<option value="">Erro ao carregar usuários</option>';
        select.disabled = false;
      });
  }

  // Se já tem um profissional preenchido, carrega seus usuários automaticamente
  const nomeProf = document.getElementById("nome_profissional")?.value;
  if (nomeProf) {
    // Aguarda um pouco para o DOM carregar completamente
    setTimeout(() => {
      carregarUsuariosDoProfissional(nomeProf);
    }, 500);
  }

    // ========== MODAL EDITAR USUÁRIO ==========
  if (openEditBtns.length && edit) {
    openEditBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        console.log("🖱️ Botão Editar clicado - PREENCHENDO FORMULÁRIO");
        
        // Pega o ID do usuário do botão
        const userId = btn.getAttribute("data-user-id");
        const profId = btn.getAttribute("data-prof-id") || "";
        
        console.log("📋 Usuário ID:", userId, "Profissional ID:", profId);
        
        // Abre o modal
        edit.style.display = "flex";
        
        // 1. CRIA campo hidden para id_profissional se não existir
        let profIdInput = document.getElementById("inputProfId");
        if (!profIdInput) {
          profIdInput = document.createElement("input");
          profIdInput.type = "hidden";
          profIdInput.name = "id_profissional";
          profIdInput.id = "inputProfId";
          document.getElementById("editForm").appendChild(profIdInput);
          console.log("✅ Campo id_profissional criado");
        }
        
        // Preenche com o ID do profissional
        if (profId) {
          profIdInput.value = profId;
        } else {
          // Tenta pegar da URL
          const urlParams = new URLSearchParams(window.location.search);
          const profIdFromUrl = urlParams.get("id");
          profIdInput.value = profIdFromUrl || "";
        }
        console.log("   id_profissional:", profIdInput.value);
        
        // 2. BUSCA os dados do usuário via AJAX
        fetch(`profissionais/buscarUsuario.php?id_usuario=${userId}`)
          .then((response) => {
            if (!response.ok) throw new Error(`Erro HTTP ${response.status}`);
            return response.json();
          })
          .then((dados) => {
            console.log("✅ Dados recebidos:", dados);
            
            if (dados.erro) {
              console.error("❌ Erro da API:", dados.erro);
              return;
            }
            
            // 3. PREENCHE o formulário com os dados
            const camposParaPreencher = {
              'id_usuario': dados.id_usuario || "",
              'nome': dados.nome_usuario || "",
              'numero_prontuario': dados.prontuario || "",
              'contato_usuario': dados.contato || "",
              'diagnostico': dados.diagnostico || "",
              'quantidade_terapias': dados.qtd_terapias || "",
              'informacao_adicional': dados.info_adicional || "",
            };
            
            // Preenche cada campo
            for (const [nomeCampo, valor] of Object.entries(camposParaPreencher)) {
              const campo = edit.querySelector(`[name="${nomeCampo}"]`);
              if (campo) {
                const valorAntigo = campo.value;
                campo.value = valor;
                console.log(`   ${nomeCampo}: "${valorAntigo}" → "${valor}"`);
              } else {
                console.warn(`⚠️ Campo não encontrado: ${nomeCampo}`);
              }
            }
            
            // Select de situação
            const situacaoSelect = edit.querySelector('select[name="situacao"]');
            if (situacaoSelect && dados.situacao) {
              const valorAntigo = situacaoSelect.value;
              situacaoSelect.value = dados.situacao;
              console.log(`   situacao: "${valorAntigo}" → "${dados.situacao}"`);
            }
            
            console.log("✅ Formulário preenchido com sucesso!");
          })
          .catch((error) => {
            console.error("❌ Erro ao buscar dados:", error);
            alert("Erro ao carregar dados do usuário");
          });
      });
    });
  }

  if (closeEdit && edit) {
    closeEdit.addEventListener("click", () => {
      edit.style.display = "none";
    });
  }

});

