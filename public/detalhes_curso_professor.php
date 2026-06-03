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

// Verificar se o ID do curso foi fornecido
if (!isset($_GET['id'])) {
    header('Location: cursos_professor.php');
    exit();
}

$curso_id = $_GET['id'];
$professor_id = $_SESSION['user_id'];

// Buscar dados do professor
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$professor = $stmt->get_result()->fetch_assoc();

// Buscar detalhes do curso atribuído ao professor
$curso_query = "SELECT c.* FROM cursos c
                JOIN atribuicoes_cursos ac ON c.id = ac.curso_id
                WHERE c.id = ? AND ac.professor_id = ?";
$stmt = $conn->prepare($curso_query);
$stmt->bind_param("ii", $curso_id, $professor_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) {
    header('Location: cursos_professor.php');
    exit();
}

// Buscar alunos inscritos neste curso com este professor
$alunos_query = "SELECT DISTINCT u.id, u.nome, u.email, COUNT(a.id) as total_aulas
                 FROM usuarios u 
                 JOIN agendamentos a ON u.id = a.aluno_id 
                 WHERE a.curso_id = ? AND a.professor_id = ?
                 GROUP BY u.id 
                 ORDER BY u.nome";
$stmt = $conn->prepare($alunos_query);
$stmt->bind_param("ii", $curso_id, $professor_id);
$stmt->execute();
$alunos = $stmt->get_result();

// Buscar aulas deste curso com este professor
$aulas_query = "SELECT a.*, u.nome as aluno_nome 
                FROM agendamentos a 
                JOIN usuarios u ON a.aluno_id = u.id 
                WHERE a.curso_id = ? AND a.professor_id = ?
                ORDER BY a.data_agendamento, a.hora_inicio";
