<?php
session_start();

// Verificar se o usuário está logado e é aluno
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Buscar dados do aluno
$aluno_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

// Buscar todas as aulas do aluno
$aulas_query = "SELECT a.*, c.nome as curso_nome, c.id as curso_id, c.duracao_horas, u.nome as professor_nome 
                FROM agendamentos a 
                JOIN cursos c ON a.curso_id = c.id 
                JOIN usuarios u ON a.professor_id = u.id 
                WHERE a.aluno_id = ? 
                ORDER BY a.data_agendamento DESC, a.hora_inicio DESC";
$stmt = $conn->prepare($aulas_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$todas_aulas = $stmt->get_result();

// Buscar próximas aulas
$proximas_query = "SELECT a.*, c.nome as curso_nome, c.id as curso_id, c.duracao_horas, u.nome as professor_nome 
                   FROM agendamentos a 
                   JOIN cursos c ON a.curso_id = c.id 
                   JOIN usuarios u ON a.professor_id = u.id 
                   WHERE a.aluno_id = ? AND a.data_agendamento >= CURDATE()
                   ORDER BY a.data_agendamento, a.hora_inicio";
$stmt = $conn->prepare($proximas_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$proximas_aulas = $stmt->get_result();

// Buscar aulas passadas
$passadas_query = "SELECT a.*, c.nome as curso_nome, c.id as curso_id, c.duracao_horas, u.nome as professor_nome 
                   FROM agendamentos a 
                   JOIN cursos c ON a.curso_id = c.id 
                   JOIN usuarios u ON a.professor_id = u.id 
                   WHERE a.aluno_id = ? AND a.data_agendamento < CURDATE()
                   ORDER BY a.data_agendamento DESC, a.hora_inicio DESC";
$stmt = $conn->prepare($passadas_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aulas_passadas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Minhas Aulas</title>
    <script>
        (function() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            if (isDark) document.documentElement.classList.add('dark-mode');
        })();
    </script>

    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            /* Paleta Principal - Verde para Alunos */
            --primary-color: #059669;
            --primary-dark: #047857;
            --primary-light: #10b981;
            --primary-accent: #34d399;
            
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
            --gradient-primary: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);
            --gradient-accent: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --border-radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            
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

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 30%, #e2e8f0 70%, #f8fafc 100%);
            background-attachment: fixed;
            color: #1e293b;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            min-height: 100vh;
            font-feature-settings: 'kern' 1, 'liga' 1;
            font-optical-sizing: auto;
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

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            background: transparent;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--gradient-primary);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar-header {
            padding: 32px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            transition: var(--transition);
        }

        .sidebar-logo:hover {
            transform: translateX(4px);
        }

        .sidebar-logo i {
            font-size: 1.5rem;
        }

        .sidebar-nav {
            padding: 16px 0;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-item {
            margin: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
            font-weight: 500;
            letter-spacing: 0.01em;
            position: relative;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: rgba(255, 255, 255, 0.15);
            transition: width 0.3s ease;
        }

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            width: 100%;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.12);
            color: white;
            border-left-color: white;
            transform: translateX(4px);
        }

        .sidebar-link.active {
            font-weight: 600;
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.1);
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
            padding-top: 60px;
            animation: fadeIn 0.5s ease-out;
            background: transparent;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%) !important;
            padding: var(--spacing-lg) var(--spacing-lg) var(--spacing-md) var(--spacing-lg);
            margin: -40px -40px var(--spacing-xl) -40px;
            margin-top: 20px;
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
            gap: 24px;
            flex-wrap: wrap;
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: 600;
            background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .header h1 i {
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
        }

        .user-info-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .user-info-name {
            font-weight: 500;
            color: #334155;
            font-size: 0.9375rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .user-info-role {
            font-size: 0.8125rem;
            color: #64748b;
            font-weight: 400;
            white-space: nowrap;
        }

        .user-avatar {
            width: 52px;
            height: 52px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25), 0 2px 4px rgba(5, 150, 105, 0.15);
            border: 3px solid rgba(255, 255, 255, 0.4);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            flex-shrink: 0;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .user-avatar:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35), 0 4px 8px rgba(5, 150, 105, 0.2);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .user-avatar.online::after {
            content: '';
            position: absolute;
            bottom: 3px;
            right: 3px;
            width: 14px;
            height: 14px;
            background: var(--success-color);
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.4), 0 0 0 2px rgba(5, 150, 105, 0.1);
            animation: pulse 2s infinite;
            z-index: 2;
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--danger-color) 0%, var(--danger-light) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .logout-text {
            display: inline;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: var(--spacing-xl);
            align-items: stretch;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            padding: 28px 24px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out backwards;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            border-color: rgba(5, 150, 105, 0.25);
        }

        .stat-card .icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 16px;
            opacity: 0.85;
            transition: var(--transition);
            filter: drop-shadow(0 2px 4px rgba(5, 150, 105, 0.2));
        }

        .stat-card:hover .icon {
            transform: scale(1.1) rotate(5deg);
            opacity: 1;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: -0.03em;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .stat-card .label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 400;
            line-height: 1.5;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 8px;
            margin-bottom: var(--spacing-lg);
            border-bottom: 2px solid rgba(226, 232, 240, 0.6);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
            padding: 0 var(--spacing-md);
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06);
        }

        .tab {
            padding: 16px 28px;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: #64748b;
            border-bottom: 3px solid transparent;
            transition: var(--transition);
            position: relative;
            font-size: 0.9375rem;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.01em;
        }

        .tab::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .tab.active::after {
            transform: scaleX(1);
        }

        .tab.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .tab:hover {
            color: var(--primary-color);
        }

        .tab i {
            font-size: 0.875rem;
        }

        /* Class Cards */
        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: var(--spacing-lg);
        }

        .class-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            border-radius: var(--border-radius-lg);
            padding: 24px;
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out backwards;
        }

        .class-card:nth-child(1) { animation-delay: 0.1s; }
        .class-card:nth-child(2) { animation-delay: 0.2s; }
        .class-card:nth-child(3) { animation-delay: 0.3s; }
        .class-card:nth-child(4) { animation-delay: 0.4s; }

        .class-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .class-card:hover::before {
            transform: scaleX(1);
        }

        .class-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            transform: translateY(-4px);
            border-color: rgba(5, 150, 105, 0.25);
        }

        .class-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .class-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .class-course {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 400;
        }

        .class-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-upcoming {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .status-completed {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .status-cancelled {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
        }

        .class-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(248, 250, 252, 0.5) 0%, rgba(241, 245, 249, 0.3) 100%);
            border-radius: var(--border-radius-sm);
            border: 1px solid rgba(226, 232, 240, 0.5);
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            color: #475569;
            font-weight: 500;
        }

        .detail-item i {
            color: var(--primary-color);
            width: 18px;
            font-size: 1rem;
            opacity: 0.8;
        }

        .class-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.01em;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3), 0 2px 4px -1px rgba(5, 150, 105, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(5, 150, 105, 0.4), 0 4px 8px -2px rgba(5, 150, 105, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: #475569;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-color: rgba(5, 150, 105, 0.2);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, var(--danger-light) 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.3), 0 2px 4px -1px rgba(220, 38, 38, 0.2);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(220, 38, 38, 0.4), 0 4px 8px -2px rgba(220, 38, 38, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #64748b;
            margin-top: 20px;
        }

        .empty-state i {
            font-size: 5rem;
            margin-bottom: 32px;
            opacity: 0.3;
            color: var(--primary-color);
            display: block;
        }

        .empty-state h3 {
            font-size: 1.75rem;
            margin-bottom: 12px;
            color: #334155;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .empty-state p {
            margin-bottom: 32px;
            font-size: 1.0625rem;
            color: #64748b;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
        }

        .tab-content {
            animation: fadeIn 0.4s ease-out;
        }

        /* Dark Mode Toggle */
        .dark-mode-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            background: white;
            border: 2px solid var(--border-color);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            font-size: 1.25rem;
            color: var(--dark-color);
        }

        .dark-mode-toggle:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-color);
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 30%, #334155 70%, #0f172a 100%);
            color: #f1f5f9;
        }

        body.dark-mode .header {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.95) 100%) !important;
            border-bottom-color: rgba(51, 65, 85, 0.6);
        }

        body.dark-mode .stat-card,
        body.dark-mode .class-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            border-color: rgba(51, 65, 85, 0.8);
            color: #f1f5f9;
        }

        body.dark-mode .class-title {
            color: #f1f5f9;
        }

        body.dark-mode .class-details {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.5) 0%, rgba(15, 23, 42, 0.3) 100%);
            border-color: rgba(51, 65, 85, 0.5);
        }

        body.dark-mode .detail-item {
            color: #cbd5e1;
        }

        body.dark-mode .tabs {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.95) 100%);
            border-bottom-color: rgba(51, 65, 85, 0.6);
        }

        body.dark-mode .tab {
            color: #94a3b8;
        }

        body.dark-mode .tab.active {
            color: #10b981;
        }

        body.dark-mode .dark-mode-toggle {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--gradient-primary);
            color: white;
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .mobile-menu-toggle:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-xl);
        }

        .mobile-menu-toggle.active {
            background: var(--danger-color);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Responsive - Tablets */
        @media (max-width: 1024px) and (min-width: 769px) {
            .class-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Responsive - Mobile */
        @media (max-width: 768px) {
            .dark-mode-toggle {
                top: 80px;
                right: 20px;
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .mobile-overlay {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px 16px;
                padding-top: 80px;
            }

            .header {
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
                padding: 24px 20px 20px 20px;
                margin: -20px -20px var(--spacing-xl) -20px;
                margin-top: 16px;
            }

            .header h1 {
                font-size: 1.75rem;
                width: 100%;
                justify-content: center;
                text-align: center;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
                padding: 12px 0;
                border-top: 1px solid rgba(226, 232, 240, 0.5);
            }

            .user-avatar {
                width: 44px;
                height: 44px;
                font-size: 1.125rem;
            }

            .logout-text {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .tabs {
                flex-direction: column;
                gap: 0;
                padding: 0;
            }

            .tab {
                width: 100%;
                justify-content: center;
                border-bottom: 1px solid rgba(226, 232, 240, 0.3);
            }

            .class-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .class-details {
                grid-template-columns: 1fr;
            }

            .class-card {
                padding: 24px;
            }

            .class-actions {
                flex-direction: column;
            }

            .class-actions .btn {
                width: 100%;
            }
        }
    
/* ALUNO_THEME_OVERRIDES_START */
:root {
    --primary-color: #1e3a8a;
    --primary-dark: #0f172a;
    --primary-light: #2563eb;
    --primary-accent: #2563eb;
    --secondary-color: #64748b;
    --secondary-light: #94a3b8;
    --secondary-dark: #475569;
    --success-color: #059669;
    --success-light: #10b981;
    --warning-color: #d97706;
    --warning-light: #f59e0b;
    --danger-color: #dc2626;
    --danger-light: #ef4444;
    --info-color: #2563eb;
    --info-light: #60a5fa;
    --light-color: #f8fafc;
    --light-secondary: #f1f5f9;
    --dark-color: #0f172a;
    --dark-secondary: #1e293b;
    --border-color: #e2e8f0;
    --border-light: #f1f5f9;
    --gradient-primary: linear-gradient(135deg, #0f172a 0%, #1e3a8a 52%, #2563eb 100%);
    --gradient-accent: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    --gradient-success: linear-gradient(135deg, #059669 0%, #047857 100%);
    --gradient-warning: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    --gradient-danger: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --border-radius: 12px;
    --border-radius-sm: 8px;
    --border-radius-lg: 16px;
    --border-radius-full: 9999px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --section-gap: 40px;
    --card-gap: 28px;
    --card-padding: 28px;
    --card-padding-lg: 32px;
    --card-padding-xl: 36px;
    --font-family-base: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.0625rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;
    --font-size-3xl: 2.5rem;
    --font-weight-medium: 500;
    --font-weight-semibold: 600;
    --font-weight-bold: 700;
    --font-weight-extrabold: 800;
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-muted: #64748b;
}
body {
    font-family: var(--font-family-base) !important;
    background:
        radial-gradient(circle at 8% 4%, rgba(59, 130, 246, 0.16), transparent 24%),
        radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.10), transparent 26%),
        linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%) !important;
    color: var(--text-primary) !important;
    text-rendering: optimizeLegibility;
}
.dashboard-container { background: transparent; max-width: 100%; overflow-x: hidden; }
.sidebar {
    background:
        radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
        linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 18px 0 55px rgba(15, 23, 42, 0.18);
}
.sidebar-header {
    padding: 28px 20px !important;
    background: rgba(255, 255, 255, 0.06) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.14) !important;
    backdrop-filter: blur(18px);
}
.sidebar-logo {
    font-size: var(--font-size-2xl) !important;
    font-weight: var(--font-weight-extrabold) !important;
    letter-spacing: -0.03em;
}
.sidebar-logo i {
    display: inline-grid; place-items: center;
    width: 44px; height: 44px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.14);
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16);
}
.sidebar-link, .nav-link {
    margin: 4px 12px;
    padding: 13px 14px !important;
    border-radius: 14px;
    border-left: 0 !important;
    font-weight: var(--font-weight-semibold) !important;
    color: rgba(255, 255, 255, 0.85) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.sidebar-link:hover, .sidebar-link.active,
.nav-link:hover, .nav-link.active {
    background: rgba(255, 255, 255, 0.14) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.16) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 10px 26px rgba(0, 0, 0, 0.12);
    transform: translateX(3px);
}
.sidebar-group { margin:0 12px 18px; padding-bottom:16px; border-bottom:1px solid rgba(255,255,255,.1); }
.sidebar-group:last-child { border-bottom:none; margin-bottom:0; }
.sidebar-group-title { margin:0 0 8px; padding:8px 10px; color:rgba(255,255,255,.55); font-size:.68rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
.sidebar-nav { padding-bottom:80px; }
.sidebar-footer-fixed { position:fixed; bottom:0; left:0; width:280px; padding:12px 16px; border-top:1px solid rgba(255,255,255,.1); background:linear-gradient(180deg,rgba(15,23,42,.95) 0%,rgba(30,58,138,.95) 100%); z-index:1001; backdrop-filter:blur(18px); }
.sidebar-user { display:flex; align-items:center; gap:12px; padding:8px; border-radius:16px; background:rgba(255,255,255,.09); border:1px solid rgba(255,255,255,.11); }
.sidebar-user-name { font-weight:700; font-size:.72rem; color:white; }
.sidebar-user-role { font-size:.62rem; color:rgba(255,255,255,.65); }
.logout-btn-small { margin-left:auto; color:rgba(255,255,255,.7); font-size:.75rem; text-decoration:none; padding:4px 8px; border-radius:8px; white-space:nowrap; transition:color .2s; }
.logout-btn-small:hover { color:white; background:rgba(255,255,255,.1); }
.main-content { padding: 40px !important; min-height: 100vh; }
.header {
    position: relative; overflow: hidden;
    margin-bottom: var(--section-gap) !important;
    padding: var(--card-padding-xl) !important;
    border: 1px solid rgba(255, 255, 255, 0.42);
    border-radius: 28px;
    background:
        radial-gradient(circle at 8% 18%, rgba(255, 255, 255, 0.22), transparent 30%),
        linear-gradient(135deg, #0f172a 0%, #1e3a8a 58%, #2563eb 100%) !important;
    box-shadow: 0 28px 80px rgba(30, 58, 138, 0.2);
}
.header::before {
    content: ''; position: absolute; inset: 0; opacity: 0.18;
    background:
        linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
        linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
    background-size: 42px 42px;
}
.header h1, .header .user-info { position: relative; z-index: 1; }
.header h1 {
    color: #ffffff !important;
    font-size: clamp(2rem, 4vw, 3rem) !important;
    font-weight: var(--font-weight-extrabold) !important;
    letter-spacing: -0.055em;
    background: none !important;
    -webkit-background-clip: initial !important;
    background-clip: initial !important;
    -webkit-text-fill-color: #ffffff !important;
    text-shadow: 0 2px 18px rgba(15, 23, 42, 0.25);
}
.header h1 i { color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; }
.user-info {
    padding: 10px 12px;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(16px);
}
.user-info div, .user-info a, .user-info div[style] {
    color: #ffffff !important; opacity: 1 !important;
}
.user-info > div:last-of-type > div:first-child {
    font-weight: var(--font-weight-extrabold) !important;
    color: #ffffff !important;
    font-size: var(--font-size-base);
}
.user-info > div:last-of-type > div:last-child {
    color: rgba(255, 255, 255, 0.82) !important;
    font-weight: var(--font-weight-semibold) !important;
    font-size: var(--font-size-sm) !important;
}
.user-avatar {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
    color: #ffffff !important;
    font-size: var(--font-size-lg) !important;
    font-weight: var(--font-weight-bold) !important;
    box-shadow: 0 16px 35px rgba(15, 23, 42, 0.22);
}
.logout-btn {
    border-radius: 999px !important;
    background: rgba(239, 68, 68, 0.92) !important;
    color: #ffffff !important;
    font-size: var(--font-size-sm) !important;
    font-weight: var(--font-weight-bold) !important;
}
.stats-grid, .stats-overview, .quick-actions-grid, .charts-grid,
.content-grid, .cards-grid, .settings-grid, .mini-stats-grid,
.courses-grid, .lessons-grid, .certificates-grid {
    gap: var(--card-gap) !important;
    margin-bottom: var(--section-gap) !important;
    align-items: stretch;
}
.content-card, .chart-card, .stat-card, .mini-stat-card,
.course-header, .setting-card, .settings-card, .table-container,
.table-responsive, .filters-section, .report-card, .summary-card,
.catalog-course-card, .professor-course-card, .quick-action-card,
.course-card, .lesson-card, .certificate-card, .profile-card {
    position: relative; overflow: hidden;
    background: rgba(255, 255, 255, 0.92) !important;
    border: 1px solid rgba(255, 255, 255, 0.78) !important;
    border-radius: 24px !important;
    box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
    backdrop-filter: blur(18px);
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.content-card, .chart-card, .stat-card, .mini-stat-card,
.course-header, .setting-card, .settings-card,
.report-card, .summary-card, .quick-action-card,
.course-card, .lesson-card, .certificate-card, .profile-card {
    padding: var(--card-padding) !important;
}
.catalog-course-card, .professor-course-card { padding: 24px !important; }
.content-card::before, .chart-card::before, .stat-card::before, .mini-stat-card::before,
.course-header::before, .setting-card::before, .settings-card::before,
.report-card::before, .summary-card::before, .catalog-course-card::before,
.professor-course-card::before, .quick-action-card::before,
.course-card::before, .lesson-card::before, .certificate-card::before, .profile-card::before {
    content: ''; position: absolute; inset: 0 0 auto 0; height: 5px;
    background: linear-gradient(90deg, #1e3a8a, #2563eb) !important;
}
.stat-card.success::before, .mini-stat-card.success::before,
.report-card.success::before, .certificate-card::before {
    background: linear-gradient(90deg, #059669, #10b981) !important;
}
.stat-card.warning::before, .mini-stat-card.warning::before, .report-card.warning::before {
    background: linear-gradient(90deg, #d97706, #f59e0b) !important;
}
.stat-card.danger::before, .mini-stat-card.danger::before, .report-card.danger::before {
    background: linear-gradient(90deg, #dc2626, #ef4444) !important;
}
.content-card:hover, .chart-card:hover, .stat-card:hover, .mini-stat-card:hover,
.course-header:hover, .setting-card:hover, .settings-card:hover,
.report-card:hover, .summary-card:hover, .catalog-course-card:hover,
.professor-course-card:hover, .quick-action-card:hover,
.course-card:hover, .lesson-card:hover, .certificate-card:hover, .profile-card:hover {
    transform: translateY(-7px);
    border-color: rgba(37, 99, 235, 0.18) !important;
    box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14) !important;
}
.stat-card .icon, .mini-stat-card .icon, .summary-icon, .metric-icon {
    display: inline-grid; place-items: center;
    width: 58px; height: 58px;
    border-radius: 20px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: #ffffff !important;
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
}
.stat-card.success .icon, .mini-stat-card.success .icon {
    background: linear-gradient(135deg, var(--success-color), #047857);
}
.stat-card.warning .icon, .mini-stat-card.warning .icon {
    background: linear-gradient(135deg, var(--warning-color), #b45309);
}
.stat-card.info .icon, .mini-stat-card.info .icon {
    background: linear-gradient(135deg, var(--info-color), var(--primary-color));
}
.stat-card .icon, .mini-stat-card .icon {
    margin-bottom: 14px;
}
.stat-card h3 {
    margin-bottom: 10px !important;
}
.stat-card .value, .mini-stat-card .value {
    letter-spacing: -0.055em !important;
    margin-bottom: 8px !important;
}
.stat-card .progress-bar {
    margin-top: auto !important;
    margin-bottom: 0 !important;
}
.stat-card {
    padding: var(--card-padding) !important;
}
.content-card h2, .chart-title, .section-title, .card-title {
    color: var(--text-primary) !important;
    font-weight: var(--font-weight-extrabold) !important;
}
.content-card h2 { font-size: var(--font-size-2xl) !important; margin-bottom: 32px !important; }
.content-card h2 i, .chart-title i {
    display: inline-grid; place-items: center;
    width: 38px; height: 38px;
    border-radius: 14px;
    background: rgba(37, 99, 235, 0.10);
    color: var(--primary-light) !important;
}
.chart-title { font-size: var(--font-size-lg) !important; margin-bottom: 18px !important; }
.stat-card .value, .mini-stat-card .value, .metric-value, .summary-value {
    color: var(--text-primary) !important;
    font-size: var(--font-size-3xl) !important;
    font-weight: var(--font-weight-extrabold) !important;
    letter-spacing: -0.055em;
}
.stat-card h3, .mini-stat-card h3, .metric-label, .summary-label,
.table th, .course-field-label {
    color: var(--text-secondary) !important;
    font-size: var(--font-size-xs) !important;
    font-weight: var(--font-weight-bold) !important;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}
.table, table { border-collapse: separate; border-spacing: 0; }
.table thead th, .table th, table th {
    background: #f8fafc !important;
    color: var(--text-secondary) !important;
    font-size: var(--font-size-xs) !important;
    font-weight: var(--font-weight-bold) !important;
}
.table td, table td {
    color: var(--text-secondary) !important;
    font-size: var(--font-size-sm) !important;
    font-weight: var(--font-weight-medium) !important;
}
.table tbody tr:hover, table tbody tr:hover { background: rgba(37, 99, 235, 0.035) !important; }
.search-input, input[type="text"], input[type="email"], input[type="password"],
input[type="number"], select, textarea {
    font-family: var(--font-family-base);
    font-size: var(--font-size-sm) !important;
    color: var(--text-primary) !important;
    border-radius: 14px !important;
}
.filters-section { padding: 24px !important; gap: 18px !important; margin-bottom: 28px !important; }
.btn, .button, .filter-btn, .export-btn, button[type="submit"] {
    font-size: var(--font-size-sm) !important;
    font-weight: var(--font-weight-bold) !important;
    border-radius: 999px !important;
}
.quick-action-card, .action-card { gap: 18px !important; padding: var(--card-padding) !important; }
.quick-action-title, .action-title {
    color: var(--text-primary) !important;
    font-size: var(--font-size-lg) !important;
    font-weight: var(--font-weight-extrabold) !important;
}
.quick-action-desc, .action-desc, .course-meta-line, .text-muted,
.empty-state p, .helper-text, .description, small {
    color: var(--text-secondary) !important;
    font-size: var(--font-size-sm) !important;
    font-weight: var(--font-weight-semibold) !important;
}
/* Toast styles */
.toast-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 13000;
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 400px;
    pointer-events: none;
}
.toast {
    background: #ffffff;
    padding: 16px 20px;
    border-radius: 14px;
    box-shadow: 0 18px 48px rgba(15, 23, 42, 0.14);
    border-left: 4px solid var(--primary-light);
    display: flex;
    align-items: center;
    gap: 12px;
    animation: alunoToastSlideIn 0.3s ease-out;
    pointer-events: auto;
    position: relative;
    overflow: hidden;
    min-width: 300px;
}
.toast::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #1e3a8a, #2563eb);
    transform: scaleX(0);
    animation: alunoToastProgress 3s linear forwards;
    transform-origin: left;
}
.toast.success { border-left-color: var(--success-color); }
.toast.success::before { background: linear-gradient(90deg, #059669, #10b981); }
.toast.error { border-left-color: var(--danger-color); }
.toast.error::before { background: linear-gradient(90deg, #dc2626, #ef4444); }
.toast.warning { border-left-color: var(--warning-color); }
.toast.warning::before { background: linear-gradient(90deg, #d97706, #f59e0b); }
.toast.info { border-left-color: var(--info-color); }
.toast-icon { font-size: 1.4rem; flex-shrink: 0; color: var(--primary-light); }
.toast.success .toast-icon { color: var(--success-color); }
.toast.error .toast-icon { color: var(--danger-color); }
.toast.warning .toast-icon { color: var(--warning-color); }
.toast-content { flex: 1; }
.toast-title {
    font-weight: var(--font-weight-bold);
    font-size: var(--font-size-base);
    color: var(--text-primary);
    margin-bottom: 2px;
}
.toast-message {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    font-weight: var(--font-weight-medium);
}
.toast-close {
    background: transparent; border: none; cursor: pointer;
    color: #94a3b8; padding: 4px;
    font-size: 1rem; line-height: 1;
    transition: color 0.2s;
}
.toast-close:hover { color: var(--text-primary); }
@keyframes alunoToastSlideIn {
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes alunoToastProgress { to { transform: scaleX(1); } }
@media (max-width: 768px) {
    .main-content { padding: 20px !important; }
    .header { padding: 24px !important; }
    .content-card, .chart-card, .stat-card, .mini-stat-card,
    .course-header, .setting-card, .settings-card, .report-card,
    .summary-card, .course-card, .lesson-card, .certificate-card, .profile-card {
        padding: 24px !important;
    }
    .toast-container { left: 16px; right: 16px; bottom: 16px; max-width: none; }
    .toast { min-width: 0; width: 100%; }
}
/* Modal de confirmação bonito */
.confirm-overlay {
    position: fixed; inset: 0; z-index: 15000;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    opacity: 0;
    transition: opacity 0.22s ease;
}
.confirm-overlay.open { opacity: 1; }
.confirm-modal {
    width: 100%; max-width: 440px;
    background: #ffffff;
    border-radius: 22px;
    padding: 32px 28px 24px;
    text-align: center;
    box-shadow: 0 30px 90px rgba(15, 23, 42, 0.4);
    transform: translateY(20px) scale(0.96);
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.confirm-overlay.open .confirm-modal { transform: translateY(0) scale(1); }
.confirm-icon {
    width: 72px; height: 72px;
    margin: 0 auto 18px;
    display: grid; place-items: center;
    border-radius: 50%;
    font-size: 1.8rem;
    color: #ffffff;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.15);
}
.confirm-icon.danger { background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 18px 40px rgba(239, 68, 68, 0.35); }
.confirm-icon.warning { background: linear-gradient(135deg, #d97706, #f59e0b); box-shadow: 0 18px 40px rgba(245, 158, 11, 0.35); }
.confirm-icon.primary { background: linear-gradient(135deg, #1e3a8a, #2563eb); box-shadow: 0 18px 40px rgba(37, 99, 235, 0.35); }
.confirm-icon.success { background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 18px 40px rgba(16, 185, 129, 0.35); }
.confirm-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin-bottom: 10px;
}
.confirm-message {
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.55;
    margin-bottom: 24px;
    font-weight: 500;
}
.confirm-message strong { color: #0f172a; font-weight: 700; }
.confirm-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.confirm-btn {
    flex: 1;
    min-width: 140px;
    padding: 12px 22px;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 800;
    font-family: inherit;
    cursor: pointer;
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}
.confirm-btn.ghost {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.confirm-btn.ghost:hover { background: #e2e8f0; }
.confirm-btn.danger {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: #ffffff;
    box-shadow: 0 14px 28px rgba(239, 68, 68, 0.32);
}
.confirm-btn.danger:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(239, 68, 68, 0.4); }
.confirm-btn.primary {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #ffffff;
    box-shadow: 0 14px 28px rgba(37, 99, 235, 0.32);
}
.confirm-btn.primary:hover { transform: translateY(-2px); }
.confirm-btn.warning {
    background: linear-gradient(135deg, #d97706, #f59e0b);
    color: #ffffff;
    box-shadow: 0 14px 28px rgba(245, 158, 11, 0.32);
}
@media (max-width: 480px) {
    .confirm-actions { flex-direction: column-reverse; }
    .confirm-btn { width: 100%; }
}
/* ALUNO_THEME_OVERRIDES_END */

</style>
    <link rel="stylesheet" href="dark-mode.css">
    <style>
        .header-actions {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .header-actions #darkModeToggle {
            position: relative !important;
            top: auto !important;
            right: auto !important;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <div id="toastContainer" class="toast-container"></div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="dashboard_aluno.php" class="sidebar-logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>EduConnect</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Navegação</div>
                        <li class="sidebar-item">
                            <a href="dashboard_aluno.php" class="sidebar-link">
                                <i class="fas fa-tachometer-alt sidebar-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </div>
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Acadêmico</div>
                        <li class="sidebar-item">
                            <a href="meus_cursos_aluno.php" class="sidebar-link">
                                <i class="fas fa-book sidebar-icon"></i>
                                <span>Meus Cursos</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="minhas_aulas_aluno.php" class="sidebar-link active">
                                <i class="fas fa-calendar-alt sidebar-icon"></i>
                                <span>Minhas Aulas</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="buscar_cursos_aluno.php" class="sidebar-link">
                                <i class="fas fa-search sidebar-icon"></i>
                                <span>Buscar Cursos</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="certificados_aluno.php" class="sidebar-link">
                                <i class="fas fa-certificate sidebar-icon"></i>
                                <span>Certificados</span>
                            </a>
                        </li>
                    </div>
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Conta</div>
                        <li class="sidebar-item">
                            <a href="perfil_aluno.php" class="sidebar-link">
                                <i class="fas fa-user sidebar-icon"></i>
                                <span>Perfil</span>
                            </a>
                        </li>
                    </div>
                </ul>
            </nav>
            <div class="sidebar-footer-fixed">
                <div class="sidebar-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($aluno['nome'], 0, 1)); ?></div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name"><?php echo htmlspecialchars($aluno['nome']); ?></div>
                        <div class="sidebar-user-role">Aluno</div>
                    </div>
                    <a href="logout.php" class="logout-btn-small"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1><i class="fas fa-calendar-alt"></i> Minhas Aulas</h1>
                
                <div class="header-actions">
                    <button id="darkModeToggle" title="Alternar tema">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <div class="user-info">
                    <div class="user-avatar online" title="<?php echo htmlspecialchars($aluno['nome']); ?>">
                        <?php 
                        $foto_perfil = isset($aluno['foto']) && !empty($aluno['foto']) ? $aluno['foto'] : 
                                      (isset($aluno['avatar']) && !empty($aluno['avatar']) ? $aluno['avatar'] : null);
                        
                        if ($foto_perfil && file_exists($foto_perfil)): 
                        ?>
                            <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="<?php echo htmlspecialchars($aluno['nome']); ?>">
                        <?php else: ?>
                            <span><?php echo strtoupper(substr($aluno['nome'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="user-info-details">
                        <div class="user-info-name"><?php echo htmlspecialchars($aluno['nome']); ?></div>
                        <div class="user-info-role">Aluno</div>
                    </div>
                    <a href="logout.php" class="logout-btn" title="Sair do sistema">
                        <i class="fas fa-sign-out-alt"></i> <span class="logout-text">Sair</span>
                    </a>
                </div>
            </header>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="value"><?php echo $proximas_aulas->num_rows; ?></div>
                    <div class="label">Próximas Aulas</div>
                </div>
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="value"><?php echo $aulas_passadas->num_rows; ?></div>
                    <div class="label">Aulas Concluídas</div>
                </div>
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="value"><?php echo $todas_aulas->num_rows; ?></div>
                    <div class="label">Total de Aulas</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="showTab('upcoming', this)">
                    <i class="fas fa-calendar-check"></i> Próximas Aulas
                </button>
                <button class="tab" onclick="showTab('completed', this)">
                    <i class="fas fa-check-circle"></i> Aulas Concluídas
                </button>
                <button class="tab" onclick="showTab('all', this)">
                    <i class="fas fa-list"></i> Todas as Aulas
                </button>
            </div>

            <!-- Próximas Aulas -->
            <div id="upcoming" class="tab-content">
                <?php if ($proximas_aulas->num_rows > 0): ?>
                    <div class="class-grid">
                        <?php while ($aula = $proximas_aulas->fetch_assoc()): ?>
                            <div class="class-card">
                                <div class="class-header">
                                    <div>
                                        <div class="class-title"><?php echo $aula['curso_nome']; ?></div>
                                        <div class="class-course">Aula Programada</div>
                                    </div>
                                    <span class="class-status status-upcoming">
                                        <i class="fas fa-clock" style="font-size: 0.7rem;"></i>
                                        Próxima
                                    </span>
                                </div>
                                
                                <div class="class-details">
                                    <div class="detail-item">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($aula['professor_nome']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('d/m/Y', strtotime($aula['data_agendamento'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo $aula['hora_inicio']; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span><?php echo (int)($aula['duracao_horas'] ?? 0); ?>h</span>
                                    </div>
                                </div>
                                
                                <div class="class-actions">
                                    <button type="button" class="btn btn-primary" onclick="entrarAula(<?php echo $aula['id']; ?>)">
                                        <i class="fas fa-video"></i>
                                        Entrar na Aula
                                    </button>
                                    <a href="detalhes_curso_aluno.php?id=<?php echo $aula['curso_id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-info-circle"></i>
                                        Detalhes
                                    </a>
                                    <button class="btn btn-danger" onclick="excluirAula(<?php echo $aula['id']; ?>, '<?php echo htmlspecialchars($aula['curso_nome'], ENT_QUOTES); ?>')">
                                        <i class="fas fa-trash"></i>
                                        Excluir
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar"></i>
                        <h3>Nenhuma aula agendada</h3>
                        <p>Você não tem aulas futuras agendadas. Explore os cursos disponíveis para começar!</p>
                        <a href="buscar_cursos_aluno.php" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Buscar Cursos
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Aulas Concluídas -->
            <div id="completed" class="tab-content" style="display: none;">
                <?php if ($aulas_passadas->num_rows > 0): ?>
                    <div class="class-grid">
                        <?php while ($aula = $aulas_passadas->fetch_assoc()): ?>
                            <div class="class-card">
                                <div class="class-header">
                                    <div>
                                        <div class="class-title"><?php echo $aula['curso_nome']; ?></div>
                                        <div class="class-course">Aula Concluída</div>
                                    </div>
                                    <span class="class-status status-completed">
                                        <i class="fas fa-check-circle" style="font-size: 0.7rem;"></i>
                                        Concluída
                                    </span>
                                </div>
                                
                                <div class="class-details">
                                    <div class="detail-item">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($aula['professor_nome']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('d/m/Y', strtotime($aula['data_agendamento'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo $aula['hora_inicio']; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span><?php echo (int)($aula['duracao_horas'] ?? 0); ?>h</span>
                                    </div>
                                </div>
                                
                                <div class="class-actions">
                                    <button type="button" class="btn btn-secondary" onclick="verGravacao(<?php echo $aula['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                        Ver Gravação
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="baixarMaterial(<?php echo $aula['id']; ?>)">
                                        <i class="fas fa-download"></i>
                                        Material
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>Nenhuma aula concluída</h3>
                        <p>Você ainda não concluiu nenhuma aula. Participe das aulas para ver seu histórico aqui!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Todas as Aulas -->
            <div id="all" class="tab-content" style="display: none;">
                <?php if ($todas_aulas->num_rows > 0): ?>
                    <div class="class-grid">
                        <?php while ($aula = $todas_aulas->fetch_assoc()): ?>
                            <?php 
                            $is_upcoming = strtotime($aula['data_agendamento']) >= strtotime(date('Y-m-d'));
                            $status_class = $is_upcoming ? 'status-upcoming' : 'status-completed';
                            $status_text = $is_upcoming ? 'Próxima' : 'Concluída';
                            ?>
                            <div class="class-card">
                                <div class="class-header">
                                    <div>
                                        <div class="class-title"><?php echo $aula['curso_nome']; ?></div>
                                        <div class="class-course"><?php echo $is_upcoming ? 'Aula Programada' : 'Aula Concluída'; ?></div>
                                    </div>
                                    <span class="class-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </div>
                                
                                <div class="class-details">
                                    <div class="detail-item">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($aula['professor_nome']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('d/m/Y', strtotime($aula['data_agendamento'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo $aula['hora_inicio']; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span><?php echo (int)($aula['duracao_horas'] ?? 0); ?>h</span>
                                    </div>
                                </div>
                                
                                <div class="class-actions">
                                    <?php if ($is_upcoming): ?>
                                        <button type="button" class="btn btn-primary" onclick="entrarAula(<?php echo $aula['id']; ?>)">
                                            <i class="fas fa-video"></i>
                                            Entrar na Aula
                                        </button>
                                        <button class="btn btn-danger" onclick="excluirAula(<?php echo $aula['id']; ?>, '<?php echo htmlspecialchars($aula['curso_nome'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-trash"></i>
                                            Excluir
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary" onclick="verGravacao(<?php echo $aula['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                            Ver Gravação
                                        </button>
                                    <?php endif; ?>
                                    <a href="detalhes_curso_aluno.php?id=<?php echo $aula['curso_id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-info-circle"></i>
                                        Detalhes
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>Nenhuma aula encontrada</h3>
                        <p>Você ainda não tem aulas agendadas. Explore os cursos disponíveis para começar!</p>
                        <a href="buscar_cursos_aluno.php" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Buscar Cursos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function showTab(tabName, tabElement) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabName).style.display = 'block';
            tabElement.classList.add('active');
        }

        function showToast(title, message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <i class="fas ${icons[type]} toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'toastSlideIn 0.3s ease-out reverse';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        }

        function entrarAula(id) {
            showToast('Conectando à aula...', 'Preparando sua sala virtual. Aguarde um instante.', 'info');
            setTimeout(function(){
                window.location.href = 'aula_ao_vivo.php?agendamento_id=' + id;
            }, 900);
        }
        function verGravacao(id) {
            showToast('Gravação em breve', 'O vídeo da aula gravada estará disponível em breve.', 'info');
        }
        function baixarMaterial(id) {
            showToast('Material em preparação', 'Os materiais complementares serão liberados nesta área.', 'info');
        }

        async function excluirAula(agendamentoId, cursoNome) {
            // ... (rest of function)
            if (confirm('Deseja realmente excluir esta aula?')) {
                 fetch('excluir_aula_aluno.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'agendamento_id=' + agendamentoId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Aula excluída', data.message, 'success');
                        setTimeout(function(){ location.reload(); }, 1200);
                    } else {
                        showToast('Erro', data.message, 'error');
                    }
                });
            }
        }
    </script>
    <script src="sidebar.js"></script>
    <script src="dark-mode.js"></script>
</body>
</html>
