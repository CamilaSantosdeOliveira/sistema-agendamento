// dark-mode.js
if (typeof window.darkModeInitialized === 'undefined') {
    window.darkModeInitialized = true;

    function initDarkMode() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        // Aplicar imediatamente se estiver no localStorage
        const isDark = localStorage.getItem('darkMode') === 'true';
        if (isDark) {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark-mode');
        }

        if (darkModeToggle) {
            // Convert to pill switch
            if (!darkModeToggle.classList.contains('theme-toggle-pill-active')) {
                darkModeToggle.innerHTML = '<div class="theme-slider"></div><i class="fas fa-sun"></i><i class="fas fa-moon"></i>';
                darkModeToggle.classList.add('theme-toggle-pill-active');
            }

            // Usar onclick direto para garantir que não existam múltiplos listeners
            darkModeToggle.onclick = function(e) {
                e.preventDefault();
                const nowDark = body.classList.toggle('dark-mode');
                document.documentElement.classList.toggle('dark-mode', nowDark);
                localStorage.setItem('darkMode', nowDark);
                updateToggleUI(nowDark);
                
                window.dispatchEvent(new Event('themeChanged'));
            };
        }

        function updateToggleUI(isDark) {
            if (!darkModeToggle) return;
            
            if (darkModeToggle.classList.contains('theme-toggle-pill-active')) {
                // Pill switch UI is handled entirely by CSS!
                return;
            }

            const icon = darkModeToggle.querySelector('i');
            const span = darkModeToggle.querySelector('span');
            
            if (isDark) {
                if (icon) icon.className = 'fas fa-sun';
                if (span) span.textContent = 'Modo Claro';
            } else {
                if (icon) icon.className = 'fas fa-moon';
                if (span) span.textContent = 'Modo Escuro';
            }
        }
        
        // Initial UI update
        updateToggleUI(isDark);

        // Atalho de teclado (Ctrl+D)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                darkModeToggle?.click();
            }
        });

        // Remover classe de init para mostrar o body
        document.documentElement.classList.remove('dark-mode-init');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDarkMode);
    } else {
        initDarkMode();
    }
}
