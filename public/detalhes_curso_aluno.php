<?php
session_start();

// Verificar se o usuário está logado e é aluno
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    header('Location: login.php');
    exit();
}

// Verificar se foi passado um ID de curso
if (!isset($_GET['id'])) {
    header('Location: buscar_cursos_aluno.php');
    exit();
}

include 'db.php';

// Garantir que a tabela de avaliações exista
$conn->query("CREATE TABLE IF NOT EXISTS avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    aluno_id INT NOT NULL,
    avaliacao TINYINT NOT NULL,
    comentario TEXT NULL,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_curso (curso_id),
    INDEX idx_aluno (aluno_id)
)");

// Buscar dados do aluno
$aluno_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

// Buscar detalhes do curso
$curso_id = $_GET['id'];
$curso_query = "SELECT c.*, 
                (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id AND aluno_id = ?) as ja_inscrito,
                (SELECT COUNT(*) FROM agendamentos WHERE curso_id = c.id) as total_alunos,
                (SELECT AVG(avaliacao) FROM avaliacoes WHERE curso_id = c.id) as media_avaliacao,
                (SELECT COUNT(*) FROM avaliacoes WHERE curso_id = c.id) as total_avaliacoes
                FROM cursos c 
                WHERE c.id = ?";
$stmt = $conn->prepare($curso_query);
$stmt->bind_param("ii", $aluno_id, $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) {
    header('Location: buscar_cursos_aluno.php');
    exit();
}

// Buscar módulos do curso (simulado)
$modulos = [
    [
        'titulo' => 'Introdução ao Curso',
        'duracao' => '2h',
        'descricao' => 'Visão geral e objetivos do curso',
        'aulas' => 3,
        'topicos' => ['Apresentação do instrutor', 'Estrutura do curso', 'Recursos e materiais'],
        'progresso' => 0,
        'status' => 'disponivel'
    ],
    [
        'titulo' => 'Fundamentos Básicos',
        'duracao' => '4h',
        'descricao' => 'Conceitos fundamentais e teoria',
        'aulas' => 5,
        'topicos' => ['Conceitos centrais', 'Boas práticas', 'Exemplos guiados', 'Quiz de fixação'],
        'progresso' => 0,
        'status' => 'disponivel'
    ],
    [
        'titulo' => 'Prática e Exercícios',
        'duracao' => '6h',
        'descricao' => 'Aplicação prática dos conceitos',
        'aulas' => 7,
        'topicos' => ['Exercícios guiados', 'Desafios práticos', 'Code review', 'Discussão em grupo'],
        'progresso' => 0,
        'status' => 'bloqueado'
    ],
    [
        'titulo' => 'Projeto Final',
        'duracao' => '8h',
        'descricao' => 'Desenvolvimento de projeto completo',
        'aulas' => 4,
        'topicos' => ['Briefing do projeto', 'Implementação guiada', 'Apresentação', 'Certificado'],
        'progresso' => 0,
        'status' => 'bloqueado'
    ]
];

// Buscar avaliações recentes (simulado)
$avaliacoes = [
    ['nome' => 'Maria Silva', 'avaliacao' => 5, 'comentario' => 'Excelente curso! Muito bem estruturado e didático.'],
    ['nome' => 'João Santos', 'avaliacao' => 4, 'comentario' => 'Bom conteúdo, professor muito atencioso.'],
    ['nome' => 'Ana Costa', 'avaliacao' => 5, 'comentario' => 'Superou minhas expectativas. Recomendo!'],
    ['nome' => 'Pedro Lima', 'avaliacao' => 4, 'comentario' => 'Material de qualidade e aulas bem organizadas.']
];

