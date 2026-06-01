<?php
session_start();
$mensagem = '';
$tipo = '';
$enviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Por favor, informe um e-mail válido.';
        $tipo = 'error';
    } else {
        // Simulação: em produção enviaria e-mail real
        $mensagem = 'Se o e-mail estiver cadastrado, você receberá as instruções em alguns minutos.';
        $tipo = 'success';
        $enviado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha — EduConnect Tech</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth.css?v=1">
</head>
<body>
    <div class="auth-shell">
        <aside class="auth-brand">
            <span class="dot-grid" aria-hidden="true"></span>
            <div class="brand-top">
                <a href="login.php" class="brand-logo" style="text-decoration: none; color: inherit;">
                    <span class="brand-logo-icon">
                        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <defs>
                                <linearGradient id="ecLogoGradR" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#ffffff"/>
                                    <stop offset="1" stop-color="#93c5fd"/>
                                </linearGradient>
                            </defs>
                            <path d="M16 4 L28 10 L16 16 L4 10 Z" fill="url(#ecLogoGradR)"/>
                            <path d="M8 13 L8 19 C8 22 12 24 16 24 C20 24 24 22 24 19 L24 13" stroke="url(#ecLogoGradR)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <circle cx="28" cy="11" r="1.4" fill="#ffffff"/>
                            <path d="M28 12 L28 17" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <div class="brand-logo-text">
                        EduConnect
                        <small>Tech Platform</small>
                    </div>
                </a>
            </div>
            <div class="brand-mid">
                <h2 class="brand-headline">Esqueceu a senha? <span>Sem problemas.</span></h2>
                <p class="brand-sub">Informe seu e-mail e nós enviaremos um link seguro para você criar uma nova senha em segundos.</p>
                <div class="brand-features">
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class="fas fa-envelope-open-text"></i></div>
                        <div class="brand-feature-text">
                            <strong>Link enviado por e-mail</strong>
                            <span>Você recebe o link com instruções claras na sua caixa de entrada.</span>
                        </div>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class="fas fa-lock"></i></div>
                        <div class="brand-feature-text">
                            <strong>Seguro e criptografado</strong>
                            <span>O link expira em 30 minutos e só funciona uma vez.</span>
                        </div>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class="fas fa-headset"></i></div>
                        <div class="brand-feature-text">
                            <strong>Precisa de ajuda?</strong>
                            <span>Nosso suporte responde rapidinho pelo chat ou e-mail.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="brand-bottom">
                <div class="meta">
                    <span class="dot-online"></span>
                    <span>Sistema online · v3.0</span>
                </div>
                <span>&copy; <?php echo date('Y'); ?> EduConnect Tech</span>
            </div>
        </aside>

        <main class="auth-form-wrap">
            <div class="login-container">
                <div class="login-header">
                    <div class="login-logo">
                        <span class="form-logo-mark">
                            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <linearGradient id="ecLogoGradR2" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#1e3a8a"/>
                                        <stop offset="1" stop-color="#2563eb"/>
                                    </linearGradient>
                                </defs>
                                <path d="M16 4 L28 10 L16 16 L4 10 Z" fill="url(#ecLogoGradR2)"/>
                                <path d="M8 13 L8 19 C8 22 12 24 16 24 C20 24 24 22 24 19 L24 13" stroke="url(#ecLogoGradR2)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                <circle cx="28" cy="11" r="1.4" fill="#1e3a8a"/>
                                <path d="M28 12 L28 17" stroke="#1e3a8a" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h1>EduConnect</h1>
                    </div>
                    <p>Recuperação de senha</p>
                </div>
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Alternar modo escuro" title="Alternar tema">
                    <i class="fas fa-moon"></i>
                </button>

                <div class="auth-welcome">
                    <h2>Vamos te ajudar a entrar</h2>
                    <p>Digite o e-mail vinculado à sua conta. Enviaremos um link para você redefinir sua senha.</p>
                </div>

                <div class="login-form">
                    <?php if ($mensagem): ?>
                        <div class="alert alert-<?php echo $tipo; ?>" role="alert">
                            <i class="fas fa-<?php echo $tipo === 'success' ? 'circle-check' : 'circle-exclamation'; ?>"></i>
                            <span><?php echo htmlspecialchars($mensagem); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!$enviado): ?>
                    <form method="POST" novalidate aria-label="Formulário de recuperação de senha">
                        <div class="form-group">
                            <label for="email">E-mail da conta</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope input-icon"></i>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    placeholder="seu@email.com"
                                    autocomplete="email"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    aria-required="true">
                            </div>
                        </div>

                        <button type="submit" class="btn-login" id="btnSubmit" aria-label="Enviar link de recuperação">
                            <span class="btn-spinner"></span>
                            <span class="btn-label">Enviar link de recuperação</span>
                            <i class="fas fa-paper-plane btn-label" style="font-size: 0.85rem;"></i>
                        </button>
                    </form>
                    <?php else: ?>
                    <div style="text-align:center; padding: 8px 0 20px;">
                        <div style="width: 88px; height: 88px; margin: 0 auto 18px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); display: grid; place-items: center; font-size: 2rem; color: #fff; box-shadow: 0 18px 40px rgba(16, 185, 129, 0.35);">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text); margin-bottom: 8px;">Verifique sua caixa de entrada</h3>
                        <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.55;">Se o e-mail informado estiver cadastrado, você receberá um link em poucos minutos.<br>Não esqueça de olhar o spam.</p>
                        <a href="login.php" class="btn-login" style="margin-top: 24px; text-decoration: none;">
                            <i class="fas fa-arrow-left btn-label" style="font-size: 0.85rem;"></i>
                            <span class="btn-label">Voltar para o login</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <div class="login-links">
                        Lembrou da senha?
                        <a href="login.php" aria-label="Voltar para login">Fazer login</a>
                        ·
                        <a href="cadastro.html" aria-label="Criar conta nova">Criar conta</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/auth.js?v=1"></script>
    <script>
        document.getElementById('btnSubmit')?.addEventListener('click', function(e) {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                this.classList.add('loading');
            }
        });
    </script>
</body>
</html>
