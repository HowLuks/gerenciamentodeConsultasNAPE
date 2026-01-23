    document.addEventListener('DOMContentLoaded', function() {
      // Elementos do sidebar
      const sidebar = document.getElementById('sidebar');
      const menuBtn = document.getElementById('menu-btn');
      const overlay = document.getElementById('overlay');
      const content = document.getElementById('content');
      
      // Elementos do formulário
      const editForm = document.getElementById('editForm');
      const cancelBtn = document.getElementById('cancelBtn');

      // Controle do sidebar
      function handleSidebar() {
        if (window.innerWidth >= 1024) {
          sidebar.classList.add('active');
          overlay.classList.remove('active');
        } else {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
        }
      }

      // Inicializar sidebar
      handleSidebar();
      window.addEventListener('resize', handleSidebar);

      // Abrir/fechar sidebar no mobile
      if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
          sidebar.classList.toggle('active');
          if (window.innerWidth < 1024) {
            overlay.classList.toggle('active');
          }
        });
      }

      // Fechar sidebar ao clicar no overlay
      if (overlay) {
        overlay.addEventListener('click', () => {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
        });
      }

      // Formatação do CPF
      const cpfInput = document.getElementById('cpf');
      if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
          let value = e.target.value.replace(/\D/g, '');
          
          if (value.length > 11) {
            value = value.substring(0, 11);
          }
          
          if (value.length > 9) {
            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
          } else if (value.length > 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
          } else if (value.length > 3) {
            value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
          }
          
          e.target.value = value;
        });
    }

      // Botão Cancelar
      if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            window.history.back();
        });
      }

      // Fechar sidebar ao clicar fora (mobile)
      window.addEventListener('click', function(e) {
        if (window.innerWidth < 1024 && 
            sidebar.classList.contains('active') &&
            !sidebar.contains(e.target) &&
            e.target !== menuBtn) {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
        }
      });
    });