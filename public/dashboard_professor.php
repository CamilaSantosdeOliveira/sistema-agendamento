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

// Contar cursos atribuídos ao professor
$cursos_query = "SELECT COUNT(DISTINCT ac.curso_id) as count 
                 FROM atribuicoes_cursos ac
                 JOIN cursos c ON c.id = ac.curso_id
                 WHERE ac.professor_id = ? AND c.status = 'ativo'";
$stmt = $conn->prepare($cursos_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$cursos_count = $stmt->get_result()->fetch_assoc()['count'];

// Contar alunos do professor
$alunos_query = "SELECT COUNT(DISTINCT a.aluno_id) as count FROM agendamentos a 
                 WHERE a.professor_id = ?";
$stmt = $conn->prepare($alunos_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$alunos_count = $stmt->get_result()->fetch_assoc()['count'];

// Contar aulas agendadas
$aulas_query = "SELECT COUNT(*) as count FROM agendamentos WHERE professor_id = ? AND data_agendamento >= CURDATE()";
$stmt = $conn->prepare($aulas_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$aulas_count = $stmt->get_result()->fetch_assoc()['count'];

// Buscar cursos atribuídos ao professor
$cursos_professor_query = "SELECT DISTINCT c.*, ac.data_atribuicao, ac.status as status_atribuicao FROM cursos c 
                          JOIN atribuicoes_cursos ac ON c.id = ac.curso_id 
                          WHERE ac.professor_id = ? AND c.status = 'ativo'
                          ORDER BY c.nome";
$stmt = $conn->prepare($cursos_professor_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$cursos_professor = $stmt->get_result();

// Buscar próximas aulas
$aulas_query = "SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome 
                FROM agendamentos a 
                JOIN cursos c ON a.curso_id = c.id 
                JOIN usuarios u ON a.aluno_id = u.id 
                WHERE a.professor_id = ? AND a.data_agendamento >= CURDATE() 
                ORDER BY a.data_agendamento, a.hora_inicio 
                LIMIT 5";
$stmt = $conn->prepare($aulas_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$proximas_aulas = $stmt->get_result();

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM cursos");
$stmt->execute();
$catalogo_count = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);

$dashboard_sem_dados = ((int)$cursos_count + (int)$alunos_count + (int)$aulas_count) === 0;
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Dashboard Professor</title>
    <meta name="description" content="Painel do professor EduConnect Tech - Gerencie seus cursos, aulas e alunos de forma simples e eficiente.">
    <meta name="author" content="EduConnect Tech">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="EduConnect - Dashboard Professor">
    <meta property="og:description" content="Painel do professor EduConnect Tech - Gerencie seus cursos, aulas e alunos.">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="EduConnect Tech">
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- jsPDF e html2canvas para exportação -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha512-qZvrmS2ekKPF2mSznTQsxqPgnpkI4DNTlrdUmTzrDgektczlKNRRhy5X5AAOnx5S09ydFYWWNSfcEqDTTHgtNA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <style>
        :root {
            /* Paleta Principal Padronizada */
            --primary-color: #1e3a8a;
            --primary-dark: #0f172a;
            --primary-light: #2563eb;
            --primary-accent: #2563eb;
            
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
            --info-color: #2563eb;
            --info-light: #60a5fa;
            
            /* Cores Neutras Profissionais */
            --light-color: #f8fafc;
            --light-secondary: #f1f5f9;
            --dark-color: #0f172a;
            --dark-secondary: #1e293b;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            
            /* Gradientes Profissionais */
            --gradient-primary: linear-gradient(135deg, #0f172a 0%, #1e3a8a 52%, #2563eb 100%);
            --gradient-accent: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
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
            --section-gap: 40px;
            --card-gap: 28px;
            --card-padding: 28px;
            --card-padding-lg: 32px;
            --card-padding-xl: 36px;

            /* Tipografia */
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family-base);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 30%, #e2e8f0 70%, #f8fafc 100%);
            background-attachment: fixed;
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .main-content .stat-card * {
            color: inherit;
        }

        .main-content .content-card * {
            color: inherit;
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
                opacity: 0.5;
                transform: scale(1);
            }
            50% {
                opacity: 0.3;
                transform: scale(1.05);
            }
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            background: transparent;
            max-width: 100%;
            overflow-x: hidden;
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
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-bold);
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
            font-weight: var(--font-weight-semibold);
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
            min-width: 0;
            max-width: calc(100vw - 280px);
            overflow-x: hidden;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--section-gap);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%) !important;
            padding: var(--card-padding-xl);
            margin: -40px -40px var(--spacing-2xl) -40px;
            margin-top: 20px;
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        .header h1 {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
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
            font-weight: var(--font-weight-bold);
            font-size: var(--font-size-lg);
            box-shadow: var(--shadow-md);
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: var(--transition);
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--danger-color) 0%, var(--danger-light) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            text-decoration: none;
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-bold);
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: var(--card-gap);
            margin-bottom: var(--section-gap);
            align-items: stretch;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            padding: var(--card-padding);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out backwards;
            display: flex;
            flex-direction: column;
            min-height: 180px;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

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
            border-color: rgba(37, 99, 235, 0.25);
        }

        .stat-card h3 {
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-bold);
            color: var(--text-secondary);
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            line-height: 1.4;
        }

        .stat-card .value {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-extrabold);
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .stat-card .icon {
            font-size: 2.25rem;
            color: var(--primary-color);
            margin-bottom: 18px;
            opacity: 0.85;
            transition: var(--transition);
            filter: drop-shadow(0 2px 4px rgba(37, 99, 235, 0.2));
        }

        .stat-card:hover .icon {
            transform: scale(1.1) rotate(5deg);
            opacity: 1;
        }

        .stat-card .description {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
            font-weight: var(--font-weight-medium);
            margin-top: 8px;
        }
        
        /* Progress Bar nos Stat Cards */
        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(226, 232, 240, 0.5);
            border-radius: var(--border-radius-full);
            overflow: hidden;
            margin-top: 16px;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            border-radius: var(--border-radius-full);
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
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
            animation: progressShimmer 2s infinite;
        }
        
        .stat-card.success .progress-fill {
            background: var(--gradient-success);
        }
        
        .stat-card.warning .progress-fill {
            background: linear-gradient(90deg, var(--warning-color), var(--warning-light));
        }
        
        .stat-card.info .progress-fill {
            background: linear-gradient(90deg, var(--info-color), var(--info-light));
        }
        
        @keyframes progressShimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        /* Stat Change Indicator */
        .stat-change {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-semibold);
            margin-top: 8px;
            color: var(--success-color);
        }
        
        .stat-change-icon {
            font-size: 0.75rem;
        }
        
        /* Divisor Visual */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(226, 232, 240, 0.8), 
                transparent);
            margin: var(--spacing-2xl) 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 1px;
            background: var(--gradient-primary);
            border-radius: 2px;
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

        /* Content Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--card-gap);
            margin-top: 0;
            align-items: stretch;
        }

        .content-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            padding: var(--card-padding-xl);
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out backwards;
            position: relative;
            overflow: hidden;
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
        
        /* Tooltips Informativos Melhorados */
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
            padding: 10px 14px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border-radius: 8px;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 10000;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: tooltipFadeIn 0.2s ease-out;
            pointer-events: none;
            font-weight: 500;
            max-width: 250px;
            word-wrap: break-word;
            white-space: normal;
            text-align: center;
        }
        
        [data-tooltip]:hover::after,
        [data-tooltip]:focus::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #1e293b;
            margin-bottom: 4px;
            z-index: 10000;
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

        .dark-mode [data-tooltip]:hover::before {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #1e293b;
        }

        .dark-mode [data-tooltip]:hover::after {
            border-top-color: #f1f5f9;
        }
        
        /* PROFESSOR_THEME_OVERRIDES_END */
    </style>
    <script>
        (function() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            if (isDark) document.documentElement.classList.add('dark-mode');
        })();
    </script>
    <style>
        
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
            font-weight: var(--font-weight-bold);
            color: var(--dark-color);
            margin-bottom: 4px;
            font-size: var(--font-size-base);
        }
        
        .toast-message {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
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

        .content-card h2 {
            font-size: var(--font-size-2xl);
            font-weight: var(--font-weight-extrabold);
            color: var(--text-primary);
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.01em;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            position: relative;
        }

        .content-card h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
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
            font-weight: var(--font-weight-bold);
            color: var(--text-secondary);
            font-size: var(--font-size-xs);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: #f1f5f9;
        }

        .table td {
            color: var(--text-secondary);
            font-weight: var(--font-weight-medium);
            font-size: var(--font-size-sm);
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: #f1f5f9;
            transform: scale(1.01);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: var(--border-radius-full);
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
        }

        .status-active {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            box-shadow: 0 2px 4px rgba(22, 101, 52, 0.1);
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            box-shadow: 0 2px 4px rgba(146, 64, 14, 0.1);
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

        /* Loading Skeleton Melhorado */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
            border-radius: var(--border-radius);
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
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
        .dark-mode {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 30%, #334155 70%, #0f172a 100%);
            color: #f1f5f9;
        }

        .dark-mode .header {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.95) 100%) !important;
            border-bottom-color: rgba(51, 65, 85, 0.6);
        }

        .dark-mode .stat-card,
        .dark-mode .content-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            border-color: rgba(51, 65, 85, 0.8);
            color: #f1f5f9;
        }

        .dark-mode .header h1,
        .dark-mode .stat-card .value,
        .dark-mode .content-card h2 {
            background: linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dark-mode .table th {
            background: #0f172a;
            color: #cbd5e1;
        }

        .dark-mode .table td {
            color: #e2e8f0;
        }

        .dark-mode .table tbody tr:hover {
            background: #1e293b;
        }

        .dark-mode .dark-mode-toggle {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        /* Gráficos Container */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--card-gap);
            margin-bottom: var(--section-gap);
            align-items: stretch;
        }

        .chart-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: var(--card-padding-lg);
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: var(--transition);
        }

        .dark-mode .chart-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: rgba(51, 65, 85, 0.8);
        }

        .chart-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12);
        }

        .chart-title {
            font-size: var(--font-size-lg);
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dark-mode .chart-title {
            color: #cbd5e1;
        }

        .chart-canvas-wrap {
            position: relative;
            max-width: 250px;
            height: 250px;
            margin: 0 auto;
        }

        .chart-card canvas {
            width: 100% !important;
            height: 250px !important;
            max-height: 250px !important;
        }

        #cursosChart,
        #alunosChart,
        #aulasChart {
            display: block;
            max-height: 250px;
        }

        .chart-center-info {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            text-align: center;
        }

        .chart-center-number {
            font-size: 2.25rem;
            font-weight: 850;
            color: #1e3a8a;
            letter-spacing: -0.06em;
            line-height: 1;
        }

        .chart-center-label {
            margin-top: 6px;
            color: var(--text-secondary);
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-bold);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .dark-mode .chart-center-number {
            color: #ffffff;
            text-shadow: 0 1px 6px rgba(0, 0, 0, 0.5);
        }
        
        .dark-mode .chart-center-label {
            color: #e2e8f0;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
        }

        .course-summary-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .course-summary-item {
            padding: 20px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.10), rgba(37, 99, 235, 0.08));
            border: 1px solid rgba(37, 99, 235, 0.14);
        }

        .course-summary-item.success {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(52, 211, 153, 0.08));
            border-color: rgba(5, 150, 105, 0.14);
        }

        .course-summary-item.warning {
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.1), rgba(251, 191, 36, 0.08));
            border-color: rgba(217, 119, 6, 0.14);
        }

        .course-summary-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-bold);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }

        .course-summary-value {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: var(--font-weight-extrabold);
            letter-spacing: -0.04em;
        }

        .courses-table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            margin-top: 8px;
            padding-bottom: 8px;
            scrollbar-width: thin;
        }

        .courses-table {
            min-width: 680px;
            table-layout: fixed;
        }

        .courses-table th,
        .courses-table td {
            padding: 14px 12px;
            vertical-align: middle;
        }

        .courses-table th:nth-child(1),
        .courses-table td:nth-child(1) {
            width: 32%;
        }

        .courses-table th:nth-child(2),
        .courses-table td:nth-child(2) {
            width: 19%;
        }

        .courses-table th:nth-child(3),
        .courses-table td:nth-child(3) {
            width: 18%;
        }

        .courses-table th:nth-child(4),
        .courses-table td:nth-child(4) {
            width: 13%;
        }

        .courses-table th:nth-child(5),
        .courses-table td:nth-child(5) {
            width: 9%;
        }

        .courses-table th:nth-child(6),
        .courses-table td:nth-child(6) {
            width: 9%;
            text-align: right;
        }

        .course-title-cell {
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            margin-bottom: 6px;
            font-size: var(--font-size-base);
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1.35;
        }

        .course-meta-line {
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .course-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: var(--border-radius-sm);
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-bold);
            max-width: 100%;
            white-space: normal;
            line-height: 1.25;
        }

        .course-action-link {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 999px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 70px;
            transition: var(--transition);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
            white-space: nowrap;
        }
        .course-action-link i { font-size: 0.7rem; }
        .course-action-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.32);
        }

        .professor-courses-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 12px;
        }

        .professor-course-card {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
            gap: 18px 24px;
            align-items: end;
            padding: 22px 24px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.045);
            transition: var(--transition);
        }

        /* Título sempre ocupa a linha inteira */
        .professor-course-card > div:first-child {
            grid-column: 1 / -1;
            margin-bottom: 4px;
        }

        .professor-course-card:hover {
            border-color: rgba(37, 99, 235, 0.22);
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
            transform: translateY(-2px);
        }

        .course-meta-line {
            margin-top: 8px;
            gap: 16px !important;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        .course-meta-line span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .course-field-label {
            color: var(--text-secondary);
            font-size: 0.7rem;
            font-weight: var(--font-weight-extrabold);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .course-students-mini {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-primary);
            font-weight: var(--font-weight-bold);
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .professor-course-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                padding: 20px;
            }
            .professor-course-card > div:last-child {
                grid-column: 1 / -1;
                justify-self: start;
            }
        }

        @media (max-width: 480px) {
            .professor-course-card { grid-template-columns: 1fr; gap: 14px; }
            .course-action-link { width: 100%; }
            .course-meta-line { gap: 12px !important; }
        }

        /* Filtros e Busca */
            .filters-section {
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                padding: 24px;
                border-radius: var(--border-radius-lg);
                box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06);
                margin-bottom: 28px;
                display: flex;
                gap: 18px;
                flex-wrap: wrap;
                align-items: center;
            }

            /* Indicador de atualização automática */
            .auto-update-indicator {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: var(--gradient-primary);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 0.75rem;
                box-shadow: var(--shadow-lg);
                z-index: 999;
                display: flex;
                align-items: center;
                gap: 8px;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .auto-update-indicator.active {
                opacity: 1;
            }

            .auto-update-indicator i {
                animation: spin 2s linear infinite;
            }

            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

        .dark-mode .filters-section {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .dark-mode #exportMenu {
            background: #1e293b;
            border: 1px solid #334155;
        }

        .dark-mode #exportMenu button {
            background: #1e293b;
            color: #e2e8f0;
        }

        .dark-mode #exportMenu button:hover {
            background: #334155;
        }

        .search-input {
            flex: 1 1 260px;
            min-width: 260px;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            font-size: var(--font-size-sm);
            transition: var(--transition);
            background: white;
            color: var(--text-primary);
        }

        .dark-mode .search-input {
            background: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .filters-section select {
            cursor: pointer;
        }

        .filters-section select:hover {
            border-color: var(--primary-color);
        }

        /* Estilo para cabeçalhos ordenáveis */
        .table th[onclick] {
            transition: var(--transition);
        }

        .table th[onclick]:hover {
            background: rgba(37, 99, 235, 0.05);
        }

        /* Mensagem de filtro */
        #filterMessage {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            color: #64748b;
            margin-top: 16px;
        }

        .dark-mode #filterMessage {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
            color: #94a3b8;
        }

        .filter-btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-bold);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .export-btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-bold);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Responsive */
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
                padding: 20px;
                padding-top: 40px;
                max-width: 100vw;
            }

            .header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
                padding: 24px 20px 20px 20px;
                margin: -20px -20px var(--spacing-xl) -20px;
                margin-top: 16px;
            }

            .header h1 {
                font-size: 1.75rem;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 24px 20px;
            }

            .stat-card .value {
                font-size: 2.25rem;
            }

            .stat-card h3 {
                font-size: 0.71875rem;
                margin-bottom: 12px;
            }

            .stat-card .icon {
                font-size: 2rem;
                margin-bottom: 14px;
            }

            .content-card {
                padding: 24px;
            }

            .filters-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filters-section .search-input,
            .filters-section select {
                width: 100%;
                min-width: 100%;
            }

            .auto-update-indicator {
                bottom: 80px;
                right: 20px;
                font-size: 0.7rem;
                padding: 6px 12px;
            }

            .table th[onclick] {
                font-size: 0.75rem;
                padding: 12px 8px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 16px;
            }

            .header {
                padding: 16px;
                margin: -16px -16px var(--spacing-lg) -16px;
            }

            .stat-card .value {
                font-size: 1.75rem;
            }
        }

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(59, 130, 246, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.10), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%) !important;
        }

        .sidebar {
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
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
            padding: 40px;
            min-height: 100vh;
        }

        .header {
            position: relative;
            overflow: hidden;
            padding: var(--card-padding-xl);
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
            color: #ffffff;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 850;
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
        .user-info a {
            color: #ffffff !important;
        }

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

        .stats-grid {
            gap: var(--card-gap);
            margin-bottom: var(--section-gap);
        }

        .stat-card,
        .chart-card,
        .content-card {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.78) !important;
            border-radius: 24px !important;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
            backdrop-filter: blur(18px);
        }

        .stat-card {
            padding: var(--card-padding);
        }

        .stat-card:hover,
        .chart-card:hover,
        .content-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14) !important;
        }

        .stat-card .icon {
            display: inline-grid;
            place-items: center;
            width: 58px;
            height: 58px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: #ffffff;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
        }

        .stat-card.success .icon {
            background: linear-gradient(135deg, var(--success-color), #047857);
        }

        .stat-card.warning .icon {
            background: linear-gradient(135deg, var(--warning-color), #b45309);
        }

        .stat-card.info .icon {
            background: linear-gradient(135deg, var(--info-color), var(--primary-color));
        }

        .stat-card .value {
            letter-spacing: -0.055em;
        }

        .chart-card,
        .content-card {
            position: relative;
            overflow: hidden;
        }

        .chart-card::before,
        .content-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
            transform: none;
        }

        .filters-section {
            padding: 24px;
            border: 1px solid rgba(226, 232, 240, 0.75);
            border-radius: 18px;
            background: rgba(248, 250, 252, 0.78);
        }

        .search-input,
        .filters-section select {
            min-height: 46px;
            border-radius: 14px;
        }

        .filter-btn,
        .export-btn {
            border-radius: 999px;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.14);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .table tbody tr:hover {
            background: rgba(37, 99, 235, 0.035);
        }

        .toast-container {
            top: auto;
            bottom: 24px;
            z-index: 13000;
        }

        .stat-card.primary {
            background:
                radial-gradient(circle at 92% 12%, rgba(37, 99, 235, 0.16), transparent 28%),
                rgba(255, 255, 255, 0.92) !important;
        }

        .stat-card.success {
            background:
                radial-gradient(circle at 92% 12%, rgba(16, 185, 129, 0.16), transparent 28%),
                rgba(255, 255, 255, 0.92) !important;
        }

        .stat-card.warning {
            background:
                radial-gradient(circle at 92% 12%, rgba(245, 158, 11, 0.16), transparent 28%),
                rgba(255, 255, 255, 0.92) !important;
        }

        .stat-card.info {
            background:
                radial-gradient(circle at 92% 12%, rgba(37, 99, 235, 0.14), transparent 28%),
                rgba(255, 255, 255, 0.92) !important;
        }

        .chart-card:nth-child(1)::before {
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
        }

        .chart-card:nth-child(2)::before {
            background: linear-gradient(90deg, #059669, #10b981);
        }

        .chart-card:nth-child(3)::before {
            background: linear-gradient(90deg, #d97706, #f59e0b);
        }

        .charts-grid {
            gap: var(--card-gap);
            margin-bottom: var(--section-gap);
        }

        .chart-card {
            padding: var(--card-padding) !important;
            min-height: auto;
        }

        .chart-title {
            margin-bottom: 18px;
        }

        .content-card:nth-child(1)::before {
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
        }

        .content-card:nth-child(2)::before {
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
        }

        .chart-title i,
        .content-card h2 i {
            display: inline-grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: rgba(37, 99, 235, 0.10);
        }

        .content-card:nth-child(2) h2 i {
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary-light);
        }

        .status-badge,
        .agendamento-status {
            border-radius: 999px;
            font-weight: 850;
            letter-spacing: 0.03em;
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: var(--card-gap);
            margin-bottom: var(--section-gap);
            align-items: stretch;
        }

        .quick-action-card {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: var(--card-padding);
            border-radius: 22px;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.78);
            box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .quick-action-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
        }

        .quick-action-card:nth-child(2)::before {
            background: linear-gradient(90deg, #059669, #10b981);
        }

        .quick-action-card:nth-child(3)::before {
            background: linear-gradient(90deg, #d97706, #f59e0b);
        }

        .quick-action-card:nth-child(4)::before {
            background: linear-gradient(90deg, #dc2626, #ef4444);
        }

        .quick-action-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.13);
        }

        .quick-action-icon {
            width: 52px;
            height: 52px;
            display: inline-grid;
            place-items: center;
            border-radius: 18px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            flex: 0 0 auto;
        }

        .quick-action-card:nth-child(2) .quick-action-icon {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .quick-action-card:nth-child(3) .quick-action-icon {
            background: linear-gradient(135deg, #d97706, #b45309);
        }

        .quick-action-card:nth-child(4) .quick-action-icon {
            background: linear-gradient(135deg, #dc2626, #991b1b);
        }

        .quick-action-title {
            color: var(--text-primary);
            font-weight: var(--font-weight-extrabold);
            font-size: var(--font-size-lg);
            margin-bottom: 3px;
        }

        .quick-action-desc {
            color: var(--text-secondary);
            font-weight: var(--font-weight-semibold);
            font-size: var(--font-size-sm);
        }

        .empty-dashboard-insight {
            display: flex;
            gap: 22px;
            align-items: flex-start;
            padding: var(--card-padding);
            margin-bottom: var(--section-gap);
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.10), rgba(37, 99, 235, 0.06));
            border: 1px solid rgba(37, 99, 235, 0.16);
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
        }

        .empty-dashboard-insight i {
            width: 48px;
            height: 48px;
            display: inline-grid;
            place-items: center;
            border-radius: 17px;
            color: #ffffff;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            flex: 0 0 auto;
        }

        .empty-dashboard-insight strong {
            display: block;
            color: var(--text-primary);
            font-size: var(--font-size-xl);
            margin-bottom: 5px;
            font-weight: var(--font-weight-extrabold);
        }

        .empty-dashboard-insight span {
            color: var(--text-secondary);
            font-weight: var(--font-weight-semibold);
            font-size: var(--font-size-sm);
            line-height: 1.55;
        }
    </style>
    <link rel="stylesheet" href="dark-mode.css?v=3">
    <style>
        /* ===== ADMIN DASHBOARD STYLE OVERRIDES ===== */
        body.admin-dashboard { background: linear-gradient(135deg,#f0f4ff 0%,#e8f0fe 25%,#f0f4ff 50%,#e8effe 75%,#f0f4ff 100%) !important; }
        .sidebar { background: radial-gradient(circle at top left,rgba(37,99,235,.16),transparent 34%),linear-gradient(180deg,#020617 0%,#0f172a 48%,#1e3a8a 100%) !important; }
        .sidebar-group { margin:0 12px 18px; padding-bottom:16px; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar-group:last-child { border-bottom:none; margin-bottom:0; }
        .sidebar-group-title { margin:0 0 8px; padding:8px 10px; color:rgba(255,255,255,.55); font-size:.68rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .sidebar-nav { padding-bottom:80px; }
        .sidebar-footer-fixed { position:fixed; bottom:0; left:0; width:280px; padding:12px 16px; border-top:1px solid rgba(255,255,255,.1); background:linear-gradient(180deg,rgba(15,23,42,.95) 0%,rgba(30,58,138,.95) 100%); z-index:1001; backdrop-filter:blur(18px); }
        .sidebar-user { display:flex; align-items:center; gap:12px; padding:8px; border-radius:16px; background:rgba(255,255,255,.09); border:1px solid rgba(255,255,255,.11); }
        .sidebar-user-name { font-weight:700; font-size:.72rem; color:white; }
        .sidebar-user-role { font-size:.62rem; color:rgba(255,255,255,.65); }
        .logout-btn-small { background:rgba(239,68,68,.82); color:white; text-decoration:none; padding:7px 10px; border-radius:999px; font-size:.65rem; font-weight:800; }
        .page-header { position:relative; margin-bottom:28px; padding:28px; background:radial-gradient(circle at 8% 18%,rgba(255,255,255,.22),transparent 30%),linear-gradient(135deg,#0f172a 0%,#1e3a8a 58%,#2563eb 100%) !important; border:1px solid rgba(255,255,255,.42); border-radius:28px; box-shadow:0 28px 80px rgba(30,58,138,.2); overflow:hidden; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
        .page-header::before { content:''; position:absolute; inset:0; opacity:.18; background:linear-gradient(90deg,rgba(255,255,255,.34) 1px,transparent 1px),linear-gradient(rgba(255,255,255,.28) 1px,transparent 1px); background-size:42px 42px; pointer-events:none; }
        .breadcrumb { font-size:.875rem; color:rgba(255,255,255,.7); margin-bottom:8px; }
        .breadcrumb-link { color:rgba(255,255,255,.9); text-decoration:none; font-weight:600; }
        .breadcrumb-separator { margin:0 6px; }
        .breadcrumb-current { color:rgba(255,255,255,.75); font-weight:500; }
        .page-title h1 { font-size:clamp(2rem,4vw,3rem); font-weight:800; color:white; letter-spacing:-.055em; margin:0; display:flex; align-items:center; gap:12px; background:none !important; -webkit-text-fill-color:white !important; text-shadow:0 2px 18px rgba(15,23,42,.25); }
        .page-title h1 i { color:white !important; -webkit-text-fill-color:white !important; background:transparent !important; width:auto !important; height:auto !important; border-radius:0 !important; display:inline !important; font-size:1em; }
        .page-subtitle { color:rgba(255,255,255,.75); font-size:.95rem; margin-top:6px; }
        .page-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:22px; margin-bottom:34px; }
        .stat-card { position:relative; padding:28px; overflow:hidden; min-height:180px; background:radial-gradient(circle at 92% 8%,rgba(37,99,235,.16),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; border:1px solid rgba(226,232,240,.8) !important; border-radius:18px !important; box-shadow:0 2px 8px rgba(0,0,0,.06),0 1px 3px rgba(0,0,0,.04) !important; transition:all .35s cubic-bezier(.4,0,.2,1); display:flex; flex-direction:column; }
        .stat-card::before, .stat-card:hover::before { display:none !important; content:none !important; }
        .stat-card.primary { background:radial-gradient(circle at 92% 8%,rgba(37,99,235,.16),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
        .stat-card.success { background:radial-gradient(circle at 92% 8%,rgba(16,185,129,.18),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
        .stat-card.warning { background:radial-gradient(circle at 92% 8%,rgba(245,158,11,.20),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
        .stat-card.info    { background:radial-gradient(circle at 92% 8%,rgba(96,165,250,.20),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
        .stat-card:hover         { transform:translateY(-4px) !important; box-shadow:inset 0 3px 0 #2563eb,  0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; border-color:rgba(37,99,235,.25) !important; }
        .stat-card.success:hover { box-shadow:inset 0 3px 0 #059669, 0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; }
        .stat-card.warning:hover { box-shadow:inset 0 3px 0 #d97706, 0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; }
        .stat-card.info:hover    { box-shadow:inset 0 3px 0 #0891b2, 0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; }
        .stat-header { display:flex !important; flex-direction:column-reverse !important; align-items:flex-start !important; gap:14px !important; margin-bottom:14px !important; }
        .stat-title { font-size:.72rem !important; font-weight:800 !important; text-transform:uppercase !important; letter-spacing:.12em !important; color:#64748b !important; margin:0 !important; }
        .stat-icon { display:inline-grid !important; place-items:center !important; width:52px !important; height:52px !important; border-radius:18px !important; background:linear-gradient(135deg,#1e3a8a,#2563eb) !important; color:white !important; font-size:1.3rem !important; box-shadow:0 12px 28px rgba(15,23,42,.16) !important; }
        .stat-card.success .stat-icon { background:linear-gradient(135deg,#059669,#047857) !important; }
        .stat-card.warning .stat-icon { background:linear-gradient(135deg,#d97706,#b45309) !important; }
        .stat-card.info .stat-icon { background:linear-gradient(135deg,#0891b2,#0e7490) !important; }
        .stat-icon i { width:auto !important; height:auto !important; background:transparent !important; border-radius:0 !important; box-shadow:none !important; display:inline !important; }
        .stat-value { font-size:2.5rem !important; font-weight:800 !important; letter-spacing:-.04em !important; color:#0f172a !important; margin-bottom:18px !important; line-height:1 !important; }
        .stat-change { font-size:.82rem !important; color:#475569 !important; font-weight:600 !important; display:inline-flex !important; align-items:center !important; gap:6px !important; }
        .stat-change-icon { color:#2563eb; }
        .stat-card.success .stat-change-icon { color:#10b981; }
        .stat-card.warning .stat-change-icon { color:#f59e0b; }
        .stat-card.info .stat-change-icon { color:#06b6d4; }
        .progress-bar { margin-top:auto !important; padding-top:14px; height:auto !important; background:transparent !important; overflow:visible !important; }
        .progress-bar::before { content:''; display:block; height:6px; border-radius:999px; background:rgba(226,232,240,.85); }
        .progress-fill { height:6px !important; margin-top:-6px; background:linear-gradient(90deg,#1e3a8a,#2563eb) !important; border-radius:999px !important; transition:width .6s ease; position:relative; z-index:1; }
        .progress-fill.success { background:linear-gradient(90deg,#059669,#10b981) !important; }
        .progress-fill.warning { background:linear-gradient(90deg,#d97706,#f59e0b) !important; }
        .progress-fill.info { background:linear-gradient(90deg,#0891b2,#06b6d4) !important; }
        .dark-mode .page-header { background:rgba(30,41,59,.92) !important; border-color:rgba(255,255,255,.1) !important; }
        .dark-mode .page-title h1 { color:#f8fafc !important; -webkit-text-fill-color:#f8fafc !important; }
        .dark-mode .stat-card { background:rgba(30,41,59,.92) !important; border-color:rgba(255,255,255,.1) !important; }
        .dark-mode .stat-value { color:#f8fafc !important; }
        .dark-mode .stat-change { color:rgba(248,250,252,.72) !important; }
        body.dark-mode .progress-bar { background:transparent !important; height:auto !important; padding-top:14px !important; overflow:visible !important; }
        body.dark-mode .progress-bar::before { display:block !important; background:rgba(255,255,255,0.18) !important; }
        .dark-mode .course-pill { background:rgba(255,255,255,.08) !important; color:rgba(255,255,255,.75) !important; }
        .proxima-aula-card { border:1px solid var(--border-color); border-radius:var(--border-radius); padding:20px; background:var(--light-secondary); transition:var(--transition); }
        .proxima-aula-titulo { font-weight:500; margin-bottom:8px; color:#334155; font-size:1.0625rem; }
        .proxima-aula-data { font-size:0.875rem; color:var(--primary-color); font-weight:500; display:flex; align-items:center; gap:8px; padding:8px 12px; background:rgba(37,99,235,.1); border-radius:var(--border-radius-sm); }
        .dark-mode .proxima-aula-card { background:rgba(255,255,255,.04) !important; border-color:rgba(255,255,255,.1) !important; }
        .dark-mode .proxima-aula-titulo { color:#f1f5f9 !important; }
        .dark-mode .proxima-aula-data { background:rgba(99,141,255,.18) !important; color:#93c5fd !important; }
    </style>
</head>
<body class="admin-dashboard">

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
                <a href="#" class="sidebar-logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>EduConnect</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Visão Geral</div>
                        <li class="sidebar-item">
                            <a href="dashboard_professor.php" class="sidebar-link active">
                                <i class="fas fa-tachometer-alt sidebar-icon"></i>
                                Dashboard
                            </a>
                        </li>
                    </div>
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Acadêmico</div>
                        <li class="sidebar-item">
                            <a href="cursos_professor.php" class="sidebar-link">
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
                        <span class="breadcrumb-current">Início</span>
                    </div>
                    <h1><i class="fas fa-chalkboard-teacher"></i> Dashboard Professor</h1>

                </div>
                <div class="page-actions">
                    <button id="darkModeToggle" title="Alternar tema" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:white;width:40px;height:40px;border-radius:12px;cursor:pointer;font-size:1rem;">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-header">
                        <h3 class="stat-title">Meus Cursos</h3>
                        <div class="stat-icon"><i class="fas fa-book"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $cursos_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Cursos ativos
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:<?php echo min(($cursos_count/10)*100,100); ?>%"></div>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-header">
                        <h3 class="stat-title">Alunos</h3>
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $alunos_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Estudantes inscritos
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill success" style="width:<?php echo min(($alunos_count/20)*100,100); ?>%"></div>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <h3 class="stat-title">Próximas Aulas</h3>
                        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $aulas_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Aulas agendadas
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill warning" style="width:<?php echo min(($aulas_count/15)*100,100); ?>%"></div>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-header">
                        <h3 class="stat-title">Avaliação</h3>
                        <div class="stat-icon"><i class="fas fa-star"></i></div>
                    </div>
                    <div class="stat-value">4.8</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Média dos alunos
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill info" style="width:96%"></div>
                    </div>
                </div>
            </div>

            <?php if ($dashboard_sem_dados): ?>
                <div class="empty-dashboard-insight">
                    <i class="fas fa-compass"></i>
                    <div>
                        <strong>Seu painel ainda está começando</strong>
                        <span>Quando houver cursos atribuídos, alunos ou aulas agendadas, seus indicadores serão atualizados automaticamente. Enquanto isso, consulte o catálogo ou acompanhe sua agenda.</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="quick-actions-grid">
                <a href="cursos_professor_todos.php" class="quick-action-card">
                    <span class="quick-action-icon"><i class="fas fa-layer-group"></i></span>
                    <span>
                        <span class="quick-action-title">Ver catálogo</span>
                        <span class="quick-action-desc"><?php echo $catalogo_count; ?> cursos disponíveis</span>
                    </span>
                </a>
                <a href="aulas_professor.php" class="quick-action-card">
                    <span class="quick-action-icon"><i class="fas fa-calendar-check"></i></span>
                    <span>
                        <span class="quick-action-title">Minha agenda</span>
                        <span class="quick-action-desc"><?php echo $aulas_count; ?> próximas aulas</span>
                    </span>
                </a>
                <a href="relatorios_professor.php" class="quick-action-card">
                    <span class="quick-action-icon"><i class="fas fa-chart-line"></i></span>
                    <span>
                        <span class="quick-action-title">Relatórios</span>
                        <span class="quick-action-desc">Acompanhar desempenho</span>
                    </span>
                </a>
                <a href="exportar_pdf_professor.php" class="quick-action-card" target="_blank">
                    <span class="quick-action-icon"><i class="fas fa-file-pdf"></i></span>
                    <span>
                        <span class="quick-action-title">Exportar PDF</span>
                        <span class="quick-action-desc">Gerar relatório profissional</span>
                    </span>
                </a>
            </div>

            <!-- Gráficos -->
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        Distribuição de Cursos
                    </div>
                    <div class="chart-canvas-wrap">
                        <canvas id="cursosChart"></canvas>
                        <div class="chart-center-info">
                            <div class="chart-center-number"><?php echo $cursos_count; ?></div>
                            <div class="chart-center-label">Curso<?php echo ((int)$cursos_count === 1) ? '' : 's'; ?> ativo<?php echo ((int)$cursos_count === 1) ? '' : 's'; ?></div>
                        </div>
                    </div>
                </div>
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-users"></i>
                        Crescimento de Alunos
                    </div>
                    <canvas id="alunosChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-calendar-check"></i>
                        Aulas por Mês
                    </div>
                    <canvas id="aulasChart"></canvas>
                </div>
            </div>
            
            <!-- Divisor Visual -->
            <div class="divider"></div>

            <!-- Content -->
            <div class="content-grid">
                <!-- Meus Cursos -->
                <div class="content-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h2 style="margin: 0;">
                            <i class="fas fa-book"></i>
                            Meus Cursos
                        </h2>
                        <div style="position: relative; display: inline-block;">
                            <button class="export-btn" onclick="toggleExportMenu()" <?php echo ($cursos_professor->num_rows == 0) ? 'style="opacity: 0.6; cursor: not-allowed;" title="Não há dados para exportar"' : ''; ?>>
                                <i class="fas fa-download"></i> Exportar <i class="fas fa-chevron-down" style="font-size: 0.7rem; margin-left: 4px;"></i>
                            </button>
                            <div id="exportMenu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 8px; background: white; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-lg); min-width: 150px; z-index: 1000; overflow: hidden;">
                                <button onclick="exportTable('cursosTable', 'csv')" style="width: 100%; padding: 12px 16px; border: none; background: white; text-align: left; cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 8px; font-size: 0.875rem;">
                                    <i class="fas fa-file-csv"></i> CSV
                                </button>
                                <button onclick="exportTable('cursosTable', 'pdf')" style="width: 100%; padding: 12px 16px; border: none; background: white; text-align: left; cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 8px; font-size: 0.875rem; border-top: 1px solid var(--border-color);">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button onclick="exportTable('cursosTable', 'excel')" style="width: 100%; padding: 12px 16px; border: none; background: white; text-align: left; cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 8px; font-size: 0.875rem; border-top: 1px solid var(--border-color);">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if ($cursos_professor->num_rows > 0): ?>
                    <div class="course-summary-strip">
                        <div class="course-summary-item">
                            <div class="course-summary-label"><i class="fas fa-link"></i> Cursos atribuídos</div>
                            <div class="course-summary-value"><?php echo $cursos_count; ?></div>
                        </div>
                        <div class="course-summary-item success">
                            <div class="course-summary-label"><i class="fas fa-users"></i> Alunos atendidos</div>
                            <div class="course-summary-value"><?php echo $alunos_count; ?></div>
                        </div>
                        <div class="course-summary-item warning">
                            <div class="course-summary-label"><i class="fas fa-calendar-check"></i> Próximas aulas</div>
                            <div class="course-summary-value"><?php echo $aulas_count; ?></div>
                        </div>
                    </div>
                    <!-- Filtros Avançados -->
                    <div class="filters-section">
                        <input type="text" class="search-input" id="cursoSearch" placeholder="🔍 Buscar curso, categoria ou nível..." onkeyup="filterTable('cursosTable', 'cursoSearch')">
                        <select class="search-input" id="filterCategoria" style="min-width: 150px;" onchange="filterTable('cursosTable', 'cursoSearch')">
                            <option value="">Todas as categorias</option>
                            <?php 
                            $cursos_professor->data_seek(0);
                            $categorias = [];
                            while ($curso = $cursos_professor->fetch_assoc()) {
                                if (!in_array($curso['categoria'], $categorias)) {
                                    $categorias[] = $curso['categoria'];
                                    echo '<option value="' . htmlspecialchars($curso['categoria']) . '">' . htmlspecialchars($curso['categoria']) . '</option>';
                                }
                            }
                            $cursos_professor->data_seek(0);
                            ?>
                        </select>
                        <select class="search-input" id="filterNivel" style="min-width: 120px;" onchange="filterTable('cursosTable', 'cursoSearch')">
                            <option value="">Todos os níveis</option>
                            <option value="Iniciante">Iniciante</option>
                            <option value="Intermediário">Intermediário</option>
                            <option value="Avançado">Avançado</option>
                        </select>
                        <button class="filter-btn" onclick="resetFilters('cursosTable', 'cursoSearch')">
                            <i class="fas fa-redo"></i> Limpar
                        </button>
                    </div>
                        <div class="professor-courses-list" id="cursosTable">
                            <?php while ($curso = $cursos_professor->fetch_assoc()): ?>
                                <?php
                                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM inscricoes WHERE curso_id = ?");
                                $stmt->bind_param("i", $curso['id']);
                                $stmt->execute();
                                $alunos_curso = $stmt->get_result()->fetch_assoc()['count'];
                                ?>
                                <div class="professor-course-card" data-nome="<?php echo htmlspecialchars($curso['nome']); ?>" data-categoria="<?php echo htmlspecialchars($curso['categoria']); ?>" data-nivel="<?php echo htmlspecialchars($curso['nivel']); ?>" data-alunos="<?php echo (int)$alunos_curso; ?>" data-status="Ativo" data-duracao="<?php echo (int)$curso['duracao_horas']; ?>h" data-atribuido="<?php echo !empty($curso['data_atribuicao']) ? date('d/m/Y', strtotime($curso['data_atribuicao'])) : 'Sem data'; ?>" data-preco="<?php echo !empty($curso['preco']) ? 'R$ ' . number_format($curso['preco'], 2, ',', '.') : '-'; ?>">
                                    <div>
                                        <div class="course-title-cell">
                                            <i class="fas fa-book-open" style="color: var(--primary-color); font-size: 0.875rem;"></i>
                                            <?php echo htmlspecialchars($curso['nome']); ?>
                                        </div>
                                        <div class="course-meta-line">
                                            <span><i class="fas fa-clock"></i> <?php echo $curso['duracao_horas']; ?>h</span>
                                            <span><i class="fas fa-calendar-plus"></i> <?php echo !empty($curso['data_atribuicao']) ? date('d/m/Y', strtotime($curso['data_atribuicao'])) : 'Sem data'; ?></span>
                                            <?php if (!empty($curso['preco'])): ?>
                                                <span style="color: var(--success-color); font-weight: 700;"><i class="fas fa-tag"></i> R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="course-field-label">Categoria</div>
                                        <span class="course-pill" style="background: var(--light-secondary); color: var(--secondary-dark);">
                                            <i class="fas fa-tag" style="font-size: 0.75rem; color: var(--primary-color);"></i>
                                            <?php echo htmlspecialchars($curso['categoria']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="course-field-label">Nível</div>
                                        <span class="course-pill" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%); color: var(--primary-color);">
                                            <i class="fas fa-signal" style="font-size: 0.75rem;"></i>
                                            <?php echo htmlspecialchars($curso['nivel']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="course-field-label">Alunos</div>
                                        <div class="course-students-mini">
                                            <i class="fas fa-users" style="color: var(--success-color);"></i>
                                            <span><?php echo $alunos_curso; ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="course-field-label">Status</div>
                                        <span class="status-badge status-active">
                                            <i class="fas fa-check-circle" style="margin-right: 4px; font-size: 0.7rem;"></i>
                                            Ativo
                                        </span>
                                    </div>
                                    <div>
                                        <a href="detalhes_curso_professor.php?id=<?php echo $curso['id']; ?>" class="course-action-link" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px -1px rgba(37, 99, 235, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)';">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 80px 40px; color: var(--secondary-color); margin-top: 20px;">
                            <div style="width: 140px; height: 140px; margin: 0 auto 40px; background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, rgba(37, 99, 235, 0.06) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1), 0 2px 4px -1px rgba(37, 99, 235, 0.06); position: relative;">
                                <div style="position: absolute; inset: -4px; border-radius: 50%; background: linear-gradient(135deg, rgba(37, 99, 235, 0.2), rgba(37, 99, 235, 0.05)); opacity: 0.5; animation: pulse 2s ease-in-out infinite;"></div>
                                <i class="fas fa-book" style="font-size: 4rem; color: var(--primary-color); opacity: 0.7; position: relative; z-index: 1;"></i>
                            </div>
                            <h3 style="font-size: 1.75rem; font-weight: 600; color: #334155; margin-bottom: 16px; letter-spacing: -0.02em; line-height: 1.2;">
                                Nenhum curso encontrado
                            </h3>
                            <p style="font-size: 1.0625rem; color: var(--secondary-color); margin-bottom: 12px; max-width: 550px; margin-left: auto; margin-right: auto; line-height: 1.7; font-weight: 500;">
                                Você ainda não foi designado para nenhum curso.
                            </p>
                            <p style="font-size: 0.9375rem; color: var(--secondary-light); margin-bottom: 48px; max-width: 500px; margin-left: auto; margin-right: auto;">
                                Entre em contato com o administrador para ser designado a cursos.
                            </p>
                            <div style="display: inline-flex; gap: 16px; flex-wrap: wrap; justify-content: center;">
                                <a href="cursos_professor_todos.php" style="background: var(--gradient-primary); color: white; border: none; padding: 16px 32px; border-radius: var(--border-radius); cursor: pointer; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3), 0 2px 4px -1px rgba(37, 99, 235, 0.2); letter-spacing: 0.01em;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 15px -3px rgba(37, 99, 235, 0.4), 0 4px 6px -2px rgba(37, 99, 235, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(37, 99, 235, 0.3), 0 2px 4px -1px rgba(37, 99, 235, 0.2)';">
                                    <i class="fas fa-list"></i> Ver Todos os Cursos
                                </a>
                                <a href="dashboard_professor.php" style="background: white; color: var(--secondary-dark); border: 2px solid var(--border-color); padding: 16px 32px; border-radius: var(--border-radius); cursor: pointer; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); letter-spacing: 0.01em; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);" onmouseover="this.style.background='var(--light-secondary)'; this.style.borderColor='var(--primary-color)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';" onmouseout="this.style.background='white'; this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)';">
                                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Próximas Aulas -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        Próximas Aulas
                    </h2>
                    
                    <?php if ($proximas_aulas->num_rows > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <?php while ($aula = $proximas_aulas->fetch_assoc()): ?>
                                <div class="proxima-aula-card" onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='var(--primary-color)'; this.style.boxShadow='var(--shadow-sm)';" onmouseout="this.style.transform='translateX(0)'; this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                                    <div class="proxima-aula-titulo"><?php echo $aula['curso_nome']; ?></div>
                                    <div style="font-size: 0.875rem; color: var(--secondary-color); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user" style="color: var(--primary-color);"></i>
                                        <span style="font-weight: 500;"><?php echo $aula['aluno_nome']; ?></span>
                                    </div>
                                    <div class="proxima-aula-data">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('d/m/Y', strtotime($aula['data_agendamento'])); ?> às <?php echo $aula['hora_inicio']; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px 20px; color: var(--secondary-color);">
                            <i class="fas fa-calendar" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3; color: var(--primary-color);"></i>
                            <p style="font-weight: 500; color: #334155;">Nenhuma aula agendada.</p>
                            <p style="font-size: 0.875rem; margin-top: 4px;">Suas próximas aulas aparecerão aqui.</p>
                        </div>
                    <?php endif; ?>
                </div>
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
        
        // ===== ANIMAÇÕES DE CONTAGEM =====
        function animateValue(element, start, end, duration = 1000) {
            if (!element) return;
            
            const startTime = performance.now();
            const range = end - start;
            const isDecimal = end % 1 !== 0;
            const decimals = isDecimal ? 1 : 0;
            
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function (ease-out)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = start + (range * easeOut);
                
                if (isDecimal) {
                    element.textContent = current.toFixed(decimals);
                } else {
                    element.textContent = Math.floor(current);
                }
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    if (isDecimal) {
                        element.textContent = end.toFixed(decimals);
                    } else {
                        element.textContent = end;
                    }
                }
            }
            
            requestAnimationFrame(update);
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

        // ===== MODO ESCURO MOVIDO PARA dark-mode.js =====

        // ===== GRÁFICOS MELHORADOS =====
        function initCharts() {
            const isDarkMode = document.body.classList.contains('dark-mode') || document.documentElement.classList.contains('dark-mode') || localStorage.getItem('darkMode') === 'true';
            const chartTextColor = isDarkMode ? '#cbd5e1' : '#334155';
            const chartGridColor = isDarkMode ? 'rgba(148, 163, 184, 0.15)' : 'rgba(0, 0, 0, 0.05)';
            const chartBorderColor = isDarkMode ? '#1e293b' : '#ffffff';
            // Gráfico de Cursos com tooltips
            const cursosCtx = document.getElementById('cursosChart');
            if (cursosCtx) {
                window.cursosChart = new Chart(cursosCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Cursos Ativos', 'Cursos Pendentes'],
                        datasets: [{
                            data: [<?php echo $cursos_count; ?>, <?php echo max(0, $catalogo_count - $cursos_count); ?>],
                            backgroundColor: [
                                'rgba(37, 99, 235, 0.92)',
                                'rgba(203, 213, 225, 0.82)'
                            ],
                            hoverBackgroundColor: [
                                'rgba(30, 64, 175, 1)',
                                'rgba(148, 163, 184, 0.9)'
                            ],
                            borderWidth: 4,
                            borderColor: chartBorderColor,
                            hoverOffset: 10,
                            cutout: '58%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: { 
                                        size: 12,
                                        weight: '500'
                                    },
                                    color: chartTextColor
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(30, 41, 59, 0.95)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                borderColor: 'rgba(37, 99, 235, 0.5)',
                                borderWidth: 2,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} curso${value !== 1 ? 's' : ''} (${percentage}%)`;
                                    },
                                    labelColor: function(context) {
                                        return {
                                            borderColor: context.dataset.backgroundColor[context.dataIndex],
                                            backgroundColor: context.dataset.backgroundColor[context.dataIndex]
                                        };
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        onHover: (event, activeElements) => {
                            event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });
            }

            // Gráfico de Alunos melhorado
            const alunosCtx = document.getElementById('alunosChart');
            if (alunosCtx) {
                window.alunosChart = new Chart(alunosCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                        datasets: [{
                            label: 'Alunos',
                            data: [<?php echo max(0, $alunos_count - 5); ?>, <?php echo max(0, $alunos_count - 3); ?>, <?php echo max(0, $alunos_count - 1); ?>, <?php echo $alunos_count; ?>, <?php echo $alunos_count; ?>, <?php echo $alunos_count; ?>],
                            borderColor: 'rgba(5, 150, 105, 1)',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: 'rgba(5, 150, 105, 1)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(30, 41, 59, 0.95)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                borderColor: 'rgba(5, 150, 105, 0.5)',
                                borderWidth: 2,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return `Alunos: ${context.parsed.y} aluno${context.parsed.y !== 1 ? 's' : ''}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: chartGridColor
                                },
                                ticks: {
                                    color: chartTextColor
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: chartTextColor
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }

            // Gráfico de Aulas melhorado
            const aulasCtx = document.getElementById('aulasChart');
            if (aulasCtx) {
                window.aulasChart = new Chart(aulasCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                        datasets: [{
                            label: 'Aulas',
                            data: [<?php echo max(0, $aulas_count - 3); ?>, <?php echo max(0, $aulas_count - 2); ?>, <?php echo max(0, $aulas_count - 1); ?>, <?php echo $aulas_count; ?>],
                            backgroundColor: [
                                'rgba(217, 119, 6, 0.8)',
                                'rgba(217, 119, 6, 0.7)',
                                'rgba(217, 119, 6, 0.9)',
                                'rgba(217, 119, 6, 1)'
                            ],
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        return `Aulas: ${context.parsed.y}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: chartGridColor
                                },
                                ticks: {
                                    stepSize: 1,
                                    color: chartTextColor
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: chartTextColor
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }
        }

        // ===== DEBOUNCE FUNCTION =====
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // ===== FILTROS E BUSCA AVANÇADA (COM DEBOUNCE) =====
        
        // Escutar a mudança de tema para atualizar as cores dos gráficos dinamicamente
        window.addEventListener('themeChanged', function() {
            const isDark = document.body.classList.contains('dark-mode') || document.documentElement.classList.contains('dark-mode') || localStorage.getItem('darkMode') === 'true';
            const textColor = isDark ? '#cbd5e1' : '#334155';
            const borderColor = isDark ? '#1e293b' : '#ffffff';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.15)' : 'rgba(0, 0, 0, 0.05)';
            
            if (window.cursosChart) {
                window.cursosChart.options.plugins.legend.labels.color = textColor;
                window.cursosChart.data.datasets[0].borderColor = borderColor;
                window.cursosChart.update();
            }
            if (window.alunosChart) {
                window.alunosChart.options.scales.x.ticks.color = textColor;
                window.alunosChart.options.scales.y.ticks.color = textColor;
                window.alunosChart.options.scales.y.grid.color = gridColor;
                window.alunosChart.update();
            }
            if (window.aulasChart) {
                window.aulasChart.options.scales.x.ticks.color = textColor;
                window.aulasChart.options.scales.y.ticks.color = textColor;
                window.aulasChart.options.scales.y.grid.color = gridColor;
                window.aulasChart.update();
            }
        });
        const debouncedFilterTable = debounce(function(tableId, searchId) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const search = document.getElementById(searchId);
            const filterCategoria = document.getElementById('filterCategoria');
            const filterNivel = document.getElementById('filterNivel');
            
            const searchFilter = search ? search.value.toLowerCase() : '';
            const categoriaFilter = filterCategoria ? filterCategoria.value.toLowerCase() : '';
            const nivelFilter = filterNivel ? filterNivel.value.toLowerCase() : '';
            
            const rows = table.matches('.professor-courses-list') ? table.querySelectorAll('.professor-course-card') : table.querySelectorAll('tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const categoria = row.dataset.categoria ? row.dataset.categoria.toLowerCase() : (row.cells && row.cells[1] ? row.cells[1].textContent.toLowerCase() : '');
                const nivel = row.dataset.nivel ? row.dataset.nivel.toLowerCase() : (row.cells && row.cells[2] ? row.cells[2].textContent.toLowerCase() : '');
                
                const matchSearch = !searchFilter || text.includes(searchFilter);
                const matchCategoria = !categoriaFilter || categoria.includes(categoriaFilter);
                const matchNivel = !nivelFilter || nivel.includes(nivelFilter);
                
                if (matchSearch && matchCategoria && matchNivel) {
                    row.style.display = '';
                    row.style.animation = 'fadeIn 0.3s ease-out';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Mostrar mensagem se não houver resultados
            updateFilterMessage(visibleCount, rows.length);
        }, 300);

        function filterTable(tableId, searchId) {
            debouncedFilterTable(tableId, searchId);
        }

        function updateFilterMessage(visible, total) {
            let messageEl = document.getElementById('filterMessage');
            if (!messageEl && visible === 0 && total > 0) {
                messageEl = document.createElement('div');
                messageEl.id = 'filterMessage';
                messageEl.style.cssText = 'text-align: center; padding: 20px; color: #64748b; margin-top: 16px;';
                const table = document.getElementById('cursosTable');
                if (table && table.parentElement) {
                    table.parentElement.appendChild(messageEl);
                }
            }
            if (messageEl) {
                if (visible === 0 && total > 0) {
                    messageEl.innerHTML = '<i class="fas fa-filter"></i> Nenhum resultado encontrado com os filtros aplicados.';
                } else {
                    messageEl.remove();
                }
            }
        }

        function resetFilters(tableId, searchId) {
            const search = document.getElementById(searchId);
            const filterCategoria = document.getElementById('filterCategoria');
            const filterNivel = document.getElementById('filterNivel');
            
            if (search) search.value = '';
            if (filterCategoria) filterCategoria.value = '';
            if (filterNivel) filterNivel.value = '';
            
            filterTable(tableId, searchId);
        }

        // ===== ORDENAÇÃO DE TABELA =====
        let sortDirection = {};
        
        function sortTable(tableId, columnIndex) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isNumeric = columnIndex === 3; // Coluna de Alunos
            
            // Determinar direção
            if (!sortDirection[tableId + columnIndex]) {
                sortDirection[tableId + columnIndex] = 'asc';
            } else {
                sortDirection[tableId + columnIndex] = sortDirection[tableId + columnIndex] === 'asc' ? 'desc' : 'asc';
            }
            
            const direction = sortDirection[tableId + columnIndex];
            
            // Ordenar
            rows.sort((a, b) => {
                let aVal = a.cells[columnIndex] ? a.cells[columnIndex].textContent.trim() : '';
                let bVal = b.cells[columnIndex] ? b.cells[columnIndex].textContent.trim() : '';
                
                if (isNumeric) {
                    aVal = parseInt(aVal) || 0;
                    bVal = parseInt(bVal) || 0;
                } else {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }
                
                if (direction === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
            
            // Atualizar ícones
            const headers = table.querySelectorAll('th');
            headers.forEach((th, idx) => {
                if (idx === columnIndex) {
                    const icon = th.querySelector('i');
                    if (icon) {
                        icon.className = direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                    }
                } else {
                    const icon = th.querySelector('i');
                    if (icon && icon.classList.contains('fa-sort-up') || icon.classList.contains('fa-sort-down')) {
                        icon.className = 'fas fa-sort';
                    }
                }
            });
            
            // Reordenar no DOM
            rows.forEach(row => tbody.appendChild(row));
        }

        // ===== EXPORTAÇÃO =====
        function toggleExportMenu() {
            const menu = document.getElementById('exportMenu');
            if (!menu) return;
            
            // Verificar se há tabela antes de mostrar o menu
            const table = document.getElementById('cursosTable');
            if (!table) {
                showToast('Aviso', 'Não há dados para exportar. Você precisa ter cursos cadastrados primeiro.', 'warning');
                return;
            }
            
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        // Fechar menu ao clicar fora
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            const btn = document.querySelector('.export-btn');
            if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        function exportTable(tableId, format = 'csv') {
            const table = document.getElementById(tableId);
            const menu = document.getElementById('exportMenu');

            if (!table) {
                showToast('Aviso', 'Não há dados para exportar. Você precisa ter cursos cadastrados primeiro.', 'warning');
                if (menu) menu.style.display = 'none';
                return;
            }

            const isCardsList = table.matches('.professor-courses-list');
            const headers = ['Curso', 'Categoria', 'Nível', 'Alunos', 'Status', 'Duração', 'Atribuído em', 'Preço'];
            let dataRows = [];

            if (isCardsList) {
                dataRows = Array.from(table.querySelectorAll('.professor-course-card'))
                    .filter(card => window.getComputedStyle(card).display !== 'none')
                    .map(card => [
                        card.dataset.nome || '',
                        card.dataset.categoria || '',
                        card.dataset.nivel || '',
                        card.dataset.alunos || '0',
                        card.dataset.status || 'Ativo',
                        card.dataset.duracao || '',
                        card.dataset.atribuido || '',
                        card.dataset.preco || '-'
                    ]);
            } else {
                const rows = Array.from(table.querySelectorAll('tbody tr'))
                    .filter(row => window.getComputedStyle(row).display !== 'none');
                dataRows = rows.map(row => Array.from(row.querySelectorAll('td')).map(cell => cell.innerText.trim()));
            }

            if (dataRows.length === 0) {
                showToast('Aviso', 'Não há dados visíveis para exportar. Limpe os filtros primeiro.', 'warning');
                if (menu) menu.style.display = 'none';
                return;
            }

            const date = new Date().toISOString().split('T')[0];
            const filename = 'cursos_' + date;
            const escapeCsv = value => '"' + String(value).replace(/"/g, '""') + '"';
            const escapeHtml = value => String(value).replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));

            if (format === 'csv') {
                const csvRows = [headers, ...dataRows].map(row => row.map(escapeCsv).join(','));
                const blob = new Blob(['\ufeff' + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                showToast('Sucesso', 'Dados exportados em CSV!', 'success');
            } else if (format === 'pdf') {
                if (typeof window.jspdf === 'undefined') {
                    showToast('Erro', 'Biblioteca jsPDF não carregada. Recarregue a página.', 'error');
                    if (menu) menu.style.display = 'none';
                    return;
                }

                try {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('l', 'mm', 'a4');
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const margin = 12;
                    const usableWidth = pageWidth - (margin * 2);
                    const colWidths = [55, 34, 28, 18, 24, 22, 28, 28];
                    let y = 18;

                    pdf.setFont('helvetica', 'bold');
                    pdf.setFontSize(16);
                    pdf.text('Relatório de Cursos do Professor', margin, y);
                    y += 8;
                    pdf.setFont('helvetica', 'normal');
                    pdf.setFontSize(9);
                    pdf.text('Gerado em ' + new Date().toLocaleDateString('pt-BR'), margin, y);
                    y += 10;

                    const drawRow = (row, isHeader = false) => {
                        let x = margin;
                        pdf.setFont('helvetica', isHeader ? 'bold' : 'normal');
                        pdf.setFontSize(isHeader ? 8 : 7);
                        if (isHeader) {
                            pdf.setFillColor(37, 99, 235);
                            pdf.setTextColor(255, 255, 255);
                            pdf.rect(margin, y - 5, usableWidth, 8, 'F');
                        } else {
                            pdf.setTextColor(30, 41, 59);
                        }
                        row.forEach((cell, index) => {
                            const text = pdf.splitTextToSize(String(cell), colWidths[index] - 2);
                            pdf.text(text.slice(0, 2), x + 1, y);
                            x += colWidths[index];
                        });
                        y += isHeader ? 8 : 10;
                    };

                    drawRow(headers, true);
                    dataRows.forEach(row => {
                        if (y > 190) {
                            pdf.addPage();
                            y = 18;
                            drawRow(headers, true);
                        }
                        drawRow(row);
                    });

                    pdf.save(filename + '.pdf');
                    showToast('Sucesso', 'PDF gerado com sucesso!', 'success');
                } catch (error) {
                    console.error('Erro ao gerar PDF:', error);
                    showToast('Erro', 'Erro ao gerar PDF. Verifique o console.', 'error');
                }
            } else if (format === 'excel') {
                const tableHtml = '<table><thead><tr>' + headers.map(header => '<th>' + escapeHtml(header) + '</th>').join('') + '</tr></thead><tbody>' + dataRows.map(row => '<tr>' + row.map(cell => '<td>' + escapeHtml(cell) + '</td>').join('') + '</tr>').join('') + '</tbody></table>';
                const blob = new Blob(['\ufeff' + tableHtml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename + '.xls');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                showToast('Sucesso', 'Dados exportados em Excel!', 'success');
            }

            if (menu) menu.style.display = 'none';
        }
        // ===== ATUALIZAÇÃO AUTOMÁTICA =====
        let autoUpdateInterval = null;
        
        function startAutoUpdate() {
            // Atualizar a cada 30 segundos
            autoUpdateInterval = setInterval(() => {
                updateDashboardData();
            }, 30000);
        }
        
        function stopAutoUpdate() {
            if (autoUpdateInterval) {
                clearInterval(autoUpdateInterval);
                autoUpdateInterval = null;
            }
        }
        
        function updateDashboardData() {
            // Mostrar indicador
            showUpdateIndicator();
            
            // Atualizar contadores com animação
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(element => {
                const currentValue = parseFloat(element.textContent) || 0;
                // Em produção, buscar dados reais do servidor via AJAX
                // Por enquanto, manter valores atuais
                // const newValue = currentValue + Math.floor(Math.random() * 2) - 1;
                // if (newValue >= 0) {
                //     animateValue(element, currentValue, newValue, 500);
                // }
            });
            
            // Atualizar gráficos suavemente
            if (window.cursosChart) {
                window.cursosChart.update('none');
            }
            if (window.alunosChart) {
                window.alunosChart.update('none');
            }
            if (window.aulasChart) {
                window.aulasChart.update('none');
            }
            
            // Ocultar indicador após 2 segundos
            setTimeout(() => {
                hideUpdateIndicator();
            }, 2000);
        }

        function showUpdateIndicator() {
            let indicator = document.getElementById('autoUpdateIndicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'autoUpdateIndicator';
                indicator.className = 'auto-update-indicator';
                indicator.innerHTML = '<i class="fas fa-sync-alt"></i> <span>Atualizando...</span>';
                document.body.appendChild(indicator);
            }
            indicator.classList.add('active');
        }

        function hideUpdateIndicator() {
            const indicator = document.getElementById('autoUpdateIndicator');
            if (indicator) {
                indicator.classList.remove('active');
            }
        }

        // ===== TOOLTIPS INFORMATIVOS =====
        function initTooltips() {
            // Tooltips nos cards de estatísticas
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                const title = card.querySelector('h3')?.textContent || '';
                card.setAttribute('data-tooltip', `Clique para ver detalhes de ${title}`);
                card.style.cursor = 'pointer';
            });
            
            // Tooltips nos gráficos
            const chartCards = document.querySelectorAll('.chart-card');
            chartCards.forEach(card => {
                const title = card.querySelector('.chart-title')?.textContent || '';
                card.setAttribute('data-tooltip', `Gráfico: ${title}`);
            });
            
            // Tooltips nos botões
            const exportBtn = document.querySelector('.export-btn');
            if (exportBtn) {
                exportBtn.setAttribute('data-tooltip', 'Exportar dados em CSV, PDF ou Excel');
            }
        }

        // ===== SHORTCUTS DE TECLADO =====
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K para buscar
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('cursoSearch');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            
            // Esc para fechar menus
            if (e.key === 'Escape') {
                const exportMenu = document.getElementById('exportMenu');
                if (exportMenu && exportMenu.style.display !== 'none') {
                    exportMenu.style.display = 'none';
                }
            }
            
            // Ctrl/Cmd + D para dark mode
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                const darkModeToggle = document.getElementById('darkModeToggle');
                if (darkModeToggle) {
                    darkModeToggle.click();
                }
            }
        });

        // ===== MELHORIAS DE ACESSIBILIDADE E INTERATIVIDADE =====
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar gráficos
            initCharts();
            
            // Inicializar tooltips
            initTooltips();
            
            // Iniciar atualização automática
            startAutoUpdate();
            
            // Adicionar event listeners para filtros com debounce
            const cursoSearch = document.getElementById('cursoSearch');
            const filterCategoria = document.getElementById('filterCategoria');
            const filterNivel = document.getElementById('filterNivel');
            
            if (cursoSearch) {
                cursoSearch.addEventListener('input', function() {
                    filterTable('cursosTable', 'cursoSearch');
                });
            }
            
            if (filterCategoria) {
                filterCategoria.addEventListener('change', function() {
                    filterTable('cursosTable', 'cursoSearch');
                });
            }
            
            if (filterNivel) {
                filterNivel.addEventListener('change', function() {
                    filterTable('cursosTable', 'cursoSearch');
                });
            }
            
            // Adicionar labels aos botões para acessibilidade
            const buttons = document.querySelectorAll('button:not([aria-label])');
            buttons.forEach(btn => {
                if (btn.textContent.trim()) {
                    btn.setAttribute('aria-label', btn.textContent.trim());
                }
            });

            // Adicionar roles aos elementos interativos
            const interactiveElements = document.querySelectorAll('.stat-card, .course-card, .chart-card');
            interactiveElements.forEach(el => {
                el.setAttribute('role', 'article');
                el.setAttribute('tabindex', '0');
            });
            
            // Pausar atualização quando a aba não está visível
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoUpdate();
                } else {
                    startAutoUpdate();
                }
            });
            // ===== MOBILE MENU TOGGLE =====
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            function toggleMobileMenu() {
                sidebar.classList.toggle('active');
                mobileMenuToggle.classList.toggle('active');
                mobileOverlay.classList.toggle('active');
                const icon = mobileMenuToggle.querySelector('i');
                if (sidebar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }

            function closeMobileMenu() {
                sidebar.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                mobileOverlay.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
            
            if (mobileMenuToggle && sidebar && mobileOverlay) {
                mobileMenuToggle.addEventListener('click', toggleMobileMenu);
                mobileOverlay.addEventListener('click', closeMobileMenu);

                // Fechar menu ao clicar fora
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                            closeMobileMenu();
                        }
                    }
                });

                // Fechar menu ao clicar em um link
                const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            closeMobileMenu();
                        }
                    });
                });

                // Fechar menu ao redimensionar a janela
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        closeMobileMenu();
                    }
                });
            }

            // Animar valores dos stat cards
            setTimeout(() => {
                const statValues = document.querySelectorAll('.stat-value');
                statValues.forEach((element, index) => {
                    const finalValue = parseFloat(element.textContent) || 0;
                    if (finalValue > 0) {
                        animateValue(element, 0, finalValue, 1000);
                    }
                });
            }, 300);
            
            // Observar elementos que entram na viewport
            document.querySelectorAll('.content-card, .stat-card').forEach(card => {
                observer.observe(card);
            });
            
            // Inicializar tooltips para elementos com data-tooltip
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                element.setAttribute('aria-label', element.getAttribute('data-tooltip'));
            });
            
            // Melhorar feedback visual em interações
            document.querySelectorAll('.btn, .stat-card, .content-card').forEach(element => {
                element.addEventListener('click', function() {
                    // Feedback tátil sutil
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
            
            // Suporte a navegação por teclado nos cards
            document.querySelectorAll('.stat-card, .content-card').forEach(card => {
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






