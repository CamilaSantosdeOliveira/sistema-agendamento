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
            --primary: #2563eb;
            --primary-dark: #1e3a8a;
            --primary-darker: #0f172a;
            --accent: #60a5fa;
            --success: #10b981;
            --danger: #ef4444;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg: #f8fafc;
            --shadow-lg: 0 30px 60px -15px rgba(15, 23, 42, 0.25);
        }
        /* keep legacy vars for any leftover usage */
        :root {
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
            body { padding: 5px; }
        }

        /* ========================================
           REDESIGN PROFISSIONAL — SPLIT SCREEN
           ======================================== */
        body {
            background: #f8fafc !important;
            padding: 0 !important;
            min-height: 100vh;
            display: block !important;
            font-family: 'Inter', sans-serif;
            color: var(--text);
        }
        body::before { display: none !important; }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(440px, 520px);
        }

        /* ===== Lado esquerdo: branding ===== */
        .auth-brand {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 18% 22%, rgba(96, 165, 250, 0.35), transparent 45%),
                radial-gradient(circle at 80% 78%, rgba(99, 102, 241, 0.35), transparent 50%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #2563eb 100%);
            color: #ffffff;
            padding: 56px 64px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .auth-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px) 0 0 / 48px 48px,
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px) 0 0 / 48px 48px;
            mask-image: radial-gradient(circle at 50% 50%, black 50%, transparent 90%);
            -webkit-mask-image: radial-gradient(circle at 50% 50%, black 50%, transparent 90%);
            pointer-events: none;
        }
        .auth-brand::after {
            content: '';
            position: absolute;
            width: 340px; height: 340px;
            border-radius: 50%;
            top: -120px; right: -120px;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.35), transparent 60%);
            filter: blur(20px);
            animation: floatBlob 12s ease-in-out infinite;
        }
        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, 30px) scale(1.1); }
        }
        .brand-top, .brand-mid, .brand-bottom {
            position: relative;
            z-index: 2;
        }
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .brand-logo-icon {
            display: inline-grid;
            place-items: center;
            width: 44px; height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.08));
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            font-size: 1.2rem;
        }
        .brand-logo-text {
            font-size: 1.35rem;
            line-height: 1.2;
        }
        .brand-logo-text small {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.18em;
            opacity: 0.75;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .brand-headline {
            margin-top: 40px;
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            max-width: 480px;
        }
        .brand-headline span {
            background: linear-gradient(135deg, #ffffff 30%, #93c5fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-sub {
            margin-top: 18px;
            max-width: 440px;
            color: rgba(226, 232, 240, 0.85);
            font-size: 1rem;
            line-height: 1.6;
            font-weight: 400;
        }
        .brand-features {
            margin-top: 36px;
            display: grid;
            gap: 14px;
            max-width: 440px;
        }
        .brand-feature {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(10px);
            transition: transform 0.3s, background 0.3s;
        }
        .brand-feature:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.10);
        }
        .brand-feature-icon {
            display: grid;
            place-items: center;
            width: 38px; height: 38px;
            flex-shrink: 0;
            border-radius: 11px;
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.35), rgba(37, 99, 235, 0.35));
            color: #ffffff;
            font-size: 0.95rem;
        }
        .brand-feature-text strong {
            display: block;
            font-size: 0.92rem;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .brand-feature-text span {
            font-size: 0.82rem;
            color: rgba(226, 232, 240, 0.78);
            line-height: 1.5;
        }
        .brand-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            color: rgba(226, 232, 240, 0.7);
            font-size: 0.82rem;
        }
        .brand-bottom .meta {
            display: flex; align-items: center; gap: 12px;
        }
        .brand-bottom .dot-online {
            width: 8px; height: 8px; border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.55);
            animation: dotPulse 2s infinite;
        }
        @keyframes dotPulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.55); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* ===== Lado direito: formulário ===== */
        .auth-form-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px 48px;
            background: #ffffff;
            position: relative;
        }
        .login-container {
            background: transparent !important;
            backdrop-filter: none !important;
            box-shadow: none !important;
            border: none !important;
            max-width: 440px !important;
            width: 100%;
            border-radius: 0 !important;
            padding: 0;
        }
        .login-header {
            background: transparent !important;
            color: var(--text) !important;
            padding: 0 0 28px 0 !important;
            text-align: left !important;
            border-radius: 0 !important;
        }
        .login-header::before { display: none !important; }
        .login-logo {
            justify-content: flex-start !important;
            margin-bottom: 18px !important;
        }
        .login-logo i {
            -webkit-text-fill-color: initial !important;
            background: none !important;
            color: var(--primary) !important;
            font-size: 1.6rem !important;
            filter: none !important;
        }
        .login-logo h1 {
            -webkit-text-fill-color: initial !important;
            background: none !important;
            color: var(--text) !important;
            text-shadow: none !important;
            font-size: 1.4rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.02em !important;
        }
        .login-header p {
            color: var(--text-muted) !important;
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.12em !important;
            opacity: 1 !important;
        }
        .auth-welcome {
            margin-top: 6px;
            margin-bottom: 32px;
        }
        .auth-welcome h2 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text);
            margin-bottom: 6px;
        }
        .auth-welcome p {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.55;
        }
        .login-form { padding: 0 !important; }
        .form-group { margin-bottom: 16px !important; }
        .form-group label {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            color: var(--text) !important;
            margin-bottom: 8px !important;
            letter-spacing: 0.02em;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i.input-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrap .toggle-pass {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .input-wrap .toggle-pass:hover {
            background: rgba(37, 99, 235, 0.08);
            color: var(--primary);
        }
        .form-group input {
            width: 100% !important;
            padding: 14px 16px 14px 46px !important;
            border: 1.5px solid var(--border) !important;
            border-radius: 14px !important;
            font-size: 0.95rem !important;
            font-family: 'Inter', sans-serif !important;
            color: var(--text) !important;
            background: #f8fafc !important;
            backdrop-filter: none !important;
            transition: all 0.25s !important;
        }
        .form-group input::placeholder {
            color: #94a3b8;
        }
        .form-group input:focus {
            outline: none !important;
            border-color: var(--primary) !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12) !important;
            transform: none !important;
        }
        .form-group input:focus + .input-icon,
        .input-wrap:focus-within i.input-icon {
            color: var(--primary);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 2px 0 18px;
            font-size: 0.85rem;
        }
        .form-options label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-weight: 600;
            cursor: pointer;
            user-select: none;
        }
        .form-options input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--primary);
        }
        .form-options a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }
        .form-options a:hover { color: var(--primary-dark); text-decoration: underline; }

        .btn-login {
            width: 100% !important;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary)) !important;
            color: #ffffff !important;
            padding: 14px 18px !important;
            border: none !important;
            border-radius: 14px !important;
            font-size: 0.95rem !important;
            font-weight: 800 !important;
            font-family: 'Inter', sans-serif !important;
            cursor: pointer;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 10px;
            letter-spacing: 0.01em;
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.32) !important;
            transition: transform 0.25s, box-shadow 0.25s !important;
            margin-top: 4px !important;
        }
        .btn-login::before {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent) !important;
        }
        .btn-login:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 22px 44px rgba(37, 99, 235, 0.42) !important;
        }
        .btn-login:active {
            transform: translateY(0) !important;
        }
        .btn-login:disabled {
            opacity: 0.7;
            cursor: progress;
            transform: none !important;
        }
        .btn-login .btn-spinner {
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255, 255, 255, 0.4);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }
        .btn-login.loading .btn-spinner { display: inline-block; }
        .btn-login.loading .btn-label { display: none; }

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 24px 0 18px;
            color: var(--text-muted);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Demo accounts collapsible */
        .demo-accounts {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 0 16px !important;
            border: none !important;
            box-shadow: none !important;
            overflow: visible !important;
        }
        .demo-accounts::before { display: none !important; }
        .demo-toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border: 1.5px dashed var(--border);
            border-radius: 14px;
            background: #f8fafc;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
        }
        .demo-toggle:hover {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.04);
            color: var(--primary);
        }
        .demo-toggle .demo-chip {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .demo-toggle i.chev {
            transition: transform 0.3s;
            color: var(--text-muted);
        }
        .demo-accounts.open .demo-toggle i.chev {
            transform: rotate(180deg);
        }
        .demo-list {
            display: grid;
            gap: 8px;
            margin-top: 12px;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 0.35s ease, opacity 0.25s ease;
        }
        .demo-accounts.open .demo-list {
            max-height: 360px;
            opacity: 1;
        }
        .demo-account {
            background: #ffffff !important;
            padding: 12px 14px !important;
            border-radius: 12px !important;
            margin: 0 !important;
            border: 1px solid var(--border) !important;
            font-size: 0.82rem !important;
            display: flex !important;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s !important;
        }
        .demo-account:hover {
            border-color: var(--primary) !important;
            background: rgba(37, 99, 235, 0.04) !important;
            transform: none !important;
            outline: none !important;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
        }
        .demo-account .demo-avatar {
            width: 34px; height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.85rem;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }
        .demo-account.professor .demo-avatar { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .demo-account.aluno .demo-avatar { background: linear-gradient(135deg, #059669, #10b981); }
        .demo-account .demo-text { flex: 1; min-width: 0; }
        .demo-account .demo-role {
            display: block;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 2px;
        }
        .demo-account .demo-email {
            display: block;
            font-size: 0.82rem;
            color: var(--text);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .demo-account strong { color: var(--text) !important; }
        .demo-accounts small {
            display: block;
            margin-top: 10px;
            font-size: 0.75rem !important;
            color: var(--text-muted) !important;
            text-align: center;
        }
        .demo-accounts h4 { display: none; }
        .demo-accounts > p { display: none; }

        .login-links {
            margin-top: 24px !important;
            padding-top: 20px !important;
            border-top: 1px solid var(--border) !important;
            font-size: 0.88rem;
        }
        .login-links a {
            color: var(--primary) !important;
            font-weight: 700 !important;
            margin: 0 12px !important;
        }

        .alert {
            border-radius: 12px !important;
            padding: 14px 16px !important;
            font-size: 0.88rem !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: alertIn 0.3s ease-out;
        }
        @keyframes alertIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .alert-success {
            background: rgba(16, 185, 129, 0.10) !important;
            color: #047857 !important;
            border: 1px solid rgba(16, 185, 129, 0.25) !important;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.10) !important;
            color: #b91c1c !important;
            border: 1px solid rgba(239, 68, 68, 0.25) !important;
        }
        .error-message {
            font-size: 0.78rem !important;
            color: var(--danger) !important;
            font-weight: 600 !important;
            margin-top: 6px !important;
            min-height: 0 !important;
        }
        .form-group input:invalid:not(:placeholder-shown) { border-color: var(--danger) !important; }
        .form-group input:valid:not(:placeholder-shown) { border-color: var(--success) !important; }

        .loading { display: none !important; }

        /* ===== Animação de entrada ===== */
        .auth-form-wrap > .login-container {
            animation: formIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes formIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .auth-brand .brand-mid > * {
            opacity: 0;
            transform: translateY(20px);
            animation: brandIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .auth-brand .brand-headline { animation-delay: 0.05s; }
        .auth-brand .brand-sub { animation-delay: 0.18s; }
        .auth-brand .brand-features .brand-feature:nth-child(1) { animation-delay: 0.3s; }
        .auth-brand .brand-features .brand-feature:nth-child(2) { animation-delay: 0.4s; }
        .auth-brand .brand-features .brand-feature:nth-child(3) { animation-delay: 0.5s; }
        .auth-brand .brand-features .brand-feature {
            opacity: 0;
            transform: translateY(20px);
            animation: brandIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes brandIn {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== Responsividade ===== */
        @media (max-width: 1280px) {
            .auth-shell { grid-template-columns: minmax(0, 1fr) minmax(420px, 460px); }
            .auth-brand { padding: 40px 44px; }
            .brand-headline { font-size: 2rem; margin-top: 28px; }
            .auth-form-wrap { padding: 40px 36px; }
        }
        @media (max-width: 1024px) {
            .auth-shell { grid-template-columns: 1fr; }
            .auth-brand {
                padding: 40px 36px 32px;
                min-height: auto;
            }
            .brand-headline { font-size: 1.85rem; margin-top: 24px; max-width: 100%; }
            .brand-sub { max-width: 640px; }
            .brand-features {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                max-width: 100%;
                margin-top: 28px;
            }
            .brand-bottom { margin-top: 24px; }
            .auth-form-wrap { padding: 40px 36px 60px; }
            .login-container { max-width: 480px !important; }
        }
        @media (max-width: 640px) {
            .auth-brand { padding: 32px 24px 24px; }
            .brand-headline { font-size: 1.55rem; }
            .brand-features { display: none; }
            .brand-bottom { display: none; }
            .auth-form-wrap { padding: 32px 24px 48px; }
        }
        @media (max-width: 480px) {
            .auth-brand { padding: 28px 22px 22px; }
            .brand-logo-text { font-size: 1.15rem; }
            .brand-headline { font-size: 1.4rem; }
            .brand-sub { font-size: 0.9rem; }
            .auth-form-wrap { padding: 26px 20px; }
            .auth-welcome h2 { font-size: 1.45rem; }
        }

        /* ===== POLIMENTOS EXTRAS ===== */

        /* SVG da logo */
        .brand-logo-icon svg, .form-logo-mark svg {
            width: 28px; height: 28px;
            display: block;
        }
        .form-logo-mark {
            display: inline-grid;
            place-items: center;
            width: 36px; height: 36px;
            border-radius: 11px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(37, 99, 235, 0.02));
            border: 1px solid rgba(37, 99, 235, 0.15);
            margin-right: 4px;
        }
        .login-logo i { display: none !important; }
        .brand-logo-icon i { display: none !important; }

        /* Padrão de pontos animado no fundo do branding */
        .auth-brand .dot-grid {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.18) 1px, transparent 1.5px);
            background-size: 28px 28px;
            mask-image: linear-gradient(180deg, transparent 0%, black 30%, black 70%, transparent 100%);
            -webkit-mask-image: linear-gradient(180deg, transparent 0%, black 30%, black 70%, transparent 100%);
            animation: dotsDrift 22s linear infinite;
            pointer-events: none;
            z-index: 1;
        }
        @keyframes dotsDrift {
            from { background-position: 0 0; }
            to { background-position: 28px 28px; }
        }

        /* Linha animada embaixo dos inputs */
        .input-wrap::after {
            content: '';
            position: absolute;
            left: 50%;
            right: 50%;
            bottom: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            border-radius: 2px;
            transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1), right 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }
        .input-wrap:focus-within::after {
            left: 12px;
            right: 12px;
        }

        /* Botão de tema (modo escuro) */
        .theme-toggle {
            position: absolute;
            top: 28px;
            right: 28px;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.15);
            color: var(--primary);
            cursor: pointer;
            font-size: 0.95rem;
            display: grid;
            place-items: center;
            transition: all 0.3s;
        }
        .theme-toggle:hover {
            background: rgba(37, 99, 235, 0.14);
            transform: rotate(-12deg) scale(1.05);
        }

        /* ===== DARK MODE ===== */
        body.dark {
            background: #0b1220 !important;
            color: #e2e8f0;
        }
        body.dark .auth-form-wrap {
            background: #0f172a;
        }
        body.dark .auth-welcome h2,
        body.dark .login-logo h1,
        body.dark .form-group label,
        body.dark .demo-account strong,
        body.dark .demo-account .demo-email,
        body.dark .login-links {
            color: #f1f5f9 !important;
        }
        body.dark .auth-welcome p,
        body.dark .login-header p,
        body.dark .demo-account .demo-role,
        body.dark .form-options label,
        body.dark .demo-accounts small {
            color: #94a3b8 !important;
        }
        body.dark .form-logo-mark {
            background: rgba(37, 99, 235, 0.15);
            border-color: rgba(37, 99, 235, 0.3);
        }
        body.dark .form-group input {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        body.dark .form-group input::placeholder {
            color: #64748b;
        }
        body.dark .form-group input:focus {
            background: #1a2438 !important;
            border-color: var(--primary) !important;
        }
        body.dark .input-wrap i.input-icon,
        body.dark .input-wrap .toggle-pass {
            color: #64748b;
        }
        body.dark .demo-toggle {
            background: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        body.dark .demo-toggle:hover {
            background: rgba(37, 99, 235, 0.12);
            border-color: var(--primary);
            color: var(--accent);
        }
        body.dark .demo-account {
            background: #1e293b !important;
            border-color: #334155 !important;
        }
        body.dark .demo-account:hover {
            background: rgba(37, 99, 235, 0.10) !important;
            border-color: var(--primary) !important;
        }
        body.dark .login-links {
            border-top-color: #1e293b !important;
        }
        body.dark .theme-toggle {
            background: rgba(96, 165, 250, 0.15);
            border-color: rgba(96, 165, 250, 0.3);
            color: var(--accent);
        }
        body.dark .alert-error {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
        }
        body.dark .alert-success {
            background: rgba(16, 185, 129, 0.15) !important;
            color: #6ee7b7 !important;
        }

        /* Parallax leve do branding */
        .auth-brand .brand-mid,
        .auth-brand .brand-top,
        .auth-brand .brand-bottom {
            will-change: transform;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <!-- ===== Lado esquerdo: branding ===== -->
        <aside class="auth-brand">
            <span class="dot-grid" aria-hidden="true"></span>
            <div class="brand-top">
                <div class="brand-logo">
                    <span class="brand-logo-icon">
                        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <defs>
                                <linearGradient id="ecLogoGrad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#ffffff"/>
                                    <stop offset="1" stop-color="#93c5fd"/>
                                </linearGradient>
                            </defs>
                            <path d="M16 4 L28 10 L16 16 L4 10 Z" fill="url(#ecLogoGrad)" opacity="0.95"/>
                            <path d="M8 13 L8 19 C8 22 12 24 16 24 C20 24 24 22 24 19 L24 13" stroke="url(#ecLogoGrad)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <circle cx="28" cy="11" r="1.4" fill="#ffffff"/>
                            <path d="M28 12 L28 17" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <div class="brand-logo-text">
                        EduConnect
                        <small>Tech Platform</small>
                    </div>
                </div>
            </div>

            <div class="brand-mid">
                <h2 class="brand-headline">Educação que <span>conecta</span> pessoas e oportunidades.</h2>
                <p class="brand-sub">Plataforma completa para agendamento de aulas, gestão de cursos e acompanhamento de alunos — tudo em um só lugar.</p>

                <div class="brand-features">
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="brand-feature-text">
                            <strong>Agendamento inteligente</strong>
                            <span>Marque, edite e organize suas aulas em segundos.</span>
                        </div>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="brand-feature-text">
                            <strong>Acompanhamento real</strong>
                            <span>Dashboards completos para alunos, professores e admins.</span>
                        </div>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><i class="fas fa-shield-halved"></i></div>
                        <div class="brand-feature-text">
                            <strong>Seguro e confiável</strong>
                            <span>Autenticação robusta e proteção dos seus dados.</span>
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

        <!-- ===== Lado direito: formulário ===== -->
        <main class="auth-form-wrap">
            <div class="login-container">
                <div class="login-header">
                    <div class="login-logo">
                        <span class="form-logo-mark">
                            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <linearGradient id="ecLogoGrad2" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#1e3a8a"/>
                                        <stop offset="1" stop-color="#2563eb"/>
                                    </linearGradient>
                                </defs>
                                <path d="M16 4 L28 10 L16 16 L4 10 Z" fill="url(#ecLogoGrad2)"/>
                                <path d="M8 13 L8 19 C8 22 12 24 16 24 C20 24 24 22 24 19 L24 13" stroke="url(#ecLogoGrad2)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                <circle cx="28" cy="11" r="1.4" fill="#1e3a8a"/>
                                <path d="M28 12 L28 17" stroke="#1e3a8a" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h1>EduConnect</h1>
                    </div>
                    <p>Painel — Acesso seguro</p>
                </div>
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Alternar modo escuro" title="Alternar tema">
                    <i class="fas fa-moon"></i>
                </button>

                <div class="auth-welcome">
                    <h2>Bem-vindo de volta</h2>
                    <p>Entre com sua conta para continuar acessando o sistema.</p>
                </div>

                <div class="login-form">
                    <div id="alert"></div>

                    <!-- Contas demo (colapsável) -->
                    <div class="demo-accounts" id="demoAccounts">
                        <button type="button" class="demo-toggle" id="demoToggleBtn" aria-expanded="false">
                            <span><i class="fas fa-vial"></i> &nbsp;Ver contas de demonstração</span>
                            <span style="display:inline-flex; gap:8px; align-items:center;">
                                <span class="demo-chip"><i class="fas fa-bolt"></i> Teste</span>
                                <i class="fas fa-chevron-down chev"></i>
                            </span>
                        </button>
                        <div class="demo-list">
                            <div class="demo-account" onclick="preencherLogin('admin@educonnect.com', '123456')" role="button" tabindex="0" aria-label="Preencher login de administrador">
                                <div class="demo-avatar">A</div>
                                <div class="demo-text">
                                    <span class="demo-role">Administrador</span>
                                    <span class="demo-email">admin@educonnect.com</span>
                                </div>
                                <i class="fas fa-arrow-right" style="color: var(--text-muted); font-size: 0.75rem;"></i>
                            </div>
                            <div class="demo-account professor" onclick="preencherLogin('ricardo.silva@educonnect.com', '123456')" role="button" tabindex="0" aria-label="Preencher login de professor">
                                <div class="demo-avatar">P</div>
                                <div class="demo-text">
                                    <span class="demo-role">Professor</span>
                                    <span class="demo-email">ricardo.silva@educonnect.com</span>
                                </div>
                                <i class="fas fa-arrow-right" style="color: var(--text-muted); font-size: 0.75rem;"></i>
                            </div>
                            <div class="demo-account aluno" onclick="preencherLogin('camilacah7890@gmail.com', '123456')" role="button" tabindex="0" aria-label="Preencher login de aluno">
                                <div class="demo-avatar">E</div>
                                <div class="demo-text">
                                    <span class="demo-role">Aluno</span>
                                    <span class="demo-email">camilacah7890@gmail.com</span>
                                </div>
                                <i class="fas fa-arrow-right" style="color: var(--text-muted); font-size: 0.75rem;"></i>
                            </div>
                            <small>Senha para todas as contas: <strong>123456</strong></small>
                        </div>
                    </div>

                    <form id="loginForm" novalidate aria-label="Formulário de login">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope input-icon"></i>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    placeholder="seu@email.com"
                                    autocomplete="email"
                                    aria-required="true"
                                    aria-describedby="email-error">
                            </div>
                            <span id="email-error" class="error-message" role="alert" aria-live="polite"></span>
                        </div>

                        <div class="form-group">
                            <label for="senha">Senha</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock input-icon"></i>
                                <input
                                    type="password"
                                    id="senha"
                                    name="senha"
                                    required
                                    placeholder="Digite sua senha"
                                    autocomplete="current-password"
                                    aria-required="true"
                                    aria-describedby="senha-error">
                                <button type="button" class="toggle-pass" id="togglePass" aria-label="Mostrar/ocultar senha">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span id="senha-error" class="error-message" role="alert" aria-live="polite"></span>
                        </div>

                        <div class="form-options">
                            <label><input type="checkbox" id="lembrar"> Lembrar de mim</label>
                            <a href="recuperar_senha.php">Esqueci minha senha</a>
                        </div>

                        <button type="submit" class="btn-login" id="btnLogin" aria-label="Entrar no sistema">
                            <span class="btn-spinner"></span>
                            <span class="btn-label">Entrar no Sistema</span>
                            <i class="fas fa-arrow-right btn-label" style="font-size: 0.8rem;"></i>
                        </button>
                    </form>

                    <div class="login-links">
                        Ainda não tem conta?
                        <a href="cadastro.html" aria-label="Criar nova conta">Criar conta</a>
                        ·
                        <a href="index.html" aria-label="Voltar para página inicial">Voltar ao início</a>
                    </div>
                </div>
            </div>
        </main>
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
            const btn = document.getElementById('btnLogin');
            if (show) { btn.classList.add('loading'); btn.disabled = true; }
            else { btn.classList.remove('loading'); btn.disabled = false; }
        }

        // Toggle das contas demo + senha + dark mode + parallax
        document.addEventListener('DOMContentLoaded', function() {
            const demoBox = document.getElementById('demoAccounts');
            const demoBtn = document.getElementById('demoToggleBtn');
            if (demoBtn) {
                demoBtn.addEventListener('click', function() {
                    const open = demoBox.classList.toggle('open');
                    demoBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
            }
            const togglePass = document.getElementById('togglePass');
            const senhaInput = document.getElementById('senha');
            if (togglePass && senhaInput) {
                togglePass.addEventListener('click', function() {
                    const isPass = senhaInput.type === 'password';
                    senhaInput.type = isPass ? 'text' : 'password';
                    togglePass.querySelector('i').className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
                });
            }

            // Dark mode com persistência
            const themeBtn = document.getElementById('themeToggle');
            const applyTheme = (mode) => {
                document.body.classList.toggle('dark', mode === 'dark');
                if (themeBtn) {
                    themeBtn.querySelector('i').className = mode === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            };
            const saved = localStorage.getItem('auth_theme') || 'light';
            applyTheme(saved);
            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    const next = document.body.classList.contains('dark') ? 'light' : 'dark';
                    localStorage.setItem('auth_theme', next);
                    applyTheme(next);
                });
            }

            // Parallax leve do branding com o movimento do mouse
            const brand = document.querySelector('.auth-brand');
            if (brand && window.matchMedia('(min-width: 1025px)').matches) {
                let raf = null;
                brand.addEventListener('mousemove', (e) => {
                    if (raf) cancelAnimationFrame(raf);
                    raf = requestAnimationFrame(() => {
                        const rect = brand.getBoundingClientRect();
                        const x = ((e.clientX - rect.left) / rect.width - 0.5) * 12;
                        const y = ((e.clientY - rect.top) / rect.height - 0.5) * 12;
                        const top = brand.querySelector('.brand-top');
                        const mid = brand.querySelector('.brand-mid');
                        const bot = brand.querySelector('.brand-bottom');
                        if (top) top.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
                        if (mid) mid.style.transform = `translate(${x * 0.6}px, ${y * 0.6}px)`;
                        if (bot) bot.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
                    });
                });
                brand.addEventListener('mouseleave', () => {
                    const els = brand.querySelectorAll('.brand-top, .brand-mid, .brand-bottom');
                    els.forEach(el => el.style.transform = '');
                });
            }
        });

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
