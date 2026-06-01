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

// Buscar todos os cursos disponíveis
$cursos_query = "SELECT c.*, 
                 (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id AND aluno_id = ?) as ja_inscrito,
                 (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id) as total_alunos
                 FROM cursos c 
                 ORDER BY c.nome";
$stmt = $conn->prepare($cursos_query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$cursos = $stmt->get_result();

// Buscar categorias únicas
$categorias_query = "SELECT DISTINCT categoria FROM cursos ORDER BY categoria";
$categorias_result = $conn->query($categorias_query);
$categorias = [];
while ($cat = $categorias_result->fetch_assoc()) {
    $categorias[] = $cat['categoria'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Buscar Cursos</title>
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

        /* Search and Filters */
        .search-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            padding: 32px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: var(--spacing-lg);
            animation: fadeInUp 0.5s ease-out backwards;
            animation-delay: 0.2s;
        }

        .search-row {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 14px 20px;
            border: 2px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--border-radius-sm);
            font-size: 1rem;
            transition: var(--transition);
            background: white;
            color: #334155;
            font-weight: 400;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
            background: #ffffff;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .filter-select {
            padding: 14px 20px;
            border: 2px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--border-radius-sm);
            font-size: 1rem;
            background: white;
            cursor: pointer;
            color: #334155;
            font-weight: 400;
            transition: var(--transition);
            min-width: 180px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }

        .search-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3), 0 2px 4px -1px rgba(5, 150, 105, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.01em;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(5, 150, 105, 0.4), 0 4px 8px -2px rgba(5, 150, 105, 0.3);
        }

        /* Course Grid */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: var(--spacing-lg);
        }

        .course-card {
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

        .course-card:nth-child(1) { animation-delay: 0.1s; }
        .course-card:nth-child(2) { animation-delay: 0.2s; }
        .course-card:nth-child(3) { animation-delay: 0.3s; }
        .course-card:nth-child(4) { animation-delay: 0.4s; }
        .course-card:nth-child(5) { animation-delay: 0.5s; }
        .course-card:nth-child(6) { animation-delay: 0.6s; }

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
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            transform: translateY(-4px);
            border-color: rgba(5, 150, 105, 0.25);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .course-category {
            font-size: 0.8125rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 500;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .course-badge {
            position: absolute;
            top: 20px;
            right: 20px;
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

        .badge-available {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .badge-enrolled {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .course-details {
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

        .course-description {
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.7;
            font-size: 0.9375rem;
            font-weight: 400;
        }

        .course-price {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }

        .course-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
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
            flex: 1;
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

        .btn-disabled {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: white;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .btn-disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Stats */
        .stats-section {
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
        body.dark-mode .course-card,
        body.dark-mode .search-section {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            border-color: rgba(51, 65, 85, 0.8);
            color: #f1f5f9;
        }

        body.dark-mode .course-title {
            color: #f1f5f9;
        }

        body.dark-mode .course-details {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.5) 0%, rgba(15, 23, 42, 0.3) 100%);
            border-color: rgba(51, 65, 85, 0.5);
        }

        body.dark-mode .detail-item {
            color: #cbd5e1;
        }

        body.dark-mode .search-input,
        body.dark-mode .filter-select {
            background: #1e293b;
            border-color: rgba(51, 65, 85, 0.8);
            color: #f1f5f9;
        }

        body.dark-mode .dark-mode-toggle {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
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
            .course-grid {
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

            .stats-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .search-section {
                padding: 24px 20px;
            }

            .search-row {
                flex-direction: column;
                gap: 12px;
            }

            .search-input,
            .filter-select,
            .search-btn {
                width: 100%;
            }

            .course-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .course-details {
                grid-template-columns: 1fr;
            }

            .course-card {
                padding: 24px;
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
    width: 100%; max-width: 460px;
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
    line-height: 1.6;
    margin-bottom: 24px;
    font-weight: 500;
}
.confirm-message strong { color: #0f172a; font-weight: 700; }
.confirm-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.confirm-btn {
    flex: 1; min-width: 140px;
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
    background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
}
.confirm-btn.ghost:hover { background: #e2e8f0; }
.confirm-btn.danger {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: #fff;
    box-shadow: 0 14px 28px rgba(239, 68, 68, 0.32);
}
.confirm-btn.danger:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(239, 68, 68, 0.4); }
.confirm-btn.primary {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    box-shadow: 0 14px 28px rgba(37, 99, 235, 0.32);
}
.confirm-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(37, 99, 235, 0.4); }
.confirm-btn.warning {
    background: linear-gradient(135deg, #d97706, #f59e0b);
    color: #fff;
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
                            <a href="minhas_aulas_aluno.php" class="sidebar-link">
                                <i class="fas fa-calendar-alt sidebar-icon"></i>
                                <span>Minhas Aulas</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="buscar_cursos_aluno.php" class="sidebar-link active">
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
                <h1><i class="fas fa-search"></i> Buscar Cursos</h1>
                
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
            <div class="stats-section">
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="value"><?php echo $cursos->num_rows; ?></div>
                    <div class="label">Cursos Disponíveis</div>
                </div>
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="value"><?php echo count($categorias); ?></div>
                    <div class="label">Categorias</div>
                </div>
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="value">4.8</div>
                    <div class="label">Avaliação Média</div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="search-section">
                <div class="search-row">
                    <input type="text" class="search-input" placeholder="Buscar por nome do curso..." id="searchInput">
                    <select class="filter-select" id="categoryFilter">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria; ?>"><?php echo $categoria; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="filter-select" id="levelFilter">
                        <option value="">Todos os Níveis</option>
                        <option value="Iniciante">Iniciante</option>
                        <option value="Intermediário">Intermediário</option>
                        <option value="Avançado">Avançado</option>
                    </select>
                    <button class="search-btn" onclick="filterCourses()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>

            <!-- Course Grid -->
            <div class="course-grid" id="courseGrid">
                <?php while ($curso = $cursos->fetch_assoc()): ?>
                    <div class="course-card" data-category="<?php echo $curso['categoria']; ?>" data-level="<?php echo $curso['nivel']; ?>" data-name="<?php echo strtolower($curso['nome']); ?>">
                        <div class="course-header">
                            <div>
                                <div class="course-title"><?php echo $curso['nome']; ?></div>
                                <div class="course-category"><?php echo $curso['categoria']; ?></div>
                            </div>
                        </div>
                        
                        <?php if ($curso['ja_inscrito'] > 0): ?>
                            <span class="course-badge badge-enrolled">
                                <i class="fas fa-check-circle" style="font-size: 0.7rem;"></i>
                                Inscrito
                            </span>
                        <?php else: ?>
                            <span class="course-badge badge-available">
                                <i class="fas fa-plus-circle" style="font-size: 0.7rem;"></i>
                                Disponível
                            </span>
                        <?php endif; ?>
                        
                        <div class="course-details">
                            <div class="detail-item">
                                <i class="fas fa-signal"></i>
                                <span><?php echo $curso['nivel']; ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo $curso['duracao_horas']; ?>h</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo $curso['total_alunos']; ?> alunos</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-star"></i>
                                <span>4.8 (<?php echo rand(10, 50); ?> avaliações)</span>
                            </div>
                        </div>
                        
                        <div class="course-description">
                            <?php echo $curso['descricao'] ?: 'Curso completo com material didático, exercícios práticos e certificado de conclusão.'; ?>
                        </div>
                        
                        <div class="course-price">
                            R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?>
                        </div>
                        
                        <div class="course-actions">
                            <?php if ($curso['ja_inscrito'] > 0): ?>
                                <button class="btn btn-disabled" disabled>
                                    <i class="fas fa-check"></i> Já Inscrito
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary" onclick="inscreverCurso(<?php echo $curso['id']; ?>)">
                                    <i class="fas fa-plus"></i> Inscrever-se
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-secondary" onclick="verDetalhes(<?php echo $curso['id']; ?>)">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>

        // ===== MOBILE MENU =====
        document.addEventListener('DOMContentLoaded', function() {
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
        });

        function filterCourses() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const levelFilter = document.getElementById('levelFilter').value;
            
            const courseCards = document.querySelectorAll('.course-card');
            let visibleCount = 0;
            
            courseCards.forEach(card => {
                const name = card.dataset.name;
                const category = card.dataset.category;
                const level = card.dataset.level;
                
                const matchesSearch = name.includes(searchTerm);
                const matchesCategory = !categoryFilter || category === categoryFilter;
                const matchesLevel = !levelFilter || level === levelFilter;
                
                if (matchesSearch && matchesCategory && matchesLevel) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Mostrar mensagem se não houver resultados
            const courseGrid = document.getElementById('courseGrid');
            let noResultsMsg = document.getElementById('noResultsMessage');
            
            if (visibleCount === 0 && courseCards.length > 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMessage';
                    noResultsMsg.className = 'empty-state';
                    noResultsMsg.innerHTML = `
                        <i class="fas fa-search"></i>
                        <h3>Nenhum curso encontrado</h3>
                        <p>Tente ajustar os filtros de busca para encontrar mais cursos.</p>
                    `;
                    courseGrid.parentNode.insertBefore(noResultsMsg, courseGrid.nextSibling);
                }
                noResultsMsg.style.display = 'block';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        }

        async function inscreverCurso(cursoId) {
            console.log('inscreverCurso chamado com cursoId:', cursoId);
            
            const ok = await showConfirm({
                title: 'Inscrever-se no curso?',
                message: 'Vamos criar sua primeira aula automaticamente para a próxima semana. Você poderá acompanhar tudo em <strong>Minhas Aulas</strong>.',
                confirmText: 'Sim, quero me inscrever',
                cancelText: 'Cancelar',
                type: 'primary',
                icon: 'fa-graduation-cap'
            });
            if (!ok) {
                return;
            }

            // Encontrar o botão e desabilitar
            const buttons = document.querySelectorAll(`button[onclick*="inscreverCurso(${cursoId})"]`);
            if (buttons.length === 0) {
                // Tentar encontrar por outro método
                const allButtons = document.querySelectorAll('.btn-primary');
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
                    if (typeof showToast === 'function') {
                        showToast('Sucesso', data.message, 'success');
                    } else {
                        alert(data.message);
                    }
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
                    
                    if (typeof showToast === 'function') {
                        showToast('Erro', errorMsg, 'error');
                    } else {
                        alert('Erro: ' + errorMsg);
                    }
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
                
                if (typeof showToast === 'function') {
                    showToast('Erro', errorMsg, 'error');
                } else {
                    alert('Erro: ' + errorMsg);
                }
            });
        }

        function verDetalhes(cursoId) {
            window.location.href = 'detalhes_curso_aluno.php?id=' + cursoId;
        }

        // Filtro em tempo real
        document.getElementById('searchInput').addEventListener('input', filterCourses);
        document.getElementById('categoryFilter').addEventListener('change', filterCourses);
        document.getElementById('levelFilter').addEventListener('change', filterCourses);

        // ===== MODAL DE CONFIRMAÇÃO =====
        function showConfirm(opts) {
            return new Promise(resolve => {
                const overlay = document.createElement('div');
                overlay.className = 'confirm-overlay';
                overlay.innerHTML = `
                    <div class="confirm-modal" role="dialog" aria-modal="true">
                        <div class="confirm-icon ${opts.type || 'primary'}">
                            <i class="fas ${opts.icon || 'fa-circle-question'}"></i>
                        </div>
                        <h3 class="confirm-title">${opts.title}</h3>
                        <p class="confirm-message">${opts.message}</p>
                        <div class="confirm-actions">
                            <button type="button" class="confirm-btn ghost" data-act="cancel">${opts.cancelText || 'Cancelar'}</button>
                            <button type="button" class="confirm-btn ${opts.type || 'primary'}" data-act="ok">${opts.confirmText || 'Confirmar'}</button>
                        </div>
                    </div>`;
                document.body.appendChild(overlay);
                requestAnimationFrame(() => overlay.classList.add('open'));
                const close = ok => {
                    overlay.classList.remove('open');
                    setTimeout(() => overlay.remove(), 220);
                    resolve(ok);
                };
                overlay.addEventListener('click', e => {
                    if (e.target === overlay) close(false);
                    const btn = e.target.closest('.confirm-btn');
                    if (btn) close(btn.dataset.act === 'ok');
                });
            });
        }

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
                <i class="fas ${icons[type] || icons.info} toast-icon"></i>
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
    </script>
    <script src="sidebar.js"></script>
    <script src="dark-mode.js"></script>
</body>
</html>