$stmt = $conn->prepare($aulas_query);
$stmt->bind_param("ii", $curso_id, $professor_id);
$stmt->execute();
$aulas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Detalhes do Curso</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
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
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
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
            font-weight: 600;
            font-size: 1.125rem;
        }

        .sidebar-nav {
            padding: 16px 0;
        }

        .nav-item {
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-right: 3px solid white;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 24px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 16px 0;
        }

        .header h1 {
            font-size: 1.875rem;
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
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .logout-btn {
            background: var(--danger-color);
            color: white;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        /* Content Cards */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .content-card {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .content-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .content-card h2 i {
            color: var(--primary-color);
        }

        /* Course Info */
        .course-info {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .course-category {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .course-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 16px;
            background: var(--light-color);
            border-radius: var(--border-radius);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin-top: 4px;
        }

        .course-description {
            color: var(--secondary-color);
            line-height: 1.6;
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

        .status-agendado {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-concluido {
            background: #dcfce7;
            color: #166534;
        }

        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
        }

        .btn:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--secondary-color);
        }

        .btn-secondary:hover {
            background: #475569;
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
            font-size: 1.5rem;
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

        .nav-link {
            margin: 4px 12px;
            padding: 13px 14px;
            border-radius: 14px;
            border-right: 0;
            font-weight: 650;
        }

        .nav-link:hover,
        .nav-link.active {
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
            margin-bottom: 32px;
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
            font-weight: 800 !important;
        }

        .user-info > div:last-of-type > div:last-child {
            color: rgba(255, 255, 255, 0.82) !important;
            font-weight: 650 !important;
        }

        .user-avatar {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, #2563eb, #10b981);
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.22);
        }

        .logout-btn {
            border-radius: 999px;
            background: rgba(239, 68, 68, 0.9) !important;
            color: #ffffff !important;
            font-weight: 800;
        }

        .course-info,
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

        .course-info::before,
        .content-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #2563eb, #7c3aed, #10b981);
        }

        .course-title {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 900;
            letter-spacing: -0.055em;
        }

        .course-category {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.10);
        }

        .course-stats {
            gap: 18px;
        }

        .stat-item {
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 20px;
            background: rgba(248, 250, 252, 0.82);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .content-card h2 {
            font-size: 1.45rem;
            font-weight: 850;
            letter-spacing: -0.035em;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table th {
            border: 0;
            color: #64748b;
            font-size: 0.76rem;
            letter-spacing: 0.08em;
        }

        .table td {
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            background: rgba(255, 255, 255, 0.82);
        }

        .table td:first-child {
            border-left: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 16px 0 0 16px;
        }

        .table td:last-child {
            border-right: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 0 16px 16px 0;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: 850;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 32px rgba(37, 99, 235, 0.25);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #475569, #334155);
        }
    
        
        
        /* ===== SIDEBAR GROUPS & FOOTER ===== */
        .sidebar-group { margin: 0 12px 18px; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-group:last-child { border-bottom: none; margin-bottom: 0; }
        .sidebar-group-title { margin: 0 0 8px; padding: 8px 10px; color: rgba(255,255,255,0.55); font-size: 0.68rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; }
        .sidebar-nav { padding-bottom: 80px; }
        .sidebar-footer-fixed { position: fixed; bottom: 0; left: 0; width: 280px; padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.1); background: linear-gradient(180deg, rgba(15,23,42,0.95) 0%, rgba(30,58,138,0.95) 100%); z-index: 1001; backdrop-filter: blur(18px); }
        .sidebar-user { display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: 16px; background: rgba(255,255,255,0.09); border: 1px solid rgba(255,255,255,0.11); }
        .sidebar-user .user-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem; flex-shrink: 0; }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name { font-weight: 700; font-size: 0.72rem; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 0.62rem; color: rgba(255,255,255,0.65); }
        .logout-btn-small { background: rgba(239,68,68,0.82); color: white; text-decoration: none; padding: 7px 10px; border-radius: 999px; font-size: 0.65rem; font-weight: 800; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px; }

        /* ===== PAGE HEADER (breadcrumb style) ===== */
        .page-header { position: relative; margin-bottom: 28px; padding: 28px; background: radial-gradient(circle at 8% 18%, rgba(255,255,255,0.22), transparent 30%), linear-gradient(135deg, #0f172a 0%, #1e3a8a 58%, #2563eb 100%) !important; border: 1px solid rgba(255,255,255,0.42); border-radius: 28px; box-shadow: 0 28px 80px rgba(30,58,138,0.2); overflow: hidden; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .page-header::before { content: ''; position: absolute; inset: 0; opacity: 0.18; background: linear-gradient(90deg, rgba(255,255,255,0.34) 1px, transparent 1px), linear-gradient(rgba(255,255,255,0.28) 1px, transparent 1px); background-size: 42px 42px; pointer-events: none; }
        .breadcrumb { font-size: 0.875rem; color: rgba(255,255,255,0.7); margin-bottom: 8px; }
        .breadcrumb-link { color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 600; }
        .breadcrumb-link:hover { text-decoration: underline; }
        .breadcrumb-separator { margin: 0 6px; }
        .breadcrumb-current { color: rgba(255,255,255,0.75); font-weight: 500; }
        .page-title { position: relative; z-index: 1; }
        .page-title h1 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; color: white; letter-spacing: -0.055em; margin: 0; display: flex; align-items: center; gap: 12px; background: none !important; -webkit-text-fill-color: white !important; text-shadow: 0 2px 18px rgba(15,23,42,0.25); }
        .page-title h1 i { color: white !important; -webkit-text-fill-color: white !important; }
        .page-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1; align-self: flex-start; margin-top: -36px; }

        /* ===== COURSE INFO CARD ===== */
        .course-info {
            position: relative;
            overflow: hidden;
            padding: 34px;
            background: rgba(255,255,255,0.92) !important;
            border: 1px solid rgba(255,255,255,0.78) !important;
            border-radius: 24px !important;
            box-shadow: 0 18px 48px rgba(15,23,42,0.08) !important;
            backdrop-filter: blur(18px);
            margin-bottom: 28px;
        }
        .course-info::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
        }
        .course-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            gap: 16px;
        }
        .course-title {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #0f172a;
            margin-bottom: 10px;
            line-height: 1.2;
        }
        .course-category-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(37,99,235,0.10);
            color: #2563eb;
            font-size: 0.82rem;
            font-weight: 700;
            border: 1px solid rgba(37,99,235,0.18);
        }
        .course-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-item {
            text-align: center;
            padding: 20px 16px;
            background: rgba(248,250,252,0.85);
            border-radius: 18px;
            border: 1px solid rgba(226,232,240,0.85);
            box-shadow: 0 4px 16px rgba(15,23,42,0.05);
        }
        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: -0.03em;
        }
        .stat-label {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 4px;
        }
        .course-description-section { margin-top: 4px; }
        .course-description-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin-bottom: 8px; }
        .course-description-text { color: #475569; line-height: 1.7; font-size: 0.95rem; }

        /* ===== DARK MODE ===== */
        .dark-mode .course-info { background: rgba(30,41,59,0.95) !important; border-color: rgba(255,255,255,0.1) !important; }
        .dark-mode .course-title { color: #f1f5f9 !important; }
        .dark-mode .stat-item { background: rgba(255,255,255,0.05) !important; border-color: rgba(255,255,255,0.1) !important; }
        .dark-mode .stat-number { color: #93c5fd !important; }
        .dark-mode .course-description-text { color: #94a3b8 !important; }
        .dark-mode .content-card { background: rgba(30,41,59,0.95) !important; border-color: rgba(255,255,255,0.1) !important; }
        /* override da regra nuclear do dark-mode.css que força 2.5rem */
        .dark-mode .stat-item .stat-number { font-size: 1.5rem !important; font-weight: 800 !important; }

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
    <link rel="stylesheet" href="dark-mode.css?v=3">
</head>
<body>
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
                            <a href="dashboard_professor.php" class="nav-link sidebar-link">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                    </div>
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Acadêmico</div>
                        <li class="sidebar-item">
                            <a href="cursos_professor.php" class="nav-link sidebar-link active">
                                <i class="fas fa-book"></i> Meus Cursos
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="aulas_professor.php" class="nav-link sidebar-link">
                                <i class="fas fa-calendar-alt"></i> Aulas
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="alunos_professor.php" class="nav-link sidebar-link">
                                <i class="fas fa-users"></i> Alunos
                            </a>
                        </li>
                    </div>
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">Sistema</div>
                        <li class="sidebar-item">
                            <a href="relatorios_professor.php" class="nav-link sidebar-link">
                                <i class="fas fa-chart-bar"></i> Relatórios
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="configuracoes_professor.php" class="nav-link sidebar-link">
                                <i class="fas fa-cog"></i> Configurações
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
                        <a href="cursos_professor.php" class="breadcrumb-link">Meus Cursos</a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current"><?php echo htmlspecialchars($curso['nome']); ?></span>
                    </div>
                    <h1><i class="fas fa-book-open"></i> Detalhes do Curso</h1>
                </div>
                <div class="page-actions">
                    <button id="darkModeToggle" class="theme-toggle-pill-active" title="Alternar tema">
                        <div class="theme-slider"></div>
                        <i class="fas fa-sun"></i>
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <!-- Course Info -->
            <div class="course-info">
                <div class="course-top">
                    <div>
                        <h2 class="course-title"><?php echo htmlspecialchars($curso['nome']); ?></h2>
                        <span class="course-category-pill"><i class="fas fa-tag" style="margin-right:6px;font-size:0.75rem;"></i><?php echo htmlspecialchars($curso['categoria']); ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                        <span class="status-badge status-active">Ativo</span>
                        <a href="cursos_professor.php" class="btn btn-secondary" style="text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="course-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo (int)$curso['duracao_horas']; ?>h</div>
                        <div class="stat-label">Duração</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars($curso['nivel']); ?></div>
                        <div class="stat-label">Nível</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></div>
                        <div class="stat-label">Preço</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $alunos->num_rows; ?></div>
                        <div class="stat-label">Alunos</div>
                    </div>
                </div>

                <div class="course-description-section">
                    <div class="course-description-label">Descrição do Curso</div>
                    <p class="course-description-text"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Alunos Inscritos -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-users"></i>
                        Alunos Inscritos
                    </h2>
                    
                    <?php if ($alunos->num_rows > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Email</th>
                                    <th>Aulas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($aluno = $alunos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $aluno['nome']; ?></td>
                                        <td><?php echo $aluno['email']; ?></td>
                                        <td><?php echo $aluno['total_aulas']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--secondary-color); padding: 20px;">
                            Nenhum aluno inscrito neste curso.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Aulas Agendadas -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        Aulas Agendadas
                    </h2>
                    
                    <?php if ($aulas->num_rows > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($aula = $aulas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $aula['aluno_nome']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($aula['data_agendamento'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($aula['hora_inicio'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $aula['status']; ?>">
                                                <?php echo ucfirst($aula['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--secondary-color); padding: 20px;">
                            Nenhuma aula agendada para este curso.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const darkBtn = document.getElementById('darkModeToggle');
            if (!darkBtn) return;
            const isDark = localStorage.getItem('darkMode') === 'true';
            document.body.classList.toggle('dark-mode', isDark);
            document.documentElement.classList.toggle('dark-mode', isDark);
            darkBtn.onclick = function() {
                const nowDark = document.body.classList.toggle('dark-mode');
                document.documentElement.classList.toggle('dark-mode', nowDark);
                localStorage.setItem('darkMode', nowDark);
            };
        });
    </script>
</body>
</html>








