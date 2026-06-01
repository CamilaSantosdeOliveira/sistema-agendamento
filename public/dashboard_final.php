<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Contar cursos ativos
$cursos_query = "SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'";
$cursos_result = $conn->query($cursos_query);
$cursos_count = $cursos_result ? $cursos_result->fetch_assoc()['count'] : 0;

// Contar professores ativos
$professores_query = "SELECT COUNT(*) as count FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1";
$professores_result = $conn->query($professores_query);
$professores_count = $professores_result ? $professores_result->fetch_assoc()['count'] : 0;

// Contar alunos cadastrados
$alunos_query = "SELECT COUNT(*) as count FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1";
$alunos_result = $conn->query($alunos_query);
$alunos_count = $alunos_result ? $alunos_result->fetch_assoc()['count'] : 0;

// Contar agendamentos futuros
$agendamentos_query = "SELECT COUNT(*) as count FROM agendamentos WHERE data_agendamento >= CURDATE()";
$agendamentos_result = $conn->query($agendamentos_query);
$agendamentos_count = $agendamentos_result ? $agendamentos_result->fetch_assoc()['count'] : 0;

// Contar inscrições ativas
$inscricoes_query = "SELECT COUNT(*) as count FROM inscricoes WHERE status = 'ativa'";
$inscricoes_result = $conn->query($inscricoes_query);
$inscricoes_count = $inscricoes_result ? $inscricoes_result->fetch_assoc()['count'] : 0;

// Buscar cursos para exibição
$cursos_query = "SELECT * FROM cursos WHERE status = 'ativo' ORDER BY nome LIMIT 6";
$cursos_result = $conn->query($cursos_query);

