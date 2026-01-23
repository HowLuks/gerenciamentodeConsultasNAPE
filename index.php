<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NAPE - Sistema de Login</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/stylesIn.css">
 </head>
  <body>
    <!-- Background Pattern -->
    <div class="background-pattern"></div>

    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <img src="imgs/icones/Agrupar 1.png" width="250" alt="" />
        <div class="header-subtitle">
          <p>Núcleo de Atendimento Pedagógico Especializado</p>
        </div>
      </div>
    </header>
    <hr>
    <!-- Main Content -->
    <main class="main-container">
      <div class="login-wrapper">
        <!-- Welcome Section -->
        <div class="welcome-section">
          <div class="welcome-content">
            <h2>Seja Bem-vindo</h2>
            <p>Acesse sua conta para continuar utilizando o sistema NAPE</p>
            <div class="features-list">
              <div class="feature-item">
                <div class="feature-icon">
                  <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M9 12l2 2 4-4" />
                    <circle cx="12" cy="12" r="9" />
                  </svg>
                </div>
                <span>Gestão de agendamentos</span>
              </div>
              <div class="feature-item">
                <div class="feature-icon">
                  <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M9 12l2 2 4-4" />
                    <circle cx="12" cy="12" r="9" />
                  </svg>
                </div>
                <span>Controle de usuários</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Login Form -->
        <div class="login-container">
          <div class="login-card">
            <div class="login-header">
              <h3>LOGIN</h3>
              <p>Entre com suas credenciais</p>
            </div>

            <form class="login-form" method="post" action="admin/verificarLogin.php" id="loginForm">
              <div class="form-group">
                <label for="username">Nome de usuário</label>
                <div class="input-wrapper">
                  <div class="input-icon">
                    <img src="imgs/icones/icons8-body-67.png" width="25" alt="" />
                  </div>
                  <input
                    type="text"
                    id="nome"
                    name="nome_profissional"
                    placeholder="Digite seu nome..."
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label for="password">Senha</label>
                <div class="input-wrapper">
                  <div class="input-icon">
                    <img src="imgs/icones/icons8-locked-padlock-78.png" width="30" alt="" />
                  </div>
                  <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha..."
                    required
                    autocomplete="current-password"
                  />
                </div>
              </div>
              <button type="submit" class="login-button" id="loginButton">
                <span class="button-text">ENTRAR</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
    <footer>
      <span>
        © 2024 NAPE - Núcleo de Atendimento Pedagógico Especializado. Todos os direitos
        reservados.
      </span>
    </footer>

    <script src="script.js"></script>
  </body>
</html>
