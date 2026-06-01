/* EduConnect — Auth shared JS (theme toggle, password toggle, demo accounts, parallax) */
(function() {
    'use strict';

    function init() {
        // Demo accounts toggle
        const demoBox = document.getElementById('demoAccounts');
        const demoBtn = document.getElementById('demoToggleBtn');
        if (demoBtn && demoBox) {
            demoBtn.addEventListener('click', function() {
                const open = demoBox.classList.toggle('open');
                demoBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        // Password toggle (any input with [data-toggle-pass])
        document.querySelectorAll('.toggle-pass').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const wrap = btn.closest('.input-wrap');
                const input = wrap && wrap.querySelector('input[type="password"], input[type="text"]');
                if (!input) return;
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                const icon = btn.querySelector('i');
                if (icon) icon.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        });

        // Dark mode com persistência
        const themeBtn = document.getElementById('themeToggle');
        const applyTheme = function(mode) {
            document.body.classList.toggle('dark', mode === 'dark');
            if (themeBtn) {
                const icon = themeBtn.querySelector('i');
                if (icon) icon.className = mode === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        };
        const saved = localStorage.getItem('auth_theme') || 'light';
        applyTheme(saved);
        if (themeBtn) {
            themeBtn.addEventListener('click', function() {
                const next = document.body.classList.contains('dark') ? 'light' : 'dark';
                localStorage.setItem('auth_theme', next);
                applyTheme(next);
            });
        }

        // Parallax leve do branding
        const brand = document.querySelector('.auth-brand');
        if (brand && window.matchMedia('(min-width: 1025px)').matches) {
            let raf = null;
            brand.addEventListener('mousemove', function(e) {
                if (raf) cancelAnimationFrame(raf);
                raf = requestAnimationFrame(function() {
                    const rect = brand.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / rect.width - 0.5) * 12;
                    const y = ((e.clientY - rect.top) / rect.height - 0.5) * 12;
                    const top = brand.querySelector('.brand-top');
                    const mid = brand.querySelector('.brand-mid');
                    const bot = brand.querySelector('.brand-bottom');
                    if (top) top.style.transform = 'translate(' + (x * 0.3) + 'px, ' + (y * 0.3) + 'px)';
                    if (mid) mid.style.transform = 'translate(' + (x * 0.6) + 'px, ' + (y * 0.6) + 'px)';
                    if (bot) bot.style.transform = 'translate(' + (x * 0.2) + 'px, ' + (y * 0.2) + 'px)';
                });
            });
            brand.addEventListener('mouseleave', function() {
                brand.querySelectorAll('.brand-top, .brand-mid, .brand-bottom').forEach(function(el) {
                    el.style.transform = '';
                });
            });
        }

        // Auto-loading no submit
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('.btn-login');
                if (btn && form.checkValidity()) {
                    btn.classList.add('loading');
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