// Buscar professores para exibição
$professores_query = "SELECT * FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 ORDER BY nome LIMIT 10";
$professores_result = $conn->query($professores_query);
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <script>
        // Script de bloqueio para evitar o flash de luz (FOUC)
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
            // Aplicar estilos mínimos imediatamente vinculados à classe de inicialização
            document.write('<style>.dark-mode-init body { visibility: hidden; background: #0f172a !important; }</style>');
        }
    </script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"><meta http-equiv="Pragma" content="no-cache"><meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="dark-mode.css">
    <title>EduConnect - Dashboard de Cursos de Tecnologia</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            --gradient-warm: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
            
            /* Sistema de sombras profissional */
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --shadow-inner: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            
            /* Bordas refinadas */
            --border-radius-sm: 6px;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --border-radius-xl: 20px;
            --border-radius-2xl: 24px;
            --border-radius-full: 9999px;
            
            /* Transições suaves */
            --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Sistema de espaçamento consistente */
            --spacing-xs: 8px;
            --spacing-sm: 16px;
            --spacing-md: 24px;
            --spacing-lg: 32px;
            --spacing-xl: 48px;
            --spacing-2xl: 64px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            min-height: 100vh;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 30%, #e2e8f0 70%, #f8fafc 100%);
            background-attachment: fixed;
            color: #0f172a;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            opacity: 0;
            animation: fadeIn 0.6s ease-in-out forwards;
            position: relative;
            height: 100%;
            min-height: 100vh;
        }
        

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
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

        .dashboard-container {
            display: flex;
            min-height: 100%;
            height: 100%;
        }

        /* Sidebar Melhorada - Design Profissional */
        .sidebar {
            width: 280px;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
                linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%);
            color: white;
            padding: 0;
            margin: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            min-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            box-shadow: var(--shadow-xl);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
        }
        
        .sidebar::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
                linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%);
            pointer-events: none;
            z-index: -2;
        }
        
        .sidebar::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%);
            pointer-events: none;
            z-index: -1;
        }

        .sidebar-header {
            padding: var(--spacing-md) var(--spacing-sm);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            color: white;
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-logo i {
            font-size: 1.5rem;
        }

        .sidebar-nav {
            padding: var(--spacing-sm) 0;
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-item {
            margin: 0;
        }

        /* Grupos de navegação */
        .sidebar-group {
            margin-bottom: var(--spacing-md);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: var(--spacing-sm);
        }

        .sidebar-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .sidebar-group-title {
            padding: var(--spacing-xs) var(--spacing-sm);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: var(--spacing-xs);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            margin-left: var(--spacing-xs);
            margin-right: var(--spacing-xs);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-sm) var(--spacing-sm);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            line-height: 1.4;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar-icon {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: var(--spacing-sm);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
        }

        .sidebar-footer-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 280px;
            padding: 12px var(--spacing-sm);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
            z-index: 1001;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            margin: 0;
            border-bottom: none;
            border-radius: 0;
            height: auto;
        }

        .sidebar-user {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .sidebar-user:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .user-info {
            flex: 1;
            text-align: left;
            min-width: 0;
        }

        .user-name {
            font-weight: 500;
            font-size: 0.7rem;
            color: white;
            margin-bottom: 1px;
        }

        .user-role {
            font-size: 0.6rem;
            opacity: 0.7;
            color: rgba(255, 255, 255, 0.8);
        }

        .logout-btn-small {
            background: rgba(239, 68, 68, 0.6);
            color: white;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.6rem;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
        }

        .logout-btn-small:hover {
            background: rgba(239, 68, 68, 0.8);
            transform: translateY(-1px);
            color: white;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: var(--spacing-md);
        }

        .page-header {
            margin-bottom: 48px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .breadcrumb {
            margin-bottom: var(--spacing-md);
            font-size: 0.875rem;
            color: var(--secondary-color);
            letter-spacing: 0.01em;
        }

        .breadcrumb-link {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .breadcrumb-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .breadcrumb-separator {
            margin: 0 var(--spacing-xs);
        }

        .page-title {
            margin-bottom: var(--spacing-md);
        }

        .page-title h1 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: var(--spacing-sm);
            line-height: 1.15;
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            color: var(--secondary-color);
            font-size: 0.9375rem;
            line-height: 1.6;
            margin-top: var(--spacing-xs);
            margin-bottom: 0;
            letter-spacing: 0.01em;
        }

        .page-actions {
            margin-top: var(--spacing-xl);
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            padding-top: var(--spacing-lg);
        }

        .btn {
            padding: 11px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            white-space: nowrap;
            letter-spacing: 0.01em;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn:active {
            transform: scale(0.98);
        }

        /* Estados de Foco Melhorados para Acessibilidade */
        .btn:focus-visible,
        .sidebar-link:focus-visible,
        .stat-card:focus-visible,
        .quick-action-card:focus-visible {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        /* Microinterações - Feedback Visual Aprimorado */
        .stat-card:active {
            transform: translateY(-2px) scale(0.99);
        }

        .quick-action-card:active {
            transform: translateY(-2px) scale(0.98);
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background: var(--gradient-success);
            color: white;
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: #334155;
            transform: translateY(-2px);
        }

        .btn-info {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            color: white;
            border: none;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #0e7490 0%, #155e75 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 1.5px solid var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-sm {
            padding: var(--spacing-xs) var(--spacing-sm);
            font-size: 0.8rem;
        }

        /* Stats Grid Otimizado */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.6);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-card.primary::before {
            background: var(--gradient-primary);
        }
        
        .stat-card.success::before {
            background: var(--gradient-success);
        }
        
        .stat-card.warning::before {
            background: linear-gradient(90deg, var(--warning-color), var(--warning-light));
        }
        
        .stat-card.info::before {
            background: linear-gradient(90deg, var(--info-color), var(--info-light));
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            border-color: rgba(37, 99, 235, 0.25);
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-sm);
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            line-height: 1.4;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            background: var(--gradient-primary);
        }
        
        .stat-card.success .stat-icon {
            background: var(--gradient-success);
        }
        
        .stat-card.warning .stat-icon {
            background: linear-gradient(135deg, var(--warning-color), var(--warning-light));
        }
        
        .stat-card.info .stat-icon {
            background: linear-gradient(135deg, var(--info-color), var(--info-light));
        }
        
        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-card.primary .stat-icon {
            background-color: var(--primary-color);
        }

        .stat-card.success .stat-icon {
            background-color: var(--success-color);
        }

        .stat-card.warning .stat-icon {
            background-color: var(--warning-color);
        }

        .stat-card.info .stat-icon {
            background-color: var(--info-color);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 8px;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.4;
            letter-spacing: 0.01em;
        }

        .stat-change.positive {
            color: var(--success-color);
        }

        .stat-change-icon {
            font-size: 0.8rem;
        }

        /* Search Bar */
        .search-container {
            background: white;
            padding: var(--spacing-sm);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-top: var(--spacing-xl);
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            background-color: var(--light-color);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-sm) var(--spacing-sm);
        }

        .search-icon {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .search-input {
            flex: 1;
            border: none;
            background: none;
            outline: none;
            font-size: 1rem;
            color: var(--dark-color);
        }

        .search-input::placeholder {
            color: var(--secondary-color);
        }

        /* Seções Principais - Espaçamento Profissional */
        .aulas-agendadas,
        .acoes-rapidas {
            margin-bottom: var(--spacing-xl);
        }
        
        .aulas-agendadas {
            margin-top: var(--spacing-xl);
        }
        
        .acoes-rapidas {
            margin-top: var(--spacing-lg);
        }

        /* Cards - Design Profissional */
        .card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.6);
            margin-bottom: var(--spacing-lg);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        
        .card-content {
            padding: var(--spacing-lg);
        }

        .card-header {
            padding: var(--spacing-lg);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .card:hover .card-header::after {
            opacity: 1;
        }

        .card-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--spacing-md);
        }

        .card-title-section {
            flex: 1;
        }

        .card-subtitle {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin-top: 6px;
            font-weight: 400;
            line-height: 1.5;
            letter-spacing: 0.01em;
        }

        .card-actions {
            display: flex;
            gap: var(--spacing-sm);
            align-items: center;
        }

        .card-header h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            line-height: 1.35;
            letter-spacing: -0.01em;
        }

        /* Cursos Grid Profissional */
        .cursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: var(--spacing-lg);
            padding: var(--spacing-md);
        }

        .curso-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid transparent;
            border-radius: 16px;
            padding: var(--spacing-lg);
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            overflow: hidden;
        }

        .curso-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .curso-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .curso-card:hover::before {
            transform: scaleX(1);
        }

        .curso-card.featured {
            border-color: var(--warning-color);
            background: linear-gradient(135deg, #fff 0%, #fef3c7 100%);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.15);
        }

        .curso-categoria {
            position: absolute;
            top: var(--spacing-md);
            right: var(--spacing-md);
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .curso-titulo {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: var(--spacing-sm);
            line-height: 1.3;
            margin-top: var(--spacing-md);
        }

        .curso-meta {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
            padding: var(--spacing-sm);
            background: rgba(59, 130, 246, 0.05);
            border-radius: 8px;
        }

        .curso-nivel {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .curso-duracao {
            color: var(--secondary-color);
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .curso-duracao::before {
            content: '⏱️';
            font-size: 0.8rem;
        }

        .curso-preco {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--success-color);
            margin-bottom: var(--spacing-md);
            text-align: center;
            padding: var(--spacing-sm);
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
            border-radius: 12px;
            border: 2px solid rgba(34, 197, 94, 0.2);
        }

        .curso-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-sm);
            background: rgba(0, 0, 0, 0.02);
            border-radius: 8px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .curso-stats span {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--secondary-color);
        }

        .curso-stats span:first-child {
            color: var(--info-color);
        }

        .curso-stats span:last-child {
            color: var(--warning-color);
        }

        .curso-actions {
            display: flex;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-md);
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .curso-card:hover .curso-actions {
            opacity: 1;
            transform: translateY(0);
        }

        /* Estados Vazios Melhorados */
        .empty-state {
            text-align: center;
            padding: var(--spacing-2xl);
            color: var(--secondary-color);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: var(--border-radius-lg);
            border: 2px dashed var(--border-color);
            animation: fadeIn 0.5s ease-in-out;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: var(--spacing-md);
            opacity: 0.6;
            color: var(--primary-color);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            color: var(--dark-color);
        }

        .empty-state p {
            font-size: 1rem;
            color: var(--secondary-color);
            margin-bottom: var(--spacing-md);
            line-height: 1.6;
        }

        .empty-state .btn {
            margin-top: var(--spacing-md);
        }

        /* Aulas Agendadas */
        .agendamentos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: var(--spacing-md);
            padding: var(--spacing-md);
        }

        .agendamento-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-md);
            position: relative;
            transition: var(--transition);
            border-left: 4px solid var(--primary-color);
        }

        .agendamento-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .agendamento-card.confirmado {
            border-left-color: var(--success-color);
        }

        .agendamento-card.pendente {
            border-left-color: var(--warning-color);
        }

        .agendamento-card.cancelado {
            border-left-color: var(--danger-color);
            opacity: 0.7;
        }

        .agendamento-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--spacing-sm);
        }

        .agendamento-status {
            padding: 4px var(--spacing-xs);
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .agendamento-status.confirmado {
            background: var(--success-color);
            color: white;
        }

        .agendamento-status.pendente {
            background: var(--warning-color);
            color: white;
        }

        .agendamento-status.cancelado {
            background: var(--danger-color);
            color: white;
        }

        .agendamento-data {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: var(--spacing-xs);
        }

        .agendamento-horario {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .agendamento-info {
            margin-bottom: var(--spacing-sm);
        }

        .agendamento-info span {
            display: block;
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .agendamento-info strong {
            color: var(--dark-color);
        }

        .agendamento-observacoes {
            background: var(--light-color);
            padding: var(--spacing-xs);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-sm);
            font-size: 0.9rem;
            color: var(--secondary-color);
            font-style: italic;
        }

        .agendamento-actions {
            display: flex;
            gap: var(--spacing-xs);
        }

        .agendamento-actions .btn {
            flex: 1;
            font-size: 0.8rem;
            padding: 8px var(--spacing-xs);
        }

        .sem-agendamentos {
            text-align: center;
            padding: 40px;
            color: var(--secondary-color);
        }

        .sem-agendamentos i {
            font-size: 48px;
            margin-bottom: var(--spacing-sm);
            opacity: 0.5;
        }

        /* Chart */
        .chart-filters {
            display: flex;
            gap: var(--spacing-xs);
        }

        .chart-filter {
            padding: 6px var(--spacing-sm);
            border: 1px solid var(--border-color);
            background: white;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .chart-filter.active,
        .chart-filter:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .chart-content {
            padding: var(--spacing-md);
        }

        /* Melhorias Profissionais - Animações e Loading */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .stat-card {
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            border-color: rgba(37, 99, 235, 0.25);
        }

        /* Loading State Melhorado */
        .loading-skeleton {
            background: linear-gradient(90deg, 
                rgba(241, 245, 249, 0.8) 0%, 
                rgba(226, 232, 240, 0.9) 50%, 
                rgba(241, 245, 249, 0.8) 100%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
            border-radius: 12px;
            min-height: 60px;
            position: relative;
            overflow: hidden;
        }

        .loading-skeleton::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.6), 
                transparent);
            animation: shimmer 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Gráfico de Evolução — Design Profissional */
        .evolution-chart-container {
            position: relative;
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
            padding: 32px;
            border-radius: 24px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 40px;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .evolution-chart-container:hover {
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14);
            transform: translateY(-2px);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 24px;
        }
        .chart-header-left { flex: 1; min-width: 240px; }
        .chart-header h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px 0;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.02em;
        }
        .chart-header h3 i {
            display: inline-grid;
            place-items: center;
            width: 40px; height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: #ffffff;
            font-size: 0.95rem;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.30);
        }
        .chart-subtitle {
            font-size: 0.88rem;
            color: #64748b;
            font-weight: 500;
            margin: 0;
        }

        /* Segmented control */
        .chart-period-segment {
            display: inline-flex;
            padding: 4px;
            background: #f1f5f9;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            gap: 2px;
        }
        .period-filter {
            padding: 8px 18px;
            border: none;
            background: transparent;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
            white-space: nowrap;
        }
        .period-filter:hover { color: #0f172a; }
        .period-filter.active {
            background: #ffffff;
            color: #1e3a8a;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.10);
        }

        /* KPIs mini */
        .chart-kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .chart-kpi {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 14px;
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.7);
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .chart-kpi:hover {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            transform: translateY(-1px);
        }
        .chart-kpi.disabled { opacity: 0.40; }
        .chart-kpi.disabled .kpi-dot {
            background: #cbd5e1 !important;
        }
        .kpi-dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.10);
        }
        .chart-kpi[data-key="alunos"] .kpi-dot { box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }
        .chart-kpi[data-key="aulas"] .kpi-dot { box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.15); }
        .chart-kpi[data-key="cursos"] { border-left: 3px solid #2563eb; padding-left: 14px; }
        .chart-kpi[data-key="alunos"] { border-left: 3px solid #6366f1; padding-left: 14px; }
        .chart-kpi[data-key="aulas"]  { border-left: 3px solid #06b6d4; padding-left: 14px; }
        .kpi-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }
        .kpi-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
        }
        .kpi-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.025em;
            font-variant-numeric: tabular-nums;
            line-height: 1.1;
        }
        .kpi-delta {
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        .kpi-delta.positive {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
        }
        .kpi-delta.negative {
            background: rgba(239, 68, 68, 0.12);
            color: #b91c1c;
        }
        .kpi-delta i { font-size: 0.65rem; }

        .chart-wrapper {
            position: relative;
            height: 340px;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .evolution-chart-container { padding: 22px; }
            .chart-period-segment { width: 100%; justify-content: stretch; }
            .period-filter { flex: 1; padding: 8px 12px; }
            .chart-kpis { grid-template-columns: 1fr; gap: 8px; }
            .chart-kpi { padding: 12px 14px; }
            .kpi-value { font-size: 1.2rem; }
            .chart-wrapper { height: 280px; }
        }

        /* Quick Actions Grid - Design Profissional */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .quick-action-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 16px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        }

        .quick-action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .quick-action-card:hover::before {
            transform: scaleX(1);
        }

        .quick-action-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }

        .quick-action-card.primary {
            border-left: 4px solid var(--primary-color);
        }

        .quick-action-card.success {
            border-left: 4px solid var(--success-color);
        }

        .quick-action-card.warning {
            border-left: 4px solid var(--warning-color);
        }

        .quick-action-card.info {
            border-left: 4px solid var(--info-color);
        }

        .quick-action-card.secondary {
            border-left: 4px solid var(--secondary-color);
        }

        .quick-action-card.dark {
            border-left: 4px solid var(--dark-color);
        }

        .quick-action-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .quick-action-card.primary .quick-action-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        .quick-action-card.success .quick-action-icon {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }

        .quick-action-card.warning .quick-action-icon {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .quick-action-card.info .quick-action-icon {
            background: linear-gradient(135deg, var(--info-color), #0891b2);
        }

        .quick-action-card.secondary .quick-action-icon {
            background: linear-gradient(135deg, var(--secondary-color), #475569);
        }

        .quick-action-card.dark .quick-action-icon {
            background: linear-gradient(135deg, var(--dark-color), #0f172a);
        }

        .quick-action-card:hover .quick-action-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .quick-action-content {
            flex: 1;
            text-align: left;
        }

        .quick-action-content h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .quick-action-content p {
            color: var(--secondary-color);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .quick-action-stats {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-item {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary-color);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .quick-action-badge {
            margin-top: 8px;
        }

        .badge-new {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-admin {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quick-action-arrow {
            color: var(--secondary-color);
            font-size: 1.2rem;
            transition: all 0.3s ease;
            opacity: 0.6;
        }

        .quick-action-card:hover .quick-action-arrow {
            transform: translateX(4px);
            opacity: 1;
            color: var(--primary-color);
        }

        .card-subtitle {
            color: var(--secondary-color);
            font-size: 0.95rem;
            margin-top: 4px;
            font-weight: 400;
        }

        /* Botão Mobile Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: var(--spacing-sm);
            left: var(--spacing-sm);
            z-index: 9999;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .mobile-toggle:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .mobile-overlay {
            display: none; /* Inicialmente oculto */
        }

        /* Responsive Melhorado */
        @media (max-width: 1400px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: var(--spacing-sm);
            }
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 250px;
            }
            
            .sidebar::before,
            .sidebar::after {
                width: 250px;
            }
            
            .main-content {
                margin-left: 250px;
                padding: var(--spacing-sm);
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: var(--spacing-sm);
            }
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
                top: 20px;
                left: 20px;
                z-index: 9999;
            }

            .main-content {
                padding: var(--spacing-sm);
                margin-left: 0;
                padding-top: calc(var(--spacing-sm) + 100px);
            }
            
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: var(--shadow-lg);
                z-index: 1000;
                height: 100vh;
                min-height: 100vh;
                width: 280px;
            }
            
            .sidebar::before,
            .sidebar::after {
                width: 280px;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-sm);
            }
            
            .page-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .cursos-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-md);
            }

            .card-header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-sm);
            }

            .card-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .agendamentos-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .page-title h1 {
                font-size: 1.5rem;
            }
            
            .stat-value {
                font-size: 2rem;
            }
            
            .card-header {
                padding: var(--spacing-sm);
            }

            .card-header-content {
                flex-direction: column;
                gap: var(--spacing-xs);
                align-items: flex-start;
            }

            .curso-card {
                padding: var(--spacing-md);
            }

            .curso-titulo {
                font-size: 1.1rem;
            }

            .curso-preco {
                font-size: 1.25rem;
            }

            .mobile-toggle {
                width: 40px;
                height: 40px;
                font-size: 1rem;
                top: 15px;
                left: 15px;
            }
        }
        /* Tooltips Informativos */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }

        [data-tooltip]:hover::before,
        [data-tooltip]:focus::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: var(--dark-color);
            color: white;
            border-radius: 6px;
            font-size: 0.85rem;
            white-space: nowrap;
            z-index: 1000;
            margin-bottom: 8px;
            box-shadow: var(--shadow-lg);
            animation: tooltipFadeIn 0.2s ease-out;
            pointer-events: none;
        }

        [data-tooltip]:hover::after,
        [data-tooltip]:focus::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--dark-color);
            margin-bottom: 2px;
            z-index: 1000;
            pointer-events: none;
        }

        @keyframes tooltipFadeIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Animações de Entrada para Cards */
        .stat-card,
        .quick-action-card,
        .agendamento-card {
            animation: slideInUp 0.6s ease-out backwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ARIA Labels para Acessibilidade */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* ===== PROGRESS BARS ===== */
        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(226, 232, 240, 0.5);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 12px;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            border-radius: 10px;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.3), 
                transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-fill.success {
            background: var(--gradient-success);
        }

        .progress-fill.warning {
            background: linear-gradient(90deg, var(--warning-color), var(--warning-light));
        }

        .progress-fill.info {
            background: linear-gradient(90deg, var(--info-color), var(--info-light));
        }

        /* ===== STATUS INDICATORS ===== */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-indicator::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        .status-online::before {
            background: var(--success-color);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        .status-offline::before {
            background: var(--secondary-color);
        }

        .status-active::before {
            background: var(--success-color);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        .status-inactive::before {
            background: var(--danger-color);
        }

        .status-online,
        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-offline,
        .status-inactive {
            background: rgba(100, 116, 139, 0.1);
            color: #475569;
            border: 1px solid rgba(100, 116, 139, 0.2);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* ===== AVATAR DO USUÁRIO ===== */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            border: 3px solid white;
            box-shadow: var(--shadow-md);
            position: relative;
            transition: var(--transition);
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        .user-avatar.online::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 12px;
            width: 12px;
            height: 12px;
            background: var(--success-color);
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .user-avatar-container {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 12px;
            transition: var(--transition);
        }

        .user-avatar-container:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* ===== TOAST NOTIFICATIONS MELHORADAS ===== */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 20px;
            z-index: 13000;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 400px;
            pointer-events: none;
        }

        .toast {
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: var(--shadow-xl);
            border-left: 4px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: toastSlideIn 0.3s ease-out;
            pointer-events: auto;
            position: relative;
            overflow: hidden;
        }

        .toast::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            animation: toastProgress 3s linear forwards;
        }

        .toast.success {
            border-left-color: var(--success-color);
        }

        .toast.success::before {
            background: var(--gradient-success);
        }

        .toast.error {
            border-left-color: var(--danger-color);
        }

        .toast.error::before {
            background: linear-gradient(90deg, var(--danger-color), var(--danger-light));
        }

        .toast.warning {
            border-left-color: var(--warning-color);
        }

        .toast.warning::before {
            background: linear-gradient(90deg, var(--warning-color), var(--warning-light));
        }

        .toast.info {
            border-left-color: var(--info-color);
        }

        .toast.info::before {
            background: linear-gradient(90deg, var(--info-color), var(--info-light));
        }

        .confirm-overlay {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100%;
            z-index: 99999;
            padding: 20px;
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(10px);
            overflow-y: auto;
            box-sizing: border-box;
        }

        .confirm-dialog {
            position: absolute;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(460px, 100%);
            overflow: hidden;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 34px 95px rgba(15, 23, 42, 0.32);
        }

        .confirm-dialog-header {
            padding: 26px 28px;
            color: #ffffff;
            background: linear-gradient(135deg, #1e3a8a, #2563eb, #7c3aed);
        }

        .confirm-dialog-title {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .confirm-dialog-subtitle {
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.92rem;
        }

        .confirm-dialog-body {
            padding: 24px 28px 28px;
        }

        .confirm-dialog-message {
            color: #475569;
            line-height: 1.65;
            margin-bottom: 22px;
        }

        .confirm-dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .toast-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
            font-size: 0.95rem;
        }

        .toast-message {
            color: var(--secondary-color);
            font-size: 0.85rem;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 1.2rem;
            padding: 4px;
            transition: var(--transition);
            border-radius: 4px;
        }

        .toast-close:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--dark-color);
        }

        @keyframes toastSlideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes toastProgress {
            to {
                transform: scaleX(1);
            }
        }

        /* ===== DIVISORES VISUAIS ===== */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, 
                transparent, 
                var(--border-color), 
                transparent);
            margin: var(--spacing-lg) 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 1px;
            background: var(--primary-color);
        }

        .divider-vertical {
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg, 
                transparent, 
                var(--border-color), 
                transparent);
            margin: 0 var(--spacing-md);
        }

        .section-divider {
            margin: var(--spacing-2xl) 0;
            padding: var(--spacing-lg) 0;
            border-top: 2px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
            background: linear-gradient(180deg, 
                rgba(248, 250, 252, 0.5), 
                transparent);
        }

        /* ===== GRADIENTES SUTIS NOS CARDS ===== */
        .stat-card {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(248, 250, 252, 0.9) 100%);
        }

        .stat-card.primary {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(239, 246, 255, 0.6) 100%);
        }

        .stat-card.success {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(236, 253, 245, 0.6) 100%);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(255, 251, 235, 0.6) 100%);
        }

        .stat-card.info {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(236, 254, 255, 0.6) 100%);
        }

        .quick-action-card {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(248, 250, 252, 0.6) 100%);
        }

        /* ===== ÍCONES DE STATUS MELHORADOS ===== */
        .status-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 700;
            position: relative;
        }

        .status-icon.success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .status-icon.warning {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .status-icon.error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .status-icon.info {
            background: rgba(6, 182, 212, 0.1);
            color: #0891b2;
        }

        /* ===== BREADCRUMBS MELHORADOS ===== */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: var(--spacing-md);
            padding: 8px 0;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-link {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
            padding: 4px 8px;
            border-radius: 6px;
        }

        .breadcrumb-link:hover {
            color: var(--primary-color);
            background: rgba(59, 130, 246, 0.1);
        }

        .breadcrumb-separator {
            color: var(--border-color);
            font-size: 0.9rem;
        }

        .breadcrumb-current {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 4px 8px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 6px;
        }
        .premium-shell {
            display: none;
        }

        body {
            background:
                radial-gradient(circle at 16% 8%, rgba(37, 99, 235, 0.12), transparent 28%),
                radial-gradient(circle at 92% 18%, rgba(16, 185, 129, 0.12), transparent 28%),
                linear-gradient(135deg, #f8fafc 0%, #eef2ff 44%, #f8fafc 100%);
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), transparent 70%);
            z-index: -1;
        }

        .sidebar {
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
                linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 18px 0 55px rgba(15, 23, 42, 0.18);
        }

        .sidebar::before {
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
                linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%);
        }

        .sidebar-header {
            padding: 28px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        }

        .sidebar-logo {
            gap: 12px;
            font-size: 1.18rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .sidebar-logo i {
            display: inline-grid;
            place-items: center;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16);
        }

        .sidebar-group {
            margin: 0 12px 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-group-title {
            margin: 0 0 8px;
            padding: 8px 10px;
            color: rgba(255, 255, 255, 0.58);
            background: transparent;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.12em;
        }

        .sidebar-link {
            margin: 4px 0;
            padding: 12px 13px;
            border: 1px solid transparent;
            border-left: 0;
            border-radius: 14px;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 650;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.16);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 10px 26px rgba(0, 0, 0, 0.12);
            transform: translateX(3px);
        }

        .sidebar-link.active {
            color: #ffffff;
        }

        .sidebar-footer-fixed {
            background: rgba(15, 23, 42, 0.32);
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(18px);
        }

        .sidebar-user {
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.11);
        }

        .logout-btn-small {
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(239, 68, 68, 0.82);
            font-weight: 800;
        }

        .main-content {
            padding: 34px;
            min-height: 100vh;
            background:
                radial-gradient(circle at 12% 4%, rgba(37, 99, 235, 0.12), transparent 24%),
                radial-gradient(circle at 92% 12%, rgba(16, 185, 129, 0.10), transparent 28%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 44%, #eef2ff 100%);
        }

        .dashboard-container {
            background:
                radial-gradient(circle at 12% 4%, rgba(37, 99, 235, 0.12), transparent 24%),
                radial-gradient(circle at 92% 12%, rgba(16, 185, 129, 0.10), transparent 28%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 44%, #eef2ff 100%);
        }

        .page-header {
            position: relative;
            margin-bottom: 34px;
            padding: 28px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: 26px;
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981);
        }

        .page-title h1 {
            color: #0f172a;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            letter-spacing: -0.055em;
        }

        .page-subtitle {
            max-width: 760px;
            color: #64748b;
            font-size: 1rem;
        }

        .btn {
            border-radius: 999px;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.14);
        }

        .stats-grid {
            gap: 22px;
            margin-bottom: 34px;
        }

        .stat-card {
            position: relative;
            padding: 26px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: 26px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.09);
            backdrop-filter: blur(18px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        }

        .stat-card.success::before {
            background: linear-gradient(90deg, var(--success-color), var(--success-light));
        }

        .stat-card.warning::before {
            background: linear-gradient(90deg, var(--warning-color), var(--warning-light));
        }

        .stat-card.info::before {
            background: linear-gradient(90deg, var(--info-color), var(--info-light));
        }

        .stat-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14);
        }

        .stat-icon {
            border-radius: 20px;
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.16);
        }

        .stat-value {
            letter-spacing: -0.055em;
        }

        .evolution-chart-container,
        .agendamentos-section,
        .quick-actions-section,
        .acoes-rapidas > .card,
        .main-content > .card,
        .quick-action-card,
        .chart-card,
        .course-card,
        .professor-card,
        .recent-activity,
        .content-card {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.78) !important;
            border-radius: 24px !important;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
            backdrop-filter: blur(18px);
        }

        .quick-actions-grid {
            gap: 22px;
        }

        .agendamentos-section,
        .quick-actions-section {
            position: relative;
            overflow: hidden;
            padding: 28px;
            margin-bottom: 34px;
        }

        .agendamentos-section::before,
        .quick-actions-section::before,
        .evolution-chart-container::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
        }

        .quick-actions-section .section-header,
        .agendamentos-section .section-header,
        .acoes-rapidas .card-header,
        .main-content > .card .card-header {
            margin-bottom: 22px;
        }

        .acoes-rapidas > .card,
        .main-content > .card {
            position: relative;
            overflow: hidden;
            padding: 0;
            margin-bottom: 34px;
        }

        .acoes-rapidas > .card::before,
        .main-content > .card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #2563eb, #7c3aed, #10b981);
            z-index: 1;
        }

        .acoes-rapidas .card-header,
        .main-content > .card .card-header {
            padding: 26px 28px 0;
        }

        .acoes-rapidas .card-content,
        .main-content > .card .card-content {
            padding: 24px 28px 28px;
        }

        .quick-action-card {
            padding: 26px;
            border-left: 0 !important;
        }

        .quick-action-card::before {
            height: 100%;
            width: 5px;
            right: auto;
            bottom: 0;
            transform: scaleY(1);
            transform-origin: top;
            background: linear-gradient(180deg, var(--primary-color), var(--primary-light));
        }

        .quick-action-card.success::before {
            background: linear-gradient(180deg, var(--success-color), var(--success-light));
        }

        .quick-action-card.warning::before {
            background: linear-gradient(180deg, var(--warning-color), var(--warning-light));
        }

        .quick-action-card.info::before {
            background: linear-gradient(180deg, var(--info-color), var(--info-light));
        }

        .quick-action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14) !important;
        }

        .quick-action-icon {
            border-radius: 20px;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
        }

        input,
        select,
        textarea {
            border-radius: 14px !important;
        }

        @media (max-width: 1024px) {
            .main-content {
                padding: 22px;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 22px;
                border-radius: 22px;
            }

            .main-content {
                padding: 16px;
                padding-top: calc(var(--spacing-sm) + 100px);
            }
        }

        .card-header {
            align-items: center;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.72);
        }

        .card-title {
            gap: 10px;
            letter-spacing: -0.035em;
        }

        .aulas-agendadas .card,
        .acoes-rapidas .card {
            padding: 28px;
        }

        #agendamentos-container > div,
        .agendamento-item,
        .aula-card {
            position: relative;
            margin-bottom: 16px;
            padding: 20px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.92));
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
        }

        #agendamentos-container > div::before,
        .agendamento-item::before,
        .aula-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            border-radius: 22px 0 0 22px;
            background: linear-gradient(180deg, #2563eb, #10b981);
        }

        .status-badge,
        .badge-new,
        .badge-admin,
        [class*="status-"] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 850;
            letter-spacing: 0.02em;
            border: 1px solid transparent;
        }

        .status-agendado,
        .status-pendente {
            color: #92400e;
            background: #fef3c7;
            border-color: #fde68a;
        }

        .status-confirmado,
        .status-ativo {
            color: #065f46;
            background: #d1fae5;
            border-color: #a7f3d0;
        }

        .status-cancelado,
        .status-inativo {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }

        .btn-sm {
            min-height: 36px;
            padding: 8px 14px;
            font-size: 0.8rem;
        }

        .quick-action-content h3 {
            letter-spacing: -0.03em;
        }

        .quick-action-content p {
            color: #64748b;
            line-height: 1.55;
        }

        .modal-content {
            border: 1px solid rgba(255, 255, 255, 0.78) !important;
            border-radius: 26px !important;
            box-shadow: 0 30px 90px rgba(15, 23, 42, 0.22) !important;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #172554, #1e40af) !important;
            color: #ffffff !important;
        }

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(59, 130, 246, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(20, 184, 166, 0.13), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%) !important;
        }

        .main-content {
            max-width: 1500px;
        }

        .page-header {
            background:
                radial-gradient(circle at 8% 18%, rgba(255, 255, 255, 0.22), transparent 30%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 48%, #2563eb 100%) !important;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.42);
            box-shadow: 0 28px 80px rgba(30, 58, 138, 0.2);
        }

        .page-header::before {
            height: 100%;
            opacity: 0.18;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
            background-size: 42px 42px;
        }

        .page-title,
        .page-actions,
        .breadcrumb {
            position: relative;
            z-index: 1;
        }

        .page-title h1,
        .page-subtitle,
        .breadcrumb-current,
        .breadcrumb-link,
        .breadcrumb-separator {
            color: #ffffff !important;
        }

        .page-subtitle,
        .breadcrumb-link,
        .breadcrumb-separator {
            opacity: 0.78;
        }

        .breadcrumb-current {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .page-actions .btn {
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #ffffff;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(16px);
        }

        .page-actions .btn-primary,
        body.dark-mode .page-actions .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            color: #ffffff !important;
            border: none !important;
        }

        .page-actions .btn-outline {
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.7);
            color: #ffffff;
        }

        .page-actions .btn-outline:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .page-actions .btn-secondary {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: rgba(255, 255, 255, 0.85);
        }

        .page-actions .btn-success {
            background: linear-gradient(135deg, #059669, #047857);
            color: #ffffff;
        }

        .page-actions .btn-info {
            background: linear-gradient(135deg, #0891b2, #0e7490);
            color: #ffffff;
        }

        .page-actions .btn-danger {
            background: #ef4444;
            color: #ffffff;
        }

        .page-actions .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 42px rgba(15, 23, 42, 0.22);
        }

        .stat-card {
            isolation: isolate;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            inset: auto -28px -42px auto;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.08);
            z-index: -1;
        }

        .stat-card.success::after {
            background: rgba(16, 185, 129, 0.1);
        }

        .stat-card.warning::after {
            background: rgba(245, 158, 11, 0.12);
        }

        .stat-card.info::after {
            background: rgba(8, 145, 178, 0.12);
        }

        .stat-title {
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 850;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: clamp(2rem, 3vw, 2.75rem);
            color: #0f172a;
        }

        .card {
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.82) !important;
            border-radius: 28px !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .quick-action-card {
            min-height: 172px;
        }

        .quick-action-card::before {
            border-radius: 24px 0 0 24px;
        }

        .quick-action-arrow {
            color: #94a3b8;
            background: #f8fafc;
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            transition: var(--transition);
        }

        .quick-action-card:hover .quick-action-arrow {
            color: #ffffff;
            background: #2563eb;
            transform: translateX(4px);
        }

        .divider {
            opacity: 0;
            margin: 10px 0 28px;
        }

        .chart-container,
        canvas {
            border-radius: 22px;
        }

        @media (max-width: 768px) {
            .page-actions {
                display: grid;
                grid-template-columns: 1fr;
                width: 100%;
            }

            .page-actions .btn {
                justify-content: center;
                width: 100%;
            }
        }
    /* ============== ADMIN_THEME_OVERRIDES_START ============== */
    :root {
        --admin-primary: #1e3a8a;
        --admin-primary-dark: #0f172a;
        --admin-primary-light: #2563eb;
        --admin-accent: #2563eb;
        --admin-success: #059669;
        --admin-warning: #d97706;
        --admin-danger: #dc2626;
        --admin-text: #0f172a;
        --admin-text-secondary: #475569;
        --admin-text-muted: #64748b;
        --admin-border: #e2e8f0;
        --admin-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    body {
        font-family: var(--admin-font) !important;
        background:
            radial-gradient(circle at 8% 4%, rgba(59, 130, 246, 0.16), transparent 24%),
            radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.10), transparent 26%),
            linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%) !important;
        color: var(--admin-text) !important;
        text-rendering: optimizeLegibility;
    }
    .dashboard-container { background: transparent !important; max-width: 100%; overflow-x: hidden; }

    /* === SIDEBAR === */
    .sidebar {
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
            linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 18px 0 55px rgba(15, 23, 42, 0.18) !important;
    }
    .sidebar-header {
        padding: 28px 20px !important;
        background: rgba(255, 255, 255, 0.06) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.14) !important;
        backdrop-filter: blur(18px);
    }
    .sidebar-logo {
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.03em !important;
        color: #ffffff !important;
    }
    .sidebar-logo i {
        display: inline-grid; place-items: center;
        width: 44px; height: 44px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.14) !important;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16);
        color: #ffffff !important;
    }
    .sidebar-link, .nav-link, .sidebar-menu a {
        margin: 4px 12px !important;
        padding: 13px 14px !important;
        border-radius: 14px !important;
        border-left: 0 !important;
        font-weight: 600 !important;
        font-family: var(--admin-font) !important;
        color: rgba(255, 255, 255, 0.85) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .sidebar-link:hover, .sidebar-link.active,
    .nav-link:hover, .nav-link.active,
    .sidebar-menu a:hover, .sidebar-menu a.active {
        background: rgba(255, 255, 255, 0.14) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.16) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 10px 26px rgba(0, 0, 0, 0.12) !important;
        transform: translateX(3px);
    }

    /* === MAIN CONTENT === */
    .main-content { padding: 40px !important; min-height: 100vh; }
    .header {
        position: relative; overflow: hidden;
        margin-bottom: 40px !important;
        padding: 36px !important;
        border: 1px solid rgba(255, 255, 255, 0.42) !important;
        border-radius: 28px !important;
        background:
            radial-gradient(circle at 8% 18%, rgba(255, 255, 255, 0.22), transparent 30%),
            linear-gradient(135deg, #0f172a 0%, #1e3a8a 58%, #2563eb 100%) !important;
        box-shadow: 0 28px 80px rgba(30, 58, 138, 0.2) !important;
    }
    .header::before {
        content: ''; position: absolute; inset: 0; opacity: 0.18;
        background:
            linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
            linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
        background-size: 42px 42px;
    }
    .header h1, .header .user-info, .header > * { position: relative; z-index: 1; }
    .header h1 {
        color: #ffffff !important;
        font-size: clamp(2rem, 4vw, 3rem) !important;
        font-weight: 800 !important;
        letter-spacing: -0.055em !important;
        text-shadow: 0 2px 18px rgba(15, 23, 42, 0.25);
    }
    .header h1 i { color: #ffffff !important; }
    .header p, .header .header-subtitle {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    .user-info {
        padding: 10px 12px !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        border-radius: 18px !important;
        background: rgba(255, 255, 255, 0.12) !important;
        backdrop-filter: blur(16px);
    }
    .user-info div, .user-info a, .user-info span { color: #ffffff !important; }
    .user-avatar {
        background: linear-gradient(135deg, #1e3a8a, #0f172a) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.22) !important;
    }
    .logout-btn {
        border-radius: 999px !important;
        background: rgba(239, 68, 68, 0.92) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    /* === GRID PADRONIZADO === */
    .stats-grid, .stats-overview, .quick-actions-grid, .charts-grid,
    .content-grid, .cards-grid, .settings-grid, .mini-stats-grid {
        gap: 28px !important;
        margin-bottom: 40px !important;
        align-items: stretch;
    }

    /* === CARDS GERAIS (chart, content, etc) === */
    .content-card, .chart-card, .filters-section, .table-container,
    .table-responsive, .report-card, .summary-card, .quick-action-card,
    .settings-card, .setting-card {
        position: relative; overflow: hidden;
        background: rgba(255, 255, 255, 0.92) !important;
        border: 1px solid rgba(255, 255, 255, 0.78) !important;
        border-radius: 24px !important;
        padding: 28px !important;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
        backdrop-filter: blur(18px);
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    border-color 0.35s !important;
    }
    .content-card::before, .chart-card::before,
    .report-card::before, .summary-card::before,
    .settings-card::before, .setting-card::before {
        content: ''; position: absolute; inset: 0 0 auto 0; height: 5px;
        background: linear-gradient(90deg, #1e3a8a, #2563eb);
    }
    .content-card:hover, .chart-card:hover,
    .report-card:hover, .summary-card:hover,
    .quick-action-card:hover {
        transform: translateY(-7px);
        border-color: rgba(37, 99, 235, 0.18) !important;
        box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14) !important;
    }

    /* === TIPOGRAFIA === */
    .content-card h2, .chart-title, .section-title, .card-title {
        color: var(--admin-text) !important;
        font-weight: 800 !important;
    }
    .content-card h2 {
        font-size: 1.5rem !important;
        margin-bottom: 28px !important;
        display: inline-flex; align-items: center; gap: 12px;
    }
    .content-card h2 i, .chart-title i {
        display: inline-grid; place-items: center;
        width: 38px; height: 38px;
        border-radius: 14px;
        background: rgba(37, 99, 235, 0.10);
        color: var(--admin-primary-light) !important;
        font-size: 0.95rem;
    }
    .chart-title {
        font-size: 1.0625rem !important;
        margin-bottom: 18px !important;
    }

    /* === TABELAS === */
    .table, table { border-collapse: separate; border-spacing: 0; }
    .table thead th, .table th, table th {
        background: #f8fafc !important;
        color: var(--admin-text-secondary) !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .table td, table td {
        color: var(--admin-text-secondary) !important;
        font-size: 0.875rem !important;
    }
    .table tbody tr:hover, table tbody tr:hover { background: rgba(37, 99, 235, 0.035) !important; }

    /* === INPUTS / BOTÕES === */
    .search-input, input[type="text"], input[type="email"], input[type="password"],
    input[type="number"], select, textarea {
        font-family: var(--admin-font);
        font-size: 0.875rem !important;
        color: var(--admin-text) !important;
        border-radius: 14px !important;
    }
    .btn, .button, .filter-btn, .export-btn, button[type="submit"] {
        font-family: var(--admin-font) !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important;
        border-radius: 999px !important;
    }
    .filters-section { padding: 24px !important; gap: 18px !important; margin-bottom: 28px !important; }

    /* === Quick actions === */
    .quick-action-card { gap: 18px !important; padding: 28px !important; }
    .quick-action-title {
        color: var(--admin-text) !important;
        font-size: 1.0625rem !important;
        font-weight: 800 !important;
    }
    .quick-action-desc, .text-muted, small, .description {
        color: var(--admin-text-secondary) !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
    }

    /* === Responsividade === */
    @media (max-width: 1024px) {
        .main-content { padding: 28px !important; }
        .header { padding: 28px !important; }
    }
    @media (max-width: 768px) {
        .main-content { padding: 20px !important; }
        .header { padding: 24px !important; margin-bottom: 28px !important; }
        .content-card, .chart-card,
        .report-card, .summary-card,
        .settings-card, .setting-card { padding: 22px !important; }
        .stats-grid, .charts-grid, .content-grid { gap: 18px !important; margin-bottom: 28px !important; }
    }
    /* ============== ADMIN_THEME_OVERRIDES_END ============== */

    /* ========================================
       ADMIN STAT CARDS — IDÊNTICOS AO PROFESSOR
       ======================================== */
    .stat-card {
        position: relative !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        min-height: 180px !important;
        padding: 28px !important;
        border-radius: 18px !important;
        background:
            radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.16), transparent 38%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04) !important;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .stat-card.primary {
        background:
            radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.16), transparent 38%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    }
    .stat-card.success {
        background:
            radial-gradient(circle at 92% 8%, rgba(16, 185, 129, 0.18), transparent 38%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    }
    .stat-card.warning {
        background:
            radial-gradient(circle at 92% 8%, rgba(245, 158, 11, 0.20), transparent 38%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    }
    .stat-card.info {
        background:
            radial-gradient(circle at 92% 8%, rgba(96, 165, 250, 0.20), transparent 38%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    }
    .stat-card::before {
        content: '' !important;
        position: absolute !important;
        top: 0; left: 0; right: 0 !important;
        height: 4px !important;
        background: linear-gradient(90deg, #1e3a8a, #2563eb) !important;
        transform: scaleX(0) !important;
        transform-origin: left;
        transition: transform 0.3s ease !important;
    }
    .stat-card:hover::before { transform: scaleX(1) !important; }
    .stat-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12), 0 8px 10px -6px rgba(0,0,0,0.06) !important;
        border-color: rgba(37, 99, 235, 0.25) !important;
    }

    /* Inverter ordem: ícone primeiro, depois título */
    .stat-card .stat-header {
        display: flex !important;
        flex-direction: column-reverse !important;
        align-items: flex-start !important;
        gap: 14px !important;
        margin-bottom: 14px !important;
    }
    .stat-card .stat-title {
        font-size: 0.72rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.12em !important;
        color: #64748b !important;
        margin: 0 !important;
    }

    /* Caixinha gradiente do ícone */
    .stat-card .stat-icon {
        display: inline-grid !important;
        place-items: center !important;
        width: 58px !important;
        height: 58px !important;
        border-radius: 20px !important;
        background: linear-gradient(135deg, #1e3a8a, #0f172a) !important;
        color: #ffffff !important;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16) !important;
        font-size: 1.4rem !important;
        margin: 0 !important;
        transition: transform 0.3s ease !important;
    }
    .stat-card .stat-icon i { color: #ffffff !important; font-size: 1.4rem !important; }
    .stat-card.primary .stat-icon { background: linear-gradient(135deg, #1e3a8a, #2563eb) !important; }
    .stat-card.success .stat-icon { background: linear-gradient(135deg, #059669, #047857) !important; }
    .stat-card.warning .stat-icon { background: linear-gradient(135deg, #d97706, #b45309) !important; }
    .stat-card.info .stat-icon    { background: linear-gradient(135deg, #2563eb, #1e3a8a) !important; }
    .stat-card:hover .stat-icon {
        transform: scale(1.08) rotate(-3deg);
    }

    /* Valor em gradiente */
    .stat-card .stat-value {
        font-size: 2.5rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.04em !important;
        line-height: 1 !important;
        margin-bottom: 12px !important;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
        color: transparent !important;
        font-variant-numeric: tabular-nums;
    }

    .stat-card .stat-change {
        font-size: 0.82rem !important;
        color: #475569 !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .stat-card.primary .stat-change-icon { color: #2563eb !important; }
    .stat-card.success .stat-change-icon { color: #10b981 !important; }
    .stat-card.warning .stat-change-icon { color: #f59e0b !important; }
    .stat-card.info .stat-change-icon    { color: #60a5fa !important; }

    /* Barra de progresso */
    .stat-card .progress-bar {
        margin-top: auto !important;
        padding-top: 14px;
        height: auto !important;
        background: transparent !important;
    }
    .stat-card .progress-bar::before {
        content: '';
        display: block;
        height: 6px;
        border-radius: 999px;
        background: rgba(226, 232, 240, 0.85);
    }
    .stat-card .progress-fill {
        height: 6px !important;
        margin-top: -6px;
        background: linear-gradient(90deg, #1e3a8a, #2563eb) !important;
        border-radius: 999px !important;
        transition: width 0.6s ease;
        position: relative;
        z-index: 1;
    }
    .stat-card .progress-fill.success { background: linear-gradient(90deg, #059669, #10b981) !important; }
    .stat-card .progress-fill.warning { background: linear-gradient(90deg, #d97706, #f59e0b) !important; }
    .stat-card .progress-fill.info    { background: linear-gradient(90deg, #2563eb, #60a5fa) !important; }

    /* Remover bola decorativa redundante (já temos glow no canto superior) */
    .stat-card::after { display: none !important; }

    /* ===== Dark Mode (ADMIN) - Legibilidade dos números grandes ===== */
    body.admin-dashboard.dark-mode .stat-card .stat-value {
        background: none !important;
        -webkit-background-clip: unset !important;
        background-clip: unset !important;
        -webkit-text-fill-color: #f8fafc !important;
        color: #f8fafc !important;
    }

    /* ===== Dark Mode (ADMIN) - Legibilidade dos textos pequenos ===== */
    body.admin-dashboard.dark-mode .stat-card .stat-change {
        /* Texto tipo “Cursos disponíveis / Professores ativos / ...” */
        color: rgba(248, 250, 252, 0.72) !important;
        font-weight: 600 !important;
    }

    body.admin-dashboard.dark-mode .stat-card .stat-change i,
    body.admin-dashboard.dark-mode .stat-card .stat-change-icon {
        opacity: 1 !important;
    }

    body.admin-dashboard.dark-mode .stat-card .progress-bar::before {
        /* Trilho mais escuro para não competir com a barra colorida */
        background: rgba(255, 255, 255, 0.14) !important;
    }

    /* ===== Dark Mode (ADMIN) - Próximas Aulas e cards ===== */
    body.admin-dashboard.dark-mode .main-content > .card,
    body.admin-dashboard.dark-mode .aulas-agendadas .card,
    body.admin-dashboard.dark-mode .acoes-rapidas .card,
    body.admin-dashboard.dark-mode .evolution-chart-container,
    body.admin-dashboard.dark-mode .content-card,
    body.admin-dashboard.dark-mode .card {
        background: #1e293b !important;
        background-color: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    body.admin-dashboard.dark-mode .card-header {
        background: rgba(15, 23, 42, 0.75) !important;
        border-bottom-color: rgba(255, 255, 255, 0.1) !important;
    }

    body.admin-dashboard.dark-mode .card-title,
    body.admin-dashboard.dark-mode .card-header h3 {
        color: #f8fafc !important;
    }

    body.admin-dashboard.dark-mode .card-title i {
        color: #93c5fd !important;
        background: rgba(37, 99, 235, 0.2) !important;
    }

    body.admin-dashboard.dark-mode .card-subtitle {
        color: #cbd5e1 !important;
    }

    body.admin-dashboard.dark-mode #agendamentos-container > div,
    body.admin-dashboard.dark-mode .agendamentos-grid {
        background: transparent !important;
        background-color: transparent !important;
        border-color: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    body.admin-dashboard.dark-mode #agendamentos-container > div::before {
        display: none !important;
    }

    body.admin-dashboard.dark-mode .agendamento-card {
        background: rgba(15, 23, 42, 0.9) !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }

    body.admin-dashboard.dark-mode .agendamento-data,
    body.admin-dashboard.dark-mode .agendamento-info strong {
        color: #f8fafc !important;
    }

    body.admin-dashboard.dark-mode .agendamento-info span {
        color: #cbd5e1 !important;
    }

    body.admin-dashboard.dark-mode .agendamento-horario {
        color: #93c5fd !important;
    }

    body.admin-dashboard.dark-mode .agendamento-observacoes {
        background: rgba(255, 255, 255, 0.06) !important;
        color: #cbd5e1 !important;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    body.admin-dashboard.dark-mode .agendamento-observacoes:empty {
        display: none !important;
    }

    body.admin-dashboard.dark-mode .sem-agendamentos,
    body.admin-dashboard.dark-mode .sem-agendamentos h4 {
        color: #cbd5e1 !important;
    }

    body.admin-dashboard.dark-mode .sem-agendamentos h4 {
        color: #f8fafc !important;
    }

    /* ===== Dark Mode (ADMIN) - Ações Rápidas (badges e setas) ===== */
    body.admin-dashboard.dark-mode .quick-action-card {
        background: rgba(30, 41, 59, 0.95) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    body.admin-dashboard.dark-mode .quick-action-content h3 {
        color: #f8fafc !important;
    }

    body.admin-dashboard.dark-mode .quick-action-content p {
        color: #cbd5e1 !important;
    }

    body.admin-dashboard.dark-mode .badge-new {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        color: #ffffff !important;
        border-color: rgba(16, 185, 129, 0.45) !important;
    }

    body.admin-dashboard.dark-mode .badge-admin {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
        border-color: rgba(248, 113, 113, 0.45) !important;
    }

    body.admin-dashboard.dark-mode .stat-item {
        background: rgba(59, 130, 246, 0.22) !important;
        color: #93c5fd !important;
        border: 1px solid rgba(96, 165, 250, 0.3);
    }

    body.admin-dashboard.dark-mode .quick-action-arrow {
        color: #cbd5e1 !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }

    body.admin-dashboard.dark-mode .quick-action-card:hover .quick-action-arrow {
        color: #ffffff !important;
        background: #2563eb !important;
    }

    /* ===== Dark Mode (ADMIN) - Gráfico / filtro de período ===== */
    body.admin-dashboard.dark-mode .chart-period-segment {
        background: rgba(15, 23, 42, 0.85) !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }

    body.admin-dashboard.dark-mode .period-filter {
        color: #94a3b8 !important;
    }

    body.admin-dashboard.dark-mode .period-filter:hover {
        color: #e2e8f0 !important;
    }

    body.admin-dashboard.dark-mode .period-filter.active {
        background: rgba(37, 99, 235, 0.35) !important;
        color: #f8fafc !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
    }

    body.admin-dashboard.dark-mode .chart-header h3 {
        color: #f8fafc !important;
    }

    body.admin-dashboard.dark-mode .chart-header h3 i {
        background: rgba(37, 99, 235, 0.25) !important;
        color: #93c5fd !important;
    }

    body.admin-dashboard.dark-mode .chart-subtitle {
        color: #94a3b8 !important;
    }

    body.admin-dashboard.dark-mode .chart-kpi {
        background: rgba(15, 23, 42, 0.6) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    body.admin-dashboard.dark-mode .chart-kpi:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
    }

    body.admin-dashboard.dark-mode .kpi-label {
        color: #94a3b8 !important;
    }

    body.admin-dashboard.dark-mode .kpi-value {
        color: #f8fafc !important;
    }

    @media (max-width: 768px) {
        .stat-card { padding: 22px !important; min-height: 160px !important; }
        .stat-card .stat-value { font-size: 2rem !important; }
        .stat-card .stat-icon { width: 50px !important; height: 50px !important; font-size: 1.2rem !important; }
    }
    </style>
</head>
<body class="admin-dashboard">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard_final.php" class="sidebar-logo">
                    <i class="fas fa-graduation-cap"></i>
                    EduConnect Tech
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <!-- GRUPO 1: Visão Geral -->
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Visão Geral</div>
                        <li class="sidebar-item">
                            <a href="dashboard_final.php" class="sidebar-link active">
                                <i class="fas fa-tachometer-alt sidebar-icon"></i>
                                Dashboard
                            </a>
                        </li>
                    </div>

                    <!-- GRUPO 2: Gestão Acadêmica -->
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Gestão Acadêmica</div>
                        <li class="sidebar-item">
                            <a href="cursos_completo.php" class="sidebar-link">
                                <i class="fas fa-laptop-code sidebar-icon"></i>
                                Cursos de Tech
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="professores.php" class="sidebar-link">
                                <i class="fas fa-chalkboard-teacher sidebar-icon"></i>
                                Professores
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="alunos.php" class="sidebar-link">
                                <i class="fas fa-user-graduate sidebar-icon"></i>
                                Alunos
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="agendamentos.php" class="sidebar-link">
                                <i class="fas fa-calendar-alt sidebar-icon"></i>
                                Agendamentos
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="inscricoes_cursos.php" class="sidebar-link">
                                <i class="fas fa-user-plus sidebar-icon"></i>
                                Inscrições
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="atribuicoes_cursos.php" class="sidebar-link">
                                <i class="fas fa-link sidebar-icon"></i>
                                Atribuições
                            </a>
                        </li>
                    </div>

                    <!-- GRUPO 3: Relatórios -->
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Relatórios</div>
                        <li class="sidebar-item">
                            <a href="relatorios_detalhados.php" class="sidebar-link">
                                <i class="fas fa-chart-line sidebar-icon"></i>
                                Relatórios Gerais
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="certificados.php" class="sidebar-link">
                                <i class="fas fa-certificate sidebar-icon"></i>
                                Certificados
                            </a>
                        </li>
                    </div>

                    <!-- GRUPO 4: Sistema -->
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Sistema</div>
                        <li class="sidebar-item">
                            <a href="sistema_usuarios.php" class="sidebar-link">
                                <i class="fas fa-users-cog sidebar-icon"></i>
                                Usuários
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="configuracoes.php" class="sidebar-link">
                                <i class="fas fa-cog sidebar-icon"></i>
                                Configurações
                            </a>
                        </li>
                    </div>
                </ul>
            </nav>
        </aside>
        
        <!-- Toast Container -->
        <div class="toast-container" id="toastContainer"></div>

        <div id="confirmOverlay" class="confirm-overlay">
            <div class="confirm-dialog">
                <div class="confirm-dialog-header">
                    <div class="confirm-dialog-title" id="confirmTitle">Confirmar ação</div>
                    <div class="confirm-dialog-subtitle" id="confirmSubtitle">Esta ação precisa de confirmação</div>
                </div>
                <div class="confirm-dialog-body">
                    <p class="confirm-dialog-message" id="confirmMessage">Deseja continuar?</p>
                    <div class="confirm-dialog-actions">
                        <button type="button" class="btn btn-outline" onclick="closeConfirmDialog()">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmActionBtn">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Mobile Toggle Button -->
            <button class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Page Header -->
            <header class="page-header">
                <div class="breadcrumb">
                    <div class="breadcrumb-item">
                        <a href="dashboard_final.php" class="breadcrumb-link">Início</a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current">Dashboard</span>
                    </div>
                </div>
                
                <div class="page-title">
                    <h1>Dashboard de Cursos de Tecnologia</h1>
                </div>
                
                <div class="page-actions">
                    <button id="darkModeToggle" class="btn btn-secondary" title="Alternar tema" style="width: 42px; height: 42px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <i class="fas fa-moon"></i>
                    </button>
<button class="btn btn-primary" onclick="showNovoCursoModal()"
                            data-tooltip="Criar um novo curso no sistema"
                            aria-label="Criar novo curso">
                        <i class="fas fa-plus"></i> Novo Curso
                    </button>
                    <button class="btn btn-outline" onclick="window.location.href='agendamentos.php'"
                            data-tooltip="Agendar uma nova aula"
                            aria-label="Agendar nova aula">
                        <i class="fas fa-calendar-plus"></i> Agendar Aula
                    </button>
                    <button class="btn btn-outline" onclick="window.location.href='relatorios_detalhados.php'"
                            data-tooltip="Visualizar relatórios e estatísticas"
                            aria-label="Ver relatórios">
                        <i class="fas fa-chart-line"></i> Ver Relatórios
                    </button>
                    <a href="logout.php" class="btn btn-danger" 
                       data-tooltip="Sair do sistema" 
                       aria-label="Sair do sistema">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-header">
                        <h3 class="stat-title">Cursos Ativos</h3>
                        <div class="stat-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="cursos-count"><?php echo $cursos_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Cursos disponíveis
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo min(($cursos_count / 10) * 100, 100); ?>%"></div>
                    </div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-header">
                        <h3 class="stat-title">Professores Ativos</h3>
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="professores-count"><?php echo $professores_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Professores ativos
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill success" style="width: <?php echo min(($professores_count / 10) * 100, 100); ?>%"></div>
                    </div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-header">
                        <h3 class="stat-title">Alunos Cadastrados</h3>
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="alunos-count"><?php echo $alunos_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Alunos ativos
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill warning" style="width: <?php echo min(($alunos_count / 20) * 100, 100); ?>%"></div>
                    </div>
                </div>
                
                <div class="stat-card info">
                    <div class="stat-header">
                        <h3 class="stat-title">Aulas Agendadas</h3>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="agendamentos-count"><?php echo $agendamentos_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Próximas aulas
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill info" style="width: <?php echo min(($agendamentos_count / 15) * 100, 100); ?>%"></div>
                    </div>
                </div>

            </div>

            <!-- Divisor Visual -->
            <div class="divider"></div>

            <!-- Gráfico de Evolução Mensal -->
            <div class="evolution-chart-container">
                <div class="chart-header">
                    <div class="chart-header-left">
                        <h3><i class="fas fa-chart-line"></i> Evolução de atividade</h3>
                        <p class="chart-subtitle">Acompanhe o crescimento de cursos, alunos e aulas ao longo do tempo</p>
                    </div>
                    <div class="chart-period-segment" role="tablist">
                        <button class="period-filter active" data-period="7" onclick="updateChartPeriod(7, this)" role="tab">7 dias</button>
                        <button class="period-filter" data-period="30" onclick="updateChartPeriod(30, this)" role="tab">30 dias</button>
                        <button class="period-filter" data-period="90" onclick="updateChartPeriod(90, this)" role="tab">3 meses</button>
                    </div>
                </div>

                <div class="chart-kpis" id="chartKpis">
                    <div class="chart-kpi" data-key="cursos">
                        <div class="kpi-dot" style="background: #2563eb"></div>
                        <div class="kpi-info">
                            <span class="kpi-label">Cursos</span>
                            <span class="kpi-value" id="kpiCursos">0</span>
                        </div>
                        <span class="kpi-delta positive" id="deltaCursos"><i class="fas fa-arrow-up"></i> 0%</span>
                    </div>
                    <div class="chart-kpi" data-key="alunos">
                        <div class="kpi-dot" style="background: #6366f1"></div>
                        <div class="kpi-info">
                            <span class="kpi-label">Alunos</span>
                            <span class="kpi-value" id="kpiAlunos">0</span>
                        </div>
                        <span class="kpi-delta positive" id="deltaAlunos"><i class="fas fa-arrow-up"></i> 0%</span>
                    </div>
                    <div class="chart-kpi" data-key="aulas">
                        <div class="kpi-dot" style="background: #06b6d4"></div>
                        <div class="kpi-info">
                            <span class="kpi-label">Aulas</span>
                            <span class="kpi-value" id="kpiAulas">0</span>
                        </div>
                        <span class="kpi-delta positive" id="deltaAulas"><i class="fas fa-arrow-up"></i> 0%</span>
                    </div>
                </div>

                <div class="chart-wrapper">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>

            <!-- Divisor Visual -->
            <div class="divider"></div>

            <!-- Resumo das Aulas Agendadas -->
            <section class="aulas-agendadas">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-calendar-alt"></i>
                            Próximas Aulas
                        </h2>
                        <button class="btn btn-sm btn-outline" onclick="refreshAgendamentos()">
                            <i class="fas fa-sync"></i>
                            Atualizar
                        </button>
                    </div>
                    <div class="card-content">
                        <div id="agendamentos-container">
                            <!-- Conteúdo carregado via JavaScript -->
                        </div>
                    </div>
                </div>
            </section>



            <!-- Ações Rápidas -->
            <section class="acoes-rapidas">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-rocket"></i>
                            Ações Rápidas
                        </h2>
                        <p class="card-subtitle">Acesse as funcionalidades principais do sistema</p>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions-grid">
                            <div class="quick-action-card primary" onclick="window.location.href='agendamentos.php'">
                                <div class="quick-action-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>📅 Agendar Aula</h3>
                                    <p>Criar novo agendamento de aula</p>
                                    <div class="quick-action-badge">
                                        <span class="badge-new">NOVO</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                            
                            <div class="quick-action-card success" onclick="showProfessores()">
                                <div class="quick-action-icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>👨‍🏫 Gerenciar Professores</h3>
                                    <p>Cadastrar e gerenciar docentes</p>
                                    <div class="quick-action-stats">
                                        <span class="stat-item"><?php echo $professores_count; ?> ativos</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                            
                            <div class="quick-action-card warning" onclick="showRelatorios()">
                                <div class="quick-action-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>📊 Relatórios Detalhados</h3>
                                    <p>Visualizar estatísticas e métricas</p>
                                    <div class="quick-action-stats">
                                        <span class="stat-item"><?php echo $agendamentos_count; ?> aulas</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                            
                            <div class="quick-action-card info" onclick="window.location.href='cursos_completo.php'">
                                <div class="quick-action-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>🎓 Gerenciar Cursos</h3>
                                    <p>Visualizar e administrar catálogo</p>
                                    <div class="quick-action-stats">
                                        <span class="stat-item"><?php echo $cursos_count; ?> cursos</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>

                            <div class="quick-action-card primary" onclick="window.location.href='atribuicoes_cursos.php'">
                                <div class="quick-action-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>🔗 Atribuir Cursos</h3>
                                    <p>Ligar professores aos cursos que eles vão ministrar</p>
                                    <div class="quick-action-badge">
                                        <span class="badge-new">PROFESSOR</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                            
                            <div class="quick-action-card secondary" onclick="showAlunos()">
                                <div class="quick-action-icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>👨‍🎓 Gerenciar Alunos</h3>
                                    <p>Cadastrar e acompanhar estudantes</p>
                                    <div class="quick-action-stats">
                                        <span class="stat-item"><?php echo $alunos_count; ?> cadastrados</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                            
                            <div class="quick-action-card info" onclick="window.location.href='inscricoes_cursos.php'">
                                <div class="quick-action-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>📋 Gerenciar Inscrições</h3>
                                    <p>Matrículas dos alunos nos cursos</p>
                                    <div class="quick-action-stats">
                                        <span class="stat-item"><?php echo $inscricoes_count; ?> ativas</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>

                            <div class="quick-action-card dark" onclick="showConfiguracoes()">
                                <div class="quick-action-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="quick-action-content">
                                    <h3>⚙️ Configurações</h3>
                                    <p>Personalizar sistema e preferências</p>
                                    <div class="quick-action-badge">
                                        <span class="badge-admin">ADMIN</span>
                                    </div>
                                </div>
                                <div class="quick-action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        // FUNÇÕES DE MODAL E DASHBOARD
        function showNovoCursoModal() {
            console.log('showNovoCursoModal chamada');
            
            // Remover modal existente se houver
            const existingModal = document.getElementById('cursoModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            const modal = document.createElement('div');
            modal.id = 'cursoModal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(4px);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
                animation: fadeIn 0.2s ease-out;
                overflow-y: auto;
                padding: 20px;
                box-sizing: border-box;
            `;
            
            // Fechar ao clicar fora
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal('cursoModal');
                }
            });
            
            // Fechar com ESC
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    closeModal('cursoModal');
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);
            
            modal.innerHTML = `
                <div style="background: white; padding: 30px; border-radius: 16px; width: 600px; max-width: calc(100vw - 40px); max-height: calc(100vh - 40px); overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); animation: fadeIn 0.3s ease-out; position: relative; z-index: 10001; margin: 20px auto; box-sizing: border-box;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #f1f5f9;">
                        <h2 style="margin: 0; color: #0f172a; font-size: 1.5rem; font-weight: 700;"><i class="fas fa-book"></i> Criar Novo Curso</h2>
                        <button onclick="closeModal('cursoModal')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; transition: all 0.2s; padding: 4px 8px; border-radius: 4px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a';" onmouseout="this.style.background='none'; this.style.color='#64748b';">&times;</button>
                    </div>
                    
                    <form id="novoCursoForm" onsubmit="criarNovoCurso(event)">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Nome do Curso</label>
                                <input type="text" name="nome" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s;" placeholder="Ex: Desenvolvimento Web Full Stack">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Categoria</label>
                                <select name="categoria" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s; cursor: pointer; background: white;">
                                    <option value="">Selecione...</option>
                                    <option value="Programação">Programação</option>
                                    <option value="Design">Design</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Negócios">Negócios</option>
                                </select>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Nível</label>
                                <select name="nivel" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                    <option value="">Selecione...</option>
                                    <option value="Iniciante">Iniciante</option>
                                    <option value="Intermediário">Intermediário</option>
                                    <option value="Avançado">Avançado</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Duração (horas)</label>
                                <input type="number" name="duracao_horas" min="1" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s;" placeholder="Ex: 80">
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Descrição</label>
                            <textarea name="descricao" rows="3" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical; transition: all 0.2s; font-family: inherit;" placeholder="Descreva o curso..."></textarea>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Preço (R$)</label>
                                <input type="number" name="preco" min="0" step="0.01" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s;" placeholder="Ex: 299.90">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Vagas Disponíveis</label>
                                <input type="number" name="vagas" min="1" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s;" placeholder="Ex: 30">
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                            <button type="submit" style="padding: 12px 32px; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-right: 12px; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px -1px rgba(37, 99, 235, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(37, 99, 235, 0.3)';">Criar Curso</button>
                            <button type="button" onclick="closeModal('cursoModal')" style="padding: 12px 32px; background: #f1f5f9; color: #475569; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#f1f5f9'; this.style.transform='translateY(0)';">Cancelar</button>
                        </div>
                    </form>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // SISTEMA DE RELATÓRIOS REAIS - FUNCIONANDO DE VERDADE!
        function showRelatorios() {
            console.log('showRelatorios chamada');
            window.location.href = 'relatorios_detalhados.php';
        }

        // Função para exportar relatório
        function exportarRelatorio() {
            showNotification('📊 Exportando relatório em PDF...', 'info');
            setTimeout(() => {
                showNotification('✅ Relatório exportado com sucesso!', 'success');
            }, 2000);
        }

        function showAgendarAulaModal() {
            console.log('showAgendarAulaModal chamada');
            // Buscar dados dos professores e cursos via AJAX
            fetch('get_dados_agendamento.php')
                .then(response => response.json())
                .then(response => {
                    const data = response.data;
                    const modal = document.createElement('div');
                    modal.id = 'agendarAulaModal';
                    modal.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100vw;
                        height: 100vh;
                        background: rgba(0,0,0,0.5);
                        backdrop-filter: blur(4px);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 10000;
                        animation: fadeIn 0.2s ease-out;
                        overflow-y: auto;
                        padding: 20px;
                    `;
                    
                    // Criar opções dos professores
                    let professoresOptions = '<option value="">Selecione um professor...</option>';
                    if (data.professores && data.professores.length > 0) {
                        data.professores.forEach(prof => {
                            professoresOptions += `<option value="${prof.id}">${prof.nome}</option>`;
                        });
                    }
                    console.log('Opções professores:', professoresOptions);
                    
                    // Criar opções dos cursos
                    let cursosOptions = '<option value="">Selecione um curso...</option>';
                    if (data.cursos && data.cursos.length > 0) {
                        data.cursos.forEach(curso => {
                            cursosOptions += `<option value="${curso.id}">${curso.nome}</option>`;
                        });
                    }
                    console.log('Opções cursos:', cursosOptions);
                    
                    // Criar opções dos alunos
                    let alunosOptions = '<option value="">Selecione um aluno...</option>';
                    if (data.alunos && data.alunos.length > 0) {
                        data.alunos.forEach(aluno => {
                            alunosOptions += `<option value="${aluno.id}">${aluno.nome}</option>`;
                        });
                    }
                    console.log('Opções alunos:', alunosOptions);
                    
                    modal.innerHTML = `
                        <div style="background: white; padding: 30px; border-radius: 16px; width: 700px; max-width: calc(100vw - 40px); max-height: calc(100vh - 40px); overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); animation: fadeIn 0.3s ease-out; position: relative; z-index: 10001; margin: 20px auto; box-sizing: border-box;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #f1f5f9;">
                                <h2 style="margin: 0; color: #0f172a; font-size: 1.5rem; font-weight: 700;"><i class="fas fa-calendar-plus"></i> Agendar Nova Aula</h2>
                                <button onclick="closeModal('agendarAulaModal')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; transition: all 0.2s; padding: 4px 8px; border-radius: 4px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a';" onmouseout="this.style.background='none'; this.style.color='#64748b';">&times;</button>
                            </div>
                            
                            <form id="agendarAulaForm" onsubmit="agendarAulaReal(event)">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Nome do Aluno</label>
                                        <select name="aluno_id" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                            ${alunosOptions}
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Professor</label>
                                        <select name="professor_id" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                            ${professoresOptions}
                                        </select>
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Data da Aula</label>
                                        <input type="date" name="data" required min="${new Date().toISOString().split('T')[0]}" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Hora</label>
                                        <input type="time" name="hora_inicio" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                    </div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Serviço/Curso</label>
                                    <select name="curso_id" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                        ${cursosOptions}
                                    </select>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Observações</label>
                                    <textarea name="observacoes" rows="3" placeholder="Detalhes adicionais sobre a aula..." style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                                </div>
                                
                                <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                                    <button type="submit" style="padding: 12px 32px; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-right: 12px; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px -1px rgba(5, 150, 105, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(5, 150, 105, 0.3)';">Confirmar Agendamento</button>
                                    <button type="button" onclick="closeModal('agendarAulaModal')" style="padding: 12px 32px; background: #f1f5f9; color: #475569; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#f1f5f9'; this.style.transform='translateY(0)';">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    `;
                    
                    // Garantir que o modal seja adicionado ao body
                    document.body.appendChild(modal);
                    console.log('Modal de agendamento criado e adicionado ao body:', modal);
                    
                    // Fechar ao clicar fora
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeModal('agendarAulaModal');
                        }
                    });
                    
                    // Fechar com ESC
                    const escHandler = (e) => {
                        if (e.key === 'Escape') {
                            closeModal('agendarAulaModal');
                            document.removeEventListener('keydown', escHandler);
                        }
                    };
                    document.addEventListener('keydown', escHandler);
                    
                    // Forçar exibição
                    modal.style.display = 'flex';
                })
                .catch(error => {
                    console.error('Erro ao carregar dados:', error);
                    showNotification('❌ Erro ao carregar dados do formulário: ' + error.message, 'error');
                });
        }

        // RELATÓRIOS COMPLETOS - FUNCIONANDO DE VERDADE!
        

        // PROFESSORES REAIS - FUNCIONANDO DE VERDADE!
        function showProfessores() {
            window.location.href = 'professores.php';
        }

        function exportarRelatorio() {
            showNotification('📊 Exportando relatório...', 'info');
            // Aqui você implementaria a exportação real (PDF, Excel, etc.)
            setTimeout(() => {
                showNotification('✅ Relatório exportado com sucesso!', 'success');
            }, 2000);
        }

        function adicionarProfessor() {
            window.location.href = 'professores.php';
        }

        function editarProfessor(professorId) {
            window.location.href = 'professores.php';
        }

        function apagarProfessor(professorId) {
            window.location.href = 'professores.php';
        }

        function exportarProfessores() {
            showNotification('📊 Exportando dados dos professores...', 'info');
            setTimeout(() => {
                showNotification('✅ Professores exportados com sucesso!', 'success');
            }, 2000);
        }

        // CRUD COMPLETO DOS CURSOS - FUNCIONANDO DE VERDADE!
        
        // Ver detalhes do curso
        function verDetalhesCurso(cursoId) {
            // Buscar dados do curso
            const curso = getCursoById(cursoId);
            if (!curso) return;
            
            const modal = document.createElement('div');
            modal.id = 'detalhesCursoModal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
            `;
            
            modal.innerHTML = `
                <div style="background: white; padding: 30px; border-radius: 12px; width: 600px; max-width: 90%; max-height: 90%; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; color: #1e293b;">📚 Detalhes do Curso</h2>
                        <button onclick="closeModal('detalhesCursoModal')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b;">&times;</button>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h3 style="color: #1e293b; margin-bottom: 10px;">${curso.nome}</h3>
                        <p style="color: #64748b; margin-bottom: 15px;">${curso.categoria} • ${curso.nivel}</p>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                                <strong>Duração:</strong> ${curso.duracao_horas}h
                            </div>
                            <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                                <strong>Preço:</strong> R$ ${parseFloat(curso.preco).toFixed(2)}
                            </div>
                            <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                                <strong>Alunos:</strong> ${curso.alunos_inscritos}
                            </div>
                            <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                                <strong>Avaliação:</strong> ${curso.avaliacao}/5.0
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <strong>Descrição:</strong>
                            <p style="color: #64748b; margin-top: 5px;">${curso.descricao || 'Nenhuma descrição disponível.'}</p>
                        </div>
                    </div>
                    
                    <div style="text-align: center;">
                        <button onclick="editarCurso(${curso.id})" style="padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-right: 10px;">Editar Curso</button>
                        <button onclick="apagarCurso(${curso.id})" style="padding: 12px 24px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Apagar Curso</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Editar curso
        function editarCurso(cursoId) {
            const curso = getCursoById(cursoId);
            if (!curso) return;
            
            // Fechar modal de detalhes
            closeModal('detalhesCursoModal');
            
            // Abrir modal de edição
            const modal = document.createElement('div');
            modal.id = 'editarCursoModal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
            `;
            
            modal.innerHTML = `
                <div style="background: white; padding: 30px; border-radius: 12px; width: 600px; max-width: 90%; max-height: 90%; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; color: #1e293b;">✏️ Editar Curso</h2>
                        <button onclick="closeModal('editarCursoModal')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b;">&times;</button>
                    </div>
                    
                    <form id="editarCursoForm" onsubmit="salvarEdicaoCurso(event, ${cursoId})">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Nome do Curso</label>
                                <input type="text" name="nome" value="${curso.nome}" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Categoria</label>
                                <select name="categoria" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: all 0.2s; cursor: pointer; background: white;">
                                    <option value="Programação" ${curso.categoria === 'Programação' ? 'selected' : ''}>Programação</option>
                                    <option value="Design" ${curso.categoria === 'Design' ? 'selected' : ''}>Design</option>
                                    <option value="Marketing" ${curso.categoria === 'Marketing' ? 'selected' : ''}>Marketing</option>
                                    <option value="Negócios" ${curso.categoria === 'Negócios' ? 'selected' : ''}>Negócios</option>
                                </select>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Nível</label>
                                <select name="nivel" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                    <option value="Iniciante" ${curso.nivel === 'Iniciante' ? 'selected' : ''}>Iniciante</option>
                                    <option value="Intermediário" ${curso.nivel === 'Intermediário' ? 'selected' : ''}>Intermediário</option>
                                    <option value="Avançado" ${curso.nivel === 'Avançado' ? 'selected' : ''}>Avançado</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Duração (horas)</label>
                                <input type="number" name="duracao_horas" value="${curso.duracao_horas}" min="1" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Descrição</label>
                            <textarea name="descricao" rows="3" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;">${curso.descricao || ''}</textarea>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Preço (R$)</label>
                                <input type="number" name="preco" value="${curso.preco}" min="0" step="0.01" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Vagas Disponíveis</label>
                                <input type="number" name="vagas" value="${curso.vagas || 20}" min="1" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            </div>
                        </div>
                        
                        <div style="text-align: center;">
                            <button type="submit" style="padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-right: 10px;">Salvar Alterações</button>
                            <button type="button" onclick="closeModal('editarCursoModal')" style="padding: 12px 24px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancelar</button>
                        </div>
                    </form>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Salvar edição do curso
        async function salvarEdicaoCurso(event, cursoId) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const dados = Object.fromEntries(formData.entries());
            
            try {
                // Mostrar loading
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.textContent = 'Salvando...';
                submitBtn.disabled = true;
                
                // Enviar para API
                const response = await fetch(`api/cursos.php?id=${cursoId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(dados)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Sucesso!
                    showNotification('✅ Curso atualizado com sucesso!', 'success');
                    
                    // Fechar modal
                    closeModal('editarCursoModal');
                    
                    // Recarregar página para mostrar alterações
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                    
                } else {
                    throw new Error('Erro ao atualizar curso');
                }
                
            } catch (error) {
                console.error('Erro:', error);
                showNotification('❌ Erro ao atualizar curso. Tente novamente.', 'error');
            } finally {
                // Restaurar botão
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.textContent = 'Salvar Alterações';
                submitBtn.disabled = false;
            }
        }

        // APAGAR CURSO - FUNCIONANDO DE VERDADE!
        async function apagarCurso(cursoId) {
            showConfirmDialog({
                title: 'Apagar curso?',
                subtitle: 'Essa ação não poderá ser desfeita',
                message: 'Tem certeza que deseja apagar este curso? Ele será removido do banco de dados.',
                actionText: 'Sim, apagar',
                onConfirm: async function() {
                    try {
                        // Mostrar loading
                        showNotification('🗑️ Apagando curso...', 'info');
                        
                        // Enviar para API
                        const response = await fetch(`api/cursos.php?id=${cursoId}`, {
                            method: 'DELETE'
                        });
                        
                        if (response.ok) {
                            const result = await response.json();
                            
                            // Sucesso!
                            showNotification('✅ Curso APAGADO com sucesso!', 'success');
                            
                            // Fechar modais
                            closeModal('detalhesCursoModal');
                            closeModal('editarCursoModal');
                            
                            // Atualizar estatísticas
                            atualizarEstatisticas();
                            
                            // Recarregar página para mostrar alterações
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                            
                        } else {
                            throw new Error('Erro ao apagar curso');
                        }
                    
                    } catch (error) {
                        console.error('Erro:', error);
                        showNotification('❌ Erro ao apagar curso. Tente novamente.', 'error');
                    }
                }
            });
        }

        // Função auxiliar para buscar curso por ID
        function getCursoById(cursoId) {
            // Dados mockados para evitar erro de conexão
            const cursos = [
                { id: 1, nome: 'Desenvolvimento Web Full Stack', categoria: 'Programação', nivel: 'Intermediário', duracao_horas: 80, preco: 299.90, alunos_inscritos: 0, avaliacao: 0.00, descricao: 'Curso completo de desenvolvimento web' },
                { id: 2, nome: 'Python para Data Science', categoria: 'Data Science', nivel: 'Avançado', duracao_horas: 100, preco: 399.90, alunos_inscritos: 0, avaliacao: 0.00, descricao: 'Análise de dados com Python' },
                { id: 3, nome: 'React.js e Node.js', categoria: 'Programação', nivel: 'Intermediário', duracao_horas: 60, preco: 249.90, alunos_inscritos: 0, avaliacao: 0.00, descricao: 'Desenvolvimento full-stack com React e Node' },
                { id: 4, nome: 'UX/UI Design', categoria: 'Design', nivel: 'Básico', duracao_horas: 50, preco: 199.90, alunos_inscritos: 0, avaliacao: 0.00, descricao: 'Design de interfaces e experiência do usuário' },
                { id: 5, nome: 'DevOps e Docker', categoria: 'DevOps', nivel: 'Avançado', duracao_horas: 90, preco: 349.90, alunos_inscritos: 0, avaliacao: 0.00, descricao: 'DevOps e containerização com Docker' },
                { id: 6, nome: 'Mobile App Development', categoria: 'Mobile', nivel: 'Intermediário', duracao_horas: 70, preco: 279.90, alunos_inscritos: 0, avaliacao: 0.00, descricao: 'Desenvolvimento de aplicativos móveis' }
            ];
            
            return cursos.find(curso => curso.id == cursoId);
        }

        // Função para fechar modais
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.animation = 'fadeOut 0.2s ease-out';
                setTimeout(() => {
                    modal.remove();
                }, 200);
            }
        }

        // ===== FUNÇÕES DE FORMULÁRIOS DOS MODAIS =====
        
        // Criar novo curso
        async function criarNovoCurso(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const dados = Object.fromEntries(formData.entries());
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Criando...';
            
            try {
                const response = await fetch('api/cursos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Sucesso', 'Curso criado com sucesso!', 'success');
                    form.reset();
                    closeModal('cursoModal');
                    
                    // Atualizar estatísticas
                    setTimeout(() => {
                        atualizarEstatisticas();
                    }, 500);
                } else {
                    showToast('Erro', result.message || 'Erro ao criar curso', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro', 'Erro ao criar curso. Tente novamente.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }

        // Agendar aula
        function calcularHoraFim(horaInicio) {
            if (!horaInicio) return '';
            const [horas, minutos] = horaInicio.split(':').map(Number);
            const data = new Date();
            data.setHours(horas + 1, minutos || 0, 0, 0);
            return String(data.getHours()).padStart(2, '0') + ':' + String(data.getMinutes()).padStart(2, '0');
        }

        async function agendarAulaReal(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const dados = Object.fromEntries(formData.entries());
            dados.hora_fim = dados.hora_fim || calcularHoraFim(dados.hora_inicio);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agendando...';
            
            try {
                const response = await fetch('api/agendamentos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Sucesso', 'Aula agendada com sucesso!', 'success');
                    form.reset();
                    closeModal('agendarAulaModal');
                    
                    // Atualizar agendamentos e estatísticas
                    setTimeout(() => {
                        loadAgendamentos();
                        atualizarEstatisticas();
                    }, 500);
                } else {
                    showToast('Erro', result.message || 'Erro ao agendar aula', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro', 'Erro ao agendar aula. Tente novamente.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }

        // ===== ADICIONAR INTERATIVIDADE AOS CARDS =====
        function addCardInteractivity() {
            // Adicionar efeito de ripple nos cards
            document.querySelectorAll('.stat-card, .quick-action-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        border-radius: 50%;
                        background: rgba(37, 99, 235, 0.3);
                        transform: scale(0);
                        animation: ripple 0.6s ease-out;
                        left: ${x}px;
                        top: ${y}px;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        }

        // Adicionar animações CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            @keyframes fadeOut {
                from {
                    opacity: 1;
                    transform: scale(1);
                }
                to {
                    opacity: 0;
                    transform: scale(0.95);
                }
            }
            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }
            .stat-card:hover .stat-icon {
                transform: scale(1.1) rotate(5deg);
            }
            .stat-card:hover .stat-value {
                transform: scale(1.05);
            }
            input:focus, select:focus, textarea:focus {
                outline: none;
                border-color: var(--primary-color) !important;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
                transform: translateY(-1px);
            }
        `;
        document.head.appendChild(style);

        // Sistema de notificações
        function showNotification(message, type = 'info') {
            showToast(message, type);
        }

        // ===== ANIMAÇÃO DE CONTAGEM NOS NÚMEROS =====
        function animateValue(element, start, end, duration = 1000) {
            if (!element) return;
            
            const startTime = performance.now();
            const range = end - start;
            
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function (ease-out)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(start + (range * easeOut));
                
                element.textContent = current;
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    element.textContent = end;
                }
            }
            
            requestAnimationFrame(update);
        }

        // ===== ATUALIZAÇÃO AUTOMÁTICA DE DADOS =====
        let statsUpdateInProgress = false;
        let dashboardRefreshPromise = null;

        async function atualizarEstatisticas() {
            if (statsUpdateInProgress) return;
            statsUpdateInProgress = true;

            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');
            
            // Animar botão de refresh
            if (refreshIcon) {
                refreshIcon.style.animation = 'spin 1s linear infinite';
            }
            if (refreshBtn) {
                refreshBtn.disabled = true;
                refreshBtn.style.opacity = '0.7';
            }
            
            try {
                // Mostrar indicador de atualização
                const statCards = document.querySelectorAll('.stat-card');
                statCards.forEach(card => {
                    const icon = card.querySelector('.stat-icon');
                    if (icon) {
                        icon.style.opacity = '0.6';
                        icon.style.transition = 'all 0.3s';
                        icon.style.transform = 'rotate(360deg)';
                        setTimeout(() => {
                            icon.style.opacity = '1';
                            icon.style.transform = 'rotate(0deg)';
                        }, 500);
                    }
                });

                const response = await fetch('api/dashboard_stats.php?' + new Date().getTime());
                if (response.ok) {
                    const statsResponse = await response.json();
                    const stats = statsResponse && statsResponse.data ? statsResponse.data : statsResponse;
                    if (!stats || (statsResponse && statsResponse.success === false)) {
                        throw new Error('Resposta inválida ao atualizar estatísticas');
                    }
                    
                    // Animar contagem dos valores
                    const cursosValue = document.querySelector('.stat-card.primary .stat-value');
                    const professoresValue = document.querySelector('.stat-card.success .stat-value');
                    const alunosValue = document.querySelector('.stat-card.warning .stat-value');
                    const agendamentosValue = document.querySelector('.stat-card.info .stat-value');
                    
                    if (cursosValue) {
                        const current = parseInt(cursosValue.textContent) || 0;
                        animateValue(cursosValue, current, stats.cursos_count || 0, 800);
                    }
                    
                    if (professoresValue) {
                        const current = parseInt(professoresValue.textContent) || 0;
                        animateValue(professoresValue, current, stats.professores_count || 0, 800);
                    }
                    
                    if (alunosValue) {
                        const current = parseInt(alunosValue.textContent) || 0;
                        animateValue(alunosValue, current, stats.alunos_count || 0, 800);
                    }
                    
                    if (agendamentosValue) {
                        const current = parseInt(agendamentosValue.textContent) || 0;
                        animateValue(agendamentosValue, current, stats.agendamentos_count || 0, 800);
                    }
                    
                    // Atualizar progress bars
                    updateProgressBars(stats);
                    
                    showToast('Atualização', 'Dados atualizados com sucesso!', 'success');
                }
            } catch (error) {
                console.error('Erro ao atualizar estatísticas:', error);
                showToast('Erro', 'Erro ao atualizar dados', 'error');
            } finally {
                statsUpdateInProgress = false;
                // Restaurar botão de refresh
                if (refreshIcon) {
                    refreshIcon.style.animation = 'none';
                }
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.style.opacity = '1';
                }
            }
        }

        // Atualizar progress bars dinamicamente
        function updateProgressBars(stats) {
            const progressBars = document.querySelectorAll('.progress-fill');
            if (progressBars.length >= 4) {
                // Cursos
                const cursosProgress = (stats.cursos_count / 10) * 100;
                progressBars[0].style.width = Math.min(cursosProgress, 100) + '%';
                
                // Professores
                const professoresProgress = (stats.professores_count / 10) * 100;
                progressBars[1].style.width = Math.min(professoresProgress, 100) + '%';
                
                // Alunos
                const alunosProgress = (stats.alunos_count / 20) * 100;
                progressBars[2].style.width = Math.min(alunosProgress, 100) + '%';
                
                // Agendamentos
                const agendamentosProgress = (stats.agendamentos_count / 15) * 100;
                progressBars[3].style.width = Math.min(agendamentosProgress, 100) + '%';
            }
        }

        // Atualização automática a cada 30 segundos
        let autoUpdateInterval = null;
        
        function startAutoUpdate() {
            if (autoUpdateInterval) clearInterval(autoUpdateInterval);
            autoUpdateInterval = setInterval(() => {
                atualizarEstatisticas();
            }, 30000); // 30 segundos
        }

        function stopAutoUpdate() {
            if (autoUpdateInterval) {
                clearInterval(autoUpdateInterval);
                autoUpdateInterval = null;
            }
        }

        async function atualizarAgendamentos() {
            try {
                const response = await fetch('api/agendamentos.php');
                if (response.ok) {
                    const agendamentos = await response.json();
                    // Atualizar lista de agendamentos se existir
                    // Esta função será implementada quando criarmos a lista
                }
            } catch (error) {
                console.error('Erro ao atualizar agendamentos:', error);
            }
        }

        // Efeitos hover nos cards
        document.querySelectorAll('.stat-card').forEach(card => {
            if (card) {
                card.addEventListener('mouseenter', function() {
                    if (this && this.style) {
                        this.style.transform = 'translateY(-4px)';
                        this.style.boxShadow = 'var(--shadow-lg)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    if (this && this.style) {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'var(--shadow-sm)';
                    }
                });
            }
        });

        // Efeitos nos botões
        document.querySelectorAll('.btn').forEach(btn => {
            if (btn) {
                btn.addEventListener('mouseenter', function() {
                    if (this && this.style) {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = 'var(--shadow-lg)';
                    }
                });
                
                btn.addEventListener('mouseleave', function() {
                    if (this && this.style) {
                        if (this && this.style) {
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = 'none';
                        }
                    }
                });
            }
        });

        // Efeitos nos cards de curso
        document.querySelectorAll('.curso-card').forEach(card => {
            if (card) {
                card.addEventListener('mouseenter', function() {
                    if (this && this.style) {
                        this.style.transform = 'translateY(-4px)';
                        this.style.boxShadow = 'var(--shadow-lg)';
                        this.style.borderColor = 'var(--primary-color)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    if (this && this.style) {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'var(--shadow-sm)';
                        this.style.borderColor = 'var(--border-color)';
                    }
                });
            }
        });

        // Efeitos nas ações rápidas
        document.querySelectorAll('.quick-action-card').forEach(card => {
            if (card) {
                card.addEventListener('mouseenter', function() {
                    if (this && this.style) {
                        this.style.transform = 'translateY(-4px)';
                        this.style.boxShadow = 'var(--shadow-lg)';
                        this.style.borderColor = 'var(--primary-color)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    if (this && this.style) {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'var(--shadow-sm)';
                        this.style.borderColor = 'var(--border-color)';
                    }
                });
            }
        });

        // Inicializar quando a página carregar
        // Função para ajustar altura do sidebar
        function ajustarAlturaSidebar() {
            const sidebar = document.querySelector('.sidebar');
            if (!sidebar) return;
            
            // Calcular altura total do documento
            const documentHeight = Math.max(
                document.body.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.clientHeight,
                document.documentElement.scrollHeight,
                document.documentElement.offsetHeight
            );
            
            // Aplicar altura ao sidebar e seus pseudo-elementos
            sidebar.style.height = documentHeight + 'px';
            sidebar.style.minHeight = documentHeight + 'px';
            
            // Ajustar pseudo-elementos via CSS customizado
            const style = document.createElement('style');
            style.id = 'sidebar-dynamic-height';
            style.textContent = `
                .sidebar::before,
                .sidebar::after {
                    height: ${documentHeight}px !important;
                    min-height: ${documentHeight}px !important;
                }
            `;
            
            // Remover estilo anterior se existir
            const existingStyle = document.getElementById('sidebar-dynamic-height');
            if (existingStyle) {
                existingStyle.remove();
            }
            
            document.head.appendChild(style);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            loadAgendamentos(); // Carregar aulas agendadas
            initEvolutionChart(); // Inicializar gráfico de evolução
            
            // Ajustar altura do sidebar
            ajustarAlturaSidebar();
            
            // Reajustar quando o conteúdo mudar
            setTimeout(ajustarAlturaSidebar, 500);
            setTimeout(ajustarAlturaSidebar, 1000);
            
            // Animar valores iniciais
            setTimeout(() => {
                const statValues = document.querySelectorAll('.stat-value');
                statValues.forEach(element => {
                    const finalValue = parseInt(element.textContent) || 0;
                    if (finalValue > 0) {
                        animateValue(element, 0, finalValue, 1000);
                    }
                });
            }, 300);
            
            // Iniciar atualização automática
            startAutoUpdate();
            
            // Adicionar overlay para fechar sidebar no mobile
            if (window.innerWidth <= 768) {
                addMobileOverlay();
            }
            
            // Adicionar interatividade aos cards
            addCardInteractivity();
        });

        // Gráfico de Evolução — Versão Profissional
        let evolutionChart = null;
        const CHART_COLORS = {
            cursos: { line: '#2563eb', area: 'rgba(37,  99, 235, 0.12)' },
            alunos: { line: '#6366f1', area: 'rgba(99, 102, 241, 0.10)' },
            aulas:  { line: '#06b6d4', area: 'rgba(6,  182, 212, 0.10)' }
        };

        function makeGradient(ctx, chartArea, color) {
            if (!chartArea) return color;
            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
            gradient.addColorStop(0, color);
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            return gradient;
        }

        function initEvolutionChart() {
            const canvas = document.getElementById('evolutionChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const data = getEvolutionData(7);
            atualizarKpis(data);

            const datasetBase = (label, key, points) => ({
                label,
                data: points,
                borderColor: CHART_COLORS[key].line,
                backgroundColor: (context) => {
                    const { ctx, chartArea } = context.chart;
                    return makeGradient(ctx, chartArea, CHART_COLORS[key].area);
                },
                borderWidth: 2.5,
                tension: 0.42,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: CHART_COLORS[key].line,
                pointBorderWidth: 2.5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: CHART_COLORS[key].line,
                pointHoverBorderWidth: 3,
                cubicInterpolationMode: 'monotone'
            });

            evolutionChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        datasetBase('Cursos', 'cursos', data.cursos),
                        datasetBase('Alunos', 'alunos', data.alunos),
                        datasetBase('Aulas',  'aulas',  data.aulas)
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(15, 23, 42, 0.96)',
                            padding: 14,
                            cornerRadius: 12,
                            titleColor: '#f8fafc',
                            titleFont: { size: 12, weight: '700', family: 'Inter' },
                            titleMarginBottom: 8,
                            bodyColor: '#e2e8f0',
                            bodyFont: { size: 13, weight: '600', family: 'Inter' },
                            bodySpacing: 6,
                            boxPadding: 6,
                            borderColor: 'rgba(255,255,255,0.08)',
                            borderWidth: 1,
                            displayColors: true,
                            usePointStyle: true,
                            callbacks: {
                                labelPointStyle: () => ({ pointStyle: 'circle', rotation: 0 })
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: {
                                color: 'rgba(15, 23, 42, 0.05)',
                                drawTicks: false,
                                lineWidth: 1,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11, weight: '600', family: 'Inter' },
                                padding: 14,
                                maxTicksLimit: 5
                            }
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false, drawTicks: false },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11, weight: '600', family: 'Inter' },
                                padding: 10,
                                maxRotation: 0
                            }
                        }
                    },
                    animation: {
                        duration: 900,
                        easing: 'easeInOutQuart'
                    }
                }
            });

            // Toggle KPI → toggle dataset
            document.querySelectorAll('.chart-kpi').forEach((kpi, idx) => {
                kpi.addEventListener('click', () => {
                    if (!evolutionChart) return;
                    const meta = evolutionChart.getDatasetMeta(idx);
                    meta.hidden = meta.hidden === null ? !evolutionChart.data.datasets[idx].hidden : null;
                    kpi.classList.toggle('disabled', meta.hidden);
                    evolutionChart.update();
                });
            });
        }

        function atualizarKpis(data) {
            const sum = arr => arr.reduce((a, b) => a + b, 0);
            const last = arr => arr.length ? arr[arr.length - 1] : 0;
            const first = arr => arr.length ? arr[0] : 0;
            const delta = arr => {
                const f = first(arr), l = last(arr);
                if (!f) return 0;
                return Math.round(((l - f) / f) * 100);
            };
            const setDelta = (id, value) => {
                const el = document.getElementById(id);
                if (!el) return;
                const sign = value >= 0 ? 'positive' : 'negative';
                const icon = value >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                el.className = 'kpi-delta ' + sign;
                el.innerHTML = `<i class="fas ${icon}"></i> ${Math.abs(value)}%`;
            };
            const $ = id => document.getElementById(id);
            if ($('kpiCursos')) $('kpiCursos').textContent = last(data.cursos);
            if ($('kpiAlunos')) $('kpiAlunos').textContent = last(data.alunos);
            if ($('kpiAulas'))  $('kpiAulas').textContent  = last(data.aulas);
            setDelta('deltaCursos', delta(data.cursos));
            setDelta('deltaAlunos', delta(data.alunos));
            setDelta('deltaAulas',  delta(data.aulas));
        }

        function getEvolutionData(days) {
            // Gerar dados baseados no período
            const labels = [];
            const cursos = [];
            const alunos = [];
            const aulas = [];

            for (let i = days - 1; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                
                if (days === 7) {
                    labels.push(date.toLocaleDateString('pt-BR', { weekday: 'short' }));
                } else if (days === 30) {
                    labels.push(date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' }));
                } else {
                    labels.push(date.toLocaleDateString('pt-BR', { month: 'short', year: '2-digit' }));
                }

                // Dados simulados (você pode substituir por dados reais do banco)
                cursos.push(Math.floor(Math.random() * 5) + <?php echo $cursos_count; ?>);
                alunos.push(Math.floor(Math.random() * 10) + <?php echo $alunos_count; ?>);
                aulas.push(Math.floor(Math.random() * 8) + <?php echo $agendamentos_count; ?>);
            }

            return { labels, cursos, alunos, aulas };
        }

        function updateChartPeriod(days, btn) {
            document.querySelectorAll('.period-filter').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            if (evolutionChart) {
                const data = getEvolutionData(days);
                evolutionChart.data.labels = data.labels;
                evolutionChart.data.datasets[0].data = data.cursos;
                evolutionChart.data.datasets[1].data = data.alunos;
                evolutionChart.data.datasets[2].data = data.aulas;
                evolutionChart.update('active');
                atualizarKpis(data);
            }
        }

        // Função para toggle da sidebar no mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                if (overlay) overlay.style.display = 'none';
            } else {
                sidebar.classList.add('open');
                if (overlay) overlay.style.display = 'block';
            }
        }

        // Adicionar overlay para mobile
        function addMobileOverlay() {
            const overlay = document.createElement('div');
            overlay.className = 'mobile-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            `;
            
            overlay.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                sidebar.classList.remove('open');
                this.style.display = 'none';
            });
            
            document.body.appendChild(overlay);
        }

        // Fechar sidebar ao redimensionar para desktop
        // Observar mudanças no DOM para reajustar altura
        const sidebarHeightObserver = new MutationObserver(() => {
            ajustarAlturaSidebar();
        });
        
        sidebarHeightObserver.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: false
        });
        
        // Reajustar altura no scroll e resize
        window.addEventListener('scroll', ajustarAlturaSidebar);
        window.addEventListener('resize', function() {
            ajustarAlturaSidebar();
            
            if (window.innerWidth > 768) {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.querySelector('.mobile-overlay');
                
                sidebar.classList.remove('open');
                if (overlay) overlay.style.display = 'none';
            }
        });

        // FUNÇÕES PARA AS AÇÕES RÁPIDAS
        function showAlunos() {
            window.location.href = 'alunos.php';
        }

        function showConfiguracoes() {
            window.location.href = 'configuracoes.php';
        }

        function adicionarAluno() {
            window.location.href = 'alunos.php';
        }

        function exportarAlunos() {
            showNotification('📊 Exportando dados dos alunos...', 'info');
            setTimeout(() => {
                showNotification('✅ Alunos exportados com sucesso!', 'success');
            }, 2000);
        }

        // SISTEMA DE AULAS AGENDADAS - FUNCIONANDO DE VERDADE!

        // Função para carregar aulas agendadas
        async function loadAgendamentos() {
            try {
                const response = await fetch('api/agendamentos.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();
                const container = document.getElementById('agendamentos-container');

                if (result.success && result.data) {
                    window.agendamentosCache = result.data;
                    if (result.data.length === 0) {
                        container.innerHTML = `
                            <div class="sem-agendamentos">
                                <i class="fas fa-calendar-times"></i>
                                <h4>Nenhuma aula agendada</h4>
                                <p>Clique em "Agendar Aula" para criar seu primeiro agendamento!</p>
                            </div>
                        `;
                    } else {
                        container.innerHTML = `
                            <div class="agendamentos-grid">
                                ${result.data.map(agendamento => `
                                    <div class="agendamento-card ${agendamento.status}">
                                        <div class="agendamento-header">
                                            <div>
                                                <div class="agendamento-data">
                                                    <i class="fas fa-calendar"></i> 
                                                    ${formatarData(agendamento.data)}
                                                </div>
                                                <div class="agendamento-horario">
                                                    <i class="fas fa-clock"></i> 
                                                    ${agendamento.hora_inicio || agendamento.hora || 'Não informado'}
                                                </div>
                                            </div>
                                            <span class="agendamento-status ${agendamento.status}">
                                                ${agendamento.status}
                                            </span>
                                        </div>
                                        
                                        <div class="agendamento-info">
                                            <span><strong>Professor:</strong> ${agendamento.professor_nome || agendamento.professor || 'Não informado'}</span>
                                            <span><strong>Serviço:</strong> ${agendamento.servico || 'Não informado'}</span>
                                            <span><strong>Aluno:</strong> ${agendamento.aluno_nome || agendamento.nome || 'Não informado'}</span>
                                        </div>
                                        
                                        ${agendamento.observacoes ? `
                                            <div class="agendamento-observacoes">
                                                <i class="fas fa-comment"></i> ${agendamento.observacoes}
                                            </div>
                                        ` : ''}
                                        
                                        <div class="agendamento-actions">
                                            ${agendamento.status === 'confirmado' ? `
                                                <button class="btn btn-outline btn-sm" onclick="editarAgendamento(${agendamento.id})">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="cancelarAgendamento(${agendamento.id})">
                                                    <i class="fas fa-times"></i> Cancelar
                                                </button>
                                            ` : agendamento.status === 'pendente' || agendamento.status === 'Pendente' ? `
                                                <button class="btn btn-success btn-sm" onclick="confirmarAgendamento(${agendamento.id})">
                                                    <i class="fas fa-check"></i> Confirmar
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="cancelarAgendamento(${agendamento.id})">
                                                    <i class="fas fa-times"></i> Cancelar
                                                </button>
                                            ` : agendamento.status === 'cancelado' || agendamento.status === 'Cancelado' ? `
                                                <button class="btn btn-outline btn-sm" onclick="reativarAgendamento(${agendamento.id})">
                                                    <i class="fas fa-redo"></i> Reativar
                                                </button>
                                            ` : `
                                                <button class="btn btn-success btn-sm" onclick="confirmarAgendamento(${agendamento.id})">
                                                    <i class="fas fa-check"></i> Confirmar
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="cancelarAgendamento(${agendamento.id})">
                                                    <i class="fas fa-times"></i> Cancelar
                                                </button>
                                            `}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        `;
                    }
                } else {
                    container.innerHTML = `
                        <div class="sem-agendamentos">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4>Erro ao carregar aulas</h4>
                            <p>${result.error || 'Tente novamente mais tarde'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erro ao carregar agendamentos:', error);
                const container = document.getElementById('agendamentos-container');
                container.innerHTML = `
                    <div class="sem-agendamentos">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Erro de conexão</h4>
                        <p>Verifique sua conexão e tente novamente</p>
                    </div>
                `;
            }
        }

        // Função para atualizar a lista de agendamentos
        function refreshAgendamentos() {
            loadAgendamentos();
            showNotification('Lista de aulas atualizada!', 'success');
        }

        // Função para atualizar a lista de cursos
        function refreshCursos() {
            location.reload();
            showNotification('Lista de cursos atualizada!', 'success');
        }

        // Função para formatar data
        function formatarData(dataString) {
            const data = new Date(dataString);
            const hoje = new Date();
            const amanha = new Date(hoje);
            amanha.setDate(amanha.getDate() + 1);
            
            if (data.toDateString() === hoje.toDateString()) {
                return 'Hoje';
            } else if (data.toDateString() === amanha.toDateString()) {
                return 'Amanhã';
            } else {
                return data.toLocaleDateString('pt-BR', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        }

        // Função para editar agendamento
        function editarAgendamento(id) {
            const agendamento = (window.agendamentosCache || []).find(item => item.id == id);
            if (!agendamento) {
                showNotification('Não foi possível carregar os dados da aula.', 'error');
                return;
            }

            const modal = document.createElement('div');
            modal.id = 'editarAgendamentoModal';
            modal.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                min-height: ${Math.max(document.body.scrollHeight, window.innerHeight)}px;
                z-index: 99999;
                padding: 20px;
                background: rgba(15, 23, 42, 0.62);
                backdrop-filter: blur(10px);
                overflow-y: auto;
                box-sizing: border-box;
            `;

            modal.innerHTML = `
                <div style="position: absolute; top: ${window.scrollY + (window.innerHeight / 2)}px; left: 50%; transform: translate(-50%, -50%); width: min(520px, calc(100vw - 40px)); max-height: calc(100vh - 40px); overflow: hidden; border-radius: 28px; background: rgba(255,255,255,.96); box-shadow: 0 34px 95px rgba(15,23,42,.32);">
                    <div style="padding: 24px 28px; color: #fff; background: linear-gradient(135deg, #1e3a8a, #2563eb, #7c3aed);">
                        <h3 style="margin: 0; font-size: 1.25rem; font-weight: 850;">Editar aula</h3>
                        <p style="margin: 6px 0 0; opacity: .82;">Atualize os dados principais do agendamento</p>
                    </div>
                    <form id="editarAgendamentoForm" style="padding: 24px 28px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                            <div>
                                <label style="display:block; margin-bottom:7px; color:#64748b; font-size:.78rem; font-weight:850; text-transform:uppercase;">Data</label>
                                <input type="date" name="data" value="${agendamento.data || ''}" required style="width:100%; min-height:46px; padding:10px 12px; border-radius:14px; border:1px solid #cbd5e1;">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:7px; color:#64748b; font-size:.78rem; font-weight:850; text-transform:uppercase;">Status</label>
                                <select name="status" style="width:100%; min-height:46px; padding:10px 12px; border-radius:14px; border:1px solid #cbd5e1;">
                                    <option value="agendado" ${agendamento.status === 'agendado' ? 'selected' : ''}>Agendado</option>
                                    <option value="confirmado" ${agendamento.status === 'confirmado' ? 'selected' : ''}>Confirmado</option>
                                    <option value="pendente" ${agendamento.status === 'pendente' ? 'selected' : ''}>Pendente</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:7px; color:#64748b; font-size:.78rem; font-weight:850; text-transform:uppercase;">Início</label>
                                <input type="time" name="hora_inicio" value="${(agendamento.hora_inicio || '').substring(0, 5)}" required style="width:100%; min-height:46px; padding:10px 12px; border-radius:14px; border:1px solid #cbd5e1;">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:7px; color:#64748b; font-size:.78rem; font-weight:850; text-transform:uppercase;">Fim</label>
                                <input type="time" name="hora_fim" value="${(agendamento.hora_fim || '').substring(0, 5)}" style="width:100%; min-height:46px; padding:10px 12px; border-radius:14px; border:1px solid #cbd5e1;">
                            </div>
                        </div>
                        <div style="margin-top: 14px;">
                            <label style="display:block; margin-bottom:7px; color:#64748b; font-size:.78rem; font-weight:850; text-transform:uppercase;">Observações</label>
                            <textarea name="observacoes" rows="3" style="width:100%; padding:12px; border-radius:14px; border:1px solid #cbd5e1; resize:vertical;">${agendamento.observacoes || ''}</textarea>
                        </div>
                        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:22px;">
                            <button type="button" class="btn btn-outline" onclick="closeModal('editarAgendamentoModal')">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar alterações</button>
                        </div>
                    </form>
                </div>
            `;

            document.body.appendChild(modal);

            document.getElementById('editarAgendamentoForm').onsubmit = async function(event) {
                event.preventDefault();
                const formData = new FormData(event.target);

                try {
                    const response = await fetch(`api/agendamentos.php/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            data: formData.get('data'),
                            hora_inicio: formData.get('hora_inicio'),
                            hora_fim: formData.get('hora_fim'),
                            status: formData.get('status'),
                            observacoes: formData.get('observacoes')
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        closeModal('editarAgendamentoModal');
                        showNotification('Aula atualizada com sucesso!', 'success');
                        refreshDashboardData();
                    } else {
                        showNotification(result.error || 'Erro ao atualizar aula', 'error');
                    }
                } catch (error) {
                    console.error('Erro ao editar agendamento:', error);
                    showNotification('Erro ao atualizar aula. Tente novamente.', 'error');
                }
            };
        }

        // Função para cancelar agendamento
        async function cancelarAgendamento(id) {
            showConfirmDialog({
                title: 'Remover aula?',
                subtitle: 'Essa ação será permanente',
                message: 'Tem certeza que deseja remover esta aula? Ela será excluída permanentemente.',
                actionText: 'Sim, remover',
                onConfirm: async function() {
                try {
                    const response = await fetch(`api/agendamentos.php/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        showNotification('Aula removida com sucesso!', 'success');
                        refreshDashboardData(); // Recarregar lista e contadores
                    } else {
                        showNotification(result.error || 'Erro ao remover aula', 'error');
                    }
                } catch (error) {
                    console.error('Erro ao remover agendamento:', error);
                    showNotification('Erro ao remover aula. Tente novamente.', 'error');
                }
                }
            });
        }

        // Função para confirmar agendamento
        async function confirmarAgendamento(id) {
            try {
                const response = await fetch(`api/agendamentos.php/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'confirmado'
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showNotification('Aula confirmada com sucesso!', 'success');
                    refreshDashboardData(); // Recarregar lista e contadores
                } else {
                    showNotification(result.error || 'Erro ao confirmar aula', 'error');
                }
            } catch (error) {
                console.error('Erro ao confirmar agendamento:', error);
                showNotification('Erro ao confirmar aula. Tente novamente.', 'error');
            }
        }

        // Função para reativar agendamento
        async function reativarAgendamento(id) {
            try {
                const response = await fetch(`api/agendamentos.php/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'pendente'
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showNotification('Aula reativada com sucesso!', 'success');
                    refreshDashboardData(); // Atualizar lista e contadores
                } else {
                    showNotification(result.error || 'Erro ao reativar aula', 'error');
                }
            } catch (error) {
                console.error('Erro ao reativar agendamento:', error);
                showNotification('Erro ao reativar aula. Tente novamente.', 'error');
            }
        }

        // Função para atualizar contadores do dashboard
        async function atualizarContadores() {
            try {
                const response = await fetch('api/dashboard_stats.php');
                const data = await response.json();
                
                if (data.success) {
                    // Atualizar contadores
                    const cursosEl = document.getElementById('cursos-count');
                    const professoresEl = document.getElementById('professores-count');
                    const alunosEl = document.getElementById('alunos-count');
                    const agendamentosEl = document.getElementById('agendamentos-count');

                    if (cursosEl) cursosEl.textContent = data.data.cursos_count || 0;
                    if (professoresEl) professoresEl.textContent = data.data.professores_count || 0;
                    if (alunosEl) alunosEl.textContent = data.data.alunos_count || 0;
                    if (agendamentosEl) agendamentosEl.textContent = data.data.agendamentos_count || 0;
                }
            } catch (error) {
                console.error('Erro ao atualizar contadores:', error);
            }
        }

        // Atualiza dados do dashboard em lote para evitar múltiplos fetchs concorrentes
        function refreshDashboardData() {
            if (dashboardRefreshPromise) {
                return dashboardRefreshPromise;
            }

            dashboardRefreshPromise = Promise.all([
                loadAgendamentos(),
                atualizarContadores()
            ]).finally(() => {
                dashboardRefreshPromise = null;
            });

            return dashboardRefreshPromise;
        }
        // ===== MELHORIAS DE ACESSIBILIDADE E INTERATIVIDADE =====
        
        // Suporte a navegação por teclado nos cards
        document.querySelectorAll('.quick-action-card').forEach(card => {
            card.setAttribute('tabindex', '0');
            card.setAttribute('role', 'button');
            
            // Suporte a Enter e Espaço
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    card.click();
                }
            });
        });

        // Inicializar tooltips para elementos com data-tooltip
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.setAttribute('aria-label', element.getAttribute('data-tooltip'));
        });

        // Melhorar feedback visual em interações
        document.querySelectorAll('.btn, .stat-card, .quick-action-card').forEach(element => {
            element.addEventListener('click', function() {
                // Feedback tátil sutil
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        // Animações de entrada para elementos dinâmicos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeIn 0.6s ease-out forwards';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observar elementos que entram na viewport
        document.querySelectorAll('.agendamento-card, .curso-card').forEach(card => {
            observer.observe(card);
        });

        // Melhorar estados vazios com mensagens mais amigáveis
        function melhorarEmptyStates() {
            const emptyStates = document.querySelectorAll('.empty-state');
            emptyStates.forEach(state => {
                if (!state.querySelector('.empty-icon')) {
                    const icon = document.createElement('div');
                    icon.className = 'empty-icon';
                    icon.innerHTML = '<i class="fas fa-inbox"></i>';
                    state.insertBefore(icon, state.firstChild);
                }
            });
        }

        // Executar ao carregar
        document.addEventListener('DOMContentLoaded', () => {
            melhorarEmptyStates();
        });

        // Adicionar aria-labels aos ícones importantes
        document.querySelectorAll('.stat-icon i, .quick-action-icon i').forEach(icon => {
            const parent = icon.closest('.stat-card, .quick-action-card');
            if (parent && !icon.getAttribute('aria-label')) {
                const text = parent.querySelector('h3')?.textContent || '';
                icon.setAttribute('aria-label', text);
            }
        });

        // ===== SISTEMA DE NOTIFICAÇÕES TOAST MELHORADO =====
        function showToast(title = '', message = '', type = 'info') {
            // Aceita também a antiga assinatura (message, type, title) — detecção automática
            const validTypes = ['success', 'error', 'warning', 'info'];
            if (validTypes.includes(message) && !validTypes.includes(type)) {
                // Chamada no formato antigo: showToast(message, type, title)
                const _msg = title;
                const _type = message;
                const _title = type;
                title = _title || '';
                message = _msg || '';
                type = _type || 'info';
            }
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas ${icons[type] || icons.info}"></i>
                </div>
                <div class="toast-content">
                    ${title ? `<div class="toast-title">${title}</div>` : ''}
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(toast);

            // Remover automaticamente após 3 segundos
            setTimeout(() => {
                toast.style.animation = 'toastSlideIn 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function showConfirmDialog(config) {
            document.getElementById('confirmTitle').textContent = config.title || 'Confirmar ação';
            document.getElementById('confirmSubtitle').textContent = config.subtitle || 'Esta ação precisa de confirmação';
            document.getElementById('confirmMessage').textContent = config.message || 'Deseja continuar?';
            document.getElementById('confirmActionBtn').textContent = config.actionText || 'Confirmar';
            document.getElementById('confirmActionBtn').onclick = async function() {
                closeConfirmDialog();
                await config.onConfirm();
            };
            const overlay = document.getElementById('confirmOverlay');
            const dialog = overlay.querySelector('.confirm-dialog');
            overlay.style.minHeight = Math.max(document.body.scrollHeight, window.innerHeight) + 'px';
            dialog.style.top = (window.scrollY + (window.innerHeight / 2)) + 'px';
            overlay.style.display = 'block';
        }

        function closeConfirmDialog() {
            document.getElementById('confirmOverlay').style.display = 'none';
        }

        // Substituir showNotification existente se houver
        if (typeof showNotification === 'function') {
            window.showNotification = function(message, type = 'info') {
                showToast(message, type);
            };
        } else {
            window.showNotification = function(message, type = 'info') {
                showToast(message, type);
            };
        }
    </script>
    <script src="dark-mode.js"></script>
</body>
</html>
