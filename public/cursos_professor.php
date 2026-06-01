<?php
session_start();

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header('Location: login.php');
    exit();
}

include 'db.php';

$conn->query("CREATE TABLE IF NOT EXISTS atribuicoes_cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT NOT NULL,
    curso_id INT NOT NULL,
    data_atribuicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    UNIQUE KEY unique_atribuicao (professor_id, curso_id)
)");

// Buscar dados do professor
$professor_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$professor = $stmt->get_result()->fetch_assoc();

// Buscar cursos atribuídos ao professor
$cursos_professor_query = "SELECT DISTINCT c.* FROM cursos c 
                          JOIN atribuicoes_cursos ac ON c.id = ac.curso_id 
                          WHERE ac.professor_id = ? AND c.status = 'ativo'
                          ORDER BY c.nome";
$stmt = $conn->prepare($cursos_professor_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$cursos_professor = $stmt->get_result();

$total_cursos_professor = $cursos_professor->num_rows;

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM cursos");
$stmt->execute();
$total_catalogo = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);

$stmt = $conn->prepare("SELECT COUNT(DISTINCT aluno_id) as total FROM agendamentos WHERE professor_id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$total_alunos_cursos = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Meus Cursos</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            --border-radius-full: 9999px;
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
            color: var(--dark-color);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
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

        .dashboard-container {
            display: flex;
            min-height: 100vh;
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
            font-weight: 800;
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
            animation: fadeIn 0.5s ease-out;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-2xl);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%) !important;
            padding: var(--spacing-xl) var(--spacing-xl) var(--spacing-lg) var(--spacing-xl);
            margin: -40px -40px var(--spacing-2xl) -40px;
            margin-top: 20px;
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        .header h1 {
            font-size: 2.25rem;
            font-weight: 600;
            background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
            font-size: 1.125rem;
            box-shadow: var(--shadow-md);
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: var(--transition);
            position: relative;
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }
        
        /* Avatar com Status Online */
        .user-avatar.online::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background: var(--success-color);
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.1);
            }
        }
        
        /* Loading State Melhorado */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
            border-radius: var(--border-radius);
        }
        
        .loading-skeleton::after {
            content: '';
            display: block;
            padding-top: 100%;
        }
        
        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
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
        
        /* ===== TOAST NOTIFICATIONS MELHORADAS ===== */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
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
            line-height: 1;
            transition: var(--transition);
        }
        
        .toast-close:hover {
            color: var(--dark-color);
        }
        
        @keyframes toastSlideIn {
            from {
                transform: translateX(100%);
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
        
        /* Estados de Foco Melhorados para Acessibilidade */
        .btn:focus-visible,
        .sidebar-link:focus-visible,
        .content-card:focus-visible {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }
        
        /* Microinterações - Feedback Visual Aprimorado */
        .content-card:active {
            transform: translateY(-2px) scale(0.98);
        }
        
        /* Ripple Effect nos Botões */
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
        
        .btn {
            position: relative;
            overflow: hidden;
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
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            letter-spacing: 0.02em;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        /* Content Card */
        .content-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            padding: 40px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .content-card::before {
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

        .content-card:hover::before {
            transform: scaleX(1);
        }

        .content-card:nth-child(1) { animation-delay: 0.2s; }
        .content-card:nth-child(2) { animation-delay: 0.3s; }

        .content-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-4px);
            border-color: rgba(37, 99, 235, 0.2);
        }

        .content-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.01em;
            padding-bottom: 16px;
            position: relative;
        }

        .content-card h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .content-card h2 i {
            color: var(--primary-color);
            font-size: 1.375rem;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-light);
        }

        .table th {
            font-weight: 700;
            color: var(--secondary-dark);
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: var(--light-secondary);
        }

        .table td {
            color: var(--dark-color);
            font-weight: 500;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: var(--light-secondary);
            transform: scale(1.01);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: var(--border-radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
        }

        .status-active {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            box-shadow: 0 2px 4px rgba(22, 101, 52, 0.1);
        }

        .btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .main-content {
                margin-left: 0;
                padding: 24px;
            }

            .header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
                padding: 24px;
                margin: -24px -24px 32px -24px;
            }

            .header h1 {
                font-size: 1.75rem;
            }

            .content-card {
                padding: 24px;
            }
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 8% 4%, rgba(59, 130, 246, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(20, 184, 166, 0.13), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%) !important;
        }

        .sidebar {
            background:
                radial-gradient(circle at top left, rgba(96, 165, 250, 0.22), transparent 34%),
                linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 18px 0 55px rgba(15, 23, 42, 0.18);
        }

        .sidebar-header {
            padding: 28px 20px;
            background: rgba(255, 255, 255, 0.06);
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(18px);
        }

        .sidebar-logo {
            font-weight: 850;
            letter-spacing: -0.03em;
        }

        .sidebar-logo i {
            display: inline-grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.14);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16);
        }

        .sidebar-link {
            margin: 4px 12px;
            padding: 13px 14px;
            border-radius: 14px;
            border-left: 0;
            font-weight: 650;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.16);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 10px 26px rgba(0, 0, 0, 0.12);
            transform: translateX(3px);
        }

        .main-content {
            padding: 34px;
            min-height: 100vh;
        }

        .header {
            position: relative;
            overflow: hidden;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.42);
            border-radius: 28px;
            background:
                radial-gradient(circle at 8% 18%, rgba(255, 255, 255, 0.22), transparent 30%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 48%, #2563eb 100%) !important;
            box-shadow: 0 28px 80px rgba(30, 58, 138, 0.2);
        }

        .header::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.18;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
            background-size: 42px 42px;
        }

        .header h1,
        .header .user-info {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            color: #ffffff;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 850;
            letter-spacing: -0.055em;
            background: none;
            -webkit-text-fill-color: #ffffff;
        }

        .user-info {
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
        }

        .user-info div,
        .user-info a,
        .user-info div[style] {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        .user-info > div:last-of-type > div:first-child {
            color: #ffffff !important;
            font-weight: 800 !important;
        }

        .user-info > div:last-of-type > div:last-child {
            color: rgba(255, 255, 255, 0.82) !important;
            font-weight: 650 !important;
        }

        .user-avatar {
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.22);
        }

        .logout-btn {
            border-radius: 999px;
            background: rgba(239, 68, 68, 0.9) !important;
            color: #ffffff !important;
            font-weight: 800;
        }

        .content-card {
            position: relative;
            overflow: hidden;
            padding: 34px;
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.78) !important;
            border-radius: 24px !important;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
            backdrop-filter: blur(18px);
        }

        .content-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #2563eb, #7c3aed, #10b981);
            transform: none;
        }

        .content-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14) !important;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 18px;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: rgba(37, 99, 235, 0.035);
            transform: translateY(-1px);
        }

        .status-badge {
            border-radius: 999px;
            font-weight: 800;
        }

        .btn {
            border-radius: 999px;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.14);
        }

        .toast-container {
            top: auto;
            bottom: 24px;
            z-index: 13000;
        }

        .content-card::before {
            background: linear-gradient(90deg, #2563eb, #7c3aed, #10b981);
        }

        .content-card h2 i {
            display: inline-grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: rgba(37, 99, 235, 0.10);
        }

        .mini-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 26px;
        }

        .mini-stat-card {
            padding: 22px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.78);
            box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
            position: relative;
            overflow: hidden;
        }

        .mini-stat-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #7c3aed);
        }

        .mini-stat-card:nth-child(2)::before {
            background: linear-gradient(90deg, #059669, #06b6d4);
        }

        .mini-stat-card:nth-child(3)::before {
            background: linear-gradient(90deg, #d97706, #fbbf24);
        }

        .mini-stat-card i {
            width: 44px;
            height: 44px;
            display: inline-grid;
            place-items: center;
            border-radius: 16px;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            margin-bottom: 14px;
        }

        .mini-stat-card:nth-child(2) i {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .mini-stat-card:nth-child(3) i {
            background: linear-gradient(135deg, #d97706, #b45309);
        }

        .mini-stat-value {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -0.05em;
            color: #0f172a;
        }

        .mini-stat-label {
            color: #64748b;
            font-weight: 700;
            margin-top: 2px;
        }

        .professor-courses-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .professor-course-card {
            position: relative;
            display: grid;
            grid-template-columns: minmax(260px, 1.35fr) minmax(300px, 1fr) auto;
            align-items: center;
            gap: 20px;
            min-height: 0;
            padding: 20px;
            border-radius: 22px;
            background:
                radial-gradient(circle at 92% 10%, rgba(37, 99, 235, 0.13), transparent 30%),
                linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.075);
            overflow: hidden;
            transition: var(--transition);
        }

        .professor-course-card:hover {
            transform: translateY(-5px);
            border-color: rgba(37, 99, 235, 0.28);
            box-shadow: 0 26px 60px rgba(15, 23, 42, 0.13);
        }

        .professor-course-head {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 14px;
        }

        .professor-course-icon {
            width: 50px;
            height: 50px;
            display: inline-grid;
            place-items: center;
            flex: 0 0 auto;
            border-radius: 18px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.25);
        }

        .professor-course-title {
            color: #0f172a;
            font-size: 1.14rem;
            font-weight: 900;
            line-height: 1.25;
            letter-spacing: -0.04em;
            margin-bottom: 8px;
        }

        .professor-course-description {
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .professor-course-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
        }

        .course-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            border-radius: 999px;
            color: #334155;
            background: rgba(241, 245, 249, 0.96);
            border: 1px solid rgba(226, 232, 240, 0.92);
            font-size: 0.79rem;
            font-weight: 850;
        }

        .professor-course-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-direction: column;
            gap: 14px;
        }

        .professor-course-footer .btn {
            white-space: nowrap;
        }

        @media (max-width: 1180px) {
            .professor-course-card {
                grid-template-columns: 1fr;
            }

            .professor-course-footer {
                align-items: stretch;
                flex-direction: row;
                justify-content: space-between;
            }
        }

        @media (max-width: 560px) {
            .professor-course-head,
            .professor-course-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .professor-course-footer .btn {
                text-align: center;
            }
        }
    
        
        
        /* PROFESSOR_THEME_OVERRIDES_START */
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

        .dashboard-container {
            background: transparent;
            max-width: 100%;
            overflow-x: hidden;
        }

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
            display: inline-grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.14);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16);
        }

        .sidebar-link,
        .nav-link {
            margin: 4px 12px;
            padding: 13px 14px !important;
            border-radius: 14px;
            border-left: 0 !important;
            font-weight: var(--font-weight-semibold) !important;
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .sidebar-link:hover,
        .sidebar-link.active,
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.14) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.16) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 10px 26px rgba(0, 0, 0, 0.12);
            transform: translateX(3px);
        }

        .main-content {
            padding: 40px !important;
            min-height: 100vh;
        }

        .header {
            position: relative;
            overflow: hidden;
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
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.18;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
            background-size: 42px 42px;
        }

        .header h1,
        .header .user-info {
            position: relative;
            z-index: 1;
        }

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

        .header h1 i {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }

        .user-info {
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
        }

        .user-info div,
        .user-info a,
        .user-info div[style] {
            color: #ffffff !important;
            opacity: 1 !important;
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

        .stats-grid,
        .stats-overview,
        .quick-actions-grid,
        .charts-grid,
        .content-grid,
        .cards-grid,
        .settings-grid,
        .mini-stats-grid {
            gap: var(--card-gap) !important;
            margin-bottom: var(--section-gap) !important;
            align-items: stretch;
        }

        .content-card,
        .chart-card,
        .stat-card,
        .mini-stat-card,
        .course-header,
        .setting-card,
        .settings-card,
        .table-container,
        .table-responsive,
        .filters-section,
        .report-card,
        .summary-card,
        .catalog-course-card,
        .professor-course-card,
        .quick-action-card {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.78) !important;
            border-radius: 24px !important;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
            backdrop-filter: blur(18px);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        border-color 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .content-card,
        .chart-card,
        .stat-card,
        .mini-stat-card,
        .course-header,
        .setting-card,
        .settings-card,
        .report-card,
        .summary-card,
        .quick-action-card {
            padding: var(--card-padding) !important;
        }

        .catalog-course-card,
        .professor-course-card {
            padding: 24px !important;
        }

        .content-card::before,
        .chart-card::before,
        .stat-card::before,
        .mini-stat-card::before,
        .course-header::before,
        .setting-card::before,
        .settings-card::before,
        .report-card::before,
        .summary-card::before,
        .catalog-course-card::before,
        .professor-course-card::before,
        .quick-action-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb) !important;
        }

        .stat-card.success::before,
        .mini-stat-card.success::before,
        .report-card.success::before {
            background: linear-gradient(90deg, #059669, #10b981) !important;
        }

        .stat-card.warning::before,
        .mini-stat-card.warning::before,
        .report-card.warning::before {
            background: linear-gradient(90deg, #d97706, #f59e0b) !important;
        }

        .stat-card.danger::before,
        .mini-stat-card.danger::before,
        .report-card.danger::before {
            background: linear-gradient(90deg, #dc2626, #ef4444) !important;
        }

        .content-card:hover,
        .chart-card:hover,
        .stat-card:hover,
        .mini-stat-card:hover,
        .course-header:hover,
        .setting-card:hover,
        .settings-card:hover,
        .report-card:hover,
        .summary-card:hover,
        .catalog-course-card:hover,
        .professor-course-card:hover,
        .quick-action-card:hover {
            transform: translateY(-7px);
            border-color: rgba(37, 99, 235, 0.18) !important;
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14) !important;
        }

        .stat-card .icon,
        .mini-stat-card .icon,
        .summary-icon,
        .metric-icon {
            display: inline-grid;
            place-items: center;
            width: 58px;
            height: 58px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: #ffffff !important;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
        }

        .stat-card.success .icon,
        .mini-stat-card.success .icon {
            background: linear-gradient(135deg, var(--success-color), #047857);
        }

        .stat-card.warning .icon,
        .mini-stat-card.warning .icon {
            background: linear-gradient(135deg, var(--warning-color), #b45309);
        }

        .stat-card.info .icon,
        .mini-stat-card.info .icon {
            background: linear-gradient(135deg, var(--info-color), var(--primary-color));
        }

        .content-card h2,
        .chart-title,
        .section-title,
        .card-title {
            color: var(--text-primary) !important;
            font-weight: var(--font-weight-extrabold) !important;
        }

        .content-card h2 {
            font-size: var(--font-size-2xl) !important;
            margin-bottom: 32px !important;
        }

        .content-card h2 i,
        .chart-title i {
            display: inline-grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary-light) !important;
        }

        .mini-stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)) !important;
            gap: var(--card-gap) !important;
            margin-bottom: var(--section-gap) !important;
        }

        .mini-stat-card {
            min-height: 172px;
            padding: var(--card-padding) !important;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .mini-stat-card::before {
            height: 5px !important;
        }

        .mini-stat-card i {
            width: 58px !important;
            height: 58px !important;
            border-radius: 20px !important;
            margin-bottom: 18px !important;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16) !important;
        }

        .mini-stat-value {
            color: var(--text-primary) !important;
            font-size: var(--font-size-3xl) !important;
            font-weight: var(--font-weight-extrabold) !important;
            line-height: 1;
            letter-spacing: -0.055em;
            margin-bottom: 10px;
        }

        .mini-stat-label {
            color: var(--text-secondary) !important;
            font-size: var(--font-size-sm) !important;
            font-weight: var(--font-weight-bold) !important;
            line-height: 1.45;
        }

        .professor-courses-grid {
            gap: 20px !important;
        }

        .professor-course-card {
            grid-template-columns: minmax(280px, 1.25fr) minmax(320px, 1fr) auto !important;
            gap: 24px !important;
            padding: 28px !important;
            border-radius: 24px !important;
            background:
                radial-gradient(circle at 92% 10%, rgba(37, 99, 235, 0.10), transparent 30%),
                linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94)) !important;
        }

        .professor-course-card::before {
            height: 5px !important;
        }

        .professor-course-head {
            gap: 16px !important;
        }

        .professor-course-icon {
            width: 58px !important;
            height: 58px !important;
            border-radius: 20px !important;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16) !important;
        }

        .professor-course-title {
            color: var(--text-primary) !important;
            font-size: 1.4rem !important;
            font-weight: var(--font-weight-extrabold) !important;
            letter-spacing: -0.04em !important;
            margin-bottom: 10px !important;
        }

        .professor-course-description {
            color: var(--text-secondary) !important;
            font-size: var(--font-size-base) !important;
            line-height: 1.65 !important;
        }

        .status-badge {
            padding: 8px 14px !important;
            border-radius: 999px !important;
            font-size: var(--font-size-xs) !important;
            font-weight: var(--font-weight-extrabold) !important;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .course-chip {
            padding: 10px 14px !important;
            border-radius: 999px !important;
            font-size: var(--font-size-sm) !important;
            font-weight: var(--font-weight-bold) !important;
        }

        .chart-title {
            font-size: var(--font-size-lg) !important;
            margin-bottom: 18px !important;
        }

        .stat-card .value,
        .mini-stat-card .value,
        .metric-value,
        .summary-value {
            color: var(--text-primary) !important;
            font-size: var(--font-size-3xl) !important;
            font-weight: var(--font-weight-extrabold) !important;
            letter-spacing: -0.055em;
        }

        .stat-card h3,
        .mini-stat-card h3,
        .metric-label,
        .summary-label,
        .table th,
        .course-field-label {
            color: var(--text-secondary) !important;
            font-size: var(--font-size-xs) !important;
            font-weight: var(--font-weight-bold) !important;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .table,
        table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th,
        .table th,
        table th {
            background: #f8fafc !important;
            color: var(--text-secondary) !important;
            font-size: var(--font-size-xs) !important;
            font-weight: var(--font-weight-bold) !important;
        }

        .table td,
        table td {
            color: var(--text-secondary) !important;
            font-size: var(--font-size-sm) !important;
            font-weight: var(--font-weight-medium) !important;
        }

        .table tbody tr:hover,
        table tbody tr:hover {
            background: rgba(37, 99, 235, 0.035) !important;
        }

        .search-input,
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        select,
        textarea {
            font-family: var(--font-family-base);
            font-size: var(--font-size-sm) !important;
            color: var(--text-primary) !important;
            border-radius: 14px !important;
        }

        .filters-section {
            padding: 24px !important;
            gap: 18px !important;
            margin-bottom: 28px !important;
        }

        .btn,
        .button,
        .filter-btn,
        .export-btn,
        button[type="submit"] {
            font-size: var(--font-size-sm) !important;
            font-weight: var(--font-weight-bold) !important;
            border-radius: 999px !important;
        }

        .quick-action-card,
        .action-card {
            gap: 18px !important;
            padding: var(--card-padding) !important;
        }

        .quick-action-title,
        .action-title {
            color: var(--text-primary) !important;
            font-size: var(--font-size-lg) !important;
            font-weight: var(--font-weight-extrabold) !important;
        }

        .quick-action-desc,
        .action-desc,
        .course-meta-line,
        .text-muted,
        .empty-state p,
        .helper-text,
        .description,
        small {
            color: var(--text-secondary) !important;
            font-size: var(--font-size-sm) !important;
            font-weight: var(--font-weight-semibold) !important;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px !important;
            }

            .header {
                padding: 24px !important;
            }

            .content-card,
            .chart-card,
            .stat-card,
            .mini-stat-card,
            .course-header,
            .setting-card,
            .settings-card,
            .report-card,
            .summary-card {
                padding: 24px !important;
            }
        }
        /* PROFESSOR_THEME_OVERRIDES_END */
    </style>
    <script>
        (function() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            if (isDark) document.documentElement.classList.add('dark-mode');
        })();
    </script>

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

        /* ===== ADMIN DASHBOARD OVERRIDES ===== */
        .sidebar-group {
            margin: 0 12px 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-group:last-child { border-bottom: none; margin-bottom: 0; }
        .sidebar-group-title {
            margin: 0 0 8px;
            padding: 8px 10px;
            color: rgba(255,255,255,0.55);
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .sidebar-nav { padding-bottom: 80px; }
        .sidebar-footer-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 280px;
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: linear-gradient(180deg, rgba(15,23,42,0.95) 0%, rgba(30,58,138,0.95) 100%);
            z-index: 1001;
            backdrop-filter: blur(18px);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 16px;
            background: rgba(255,255,255,0.09);
            border: 1px solid rgba(255,255,255,0.11);
        }
        .sidebar-user .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name { font-weight: 700; font-size: 0.72rem; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 0.62rem; color: rgba(255,255,255,0.65); }
        .logout-btn-small {
            background: rgba(239,68,68,0.82);
            color: white;
            text-decoration: none;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 800;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Page Header (admin style) */
        .page-header {
            position: relative;
            margin-bottom: 28px;
            padding: 28px;
            background:
                radial-gradient(circle at 8% 18%, rgba(255,255,255,0.22), transparent 30%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 58%, #2563eb 100%) !important;
            border: 1px solid rgba(255,255,255,0.42);
            border-radius: 28px;
            box-shadow: 0 28px 80px rgba(30,58,138,0.2);
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .page-header::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.18;
            background:
                linear-gradient(90deg, rgba(255,255,255,0.34) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,0.28) 1px, transparent 1px);
            background-size: 42px 42px;
            pointer-events: none;
        }
        .breadcrumb { font-size: 0.875rem; color: rgba(255,255,255,0.7); margin-bottom: 8px; }
        .breadcrumb-link { color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 600; }
        .breadcrumb-link:hover { text-decoration: underline; }
        .breadcrumb-separator { margin: 0 6px; }
        .breadcrumb-current { color: rgba(255,255,255,0.75); font-weight: 500; }
        .page-title h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            color: white;
            letter-spacing: -0.055em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            background: none !important;
            -webkit-text-fill-color: white !important;
            text-shadow: 0 2px 18px rgba(15,23,42,0.25);
        }
        .page-title h1 i {
            color: white !important;
            -webkit-text-fill-color: white !important;
            background: transparent !important;
            width: auto !important;
            height: auto !important;
            border-radius: 0 !important;
            display: inline !important;
            font-size: 1em;
        }
        .page-subtitle { color: rgba(255,255,255,0.75); font-size: 0.95rem; margin-top: 6px; }
        .page-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Stat Cards (admin style) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 22px;
            margin-bottom: 34px;
        }
        .stat-card {
            position: relative;
            padding: 26px;
            overflow: hidden;
            background: rgba(255,255,255,0.88) !important;
            border: 1px solid rgba(255,255,255,0.78) !important;
            border-radius: 26px !important;
            box-shadow: 0 18px 48px rgba(15,23,42,0.09) !important;
            backdrop-filter: blur(18px);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
            transform: none !important;
        }
        .stat-card.success::before { background: linear-gradient(90deg, #059669, #10b981); }
        .stat-card.warning::before { background: linear-gradient(90deg, #d97706, #f59e0b); }
        .stat-card.info::before { background: linear-gradient(90deg, #0891b2, #06b6d4); }
        .stat-card.primary { background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(239,246,255,0.6) 100%) !important; }
        .stat-card.success { background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(236,253,245,0.6) 100%) !important; }
        .stat-card.warning { background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(255,251,235,0.6) 100%) !important; }
        .stat-card.info { background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(236,254,255,0.6) 100%) !important; }
        .stat-card:hover { transform: translateY(-7px) !important; box-shadow: 0 26px 70px rgba(15,23,42,0.14) !important; }
        .stat-header {
            display: flex;
            flex-direction: column-reverse;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }
        .stat-title {
            font-size: 0.72rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.12em !important;
            color: #64748b !important;
            margin: 0 !important;
        }
        .stat-icon {
            display: inline-grid !important;
            place-items: center !important;
            width: 58px !important;
            height: 58px !important;
            border-radius: 20px !important;
            background: linear-gradient(135deg, #1e3a8a, #2563eb) !important;
            color: white !important;
            font-size: 1.4rem !important;
            box-shadow: 0 16px 32px rgba(15,23,42,0.16) !important;
        }
        .stat-card.success .stat-icon { background: linear-gradient(135deg, #059669, #047857) !important; }
        .stat-card.warning .stat-icon { background: linear-gradient(135deg, #d97706, #b45309) !important; }
        .stat-card.info .stat-icon { background: linear-gradient(135deg, #0891b2, #0e7490) !important; }
        .stat-value {
            font-size: 2.5rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.04em !important;
            color: #0f172a !important;
            margin-bottom: 12px !important;
            line-height: 1 !important;
        }
        .stat-change {
            font-size: 0.82rem !important;
            color: #475569 !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .stat-change-icon { color: #2563eb; }
        .stat-card.success .stat-change-icon { color: #10b981; }
        .stat-card.warning .stat-change-icon { color: #f59e0b; }
        .progress-bar {
            margin-top: auto !important;
            padding-top: 14px;
            height: auto !important;
            background: transparent !important;
        }
        .progress-bar::before {
            content: '';
            display: block;
            height: 6px;
            border-radius: 999px;
            background: rgba(226,232,240,0.85);
        }
        .progress-fill {
            height: 6px !important;
            margin-top: -6px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb) !important;
            border-radius: 999px !important;
            transition: width 0.6s ease;
            position: relative;
            z-index: 1;
        }
        .progress-fill.success { background: linear-gradient(90deg, #059669, #10b981) !important; }
        .progress-fill.warning { background: linear-gradient(90deg, #d97706, #f59e0b) !important; }
        .progress-fill.info    { background: linear-gradient(90deg, #0891b2, #60a5fa) !important; }

        /* Dark mode - page-header */
        .dark-mode .page-header {
            background: rgba(30,41,59,0.92) !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        .dark-mode .page-title h1 { color: #f8fafc !important; -webkit-text-fill-color: #f8fafc !important; }
        .dark-mode .breadcrumb-current { color: #cbd5e1 !important; }
        .dark-mode .stat-card { background: rgba(30,41,59,0.92) !important; border-color: rgba(255,255,255,0.1) !important; }
        .dark-mode .stat-value { color: #f8fafc !important; }
        .dark-mode .stat-change { color: rgba(248,250,252,0.72) !important; }
    </style>
</head>
<body class="admin-dashboard">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard_professor.php" class="sidebar-logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>EduConnect</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Visão Geral</div>
                        <li class="sidebar-item">
                            <a href="dashboard_professor.php" class="sidebar-link">
                                <i class="fas fa-tachometer-alt sidebar-icon"></i>
                                Dashboard
                            </a>
                        </li>
                    </div>
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Acadêmico</div>
                        <li class="sidebar-item">
                            <a href="cursos_professor.php" class="sidebar-link active">
                                <i class="fas fa-book sidebar-icon"></i>
                                Meus Cursos
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="aulas_professor.php" class="sidebar-link">
                                <i class="fas fa-calendar-alt sidebar-icon"></i>
                                Aulas
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="alunos_professor.php" class="sidebar-link">
                                <i class="fas fa-users sidebar-icon"></i>
                                Alunos
                            </a>
                        </li>
                    </div>
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Sistema</div>
                        <li class="sidebar-item">
                            <a href="relatorios_professor.php" class="sidebar-link">
                                <i class="fas fa-chart-bar sidebar-icon"></i>
                                Relatórios
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="configuracoes_professor.php" class="sidebar-link">
                                <i class="fas fa-cog sidebar-icon"></i>
                                Configurações
                            </a>
                        </li>
                    </div>
                </ul>
            </nav>
            <div class="sidebar-footer-fixed">
                <div class="sidebar-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($professor['nome'], 0, 1)); ?></div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name"><?php echo htmlspecialchars($professor['nome']); ?></div>
                        <div class="sidebar-user-role">Professor</div>
                    </div>
                    <a href="logout.php" class="logout-btn-small"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <div class="page-title">
                    <div class="breadcrumb">
                        <a href="dashboard_professor.php" class="breadcrumb-link">Dashboard</a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current">Meus Cursos</span>
                    </div>
                    <h1><i class="fas fa-book"></i> Meus Cursos</h1>
                </div>
                <div class="page-actions">
                    <button id="darkModeToggle" title="Alternar tema" style="background:rgba(100,116,139,0.1);border:1px solid rgba(100,116,139,0.2);color:#475569;width:40px;height:40px;border-radius:12px;cursor:pointer;font-size:1rem;">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Cursos Atribuídos</div>
                        <div class="stat-icon"><i class="fas fa-book"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $total_cursos_professor; ?></div>
                    <div class="stat-change"><i class="fas fa-layer-group stat-change-icon"></i> Cursos ativos</div>
                    <div class="progress-bar"><div class="progress-fill" style="width:<?php echo $total_catalogo > 0 ? min(100, round($total_cursos_professor/$total_catalogo*100)) : 0; ?>%"></div></div>
                </div>
                <div class="stat-card success">
                    <div class="stat-header">
                        <div class="stat-title">Cursos no Catálogo</div>
                        <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $total_catalogo; ?></div>
                    <div class="stat-change"><i class="fas fa-book-open stat-change-icon"></i> Total disponível</div>
                    <div class="progress-bar"><div class="progress-fill success" style="width:100%"></div></div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-header">
                        <div class="stat-title">Alunos Vinculados</div>
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $total_alunos_cursos; ?></div>
                    <div class="stat-change"><i class="fas fa-user-graduate stat-change-icon"></i> Total de alunos</div>
                    <div class="progress-bar"><div class="progress-fill warning" style="width:<?php echo $total_alunos_cursos > 0 ? min(100, $total_alunos_cursos) : 0; ?>%"></div></div>
                </div>
            </div>

            <!-- Content -->
            <div class="content-card">
                <h2>
                    <i class="fas fa-book"></i>
                    Cursos que você leciona
                </h2>
                
                <?php if ($cursos_professor->num_rows > 0): ?>
                    <div class="professor-courses-grid">
                        <?php while ($curso = $cursos_professor->fetch_assoc()): ?>
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM inscricoes WHERE curso_id = ?");
                            $stmt->bind_param("i", $curso['id']);
                            $stmt->execute();
                            $alunos_curso = $stmt->get_result()->fetch_assoc()['count'];
                            ?>
                            <article class="professor-course-card">
                                <div class="professor-course-head">
                                    <div class="professor-course-icon">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <span class="status-badge status-active">Ativo</span>
                                </div>

                                <div>
                                    <div class="professor-course-title"><?php echo htmlspecialchars($curso['nome']); ?></div>
                                    <div class="professor-course-description"><?php echo htmlspecialchars($curso['descricao']); ?></div>
                                </div>

                                <div class="professor-course-meta">
                                    <span class="course-chip"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($curso['categoria']); ?></span>
                                    <span class="course-chip"><i class="fas fa-signal"></i> <?php echo htmlspecialchars($curso['nivel']); ?></span>
                                    <span class="course-chip"><i class="fas fa-clock"></i> <?php echo (int)$curso['duracao_horas']; ?>h</span>
                                    <span class="course-chip"><i class="fas fa-users"></i> <?php echo (int)$alunos_curso; ?> aluno<?php echo ((int)$alunos_curso === 1) ? '' : 's'; ?></span>
                                </div>

                                <div class="professor-course-footer">
                                    <span class="course-chip"><i class="fas fa-dollar-sign"></i> R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></span>
                                    <a href="detalhes_curso_professor.php?id=<?php echo $curso['id']; ?>" class="btn">Ver Detalhes</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 72px 40px; color: var(--secondary-color);">
                        <div style="width: 128px; height: 128px; margin: 0 auto 30px; border-radius: 36px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(16, 185, 129, 0.10)); box-shadow: 0 24px 55px rgba(15, 23, 42, 0.10);">
                            <i class="fas fa-book-open" style="font-size: 3.6rem; color: var(--primary-color);"></i>
                        </div>
                        <h3 style="font-size: 1.75rem; font-weight: 850; color: var(--dark-color); margin-bottom: 12px; letter-spacing: -0.035em;">Nenhum curso atribuído ainda</h3>
                        <p style="font-size: 1rem; margin: 0 auto 28px; max-width: 620px; line-height: 1.7;">
                            Esta área mostra apenas os cursos vinculados ao seu perfil de professor. Assim que a coordenação atribuir um curso para você, ele aparecerá automaticamente aqui.
                        </p>
                        
                        <div style="max-width: 620px; margin: 0 auto 30px; padding: 20px; background: rgba(248, 250, 252, 0.9); border: 1px solid var(--border-color); border-radius: 20px; text-align: left;">
                            <div style="display: flex; gap: 14px; align-items: flex-start;">
                                <i class="fas fa-circle-info" style="color: var(--primary-color); font-size: 1.25rem; margin-top: 3px;"></i>
                                <div>
                                    <strong style="display: block; color: var(--dark-color); margin-bottom: 4px;">Próximo passo recomendado</strong>
                                    <span>Se você acredita que já deveria ter cursos, fale com o administrador para revisar suas atribuições.</span>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                            <a href="dashboard_professor.php" class="btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-arrow-left"></i> Voltar ao painel
                            </a>
                            <a href="cursos_professor_todos.php" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 999px; color: var(--secondary-dark); background: white; border: 1px solid var(--border-color); font-weight: 800; box-shadow: var(--shadow-sm);">
                                <i class="fas fa-list"></i> Ver catálogo de cursos
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ===== SISTEMA DE TOAST NOTIFICATIONS =====
        function showToast(title, message, type = 'info') {
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
                <i class="fas ${icons[type]} toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Auto-remove após 3 segundos
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'toastSlideIn 0.3s ease-out reverse';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        }
        
        // ===== INTERSECTION OBSERVER PARA ANIMAÇÕES =====
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // ===== MELHORIAS DE ACESSIBILIDADE E INTERATIVIDADE =====
        document.addEventListener('DOMContentLoaded', function() {
            // Observar elementos que entram na viewport
            document.querySelectorAll('.content-card, .table tbody tr').forEach(card => {
                observer.observe(card);
            });
            
            // Inicializar tooltips para elementos com data-tooltip
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                element.setAttribute('aria-label', element.getAttribute('data-tooltip'));
            });
            
            // Melhorar feedback visual em interações
            document.querySelectorAll('.btn, .content-card').forEach(element => {
                element.addEventListener('click', function() {
                    // Feedback tátil sutil
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
            
            // Suporte a navegação por teclado nos cards
            document.querySelectorAll('.content-card').forEach(card => {
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
        });
    </script>
    <script src="sidebar.js"></script>
    <script src="dark-mode.js"></script>
</body>
</html>
