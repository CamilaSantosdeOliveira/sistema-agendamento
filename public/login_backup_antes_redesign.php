<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect Tech - Sistema de Agendamento Educacional | Login</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="EduConnect Tech - Sistema completo de agendamento de aulas online. Gerencie cursos, professores, alunos e agendamentos de forma fácil e eficiente. Plataforma educacional moderna e intuitiva.">
    <meta name="keywords" content="EduConnect, agendamento de aulas, sistema educacional, cursos online, gestão educacional, plataforma educacional, agendamento online, educação, tecnologia educacional">
    <meta name="author" content="EduConnect Tech">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Portuguese">
    <meta name="revisit-after" content="7 days">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.educonnecttech.com.br/">
    <meta property="og:title" content="EduConnect Tech - Sistema de Agendamento Educacional">
    <meta property="og:description" content="Sistema completo de agendamento de aulas online. Gerencie cursos, professores, alunos e agendamentos de forma fácil e eficiente.">
    <meta property="og:image" content="https://www.educonnecttech.com.br/assets/img/logo.png">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="EduConnect Tech">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.educonnecttech.com.br/">
    <meta property="twitter:title" content="EduConnect Tech - Sistema de Agendamento Educacional">
    <meta property="twitter:description" content="Sistema completo de agendamento de aulas online. Gerencie cursos, professores, alunos e agendamentos de forma fácil e eficiente.">
    <meta property="twitter:image" content="https://www.educonnecttech.com.br/assets/img/logo.png">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://www.educonnecttech.com.br/login.php">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            /* Paleta Principal Refinada - Mais Profissional */
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --primary-accent: #6366f1;
            
            /* Cores Secundárias */
            --secondary-color: #64748b;
            --secondary-light: #94a3b8;
            --secondary-dark: #475569;
            
            /* Cores Funcionais Refinadas */
            --success-color: #059669;
            --success-light: #10b981;
            --warning-color: #d97706;
            --warning-light: #f59e0b;
            --danger-color: #dc2626;
            --danger-light: #ef4444;
            --info-color: #0891b2;
            --info-light: #06b6d4;
            
            /* Cores Neutras Profissionais */
            --light-color: #f8fafc;
            --light-secondary: #f1f5f9;
            --dark-color: #0f172a;
            --dark-secondary: #1e293b;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            
            /* Gradientes Profissionais */
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            --gradient-accent: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --gradient-success: linear-gradient(135deg, #059669 0%, #047857 100%);
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --border-radius: 4px;
            --border-radius-lg: 6px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            margin: 0;
            position: relative;
            overflow-y: auto;
            opacity: 0;
            animation: fadeIn 0.6s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px) saturate(180%);
            -webkit-backdrop-filter: blur(30px) saturate(180%);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-2xl), 0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: visible;
            width: 100%;
            max-width: 380px;
            position: relative;
            margin: 0;
            z-index: 10;
        }

        .login-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            padding: 15px 20px;
            text-align: center;
            color: white;
            border-radius: 6px 6px 0 0;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .login-logo i {
            font-size: 1.5rem;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .login-logo h1 {
            font-size: 1.2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            letter-spacing: -0.5px;
            background: linear-gradient(45deg, #ffffff, #f0f9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-header p {
            opacity: 0.95;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .login-form {
            padding: 18px 20px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.85rem;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
            font-size: 13px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
            background: white;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            padding: 11px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            margin-top: 3px;
            position: relative;
            overflow: hidden;
            display: block;
            visibility: visible;
            opacity: 1;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Estados de Foco Melhorados */
        .btn-login:focus-visible,
        .form-group input:focus-visible,
        .demo-account:focus-visible {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        /* Microinterações */
        .btn-login:active {
            transform: scale(0.98);
        }

        .demo-accounts {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 12px;
            border: 1px solid rgba(59, 130, 246, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .demo-accounts::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #1e40af, #3b82f6);
        }

        .demo-accounts h4 {
            color: var(--primary-color);
            margin-bottom: 8px;
            text-align: center;
            font-size: 0.8rem;
        }

        .demo-account {
            background: white;
            padding: 8px 10px;
            border-radius: var(--border-radius);
            margin: 5px 0;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid var(--border-color);
            font-size: 0.8rem;
        }

        .demo-account:hover,
        .demo-account:focus {
            border-color: var(--primary-color);
            background: rgba(59, 130, 246, 0.05);
            transform: translateY(-1px);
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        .demo-account strong {
            color: var(--primary-color);
        }

        .demo-accounts small {
            display: block;
            text-align: center;
            margin-top: 8px;
            color: var(--secondary-color);
            font-size: 0.75rem;
        }

        .login-links {
            text-align: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
        }

        .login-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin: 0 15px;
            transition: var(--transition);
        }

        .login-links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .alert {
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .error-message {
            display: block;
            color: var(--danger-color);
            font-size: 0.85rem;
            margin-top: 5px;
            min-height: 20px;
        }

        .form-group input:invalid:not(:placeholder-shown) {
            border-color: var(--danger-color);
        }

        .form-group input:valid:not(:placeholder-shown) {
            border-color: var(--success-color);
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            body {
                padding: 5px;
            }
            
            .login-container {
                margin: 0;
                border-radius: 6px;
                max-width: 100%;
            }
            
            .login-header {
                padding: 15px;
            }
            
            .login-form {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-shield-alt"></i>
                <h1>EduConnect Admin</h1>
            </div>
            <p>Painel Administrativo - Acesso Restrito</p>
        </div>
        
        <div class="login-form">
            <div id="alert"></div>
            <div id="loading" class="loading">
                <div class="spinner"></div>
                <p>Entrando...</p>
            </div>
            
            <!-- Contas Demo - Para demonstração -->
            <div class="demo-accounts" id="demoAccounts">
                <h4>Contas para Demonstração</h4>
                <p style="text-align: center; margin-bottom: 12px; font-size: 0.85rem; color: var(--secondary-color);">
                    Clique em uma conta para preencher automaticamente
                </p>
                <div class="demo-account" onclick="preencherLogin('admin@educonnect.com', '123456')" role="button" tabindex="0" aria-label="Preencher login de administrador">
                    <strong>👨‍💼 Administrador:</strong> admin@educonnect.com
                </div>
                <div class="demo-account" onclick="preencherLogin('ricardo.silva@educonnect.com', '123456')" role="button" tabindex="0" aria-label="Preencher login de professor">
                    <strong>👨‍🏫 Professor:</strong> ricardo.silva@educonnect.com
                </div>
                <div class="demo-account" onclick="preencherLogin('camilacah7890@gmail.com', '123456')" role="button" tabindex="0" aria-label="Preencher login de aluno">
                    <strong>👩‍🎓 Aluno:</strong> camilacah7890@gmail.com
                </div>
                <small style="display: block; text-align: center; margin-top: 10px; color: var(--secondary-color);">
                    Senha para todas as contas: <strong>123456</strong>
                </small>
            </div>
            
            <form id="loginForm" novalidate aria-label="Formulário de login">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="seu@email.com"
                        autocomplete="email"
                        aria-required="true"
                        aria-describedby="email-error">
                    <span id="email-error" class="error-message" role="alert" aria-live="polite"></span>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        required 
                        placeholder="Digite sua senha"
                        autocomplete="current-password"
                        aria-required="true"
                        aria-describedby="senha-error">
                    <span id="senha-error" class="error-message" role="alert" aria-live="polite"></span>
                </div>
                
                <button type="submit" class="btn-login" id="btnLogin" aria-label="Entrar no sistema">
                    Entrar no Sistema
                </button>
            </form>
            
            <div class="login-links">
                <a href="cadastro.html" aria-label="Criar nova conta">Criar conta</a>
                <a href="index.html" aria-label="Voltar para página inicial">Voltar ao início</a>
            </div>
        </div>
    </div>

    <script>
        function preencherLogin(email, senha) {
            document.getElementById('email').value = email;
            document.getElementById('senha').value = senha;
            document.getElementById('email').focus();
        }

        function showAlert(message, type = 'error') {
            const alertDiv = document.getElementById('alert');
            if (message) {
                alertDiv.innerHTML = `<div class="alert alert-${type}" role="alert" aria-live="assertive">${message}</div>`;
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                alertDiv.innerHTML = '';
            }
        }

        function showLoading(show = true) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
            document.getElementById('btnLogin').disabled = show;
        }

        // Validação de email melhorada
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        // Limpar mensagens de erro
        function limparErros() {
            document.getElementById('email-error').textContent = '';
            document.getElementById('senha-error').textContent = '';
        }

        // Validação em tempo real
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.trim();
            const errorSpan = document.getElementById('email-error');
            
            if (!email) {
                errorSpan.textContent = 'Email é obrigatório';
            } else if (!validarEmail(email)) {
                errorSpan.textContent = 'Email inválido';
            } else {
                errorSpan.textContent = '';
            }
        });

        document.getElementById('senha').addEventListener('blur', function() {
            const senha = this.value;
            const errorSpan = document.getElementById('senha-error');
            
            if (!senha) {
                errorSpan.textContent = 'Senha é obrigatória';
            } else if (senha.length < 6) {
                errorSpan.textContent = 'Senha deve ter no mínimo 6 caracteres';
            } else {
                errorSpan.textContent = '';
            }
        });

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value;
            
            // Validação
            let valido = true;
            limparErros();
            
            if (!email) {
                document.getElementById('email-error').textContent = 'Email é obrigatório';
                valido = false;
            } else if (!validarEmail(email)) {
                document.getElementById('email-error').textContent = 'Email inválido';
                valido = false;
            }
            
            if (!senha) {
                document.getElementById('senha-error').textContent = 'Senha é obrigatória';
                valido = false;
            } else if (senha.length < 6) {
                document.getElementById('senha-error').textContent = 'Senha deve ter no mínimo 6 caracteres';
                valido = false;
            }
            
            if (!valido) {
                showAlert('Por favor, corrija os erros no formulário.');
                return;
            }
            
            showLoading(true);
            showAlert('');
            limparErros();
            
            try {
                const response = await fetch('usuarios_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        acao: 'login', 
                        dados: { email, senha } 
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Erro na requisição');
                }
                
                const result = await response.json();
                
                if (result.sucesso) {
                    showAlert('Login realizado com sucesso! Redirecionando...', 'success');
                    
                    // Redirecionar por tipo de usuário
                    setTimeout(() => {
                        switch(result.usuario.tipo) {
                            case 'admin':
                                window.location.href = 'dashboard_final.php';
                                break;
                            case 'professor':
                                window.location.href = 'dashboard_professor.php';
                                break;
                            case 'aluno':
                                window.location.href = 'dashboard_aluno.php';
                                break;
                            default:
                                window.location.href = 'dashboard_final.php';
                        }
                    }, 1500);
                } else {
                    showAlert(result.mensagem || 'Credenciais inválidas. Tente novamente.');
                }
            } catch (error) {
                console.error('Erro:', error);
                showAlert('Erro de conexão. Verifique se o servidor está funcionando.');
            } finally {
                showLoading(false);
            }
        });

        // Permitir navegação por teclado nas contas demo
        document.querySelectorAll('.demo-account').forEach(account => {
            account.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    </script>
</body>
</html>


