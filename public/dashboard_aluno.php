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

// Contar aulas agendadas (usando agendamentos como inscriçÃµes)
$aulas_agendadas_query = "SELECT COUNT(*) as count FROM agendamentos WHERE aluno_id = ?";
$stmt = $conn->prepare($aulas_agendadas_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aulas_agendadas_count = $stmt->get_result()->fetch_assoc()['count'];

// Contar aulas assistidas (aulas passadas)
$aulas_assistidas_query = "SELECT COUNT(*) as count FROM agendamentos WHERE aluno_id = ? AND data_agendamento < CURDATE()";
$stmt = $conn->prepare($aulas_assistidas_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aulas_assistidas_count = $stmt->get_result()->fetch_assoc()['count'];

// Contar próximas aulas
$proximas_aulas_query = "SELECT COUNT(*) as count FROM agendamentos WHERE aluno_id = ? AND data_agendamento >= CURDATE()";
$stmt = $conn->prepare($proximas_aulas_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$proximas_aulas_count = $stmt->get_result()->fetch_assoc()['count'];

// Buscar cursos do aluno (via agendamentos)
$cursos_aluno_query = "SELECT DISTINCT c.*, u.nome as professor_nome 
                       FROM agendamentos a 
                       JOIN cursos c ON a.curso_id = c.id 
                       JOIN usuarios u ON a.professor_id = u.id 
                       WHERE a.aluno_id = ? 
                       ORDER BY c.nome";
$stmt = $conn->prepare($cursos_aluno_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$cursos_aluno = $stmt->get_result();

// Buscar próximas aulas
$aulas_query = "SELECT a.*, c.nome as curso_nome, u.nome as professor_nome 
                FROM agendamentos a 
                JOIN cursos c ON a.curso_id = c.id 
                JOIN usuarios u ON a.professor_id = u.id 
                WHERE a.aluno_id = ? AND a.data_agendamento >= CURDATE() 
                ORDER BY a.data_agendamento, a.hora_inicio 
                LIMIT 5";
$stmt = $conn->prepare($aulas_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$proximas_aulas = $stmt->get_result();

// Buscar cursos disponíveis (todos os cursos menos os que o aluno já tem agendamentos)
$cursos_disponiveis_query = "SELECT c.*, 
                             (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id AND aluno_id = ?) as ja_inscrito
                             FROM cursos c 
                             ORDER BY c.nome 
                             LIMIT 6";
$stmt = $conn->prepare($cursos_disponiveis_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$cursos_disponiveis = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Dashboard Aluno</title>
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

        html {
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
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
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
            font-feature-settings: 'kern' 1, 'liga' 1;
            font-optical-sizing: auto;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
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
            position: relative;
            z-index: 1;
            background: transparent;
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                overflow-x: hidden;
            }
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
            left: 0;
            top: 0;
        }

        /* Sidebar escondido por padrão no mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
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
            font-weight: 700;
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
            font-weight: 600;
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
            transition: margin-left 0.3s ease;
            width: calc(100% - 280px);
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
            gap: 24px;
            flex-wrap: wrap;
        }

        .header h1 {
            font-size: 2.25rem;
            font-weight: 700;
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
            font-size: 2rem;
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

        .user-avatar::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.2), rgba(16, 185, 129, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .user-avatar:hover::before {
            opacity: 1;
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
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 28px;
            margin-bottom: var(--spacing-xl);
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
            border-color: rgba(5, 150, 105, 0.25);
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
            filter: drop-shadow(0 2px 4px rgba(5, 150, 105, 0.2));
        }

        .stat-card:hover .icon {
            transform: scale(1.1) rotate(5deg);
            opacity: 1;
        }

        /* Donut Chart para Progresso */
        .progress-donut-container {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-donut-container canvas {
            position: absolute;
            top: 0;
            left: 0;
        }

        .progress-donut-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 2;
        }

        .progress-percentage {
            font-size: 1.375rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            display: block;
        }

        .stat-card.info {
            align-items: center;
        }

        .stat-card.info h3 {
            text-align: center;
        }

        .stat-card.info .value {
            text-align: center;
        }

        .stat-card.info .stat-change {
            justify-content: center;
        }

        /* Ajustes para modo escuro no donut chart */
        body.dark-mode .progress-donut-container canvas {
            filter: brightness(0.9);
        }

        body.dark-mode .progress-percentage {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card .description {
            font-size: 0.8125rem;
            color: #64748b;
            font-weight: 400;
            margin-top: 0;
            line-height: 1.5;
        }

        /* Progress Bar nos Stat Cards */
        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(226, 232, 240, 0.5);
            border-radius: 20px;
            overflow: hidden;
            margin-top: auto;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            border-radius: 20px;
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
        
        @keyframes progressShimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        .stat-change {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8125rem;
            font-weight: 400;
            margin-top: 4px;
            margin-bottom: 16px;
            color: #64748b;
            line-height: 1.4;
        }

        .stat-change-icon {
            font-size: 0.7rem;
        }

        /* Content Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--spacing-lg);
            margin-top: var(--spacing-md);
        }

        .content-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            padding: 44px 40px;
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
            border-color: rgba(5, 150, 105, 0.2);
        }

        .content-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 36px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.01em;
            padding-bottom: 24px;
            border-bottom: 2px solid #f1f5f9;
            position: relative;
            line-height: 1.3;
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

        /* Course Cards */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            padding: 24px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
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

        .course-card:hover::before {
            transform: scaleX(1);
        }

        .course-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
            border-color: rgba(5, 150, 105, 0.3);
        }

        body.dark-mode .course-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: rgba(51, 65, 85, 0.8);
        }

        .course-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .course-card .professor {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin-bottom: 12px;
        }

        .course-card .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 0.875rem;
            color: var(--secondary-color);
        }

        .course-card .price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--success-color);
            margin-bottom: 16px;
        }

        .enroll-btn {
            background: var(--success-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
        }

        .enroll-btn:hover {
            background: #059669;
        }

        .enroll-btn.disabled {
            background: var(--secondary-color);
            cursor: not-allowed;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table td {
            color: var(--dark-color);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
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
        body.dark-mode .content-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            border-color: rgba(51, 65, 85, 0.8);
            color: #f1f5f9;
        }

        body.dark-mode .header h1,
        body.dark-mode .stat-card .value,
        body.dark-mode .content-card h2 {
            background: linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        body.dark-mode .table th {
            background: #0f172a;
            color: #cbd5e1;
        }

        body.dark-mode .table td {
            color: #e2e8f0;
        }

        body.dark-mode .table tbody tr:hover {
            background: #1e293b;
        }

        body.dark-mode .dark-mode-toggle {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        body.dark-mode #exportMenu {
            background: #1e293b;
            border: 1px solid #334155;
        }

        body.dark-mode #exportMenu button {
            background: #1e293b;
            color: #e2e8f0;
        }

        body.dark-mode #exportMenu button:hover {
            background: #334155;
        }

        body.dark-mode #filterMessage {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
            color: #94a3b8;
        }

        /* Gráficos Container */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .chart-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 28px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: var(--transition);
        }

        body.dark-mode .chart-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: rgba(51, 65, 85, 0.8);
        }

        .chart-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12);
        }

        .chart-title {
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.dark-mode .chart-title {
            color: #cbd5e1;
        }

        /* Filtros e Busca */
        .filters-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 24px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: var(--spacing-lg);
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }

        body.dark-mode .filters-section {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            font-size: 0.875rem;
            transition: var(--transition);
            background: white;
            color: #334155;
        }

        body.dark-mode .search-input {
            background: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }

        .filter-btn {
            padding: 12px 20px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
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
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Tables Melhoradas */
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
            font-weight: 600;
            color: #475569;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: #f1f5f9;
            cursor: pointer;
            user-select: none;
        }

        .table th:hover {
            background: rgba(5, 150, 105, 0.05);
        }

        .table td {
            color: #475569;
            font-weight: 400;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: #f1f5f9;
            transform: scale(1.01);
        }

        body.dark-mode .table tbody tr:hover {
            background: #1e293b;
        }

        /* Status Badge */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
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

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 10001;
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
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Loading Skeleton */
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

        .skeleton-card {
            height: 200px;
            margin-bottom: 24px;
        }

        body.dark-mode .skeleton {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
        }

        /* Tooltips */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: max-content;
            max-width: 250px;
            background-color: #1e293b;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 8px 12px;
            position: absolute;
            z-index: 10001;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s, visibility 0.3s, transform 0.3s;
            font-size: 0.8125rem;
            font-weight: 400;
            box-shadow: var(--shadow-xl);
            pointer-events: none;
            white-space: nowrap;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-4px);
        }

        body.dark-mode .tooltip .tooltiptext {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        body.dark-mode .tooltip .tooltiptext::after {
            border-color: #f1f5f9 transparent transparent transparent;
        }

        /* Loading States */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius);
            z-index: 10;
            backdrop-filter: blur(2px);
        }

        body.dark-mode .loading-overlay {
            background: rgba(30, 41, 59, 0.9);
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(5, 150, 105, 0.1);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Smooth Progress Animation */
        .progress-fill {
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Auto Update Indicator */
        .auto-update-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--gradient-primary);
            color: white;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            z-index: 10000;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .auto-update-indicator.active {
            opacity: 1;
            transform: translateY(0);
        }

        .auto-update-indicator i {
            animation: spin 1s linear infinite;
        }

        /* Toast Notifications */
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
            min-width: 300px;
        }

        .toast-icon {
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 0.9375rem;
            color: #334155;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 0.875rem;
            color: #64748b;
        }

        .toast-close {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            border-radius: 4px;
            flex-shrink: 0;
        }

        .toast-close:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #334155;
        }

        body.dark-mode .toast {
            background: #1e293b;
            border-left-color: var(--primary-color);
        }

        body.dark-mode .toast-title {
            color: #f1f5f9;
        }

        body.dark-mode .toast-message {
            color: #cbd5e1;
        }

        body.dark-mode .toast-close {
            color: #94a3b8;
        }

        body.dark-mode .toast-close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
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
        
        .toast.error {
            border-left-color: var(--danger-color);
        }
        
        .toast.warning {
            border-left-color: var(--warning-color);
        }
        
        .toast.info {
            border-left-color: var(--info-color);
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

        /* Tooltips */
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

        /* Responsive - Tablets */
        @media (max-width: 1024px) and (min-width: 769px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .charts-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .header {
                flex-wrap: wrap;
            }

            .user-info {
                flex-wrap: wrap;
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
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease !important;
                position: fixed !important;
                z-index: 10000 !important;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3) !important;
                width: 280px !important;
                left: 0 !important;
                top: 0 !important;
            }

            .sidebar.active {
                transform: translateX(0) !important;
            }

            .mobile-overlay.active {
                z-index: 9999 !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 20px 16px !important;
                padding-top: 80px !important;
                overflow-x: hidden;
                width: 100% !important;
                max-width: 100% !important;
            }

            .dashboard-container {
                position: relative;
            }

            /* Melhorias de scroll em mobile */
            .main-content::-webkit-scrollbar {
                width: 4px;
            }

            .main-content::-webkit-scrollbar-track {
                background: transparent;
            }

            .main-content::-webkit-scrollbar-thumb {
                background: rgba(5, 150, 105, 0.3);
                border-radius: 2px;
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
                gap: 20px;
            }

            .stat-card {
                min-height: auto;
            }

            .course-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 24px 20px;
                min-height: auto;
            }

            .stat-card .value {
                font-size: 2.25rem;
                font-weight: 700;
            }

            .stat-card h3 {
                margin-bottom: 12px;
                font-size: 0.71875rem;
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
                padding: 20px;
                gap: 12px;
            }

            .filters-section .search-input,
            .filters-section select {
                width: 100%;
                min-width: 100%;
                padding: 14px 16px;
            }

            .filter-btn,
            .export-btn {
                width: 100%;
                justify-content: center;
                padding: 14px 20px;
            }

            /* Tabela responsiva */
            .table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0 -16px;
                padding: 0 16px;
            }

            .table {
                min-width: 600px;
            }

            .table th,
            .table td {
                padding: 12px 8px;
                font-size: 0.875rem;
            }

            .auto-update-indicator {
                bottom: 80px;
                right: 20px;
                font-size: 0.7rem;
                padding: 6px 12px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .chart-container {
                padding: 20px;
            }

            .course-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .course-card {
                padding: 20px;
            }

            .user-info {
                flex-wrap: wrap;
                gap: 12px;
            }

            .user-avatar {
                width: 44px;
                height: 44px;
                font-size: 1.125rem;
            }

            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: 100%;
            }

            .toast {
                min-width: auto;
                width: 100%;
            }

            .progress-donut-container {
                width: 90px;
                height: 90px;
            }

            .progress-donut-container canvas {
                width: 90px;
                height: 90px;
            }

            .progress-percentage {
                font-size: 1.125rem;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 16px;
                padding-top: 70px;
            }

            .header {
                padding: 16px;
                margin: -16px -16px var(--spacing-lg) -16px;
                margin-top: 12px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .stat-card {
                padding: 20px 16px;
            }

            .stat-card .value {
                font-size: 2rem;
            }

            .stat-card h3 {
                font-size: 0.6875rem;
            }

            .stat-card .icon {
                font-size: 1.75rem;
            }

            .content-card {
                padding: 20px;
            }

            .content-card h2 {
                font-size: 1.25rem;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .logout-btn {
                padding: 8px 16px;
                font-size: 0.8125rem;
            }

            .mobile-menu-toggle {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }

            .dark-mode-toggle {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .progress-donut-container {
                width: 80px;
                height: 80px;
            }

            .progress-donut-container canvas {
                width: 80px;
                height: 80px;
            }

            .progress-percentage {
                font-size: 1rem;
            }

            .chart-container {
                padding: 16px;
            }

            .course-card {
                padding: 16px;
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
    padding: 11px 14px !important;
    border-radius: 12px;
    border-left: 0 !important;
    font-weight: var(--font-weight-semibold) !important;
    font-size: 0.875rem !important;
    color: rgba(255, 255, 255, 0.75) !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}
.sidebar-link::before, .nav-link::before {
    display: none !important;
}
.sidebar-link:hover, .sidebar-link.active,
.nav-link:hover, .nav-link.active {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: none !important;
    transform: translateX(2px);
}
.sidebar-link.active {
    background: rgba(255, 255, 255, 0.16) !important;
    font-weight: 700 !important;
}
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
.content-card, .chart-card, .mini-stat-card,
.course-header, .setting-card, .settings-card,
.report-card, .summary-card, .quick-action-card,
.course-card, .lesson-card, .certificate-card, .profile-card {
    padding: var(--card-padding) !important;
}
.stat-card {
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
    margin-bottom: 14px;
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
.stat-card h3 {
    margin-bottom: 10px !important;
}
.stat-card .value {
    letter-spacing: -0.055em !important;
    margin-bottom: 8px !important;
}
.stat-card .progress-bar {
    margin-top: auto !important;
    margin-bottom: 0 !important;
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
/* ========================================
   DASHBOARD ALUNO — REFINAMENTO PROFISSIONAL
   ======================================== */

/* 1. PADRONIZAÇÃO DE CORES (sem excesso) */
.stat-card.primary::before { background: linear-gradient(90deg, #1e3a8a, #2563eb) !important; }
.stat-card.success::before { background: linear-gradient(90deg, #059669, #10b981) !important; }
.stat-card.warning::before { background: linear-gradient(90deg, #d97706, #f59e0b) !important; }
.stat-card.info::before    { background: linear-gradient(90deg, #2563eb, #60a5fa) !important; }
.stat-card.danger::before  { background: linear-gradient(90deg, #dc2626, #ef4444) !important; }

.stat-card.primary .icon { background: linear-gradient(135deg, #1e3a8a, #2563eb) !important; }
.stat-card.success .icon { background: linear-gradient(135deg, #059669, #10b981) !important; }
.stat-card.warning .icon { background: linear-gradient(135deg, #d97706, #f59e0b) !important; }
.stat-card.info .icon    { background: linear-gradient(135deg, #2563eb, #60a5fa) !important; }

/* 2. ESPAÇAMENTO PADRONIZADO */
.stats-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) !important;
    gap: 24px !important;
    margin-bottom: 40px !important;
}
.charts-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)) !important;
    gap: 24px !important;
    margin-bottom: 40px !important;
}
.content-grid {
    display: grid !important;
    grid-template-columns: 1.4fr 1fr !important;
    gap: 24px !important;
    margin-bottom: 40px !important;
    align-items: stretch !important;
}
@media (max-width: 1024px) {
    .content-grid { grid-template-columns: 1fr !important; }
}
.divider {
    margin: 32px 0 !important;
    border: 0 !important;
    height: 1px !important;
    background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.18), transparent) !important;
}

/* 3. TIPOGRAFIA POLIDA */
.stat-card h3 {
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.12em !important;
    color: #64748b !important;
    margin-bottom: 12px !important;
}
.stat-card .value {
    font-size: 2.5rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    letter-spacing: -0.04em !important;
    line-height: 1 !important;
    margin-bottom: 12px !important;
}
.stat-card .description, .stat-card .stat-change {
    font-size: 0.82rem !important;
    color: #475569 !important;
    font-weight: 600 !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.stat-card.primary .stat-change-icon { color: #2563eb !important; }
.stat-card.success .stat-change-icon { color: #10b981 !important; }
.stat-card.warning .stat-change-icon { color: #f59e0b !important; }
.stat-card.info .stat-change-icon    { color: #60a5fa !important; }

.chart-title {
    font-size: 0.95rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    margin-bottom: 18px !important;
    letter-spacing: -0.01em !important;
    display: flex;
    align-items: center;
    gap: 10px;
}
.chart-title i {
    display: inline-grid;
    place-items: center;
    width: 34px; height: 34px;
    border-radius: 12px;
    background: rgba(37, 99, 235, 0.10);
    color: #2563eb !important;
    font-size: 0.9rem;
}

/* 4. STAT CARDS — IDÊNTICOS AO DASHBOARD PROFESSOR */
.stat-card {
    position: relative;
    overflow: hidden;
    display: flex !important;
    flex-direction: column !important;
    min-height: 180px;
    padding: 28px !important;
    background:
        radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.16), transparent 38%),
        linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-radius: 18px !important;
    box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04) !important;
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
    transform: scaleX(0) !important;
    transform-origin: left;
    transition: transform 0.3s ease !important;
    height: 4px !important;
}
.stat-card:hover::before { transform: scaleX(1) !important; }
.stat-card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06) !important;
    border-color: rgba(37, 99, 235, 0.25) !important;
}
.stat-card .icon {
    /* Caixinha gradiente — IDÊNTICA AO DASHBOARD PROFESSOR */
    display: inline-grid !important;
    place-items: center !important;
    width: 58px !important;
    height: 58px !important;
    border-radius: 20px !important;
    background: linear-gradient(135deg, #1e3a8a, #0f172a) !important;
    color: #ffffff !important;
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16) !important;
    font-size: 1.25rem !important;
    margin-bottom: 18px !important;
    opacity: 1 !important;
    filter: none !important;
    transition: all 0.3s ease;
}
.stat-card .icon i { color: #ffffff !important; font-size: 1.4rem; }
.stat-card.primary .icon { background: linear-gradient(135deg, #1e3a8a, #2563eb) !important; }
.stat-card.success .icon { background: linear-gradient(135deg, #059669, #047857) !important; }
.stat-card.warning .icon { background: linear-gradient(135deg, #d97706, #b45309) !important; }
.stat-card.info .icon    { background: linear-gradient(135deg, #2563eb, #1e3a8a) !important; }
.stat-card:hover .icon {
    transform: scale(1.08) rotate(-3deg);
}

/* Value com gradiente igual ao professor */
.stat-card .value {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
    color: transparent !important;
}

/* Barra de progresso interna do card */
.stat-card .progress-bar {
    margin-top: 14px;
    height: 6px !important;
    background: rgba(226, 232, 240, 0.85) !important;
    border-radius: 999px !important;
    overflow: hidden;
}
.stat-card .progress-fill {
    height: 100% !important;
    background: linear-gradient(90deg, #1e3a8a, #2563eb) !important;
    border-radius: 999px !important;
    transition: width 0.6s ease;
}
.stat-card .progress-fill.success { background: linear-gradient(90deg, #059669, #10b981) !important; }
.stat-card .progress-fill.warning { background: linear-gradient(90deg, #d97706, #f59e0b) !important; }

/* Donut do card de progresso */
.progress-donut-container {
    position: relative;
    width: 110px; height: 110px;
    margin: 0 auto 12px !important;
}
.progress-donut-label {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
}
.progress-percentage {
    font-size: 1.5rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    letter-spacing: -0.04em !important;
}

/* 5. CHART CARDS POLIDOS */
.chart-card {
    position: relative;
    overflow: hidden;
    padding: 26px !important;
    background: rgba(255, 255, 255, 0.96) !important;
    border: 1px solid rgba(226, 232, 240, 0.85) !important;
    border-radius: 22px !important;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06) !important;
    min-height: 320px;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s !important;
}
.chart-card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 4px;
    background: linear-gradient(90deg, #1e3a8a, #2563eb);
}
.chart-card:hover {
    transform: translateY(-4px);
    border-color: rgba(37, 99, 235, 0.22) !important;
    box-shadow: 0 18px 42px rgba(15, 23, 42, 0.10) !important;
}
.chart-card canvas {
    max-height: 240px !important;
}

/* 6. CONTENT CARDS POLIDOS */
.content-card {
    padding: 28px !important;
    border-radius: 22px !important;
    background: rgba(255, 255, 255, 0.96) !important;
    border: 1px solid rgba(226, 232, 240, 0.85) !important;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06) !important;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s !important;
}
.content-card:hover {
    transform: translateY(-3px);
    border-color: rgba(37, 99, 235, 0.18) !important;
    box-shadow: 0 18px 42px rgba(15, 23, 42, 0.10) !important;
}
.content-card h2 {
    font-size: 1.25rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    letter-spacing: -0.02em !important;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px !important;
}
.content-card h2 i {
    display: inline-grid;
    place-items: center;
    width: 36px; height: 36px;
    border-radius: 12px;
    background: rgba(37, 99, 235, 0.10);
    color: #2563eb !important;
    font-size: 0.95rem;
}

/* 7. USER-INFO POLIMENTO */
.user-info {
    padding: 8px 10px 8px 14px !important;
    gap: 12px !important;
    border-radius: 999px !important;
}
.user-info-details {
    line-height: 1.2;
}
.user-info-name {
    color: #ffffff !important;
    font-size: 0.95rem !important;
    font-weight: 800 !important;
}
.user-info-role {
    color: rgba(255, 255, 255, 0.78) !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
}
.user-avatar {
    width: 44px !important;
    height: 44px !important;
    border-radius: 50% !important;
    overflow: hidden;
}
.user-avatar img { width: 100%; height: 100%; object-fit: cover; }
.user-avatar.online::after {
    content: '';
    position: absolute;
    bottom: 2px; right: 2px;
    width: 12px; height: 12px;
    border-radius: 50%;
    background: #10b981;
    border: 2px solid #ffffff;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    animation: dashboardPulse 2s infinite;
}
@keyframes dashboardPulse {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.55); }
    70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* 8. RESPONSIVIDADE */
@media (max-width: 768px) {
    .stats-grid, .charts-grid {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }
    .stat-card { padding: 20px !important; }
    .stat-card .value { font-size: 2rem !important; }
    .content-card { padding: 22px !important; }
    .chart-card { padding: 22px !important; min-height: 280px; }
}
@media (max-width: 480px) {
    .stat-card h3 { font-size: 0.68rem !important; }
    .chart-title { font-size: 0.88rem !important; }
}

/* ALUNO_THEME_OVERRIDES_END */

/* ===== SIDEBAR — IGUAL AOS OUTROS ===== */
.sidebar { background: radial-gradient(circle at top left,rgba(37,99,235,.16),transparent 34%),linear-gradient(180deg,#020617 0%,#0f172a 48%,#1e3a8a 100%) !important; }
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

/* ===== STAT CARDS — IGUAL AO ADMIN ===== */
.stats-grid { display:grid !important; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)) !important; gap:22px !important; margin-bottom:34px !important; }
.stat-card { position:relative; padding:28px !important; overflow:hidden; min-height:180px !important; background:radial-gradient(circle at 92% 8%,rgba(37,99,235,.16),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; border:1px solid rgba(226,232,240,.8) !important; border-radius:18px !important; box-shadow:0 2px 8px rgba(0,0,0,.06),0 1px 3px rgba(0,0,0,.04) !important; transition:all .35s cubic-bezier(.4,0,.2,1) !important; display:flex !important; flex-direction:column !important; }
.stat-card::before, .stat-card:hover::before { display:none !important; content:none !important; }
.stat-card.primary { background:radial-gradient(circle at 92% 8%,rgba(37,99,235,.16),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
.stat-card.success { background:radial-gradient(circle at 92% 8%,rgba(16,185,129,.18),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
.stat-card.warning { background:radial-gradient(circle at 92% 8%,rgba(245,158,11,.20),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
.stat-card.info    { background:radial-gradient(circle at 92% 8%,rgba(96,165,250,.20),transparent 38%),linear-gradient(135deg,#ffffff 0%,#f8fafc 100%) !important; }
.stat-card:hover         { transform:translateY(-4px) !important; box-shadow:inset 0 3px 0 #2563eb,   0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; border-color:rgba(37,99,235,.25) !important; }
.stat-card.success:hover { box-shadow:inset 0 3px 0 #059669,  0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; }
.stat-card.warning:hover { box-shadow:inset 0 3px 0 #d97706,  0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; }
.stat-card.info:hover    { box-shadow:inset 0 3px 0 #0891b2,  0 10px 25px -5px rgba(0,0,0,.12),0 8px 10px -6px rgba(0,0,0,.06) !important; }
.stat-card .stat-header { display:flex !important; flex-direction:column-reverse !important; align-items:flex-start !important; gap:14px !important; margin-bottom:14px !important; }
.stat-card .stat-title { font-size:.72rem !important; font-weight:800 !important; text-transform:uppercase !important; letter-spacing:.12em !important; color:#64748b !important; margin:0 !important; }
.stat-card .stat-icon { display:inline-grid !important; place-items:center !important; width:52px !important; height:52px !important; border-radius:18px !important; background:linear-gradient(135deg,#1e3a8a,#2563eb) !important; color:#fff !important; font-size:1.3rem !important; box-shadow:0 12px 28px rgba(15,23,42,.16) !important; }
.stat-card.primary .stat-icon { background:linear-gradient(135deg,#1e3a8a,#2563eb) !important; }
.stat-card.success .stat-icon { background:linear-gradient(135deg,#059669,#047857) !important; }
.stat-card.warning .stat-icon { background:linear-gradient(135deg,#d97706,#b45309) !important; }
.stat-card.info    .stat-icon { background:linear-gradient(135deg,#0891b2,#0e7490) !important; }
.stat-card .stat-icon i { width:auto !important; height:auto !important; background:transparent !important; border-radius:0 !important; box-shadow:none !important; display:inline !important; }
.stat-card .stat-value { font-size:2.5rem !important; font-weight:800 !important; letter-spacing:-.04em !important; color:#0f172a !important; margin-bottom:8px !important; line-height:1 !important; }
.stat-card .stat-change { font-size:.82rem !important; color:#475569 !important; font-weight:600 !important; display:inline-flex !important; align-items:center !important; gap:6px !important; }
.stat-card .stat-change-icon { color:#2563eb !important; }
.stat-card.success .stat-change-icon { color:#10b981 !important; }
.stat-card.warning .stat-change-icon { color:#f59e0b !important; }
.stat-card.info    .stat-change-icon { color:#06b6d4 !important; }
.stat-card .progress-bar { margin-top:auto !important; padding-top:14px !important; height:auto !important; background:transparent !important; overflow:visible !important; }
.stat-card .progress-bar::before { content:'' !important; display:block !important; height:6px !important; border-radius:999px !important; background:rgba(226,232,240,.85) !important; }
.stat-card .progress-fill { height:6px !important; margin-top:-6px !important; background:linear-gradient(90deg,#1e3a8a,#2563eb) !important; border-radius:999px !important; }
.stat-card .progress-fill.success { background:linear-gradient(90deg,#059669,#10b981) !important; }
.stat-card .progress-fill.warning { background:linear-gradient(90deg,#d97706,#f59e0b) !important; }
.stat-card .progress-fill.info    { background:linear-gradient(90deg,#0891b2,#06b6d4) !important; }
.dark-mode .stat-card .progress-bar::before { background:rgba(255,255,255,.10) !important; }

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
                        <div class="sidebar-group-title">Navegação</div>
                        <li class="sidebar-item">
                            <a href="dashboard_aluno.php" class="sidebar-link active">
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
                            <a href="minhas_aulas_aluno.php" class="sidebar-link">
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
                <h1><i class="fas fa-user-graduate"></i> Dashboard Aluno</h1>
                
                <div class="header-actions">
                    <button id="darkModeToggle" title="Alternar tema">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <div class="user-info">
                    <div class="user-avatar online" title="<?php echo htmlspecialchars($aluno['nome']); ?>">
                        <?php 
                        // Verificar se há foto de perfil (assumindo campo 'foto' ou 'avatar' na tabela)
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
                <div class="stat-card primary">
                    <div class="stat-header">
                        <h3 class="stat-title">Cursos Inscritos</h3>
                        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $aulas_agendadas_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Cursos ativos
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:<?php echo min(($aulas_agendadas_count/10)*100,100); ?>%"></div>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-header">
                        <h3 class="stat-title">Aulas Assistidas</h3>
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $aulas_assistidas_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Aulas concluídas
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill success" style="width:<?php echo $aulas_agendadas_count > 0 ? min(($aulas_assistidas_count/$aulas_agendadas_count)*100,100) : 0; ?>%"></div>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <h3 class="stat-title">Próximas Aulas</h3>
                        <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $proximas_aulas_count; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Aulas agendadas
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill warning" style="width:<?php echo min(($proximas_aulas_count/15)*100,100); ?>%"></div>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-header">
                        <h3 class="stat-title">Progresso Geral</h3>
                        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $aulas_agendadas_count > 0 ? round(($aulas_assistidas_count/$aulas_agendadas_count)*100) : 0; ?>%</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up stat-change-icon"></i>
                        Média de conclusão
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill info" style="width:<?php echo $aulas_agendadas_count > 0 ? min(round(($aulas_assistidas_count/$aulas_agendadas_count)*100),100) : 0; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-chart-pie"></i>
                        Distribuição de Cursos
                    </div>
                    <canvas id="cursosChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        Progresso de Aulas
                    </div>
                    <canvas id="progressChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-calendar-alt"></i>
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
                        <?php if ($cursos_aluno->num_rows > 0): ?>
                        <div style="position: relative; display: inline-block;">
                            <button class="export-btn" onclick="toggleExportMenu()" <?php echo ($cursos_aluno->num_rows == 0) ? 'style="opacity: 0.6; cursor: not-allowed;" title="Não há dados para exportar"' : ''; ?>>
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
                        <?php endif; ?>
                    </div>

                    <?php if ($cursos_aluno->num_rows > 0): ?>
                    <!-- Filtros -->
                    <div class="filters-section">
                        <div class="tooltip">
                            <input type="text" class="search-input" id="cursoSearch" placeholder="🔍 Buscar curso, professor ou nível..." aria-label="Buscar cursos">
                            <span class="tooltiptext">Pressione Ctrl+K para buscar rapidamente</span>
                        </div>
                        <select class="search-input" id="filterNivel" style="min-width: 120px;" aria-label="Filtrar por nível" title="Filtrar cursos por nível">
                            <option value="">Todos os níveis</option>
                            <option value="Iniciante">Iniciante</option>
                            <option value="Intermediário">Intermediário</option>
                            <option value="Avançado">Avançado</option>
                        </select>
                        <button class="filter-btn" onclick="resetFilters('cursosTable', 'cursoSearch')" aria-label="Limpar filtros" title="Limpar todos os filtros aplicados">
                            <i class="fas fa-redo"></i> Limpar
                        </button>
                    </div>
                        <div class="table-wrapper" style="margin-top: 8px;">
                            <table class="table" id="cursosTable">
                                <thead>
                                    <tr>
                                        <th onclick="sortTable('cursosTable', 0)" style="cursor: pointer; user-select: none;" title="Clique para ordenar">
                                            Curso <i class="fas fa-sort" style="font-size: 0.7rem; margin-left: 4px;"></i>
                                        </th>
                                        <th onclick="sortTable('cursosTable', 1)" style="cursor: pointer; user-select: none;" title="Clique para ordenar">
                                            Professor <i class="fas fa-sort" style="font-size: 0.7rem; margin-left: 4px;"></i>
                                        </th>
                                        <th onclick="sortTable('cursosTable', 2)" style="cursor: pointer; user-select: none;" title="Clique para ordenar">
                                            Nível <i class="fas fa-sort" style="font-size: 0.7rem; margin-left: 4px;"></i>
                                        </th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            <tbody>
                                <?php while ($curso = $cursos_aluno->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: #334155; margin-bottom: 6px; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-book-open" style="color: var(--primary-color); font-size: 0.875rem;"></i>
                                                <?php echo htmlspecialchars($curso['nome']); ?>
                                            </div>
                                            <div style="font-size: 0.8125rem; color: #64748b; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fas fa-tag" style="font-size: 0.7rem;"></i>
                                                    <?php echo htmlspecialchars($curso['categoria']); ?>
                                                </span>
                                                <span style="display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fas fa-clock" style="font-size: 0.7rem;"></i>
                                                    <?php echo $curso['duracao_horas']; ?>h
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="width: 32px; height: 32px; background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user" style="color: var(--primary-color); font-size: 0.875rem;"></i>
                                                </div>
                                                <span style="font-weight: 500; color: #334155;"><?php echo htmlspecialchars($curso['professor_nome']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%); border-radius: var(--border-radius-sm); font-size: 0.8125rem; font-weight: 600; color: var(--primary-color);">
                                                <i class="fas fa-signal" style="font-size: 0.75rem;"></i>
                                                <?php echo htmlspecialchars($curso['nivel']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle" style="margin-right: 4px; font-size: 0.7rem;"></i>
                                                Ativo
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="text-align: center; padding: 80px 40px; color: #64748b; margin-top: 20px;">
                            <div style="width: 140px; height: 140px; margin: 0 auto 40px; background: linear-gradient(135deg, rgba(5, 150, 105, 0.12) 0%, rgba(5, 150, 105, 0.06) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.1), 0 2px 4px -1px rgba(5, 150, 105, 0.06); position: relative;">
                                <div style="position: absolute; inset: -4px; border-radius: 50%; background: linear-gradient(135deg, rgba(5, 150, 105, 0.2), rgba(5, 150, 105, 0.05)); opacity: 0.5; animation: pulse 2s ease-in-out infinite;"></div>
                                <i class="fas fa-book" style="font-size: 4rem; color: var(--primary-color); opacity: 0.7; position: relative; z-index: 1;"></i>
                            </div>
                            <h3 style="font-size: 1.75rem; font-weight: 600; color: #334155; margin-bottom: 16px; letter-spacing: -0.02em; line-height: 1.2;">
                                Nenhum curso encontrado
                            </h3>
                            <p style="font-size: 1.0625rem; color: #64748b; margin-bottom: 12px; max-width: 550px; margin-left: auto; margin-right: auto; line-height: 1.7; font-weight: 500;">
                                Você ainda não está inscrito em nenhum curso.
                            </p>
                            <p style="font-size: 0.9375rem; color: #94a3b8; margin-bottom: 48px; max-width: 500px; margin-left: auto; margin-right: auto;">
                                Explore os cursos disponíveis abaixo e comece sua jornada de aprendizado!
                            </p>
                            <a href="buscar_cursos_aluno.php" style="background: var(--gradient-primary); color: white; border: none; padding: 16px 32px; border-radius: var(--border-radius); cursor: pointer; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3), 0 2px 4px -1px rgba(5, 150, 105, 0.2); letter-spacing: 0.01em;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 15px -3px rgba(5, 150, 105, 0.4), 0 4px 6px -2px rgba(5, 150, 105, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(5, 150, 105, 0.3), 0 2px 4px -1px rgba(5, 150, 105, 0.2)';">
                                <i class="fas fa-search"></i> Buscar Cursos
                            </a>
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
                                <div style="border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 20px; background: var(--light-secondary); transition: var(--transition);" onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='var(--primary-color)'; this.style.boxShadow='var(--shadow-sm)';" onmouseout="this.style.transform='translateX(0)'; this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                                    <div style="font-weight: 500; margin-bottom: 8px; color: #334155; font-size: 1.0625rem;"><?php echo $aula['curso_nome']; ?></div>
                                    <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user" style="color: var(--primary-color);"></i> 
                                        <span style="font-weight: 500;"><?php echo $aula['professor_nome']; ?></span>
                                    </div>
                                    <div style="font-size: 0.875rem; color: var(--primary-color); font-weight: 500; display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: rgba(5, 150, 105, 0.1); border-radius: var(--border-radius-sm);">
                                        <i class="fas fa-clock"></i> 
                                        <?php echo date('d/m/Y', strtotime($aula['data_agendamento'])); ?> às <?php echo $aula['hora_inicio']; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                            <i class="fas fa-calendar" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3; color: var(--primary-color);"></i>
                            <p style="font-weight: 500; color: #334155;">Nenhuma aula agendada.</p>
                            <p style="font-size: 0.875rem; margin-top: 4px;">Suas próximas aulas aparecerão aqui.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cursos Disponíveis -->
            <div class="content-card">
                <h2>
                    <i class="fas fa-search"></i>
                    Cursos Disponíveis
                </h2>
                
                <?php if ($cursos_disponiveis->num_rows > 0): ?>
                    <div class="course-grid">
                        <?php while ($curso = $cursos_disponiveis->fetch_assoc()): ?>
                            <div class="course-card">
                                <h3><?php echo $curso['nome']; ?></h3>
                                <div class="professor">
                                    <i class="fas fa-user"></i> Professor disponível
                                </div>
                                <div class="details">
                                    <span><?php echo $curso['categoria']; ?></span>
                                    <span><?php echo $curso['nivel']; ?></span>
                                    <span><?php echo $curso['duracao_horas']; ?>h</span>
                                </div>
                                <div class="price">R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></div>
                                <?php if ($curso['ja_inscrito'] > 0): ?>
                                    <button class="enroll-btn disabled" disabled>
                                        <i class="fas fa-check"></i> Já Inscrito
                                    </button>
                                <?php else: ?>
                                    <button class="enroll-btn" onclick="inscreverCurso(<?php echo $curso['id']; ?>)" data-curso-id="<?php echo $curso['id']; ?>">
                                        <i class="fas fa-plus"></i> Inscrever-se
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: var(--secondary-color);">
                        <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>Nenhum curso disponível no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script src="sidebar.js"></script>
    <script src="dark-mode.js"></script>

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
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'toastSlideIn 0.3s ease-out reverse';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        }
        
        // ===== ANIMAÇÃ•ES DE CONTAGEM =====
        function animateValue(element, start, end, duration = 1000) {
            if (!element) return;
            
            const startTime = performance.now();
            const range = end - start;
            const isDecimal = end % 1 !== 0;
            const decimals = isDecimal ? 1 : 0;
            
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
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


        // ===== GRÃFICOS =====
        function initCharts() {
            // Donut Chart de Progresso
            const progressDonutCtx = document.getElementById('progressDonutChart');
            if (progressDonutCtx) {
                const progressValue = <?php echo $aulas_agendadas_count > 0 ? round(($aulas_assistidas_count / $aulas_agendadas_count) * 100) : 0; ?>;
                const ctx = progressDonutCtx.getContext('2d');
                const centerX = progressDonutCtx.width / 2;
                const centerY = progressDonutCtx.height / 2;
                const radius = 45;
                const lineWidth = 11;
                
                // Função para desenhar o donut
                const drawDonut = (progress) => {
                    ctx.clearRect(0, 0, progressDonutCtx.width, progressDonutCtx.height);
                    
                    // Fundo do círculo
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                    ctx.strokeStyle = 'rgba(226, 232, 240, 0.3)';
                    ctx.lineWidth = lineWidth;
                    ctx.stroke();
                    
                    // Arco de progresso
                    const startAngle = -Math.PI / 2;
                    const endAngle = startAngle + (2 * Math.PI * progress);
                    
                    // Gradiente no arco
                    const gradient = ctx.createLinearGradient(0, 0, progressDonutCtx.width, 0);
                    gradient.addColorStop(0, 'rgba(5, 150, 105, 1)');
                    gradient.addColorStop(0.5, 'rgba(16, 185, 129, 1)');
                    gradient.addColorStop(1, 'rgba(52, 211, 153, 1)');
                    
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                    ctx.strokeStyle = gradient;
                    ctx.lineWidth = lineWidth;
                    ctx.lineCap = 'round';
                    ctx.stroke();
                };
                
                // Animação
                let currentProgress = 0;
                const targetProgress = progressValue / 100;
                const animateProgress = () => {
                    if (currentProgress < targetProgress) {
                        currentProgress += 0.02;
                        if (currentProgress > targetProgress) currentProgress = targetProgress;
                        drawDonut(currentProgress);
                        requestAnimationFrame(animateProgress);
                    } else {
                        drawDonut(targetProgress);
                    }
                };
                animateProgress();
            }

            // Gráfico de Cursos (Melhorado)
            const cursosCtx = document.getElementById('cursosChart');
            if (cursosCtx) {
                window.cursosChart = new Chart(cursosCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Cursos Ativos', 'Disponíveis'],
                        datasets: [{
                            data: [<?php echo $aulas_agendadas_count; ?>, Math.max(0, 10 - <?php echo $aulas_agendadas_count; ?>)],
                            backgroundColor: [
                                'rgba(5, 150, 105, 0.8)',
                                'rgba(100, 116, 139, 0.3)'
                            ],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            hoverBorderWidth: 4,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
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
                                    color: '#334155'
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
                                borderColor: 'rgba(5, 150, 105, 0.5)',
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
                        onHover: (event, activeElements) => {
                            event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });
            }

            // Gráfico de Progresso (Melhorado)
            const progressCtx = document.getElementById('progressChart');
            if (progressCtx) {
                const progresso = <?php echo $aulas_agendadas_count > 0 ? round(($aulas_assistidas_count / $aulas_agendadas_count) * 100) : 0; ?>;
                window.progressChart = new Chart(progressCtx, {
                    type: 'line',
                    data: {
                        labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                        datasets: [{
                            label: 'Progresso (%)',
                            data: [
                                Math.max(0, progresso - 15),
                                Math.max(0, progresso - 10),
                                Math.max(0, progresso - 5),
                                progresso
                            ],
                            borderColor: 'rgba(5, 150, 105, 1)',
                            backgroundColor: 'rgba(5, 150, 105, 0.15)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: 'rgba(5, 150, 105, 1)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 3,
                            pointHoverBackgroundColor: 'rgba(5, 150, 105, 0.8)',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
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
                                        return `Progresso: ${context.parsed.y}%`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    },
                                    font: {
                                        size: 11
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    color: 'rgba(226, 232, 240, 0.5)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#64748b'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        onHover: (event, activeElements) => {
                            event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });
            }

            // Gráfico de Aulas (Melhorado)
            const aulasCtx = document.getElementById('aulasChart');
            if (aulasCtx) {
                window.aulasChart = new Chart(aulasCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Fev', 'Mar', 'Abr'],
                        datasets: [{
                            label: 'Aulas',
                            data: [
                                <?php echo max(0, $aulas_assistidas_count - 3); ?>,
                                <?php echo max(0, $aulas_assistidas_count - 2); ?>,
                                <?php echo max(0, $aulas_assistidas_count - 1); ?>,
                                <?php echo $aulas_assistidas_count; ?>
                            ],
                            backgroundColor: [
                                'rgba(5, 150, 105, 0.8)',
                                'rgba(5, 150, 105, 0.7)',
                                'rgba(5, 150, 105, 0.9)',
                                'rgba(5, 150, 105, 1)'
                            ],
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: { 
                                display: false 
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
                                borderColor: 'rgba(5, 150, 105, 0.5)',
                                borderWidth: 2,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return `Aulas: ${context.parsed.y} aula${context.parsed.y !== 1 ? 's' : ''}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { 
                                    stepSize: 1,
                                    font: {
                                        size: 11
                                    },
                                    color: '#64748b'
                                },
                                grid: { 
                                    color: 'rgba(226, 232, 240, 0.5)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#64748b'
                                },
                                grid: { 
                                    display: false 
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        onHover: (event, activeElements) => {
                            event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                        }
                    }
                });
            }
        }

        // ===== FILTROS E BUSCA (COM DEBOUNCE) =====
        const debouncedFilterTable = debounce(function(tableId, searchId) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const search = document.getElementById(searchId);
            const filterNivel = document.getElementById('filterNivel');
            
            const searchFilter = search ? search.value.toLowerCase() : '';
            const nivelFilter = filterNivel ? filterNivel.value.toLowerCase() : '';
            
            const rows = table.querySelectorAll('tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const nivel = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
                
                const matchSearch = !searchFilter || text.includes(searchFilter);
                const matchNivel = !nivelFilter || nivel.includes(nivelFilter);
                
                if (matchSearch && matchNivel) {
                    row.style.display = '';
                    row.style.animation = 'fadeIn 0.3s ease-out';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
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
            const filterNivel = document.getElementById('filterNivel');
            
            if (search) search.value = '';
            if (filterNivel) filterNivel.value = '';
            
            filterTable(tableId, searchId);
        }

        // ===== ORDENAÇÃƒO =====
        let sortDirection = {};
        
        function sortTable(tableId, columnIndex) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isNumeric = false;
            
            if (!sortDirection[tableId + columnIndex]) {
                sortDirection[tableId + columnIndex] = 'asc';
            } else {
                sortDirection[tableId + columnIndex] = sortDirection[tableId + columnIndex] === 'asc' ? 'desc' : 'asc';
            }
            
            const direction = sortDirection[tableId + columnIndex];
            
            rows.sort((a, b) => {
                let aVal = a.cells[columnIndex] ? a.cells[columnIndex].textContent.trim() : '';
                let bVal = b.cells[columnIndex] ? b.cells[columnIndex].textContent.trim() : '';
                
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
                
                if (direction === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
            
            const headers = table.querySelectorAll('th');
            headers.forEach((th, idx) => {
                if (idx === columnIndex) {
                    const icon = th.querySelector('i');
                    if (icon) {
                        icon.className = direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                    }
                } else {
                    const icon = th.querySelector('i');
                    if (icon && (icon.classList.contains('fa-sort-up') || icon.classList.contains('fa-sort-down'))) {
                        icon.className = 'fas fa-sort';
                    }
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }

        // ===== EXPORTAÇÃƒO =====
        function toggleExportMenu() {
            const menu = document.getElementById('exportMenu');
            if (!menu) return;
            
            const table = document.getElementById('cursosTable');
            if (!table) {
                showToast('Aviso', 'Não há dados para exportar. Você precisa ter cursos cadastrados primeiro.', 'warning');
                return;
            }
            
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            const btn = document.querySelector('.export-btn');
            if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        function exportTable(tableId, format = 'csv') {
            const table = document.getElementById(tableId);
            if (!table) {
                showToast('Aviso', 'Não há dados para exportar. Você precisa ter cursos cadastrados primeiro.', 'warning');
                const menu = document.getElementById('exportMenu');
                if (menu) menu.style.display = 'none';
                return;
            }
            
            const tbody = table.querySelector('tbody');
            if (!tbody) {
                showToast('Aviso', 'Não há dados para exportar.', 'warning');
                const menu = document.getElementById('exportMenu');
                if (menu) menu.style.display = 'none';
                return;
            }
            
            const rows = tbody.querySelectorAll('tr');
            if (rows.length === 0) {
                showToast('Aviso', 'Não há dados para exportar.', 'warning');
                const menu = document.getElementById('exportMenu');
                if (menu) menu.style.display = 'none';
                return;
            }
            
            const date = new Date().toISOString().split('T')[0];
            const filename = 'meus_cursos_' + date;

            if (format === 'csv') {
                let csv = [];
                const allRows = table.querySelectorAll('tr');
                for (let i = 0; i < allRows.length; i++) {
                    const row = [], cols = allRows[i].querySelectorAll('td, th');
                    for (let j = 0; j < cols.length; j++) {
                        row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
                    }
                    csv.push(row.join(','));
                }
                const csvContent = csv.join('\n');
                const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast('Sucesso', 'Dados exportados em CSV!', 'success');
            } else if (format === 'pdf') {
                showToast('Info', 'Gerando PDF... Aguarde.', 'info');
                if (typeof html2canvas === 'undefined' || typeof window.jspdf === 'undefined') {
                    showToast('Erro', 'Bibliotecas não carregadas. Recarregue a página.', 'error');
                    return;
                }
                const exportToPDF = () => {
                    const originalDisplay = table.style.display;
                    table.style.display = 'table';
                    html2canvas(table, {
                        scale: 1.5,
                        useCORS: true,
                        allowTaint: false,
                        logging: false,
                        backgroundColor: '#ffffff'
                    }).then(canvas => {
                        const imgData = canvas.toDataURL('image/png', 0.95);
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF('l', 'mm', 'a4');
                        const pdfWidth = pdf.internal.pageSize.getWidth();
                        const pdfHeight = pdf.internal.pageSize.getHeight();
                        const imgWidth = pdfWidth - 20;
                        const imgHeight = (canvas.height * imgWidth) / canvas.width;
                        let heightLeft = imgHeight;
                        let position = 10;
                        pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                        heightLeft -= (pdfHeight - 20);
                        while (heightLeft > 0) {
                            position = heightLeft - imgHeight + 10;
                            pdf.addPage();
                            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                            heightLeft -= (pdfHeight - 20);
                        }
                        pdf.save(filename + '.pdf');
                        showToast('Sucesso', 'PDF gerado com sucesso!', 'success');
                        table.style.display = originalDisplay;
                    }).catch(error => {
                        console.error('Erro:', error);
                        showToast('Erro', 'Erro ao gerar PDF.', 'error');
                        table.style.display = originalDisplay;
                    });
                };
                setTimeout(exportToPDF, 100);
            } else if (format === 'excel') {
                let html = '<table>';
                const allRows = table.querySelectorAll('tr');
                for (let i = 0; i < allRows.length; i++) {
                    html += '<tr>';
                    const cols = allRows[i].querySelectorAll('td, th');
                    for (let j = 0; j < cols.length; j++) {
                        const tag = allRows[i].querySelector('th') ? 'th' : 'td';
                        html += '<' + tag + '>' + cols[j].innerText + '</' + tag + '>';
                    }
                    html += '</tr>';
                }
                html += '</table>';
                const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename + '.xls');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast('Sucesso', 'Dados exportados em Excel!', 'success');
            }
            document.getElementById('exportMenu').style.display = 'none';
        }

        // ===== ATUALIZAÇÃƒO AUTOMÃTICA (CONSOLIDADA) =====
        // Removida duplicação - usando a função já definida anteriormente

        // ===== TOOLTIPS =====
        function initTooltips() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                const title = card.querySelector('h3')?.textContent || '';
                card.setAttribute('data-tooltip', `Clique para ver detalhes de ${title}`);
                card.style.cursor = 'pointer';
            });
            
            const chartCards = document.querySelectorAll('.chart-card');
            chartCards.forEach(card => {
                const title = card.querySelector('.chart-title')?.textContent || '';
                card.setAttribute('data-tooltip', `Gráfico: ${title}`);
            });
        }

        // ===== INICIALIZAÇÃƒO COMPLETA =====
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar gráficos
            initCharts();
            
            // Inicializar tooltips
            initTooltips();
            
            // Iniciar atualização automática
            startAutoUpdate();
            
            // Adicionar event listeners para filtros com debounce
            const cursoSearch = document.getElementById('cursoSearch');
            const filterNivel = document.getElementById('filterNivel');
            
            if (cursoSearch) {
                cursoSearch.addEventListener('input', function() {
                    filterTable('cursosTable', 'cursoSearch');
                });
            }
            
            if (filterNivel) {
                filterNivel.addEventListener('change', function() {
                    filterTable('cursosTable', 'cursoSearch');
                });
            }
            
            // Detectar visibilidade da página para atualização automática
            document.addEventListener('visibilitychange', function() {
                isPageVisible = document.visibilityState === 'visible';
                if (isPageVisible) {
                    startAutoUpdate();
                    updateDashboardData();
                } else {
                    stopAutoUpdate();
                }
            });

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
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleMobileMenu();
                });
                mobileOverlay.addEventListener('click', closeMobileMenu);

                // Fechar menu ao clicar fora (apenas no mobile)
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        if (sidebar.classList.contains('active') && 
                            !sidebar.contains(e.target) && 
                            !mobileMenuToggle.contains(e.target) &&
                            !mobileOverlay.contains(e.target)) {
                            closeMobileMenu();
                        }
                    }
                });

                // Ajustar layout ao redimensionar
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        closeMobileMenu();
                    }
                });

                const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            closeMobileMenu();
                        }
                    });
                });

                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        closeMobileMenu();
                    }
                });
            }

            // Animar valores
            setTimeout(() => {
                const statValues = document.querySelectorAll('.stat-card .value');
                statValues.forEach((element, index) => {
                    const finalValue = parseFloat(element.textContent) || 0;
                    if (finalValue > 0) {
                        animateValue(element, 0, finalValue, 1000);
                    }
                });
            }, 300);
        });

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

        // ===== ATUALIZAÇÃƒO AUTOMÃTICA DE DADOS =====
        let autoUpdateInterval = null;
        let isPageVisible = true;

        function startAutoUpdate() {
            if (autoUpdateInterval) clearInterval(autoUpdateInterval);
            
            // Atualizar a cada 30 segundos quando a página está visível
            autoUpdateInterval = setInterval(() => {
                if (isPageVisible && document.visibilityState === 'visible') {
                    updateDashboardData();
                }
            }, 30000);
        }

        function stopAutoUpdate() {
            if (autoUpdateInterval) {
                clearInterval(autoUpdateInterval);
                autoUpdateInterval = null;
            }
        }

        function updateDashboardData() {
            // Mostrar indicador de atualização
            showUpdateIndicator();
            
            // Atualizar estatísticas sem recarregar a página
            fetch('dashboard_aluno.php?ajax=stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Animar atualização dos valores
                        const statValues = document.querySelectorAll('.stat-card .value');
                        if (statValues.length >= 3) {
                            animateValue(statValues[0], parseInt(statValues[0].textContent) || 0, data.cursos_inscritos || 0, 800);
                            animateValue(statValues[1], parseInt(statValues[1].textContent) || 0, data.aulas_assistidas || 0, 800);
                            animateValue(statValues[2], parseInt(statValues[2].textContent) || 0, data.proximas_aulas || 0, 800);
                        }
                        
                        // Atualizar progress bars
                        const progressBars = document.querySelectorAll('.progress-fill');
                        if (progressBars.length >= 3) {
                            progressBars[0].style.width = Math.min(((data.cursos_inscritos || 0) / 10) * 100, 100) + '%';
                            if (data.cursos_inscritos > 0) {
                                progressBars[1].style.width = Math.min(((data.aulas_assistidas || 0) / data.cursos_inscritos) * 100, 100) + '%';
                            }
                            progressBars[2].style.width = Math.min(((data.proximas_aulas || 0) / 15) * 100, 100) + '%';
                        }
                        
                        // Atualizar gráficos suavemente
                        if (window.cursosChart) window.cursosChart.update('none');
                        if (window.progressChart) window.progressChart.update('none');
                        if (window.aulasChart) window.aulasChart.update('none');
                    }
                    
                    // Ocultar indicador após atualização
                    setTimeout(() => hideUpdateIndicator(), 1000);
                })
                .catch(error => {
                    console.log('Atualização automática: dados não disponíveis via AJAX');
                    setTimeout(() => hideUpdateIndicator(), 1000);
                });
        }

        function showUpdateIndicator() {
            let indicator = document.getElementById('autoUpdateIndicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'autoUpdateIndicator';
                indicator.className = 'auto-update-indicator';
                indicator.innerHTML = '<i class="fas fa-sync-alt"></i> <span>Atualizando dados...</span>';
                document.body.appendChild(indicator);
            }
            indicator.classList.add('active');
        }

        function hideUpdateIndicator() {
            const indicator = document.getElementById('autoUpdateIndicator');
            if (indicator) {
                indicator.classList.remove('active');
                setTimeout(() => {
                    if (indicator && !indicator.classList.contains('active')) {
                        indicator.remove();
                    }
                }, 300);
            }
        }

        // Detectar visibilidade da página
        document.addEventListener('visibilitychange', function() {
            isPageVisible = document.visibilityState === 'visible';
            if (isPageVisible) {
                startAutoUpdate();
                updateDashboardData();
            } else {
                stopAutoUpdate();
            }
        });

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

            // Adicionar labels aos botÃµes para acessibilidade
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

        // ===== FUNÇÃƒO DE INSCRIÇÃƒO EM CURSOS =====
        function inscreverCurso(cursoId, confirmed = false) {
            console.log('inscreverCurso chamado com cursoId:', cursoId);
            
            if (!confirmed) {
                showConfirmInscricao(cursoId);
                return;
            }

            // Encontrar o botão e desabilitar
            const buttons = document.querySelectorAll(`button[data-curso-id="${cursoId}"], button[onclick*="inscreverCurso(${cursoId})"]`);
            if (buttons.length === 0) {
                // Tentar encontrar por outro método
                const allButtons = document.querySelectorAll('.enroll-btn:not(.disabled)');
                allButtons.forEach(btn => {
                    if (btn.textContent.includes('Inscrever-se')) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inscrevendo...';
                    }
                });
            } else {
                buttons.forEach(btn => {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inscrevendo...';
                });
            }

            console.log('Enviando requisição para inscrever_aluno_curso.php com curso_id:', cursoId);

            fetch('inscrever_aluno_curso.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'curso_id=' + cursoId
            })
            .then(response => {
                console.log('Resposta recebida:', response.status, response.statusText);
                
                // Verificar se a resposta é JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Resposta não é JSON:', text);
                        throw new Error('Resposta inválida do servidor: ' + text.substring(0, 100));
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);
                
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    showToast('Sucesso', data.message, 'success');
                    // Recarregar após 1 segundo
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    // Reabilitar botão em caso de erro
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-plus"></i> Inscrever-se';
                    });
                    
                    const errorMsg = data.message || 'Erro ao realizar inscrição';
                    console.error('Erro na inscrição:', errorMsg);
                    showToast('Erro', errorMsg, 'error');
                }
            })
            .catch(error => {
                console.error('Erro completo:', error);
                console.error('Stack:', error.stack);
                
                // Reabilitar botão
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-plus"></i> Inscrever-se';
                });
                
                const errorMsg = error.message || 'Erro ao realizar inscrição. Verifique sua conexão e tente novamente.';
                showToast('Erro', errorMsg, 'error');
            });
        }

        function showConfirmInscricao(cursoId) {
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.55);display:flex;align-items:center;justify-content:center;z-index:12000;';
            overlay.innerHTML = `
                <div style="background:#fff;border-radius:14px;padding:20px;max-width:420px;width:90%;box-shadow:0 20px 40px rgba(0,0,0,.2);">
                    <h3 style="margin:0 0 8px;font-size:18px;color:#0f172a;">Confirmar inscrição</h3>
                    <p style="margin:0 0 16px;color:#475569;">Deseja se inscrever neste curso?</p>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" id="cancelInscricaoBtn" style="padding:10px 14px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;cursor:pointer;">Cancelar</button>
                        <button type="button" id="confirmInscricaoBtn" style="padding:10px 14px;border-radius:8px;border:none;background:#2563eb;color:#fff;cursor:pointer;">Confirmar</button>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);

            overlay.querySelector('#cancelInscricaoBtn').onclick = () => overlay.remove();
            overlay.querySelector('#confirmInscricaoBtn').onclick = () => {
                overlay.remove();
                inscreverCurso(cursoId, true);
            };
        }
    </script>
</body>
</html>