$media_avaliacao = $curso['media_avaliacao'] ?: 4.8;
$total_avaliacoes = $curso['total_avaliacoes'] ?: count($avaliacoes);
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Detalhes do Curso</title>
    <script>
        (function() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            if (isDark) document.documentElement.classList.add('dark-mode');
        })();
    </script>

    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Dark Mode -->
    <link rel="stylesheet" href="dark-mode.css">

    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--success-color) 0%, #059669 100%);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 24px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 700;
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
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .logout-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        /* Course Header */
        .course-header {
            background: white;
            border-radius: var(--border-radius);
            padding: 32px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .course-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .course-category {
            font-size: 1rem;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }

        .course-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .stat-info h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 4px;
        }

        .stat-info p {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }

        .course-description {
            font-size: 1.1rem;
            color: var(--secondary-color);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .course-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success-color);
            margin-bottom: 24px;
        }

        .course-actions {
            display: flex;
            gap: 16px;
        }

        .btn {
            padding: 16px 32px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--success-color);
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
        }

        .btn-secondary {
            background: var(--light-color);
            color: var(--secondary-color);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-disabled {
            background: var(--secondary-color);
            color: white;
            cursor: not-allowed;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .content-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .content-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .content-card h2 i {
            color: var(--success-color);
        }

        /* Modules */
        .module-item {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 16px;
            margin-bottom: 12px;
            transition: var(--transition);
        }

        .module-item:hover {
            border-color: var(--success-color);
            box-shadow: var(--shadow-sm);
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .module-title {
            font-weight: 600;
            color: var(--dark-color);
        }

        .module-duration {
            font-size: 0.875rem;
            color: var(--success-color);
            font-weight: 600;
        }

        .module-description {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }

        /* Reviews */
        .review-item {
            border-bottom: 1px solid var(--border-color);
            padding: 16px 0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .reviewer-name {
            font-weight: 600;
            color: var(--dark-color);
        }

        .review-stars {
            color: var(--warning-color);
        }

        .review-comment {
            font-size: 0.875rem;
            color: var(--secondary-color);
            line-height: 1.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .main-content {
                margin-left: 0;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .course-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .course-actions {
                flex-direction: column;
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
    --font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
/* Module cards */
.module-card {
    position: relative;
    padding: 24px !important;
    margin-bottom: 18px;
    background: rgba(255, 255, 255, 0.96) !important;
    border: 1px solid rgba(226, 232, 240, 0.9) !important;
    border-radius: 20px !important;
    box-shadow: 0 12px 36px rgba(15, 23, 42, 0.06) !important;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s !important;
}
.module-card::before {
    content: ''; position: absolute; inset: 0 0 auto 0; height: 4px;
    border-radius: 20px 20px 0 0;
    background: linear-gradient(90deg, #1e3a8a, #2563eb);
}
.module-card.is-done::before { background: linear-gradient(90deg, #059669, #10b981); }
.module-card.is-locked::before { background: linear-gradient(90deg, #94a3b8, #cbd5e1); }
.module-card.is-locked { opacity: 0.78; }
.module-card:hover {
    transform: translateY(-4px);
    border-color: rgba(37, 99, 235, 0.25) !important;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.10) !important;
}
.module-card.is-locked:hover { transform: none; }
.module-card-top {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 18px;
    align-items: flex-start;
}
.module-number {
    width: 54px; height: 54px;
    display: grid; place-items: center;
    border-radius: 16px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 800;
    box-shadow: 0 14px 28px rgba(30, 58, 138, 0.25);
    flex-shrink: 0;
}
.module-card.is-done .module-number { background: linear-gradient(135deg, #059669, #10b981); }
.module-card.is-locked .module-number { background: linear-gradient(135deg, #94a3b8, #64748b); }
.module-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 6px;
}
.module-name {
    font-size: 1.15rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    letter-spacing: -0.02em;
    margin: 0 !important;
}
.module-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    background: rgba(37, 99, 235, 0.10);
    color: #1e3a8a;
}
.module-card.is-done .module-badge { background: rgba(16, 185, 129, 0.12); color: #047857; }
.module-card.is-locked .module-badge { background: rgba(100, 116, 139, 0.14); color: #475569; }
.module-desc {
    color: #475569 !important;
    font-size: 0.9rem !important;
    line-height: 1.55;
    margin: 0 0 10px !important;
    font-weight: 500;
}
.module-meta {
    display: flex; gap: 16px; flex-wrap: wrap;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 600;
}
.module-meta span { display: inline-flex; align-items: center; gap: 6px; }
.module-meta i { color: #2563eb; }
.module-topics {
    list-style: none;
    margin: 18px 0 0 72px;
    padding: 14px 18px;
    border-radius: 14px;
    background: rgba(248, 250, 252, 0.8);
    border: 1px solid rgba(226, 232, 240, 0.7);
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 8px 16px;
}
.module-topics li {
    display: flex; align-items: center; gap: 8px;
    color: #475569;
    font-size: 0.85rem;
    font-weight: 600;
}
.module-topics li i {
    color: #10b981;
    font-size: 0.72rem;
    flex-shrink: 0;
}
.module-card.is-locked .module-topics li i { color: #cbd5e1; }
.module-progress { margin: 16px 0 0 72px; }
.module-progress-bar {
    height: 8px;
    background: rgba(226, 232, 240, 0.9);
    border-radius: 999px;
    overflow: hidden;
}
.module-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #1e3a8a, #2563eb);
    border-radius: 999px;
    transition: width 0.4s ease;
}
.module-card.is-done .module-progress-fill { background: linear-gradient(90deg, #059669, #10b981); }
.module-actions {
    display: flex; gap: 10px; flex-wrap: wrap;
    margin: 16px 0 0 72px;
}
.module-actions .btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px !important;
    font-size: 0.85rem !important;
    border-radius: 999px !important;
    font-weight: 700 !important;
    border: none;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}
.module-actions .btn-primary {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #ffffff;
    box-shadow: 0 10px 24px rgba(30, 58, 138, 0.25);
}
.module-actions .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(30, 58, 138, 0.32); }
.module-actions .btn-ghost {
    background: rgba(248, 250, 252, 0.9);
    color: #1e3a8a;
    border: 1px solid rgba(37, 99, 235, 0.25);
}
.module-actions .btn-ghost:hover { background: rgba(37, 99, 235, 0.08); }
.module-actions .btn-locked {
    background: rgba(226, 232, 240, 0.7);
    color: #94a3b8;
    cursor: not-allowed;
}
@media (max-width: 560px) {
    .module-topics, .module-progress, .module-actions { margin-left: 0; }
    .module-card-top { grid-template-columns: 1fr; }
}
/* ALUNO_THEME_OVERRIDES_END */

/* ===== DARK MODE — detalhes_curso_aluno.php ===== */
body.dark-mode .module-card {
    background: #1e293b !important;
    border-color: rgba(255,255,255,0.08) !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4) !important;
    color: #f8fafc !important;
}
body.dark-mode .module-card:hover {
    border-color: rgba(96,165,250,0.25) !important;
    box-shadow: 0 8px 30px rgba(0,0,0,0.5) !important;
}
body.dark-mode .module-name {
    color: #f8fafc !important;
}
body.dark-mode .module-desc,
body.dark-mode .module-meta,
body.dark-mode .module-meta span,
body.dark-mode .module-meta i {
    color: #94a3b8 !important;
}
body.dark-mode .module-topics {
    background: rgba(15,23,42,0.6) !important;
    border-color: rgba(255,255,255,0.07) !important;
}
body.dark-mode .module-topics li {
    color: #cbd5e1 !important;
}
body.dark-mode .module-topics li i {
    color: #60a5fa !important;
}
body.dark-mode .module-progress-bar {
    background: #0f172a !important;
}
body.dark-mode .module-actions .btn-ghost {
    background: rgba(15,23,42,0.6) !important;
    color: #60a5fa !important;
    border-color: rgba(96,165,250,0.3) !important;
}
body.dark-mode .module-actions .btn-ghost:hover {
    background: rgba(96,165,250,0.12) !important;
}
body.dark-mode .module-actions .btn-locked {
    background: rgba(15,23,42,0.5) !important;
    color: #475569 !important;
}
body.dark-mode .review-item {
    border-color: rgba(255,255,255,0.08) !important;
}
body.dark-mode .reviewer-name {
    color: #f8fafc !important;
}
body.dark-mode .review-comment {
    color: #94a3b8 !important;
}
body.dark-mode .module-number,
body.dark-mode .module-card.is-locked .module-number {
    background: linear-gradient(135deg, #1e3a8a, #2563eb) !important;
    box-shadow: 0 8px 20px rgba(37,99,235,0.35) !important;
    opacity: 1 !important;
}
body.dark-mode .module-badge {
    background: rgba(37,99,235,0.15) !important;
    color: #93c5fd !important;
}
body.dark-mode .module-card.is-done .module-badge {
    background: rgba(16,185,129,0.15) !important;
    color: #34d399 !important;
}
body.dark-mode .module-card.is-locked .module-badge {
    background: rgba(100,116,139,0.15) !important;
    color: #64748b !important;
}

</style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
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
                <h1><i class="fas fa-info-circle"></i> Detalhes do Curso</h1>
                
                <div class="header-actions">
                    <button id="darkModeToggle" title="Alternar tema">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <!-- Course Header -->
            <div class="course-header">
                <div class="course-title"><?php echo $curso['nome']; ?></div>
                <div class="course-category"><?php echo $curso['categoria']; ?></div>
                
                <div class="course-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo $curso['duracao_horas']; ?>h</h4>
                            <p>Duração</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-signal"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo $curso['nivel']; ?></h4>
                            <p>Nível</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo $curso['total_alunos']; ?></h4>
                            <p>Alunos</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo number_format($media_avaliacao, 1); ?></h4>
                            <p>Avaliação</p>
                        </div>
                    </div>
                </div>
                
                <div class="course-description">
                    <?php echo $curso['descricao'] ?: 'Este é um curso completo e abrangente que oferece uma experiência de aprendizado única. Com material didático de qualidade, exercícios práticos e suporte especializado, você terá tudo o que precisa para dominar o conteúdo e obter seu certificado de conclusão.'; ?>
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
                            <i class="fas fa-plus"></i> Inscrever-se no Curso
                        </button>
                    <?php endif; ?>
                    <a href="buscar_cursos_aluno.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar aos Cursos
                    </a>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Modules -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-list"></i>
                        Módulos do Curso
                    </h2>
                    
                    <?php foreach ($modulos as $index => $modulo): ?>
                        <?php
                            $status = $modulo['status'] ?? 'disponivel';
                            $progresso = $modulo['progresso'] ?? 0;
                            $aulas = $modulo['aulas'] ?? 0;
                            $topicos = $modulo['topicos'] ?? [];
                            $statusInfo = [
                                'concluido' => ['label' => 'Concluído', 'icon' => 'fa-check-circle', 'class' => 'is-done'],
                                'disponivel' => ['label' => 'Disponível', 'icon' => 'fa-play-circle', 'class' => 'is-open'],
                                'bloqueado' => ['label' => 'Bloqueado', 'icon' => 'fa-lock', 'class' => 'is-locked'],
                            ][$status];
                        ?>
                        <article class="module-card <?php echo $statusInfo['class']; ?>">
                            <div class="module-card-top">
                                <div class="module-number">
                                    <span><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div class="module-main">
                                    <div class="module-title-row">
                                        <h3 class="module-name"><?php echo htmlspecialchars($modulo['titulo']); ?></h3>
                                        <span class="module-badge"><i class="fas <?php echo $statusInfo['icon']; ?>"></i> <?php echo $statusInfo['label']; ?></span>
                                    </div>
                                    <p class="module-desc"><?php echo htmlspecialchars($modulo['descricao']); ?></p>
                                    <div class="module-meta">
                                        <span><i class="fas fa-clock"></i> <?php echo $modulo['duracao']; ?></span>
                                        <span><i class="fas fa-list-ul"></i> <?php echo $aulas; ?> aula<?php echo $aulas === 1 ? '' : 's'; ?></span>
                                        <span><i class="fas fa-chart-line"></i> <?php echo $progresso; ?>% concluído</span>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($topicos)): ?>
                                <ul class="module-topics">
                                    <?php foreach ($topicos as $topico): ?>
                                        <li><i class="fas fa-check"></i><?php echo htmlspecialchars($topico); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <div class="module-progress">
                                <div class="module-progress-bar"><div class="module-progress-fill" style="width: <?php echo (int)$progresso; ?>%"></div></div>
                            </div>

                            <div class="module-actions">
                                <?php if ($status === 'bloqueado'): ?>
                                    <button type="button" class="btn btn-locked" onclick="moduloBloqueado()" disabled>
                                        <i class="fas fa-lock"></i> Conclua os módulos anteriores
                                    </button>
                                <?php else: ?>
                                    <a class="btn btn-primary" href="aula_modulo_aluno.php?curso_id=<?php echo $curso_id; ?>&modulo=<?php echo $index + 1; ?>&aula=1">
                                        <i class="fas fa-play"></i> <?php echo $progresso > 0 ? 'Continuar módulo' : 'Iniciar módulo'; ?>
                                    </a>
                                    <a class="btn btn-ghost" href="aula_modulo_aluno.php?curso_id=<?php echo $curso_id; ?>&modulo=<?php echo $index + 1; ?>&aula=1">
                                        <i class="fas fa-list"></i> Ver conteúdo
                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Reviews -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-star"></i>
                        Avaliações (<?php echo $total_avaliacoes; ?>)
                    </h2>
                    
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-name"><?php echo $avaliacao['nome']; ?></div>
                                <div class="review-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?php echo $i <= $avaliacao['avaliacao'] ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="review-comment"><?php echo $avaliacao['comentario']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function moduloBloqueado() {
            showToast('Módulo bloqueado', 'Conclua os módulos anteriores para liberar este conteúdo.', 'warning');
        }

        function inscreverCurso(cursoId) {
            if (confirm('Deseja se inscrever neste curso?')) {
                fetch('inscrever_aluno_curso.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'curso_id=' + cursoId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Inscrição confirmada', data.message, 'success');
                        setTimeout(function(){ window.location.href = 'meus_cursos_aluno.php'; }, 1200);
                    } else {
                        showToast('Não foi possível inscrever', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão', 'Erro ao realizar inscrição. Tente novamente.', 'error');
                });
            }
        }
    </script>
<!-- ALUNO_TOAST_INJECT_START -->
<div class="toast-container" id="toastContainer"></div>
<script>
if (typeof window.showToast !== 'function') {
    window.showToast = function(title, message, type) {
        type = type || 'info';
        var container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            container.id = 'toastContainer';
            document.body.appendChild(container);
        }
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        var icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        toast.innerHTML = '<i class="fas ' + (icons[type] || icons.info) + ' toast-icon"></i>' +
            '<div class="toast-content">' +
            '<div class="toast-title">' + title + '</div>' +
            '<div class="toast-message">' + message + '</div>' +
            '</div>' +
            '<button class="toast-close" onclick="this.parentElement.remove()" aria-label="Fechar">' +
            '<i class="fas fa-times"></i></button>';
        container.appendChild(toast);
        setTimeout(function () {
            if (toast.parentElement) {
                toast.style.animation = 'alunoToastSlideIn 0.3s ease-out reverse';
                setTimeout(function () { toast.remove(); }, 300);
            }
        }, 3500);
    };
}
</script>
<!-- ALUNO_TOAST_INJECT_END -->

<script src="dark-mode.js"></script>
</body>
</html>
