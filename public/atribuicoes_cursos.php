<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar dados
$professores = [];
$cursos = [];
$atribuicoes = [];

try {
    // Buscar professores
    $result = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1 ORDER BY nome");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $professores[] = $row;
        }
    }

    // Buscar cursos
    $result = $conn->query("SELECT id, nome, categoria, nivel, duracao_horas, preco FROM cursos WHERE status = 'ativo' ORDER BY nome");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cursos[] = $row;
        }
    }

    // Buscar atribuições existentes (se houver tabela)
    $result = $conn->query("SHOW TABLES LIKE 'atribuicoes_cursos'");
    if ($result && $result->num_rows > 0) {
        $result = $conn->query("
            SELECT ac.id, ac.professor_id, ac.curso_id, ac.data_atribuicao,
                   u.nome as professor_nome, c.nome as curso_nome
            FROM atribuicoes_cursos ac
            JOIN usuarios u ON ac.professor_id = u.id
            JOIN cursos c ON ac.curso_id = c.id
            ORDER BY u.nome, c.nome
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $atribuicoes[] = $row;
            }
        }
    }

} catch (Exception $e) {
    // Em caso de erro, usar arrays vazios
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Atribuições de Cursos</title>
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
            document.write('<style>.dark-mode-init body { visibility: hidden; background: #0f172a !important; }</style>');
        }
    </script>
    <link rel="stylesheet" href="dark-mode.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 32px;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .content-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .section-header {
            background: var(--light-color);
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .section-content {
            padding: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--secondary-color);
        }

        .btn-outline:hover {
            background: var(--light-color);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }

        .grid {
            display: grid;
            gap: 20px;
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            border: 1px solid var(--border-color);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .card-icon.primary {
            background: var(--primary-color);
        }

        .card-icon.success {
            background: var(--success-color);
        }

        .card-icon.warning {
            background: var(--warning-color);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .card-subtitle {
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .stat-item {
            text-align: center;
            padding: 12px;
            background: var(--light-color);
            border-radius: var(--border-radius);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--secondary-color);
            margin-top: 4px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
        }

        .table tr:hover {
            background: var(--light-color);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h4 {
            font-size: 1.25rem;
            margin-bottom: 8px;
            color: var(--dark-color);
        }

        .empty-state p {
            margin-bottom: 24px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 24px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--secondary-color);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        body { background: #f1f5f9 !important; padding: 0 !important; color: #0f172a; }

        .container {
            max-width: 1380px;
            padding: 24px;
        }

        .header {
            position: relative;
            overflow: hidden;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 30px;
            margin-bottom: 26px;
            padding: 72px 40px 48px;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
                radial-gradient(circle at 84% 26%, rgba(255, 255, 255, 0.12), transparent 34%),
                linear-gradient(135deg, #172554 0%, #1e40af 52%, #2563eb 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.42);
            box-shadow: 0 28px 80px rgba(37, 99, 235, 0.2);
        }

        .header::after {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.16;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
            background-size: 42px 42px;
            pointer-events: none;
        }

        .header h1,
        .header p {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: clamp(1.55rem, 3vw, 2.25rem);
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .header p {
            max-width: 760px;
            margin: 0 auto;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.65;
        }

        .grid {
            gap: 24px;
        }

        .grid-3 {
            margin-bottom: 28px;
        }

        .card,
        .content-section {
            isolation: isolate;
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981);
        }

        .card::after { content: none; }

        .card {
            padding: 28px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-7px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .card-icon {
            width: 58px;
            height: 58px;
            border-radius: 20px;
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.16);
        }

        .card-icon.primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }

        .card-icon.success {
            background: linear-gradient(135deg, #1e40af, #172554);
        }

        .card-icon.warning {
            background: linear-gradient(135deg, #2563eb, #172554);
        }

        .card-title {
            color: #0f172a;
            font-size: 1.16rem;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .card-subtitle {
            color: #64748b;
            line-height: 1.45;
            word-break: break-word;
        }

        .stats-grid {
            gap: 12px;
        }

        .stat-item {
            padding: 14px;
            border-radius: 18px;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.75);
        }

        .stat-number {
            color: #0f172a;
            font-size: 1.8rem;
            font-weight: 850;
            letter-spacing: -0.06em;
        }

        .stat-label {
            color: #64748b;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .section-header {
            padding: 26px 32px;
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.96), rgba(238, 242, 255, 0.96)) !important;
        }

        .section-header h3 {
            color: #0f172a;
            font-size: 1.18rem;
            font-weight: 850;
            letter-spacing: -0.035em;
        }

        .section-header h3 i {
            color: #2563eb;
        }

        .section-content {
            padding: 30px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table th {
            padding: 14px 16px;
            color: #475569;
            background: transparent;
            border-bottom: 0;
            font-size: 0.76rem;
            font-weight: 850;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .table td {
            padding: 16px;
            background: rgba(248, 250, 252, 0.92);
            border-top: 1px solid rgba(226, 232, 240, 0.75);
            border-bottom: 1px solid rgba(226, 232, 240, 0.75);
        }

        .table td:first-child {
            border-left: 1px solid rgba(226, 232, 240, 0.75);
            border-radius: 16px 0 0 16px;
        }

        .table td:last-child {
            border-right: 1px solid rgba(226, 232, 240, 0.75);
            border-radius: 0 16px 16px 0;
        }

        .table tr:hover td {
            background: #ffffff;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        }

        .badge {
            min-height: 30px;
            display: inline-flex;
            align-items: center;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 850;
            letter-spacing: 0.04em;
            border: 1px solid transparent;
        }

        .badge-success {
            color: #065f46;
            background: #d1fae5;
            border-color: #a7f3d0;
        }

        .badge-warning {
            color: #92400e;
            background: #fef3c7;
            border-color: #fde68a;
        }

        .badge-danger {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }

        .btn {
            min-height: 40px;
            border-radius: 999px;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .btn-outline {
            color: #0f172a;
            background: #ffffff;
            border: 1px solid rgba(203, 213, 225, 0.95);
        }

        .btn-outline:hover {
            color: #ffffff;
            background: #059669;
            border-color: #059669;
        }

        .back-btn {
            position: absolute;
            top: 24px;
            left: 24px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            color: #ffffff;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 999px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(16px);
            transition: all 0.25s ease;
        }

        .back-btn:hover {
            color: #ffffff;
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.22);
        }

        .empty-state {
            padding: 64px 24px;
        }

        .empty-state i {
            color: #10b981;
            opacity: 0.75;
        }

        .modal {
            background: rgba(15, 23, 42, 0.58);
            backdrop-filter: blur(8px);
        }

        .modal-content {
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.84);
            box-shadow: 0 32px 90px rgba(15, 23, 42, 0.24);
            padding: 0;
            overflow: hidden;
        }

        .modal-header {
            margin-bottom: 0;
            padding: 24px 28px;
            color: #ffffff;
            background: linear-gradient(135deg, #172554, #1e40af, #2563eb);
        }

        .modal-title {
            font-weight: 850;
            letter-spacing: -0.035em;
        }

        .close {
            color: rgba(255, 255, 255, 0.86);
        }

        #formAtribuicao {
            padding: 28px;
        }

        .form-label {
            color: #475569;
            font-size: 0.78rem;
            font-weight: 850;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .form-control {
            min-height: 48px;
            border-radius: 16px;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(203, 213, 225, 0.95);
            color: #0f172a;
        }

        .form-control:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12);
        }

        .toast-container {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 3000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast {
            min-width: 320px;
            max-width: 420px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 18px;
            border-radius: 18px;
            color: #ffffff;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.24);
            border: 1px solid rgba(255, 255, 255, 0.18);
            animation: toastIn 0.25s ease-out;
        }

        .toast.success {
            background: linear-gradient(135deg, #047857, #059669);
        }

        .toast.error {
            background: linear-gradient(135deg, #991b1b, #dc2626);
        }

        .toast.warning {
            background: linear-gradient(135deg, #92400e, #d97706);
        }

        .toast i {
            margin-top: 2px;
            font-size: 1.1rem;
        }

        .toast strong {
            display: block;
            font-weight: 800;
            margin-bottom: 3px;
        }

        .toast span {
            display: block;
            font-size: 0.9rem;
            line-height: 1.4;
            opacity: 0.92;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 24px 28px 28px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 12px;
            }

            .header {
                min-height: 230px;
                padding: 54px 22px 42px;
                border-radius: 24px;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }

            .section-content {
                padding: 20px;
            }

            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .btn-group {
                flex-direction: column;
            }
        }
    
/* ADMIN_SIDEBAR_OVERRIDE_START */
.sidebar {
    background:
        radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
        linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.12) !important;
    box-shadow: 18px 0 55px rgba(15, 23, 42, 0.18) !important;
}
.sidebar::before {
    background:
        radial-gradient(circle at top left, rgba(96, 165, 250, 0.18), transparent 32%),
        linear-gradient(180deg, #020617 0%, #0f172a 48%, #1e3a8a 100%) !important;
}
.sidebar-header {
    padding: 28px 20px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.14) !important;
    background: rgba(255, 255, 255, 0.06) !important;
    backdrop-filter: blur(18px);
}
.sidebar-logo {
    gap: 12px !important;
    font-size: 1.18rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.03em !important;
}
.sidebar-logo i {
    display: inline-grid !important;
    place-items: center !important;
    width: 44px !important;
    height: 44px !important;
    border-radius: 16px !important;
    background: rgba(255, 255, 255, 0.14) !important;
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16) !important;
}
.sidebar-group {
    margin: 0 12px 18px !important;
    padding-bottom: 16px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}
.sidebar-group-title {
    margin: 0 0 8px !important;
    padding: 8px 10px !important;
    color: rgba(255, 255, 255, 0.58) !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.12em !important;
}
.sidebar-link {
    margin: 4px 0 !important;
    padding: 12px 13px !important;
    border: 1px solid transparent !important;
    border-radius: 14px !important;
    border-left: 0 !important;
    color: rgba(255, 255, 255, 0.85) !important;
    font-weight: 650 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.sidebar-link:hover,
.sidebar-link.active {
    background: rgba(255, 255, 255, 0.14) !important;
    border-color: rgba(255, 255, 255, 0.16) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 10px 26px rgba(0, 0, 0, 0.12) !important;
    transform: translateX(3px) !important;
    color: #ffffff !important;
}
.sidebar-footer-fixed {
    background: rgba(15, 23, 42, 0.32) !important;
    border-top: 1px solid rgba(255, 255, 255, 0.14) !important;
    backdrop-filter: blur(18px) !important;
}
.sidebar-user {
    border-radius: 16px !important;
    background: rgba(255, 255, 255, 0.09) !important;
    border: 1px solid rgba(255, 255, 255, 0.11) !important;
}
/* ADMIN_SIDEBAR_OVERRIDE_END */

        .header-top {
            position: absolute !important; top: 20px !important; left: 24px !important; right: 24px !important;
            display: flex !important; justify-content: space-between !important; align-items: center !important;
            width: auto !important; margin-bottom: 0 !important; z-index: 10 !important;
        }
        .header .back-btn {
            position: relative !important; top: auto !important; left: auto !important; padding: 10px 18px !important;
        }
        .header-content { display: block !important; text-align: center !important; padding: 0 !important; position: relative !important; z-index: 1 !important; }

</style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <a href="dashboard_final.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                </a>
                <div class="header-actions">
                    <button id="darkModeToggle" title="Alternar tema" aria-label="Alternar Dark Mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
            <div class="header-content">
                <h1><i class="fas fa-tasks"></i> Atribuições de Cursos</h1>
                <p>Gerencie quais professores ministram cada curso</p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <div class="card-title">Professores</div>
                        <div class="card-subtitle">Disponíveis para atribuição</div>
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($professores); ?></span>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($professores); ?></span>
                        <div class="stat-label">Ativos</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="card-title">Cursos</div>
                        <div class="card-subtitle">Disponíveis para atribuição</div>
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($cursos); ?></span>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($cursos); ?></span>
                        <div class="stat-label">Ativos</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <i class="fas fa-link"></i>
                    </div>
                    <div>
                        <div class="card-title">Atribuições</div>
                        <div class="card-subtitle">Cursos atribuídos</div>
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($atribuicoes); ?></span>
                        <div class="stat-label">Atual</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($professores) * count($cursos); ?></span>
                        <div class="stat-label">Possível</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atribuições Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-link"></i> Atribuições Atuais</h3>
                <button class="btn btn-primary" onclick="abrirModalAtribuicao()">
                    <i class="fas fa-plus"></i> Nova Atribuição
                </button>
            </div>
            
            <div class="section-content">
                <?php if (count($atribuicoes) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Professor</th>
                                <th>Curso</th>
                                <th>Data Atribuição</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($atribuicoes as $atribuicao): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($atribuicao['professor_nome']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($atribuicao['curso_nome']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($atribuicao['data_atribuicao'])); ?></td>
                                    <td>
                                        <span class="badge badge-success">Ativo</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline btn-sm" onclick="removerAtribuicao(<?php echo $atribuicao['id']; ?>)">
                                            <i class="fas fa-trash"></i> Remover
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-link"></i>
                        <h4>Nenhuma atribuição encontrada</h4>
                        <p>Comece atribuindo cursos aos professores</p>
                        <button class="btn btn-primary" onclick="abrirModalAtribuicao()">
                            <i class="fas fa-plus"></i> Primeira Atribuição
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Professores Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-chalkboard-teacher"></i> Professores Disponíveis</h3>
            </div>
            
            <div class="section-content">
                <?php if (count($professores) > 0): ?>
                    <div class="grid grid-2">
                        <?php foreach ($professores as $professor): ?>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-icon primary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="card-title"><?php echo htmlspecialchars($professor['nome']); ?></div>
                                        <div class="card-subtitle"><?php echo htmlspecialchars($professor['email']); ?></div>
                                    </div>
                                </div>
                                <div style="margin-top: 12px;">
                                    <button class="btn btn-outline btn-sm" onclick="atribuirCursos(<?php echo $professor['id']; ?>, '<?php echo htmlspecialchars($professor['nome']); ?>')">
                                        <i class="fas fa-plus"></i> Atribuir Cursos
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h4>Nenhum professor disponível</h4>
                        <p>Adicione professores antes de fazer atribuições</p>
                        <a href="professores.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Adicionar Professores
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cursos Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-graduation-cap"></i> Cursos Disponíveis</h3>
            </div>
            
            <div class="section-content">
                <?php if (count($cursos) > 0): ?>
                    <div class="grid grid-2">
                        <?php foreach ($cursos as $curso): ?>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-icon success">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div>
                                        <div class="card-title"><?php echo htmlspecialchars($curso['nome']); ?></div>
                                        <div class="card-subtitle"><?php echo htmlspecialchars($curso['categoria']); ?> • <?php echo htmlspecialchars($curso['nivel']); ?></div>
                                    </div>
                                </div>
                                <div style="margin-top: 12px;">
                                    <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                                        <span class="badge badge-success"><?php echo $curso['duracao_horas']; ?>h</span>
                                        <span class="badge badge-warning">R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></span>
                                    </div>
                                    <button class="btn btn-outline btn-sm" onclick="atribuirProfessores(<?php echo $curso['id']; ?>, '<?php echo htmlspecialchars($curso['nome']); ?>')">
                                        <i class="fas fa-plus"></i> Atribuir Professores
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-graduation-cap"></i>
                        <h4>Nenhum curso disponível</h4>
                        <p>Adicione cursos antes de fazer atribuições</p>
                        <a href="cursos_completo.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Adicionar Cursos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Nova Atribuição -->
    <div id="modalAtribuicao" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nova Atribuição</h3>
                <span class="close" onclick="fecharModal()">&times;</span>
            </div>
            
            <form id="formAtribuicao">
                <div class="form-group">
                    <label class="form-label">Professor</label>
                    <select class="form-control" id="professor_id" required>
                        <option value="">Selecione um professor</option>
                        <?php foreach ($professores as $professor): ?>
                            <option value="<?php echo $professor['id']; ?>"><?php echo htmlspecialchars($professor['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Curso</label>
                    <select class="form-control" id="curso_id" required>
                        <option value="">Selecione um curso</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nome']); ?> (<?php echo htmlspecialchars($curso['categoria']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Atribuição</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalConfirmacao" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Remover Atribuição</h3>
                <span class="close" onclick="fecharConfirmacao()">&times;</span>
            </div>
            <div style="padding: 28px;">
                <p style="color: #475569; font-size: 1rem; line-height: 1.6;">
                    Tem certeza que deseja remover esta atribuição? O curso deixará de aparecer no dashboard desse professor.
                </p>
            </div>
            <div class="confirm-actions">
                <button type="button" class="btn btn-outline" onclick="fecharConfirmacao()">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarRemocao">Remover Atribuição</button>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="toast-container"></div>

    <script>
        let atribuicaoParaRemover = null;

        function showToast(tipo, titulo, mensagem) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info'
            };

            toast.className = `toast ${tipo}`;
            toast.innerHTML = `
                <i class="fas ${icons[tipo] || icons.info}"></i>
                <div>
                    <strong>${titulo}</strong>
                    <span>${mensagem}</span>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(12px)';
                setTimeout(() => toast.remove(), 220);
            }, 3500);
        }

        // Funções do modal
        function abrirModalAtribuicao() {
            document.getElementById('modalAtribuicao').style.display = 'block';
        }

        function abrirModalComProfessor(professorId) {
            document.getElementById('professor_id').value = professorId;
            document.getElementById('curso_id').value = '';
            abrirModalAtribuicao();
        }

        function abrirModalComCurso(cursoId) {
            document.getElementById('professor_id').value = '';
            document.getElementById('curso_id').value = cursoId;
            abrirModalAtribuicao();
        }

        function fecharModal() {
            document.getElementById('modalAtribuicao').style.display = 'none';
        }

        function abrirConfirmacao(atribuicaoId) {
            atribuicaoParaRemover = atribuicaoId;
            document.getElementById('modalConfirmacao').style.display = 'block';
        }

        function fecharConfirmacao() {
            atribuicaoParaRemover = null;
            document.getElementById('modalConfirmacao').style.display = 'none';
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('modalAtribuicao');
            if (event.target === modal) {
                fecharModal();
            }
            const confirmacao = document.getElementById('modalConfirmacao');
            if (event.target === confirmacao) {
                fecharConfirmacao();
            }
        }

        // Formulário de atribuição
        document.getElementById('formAtribuicao').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const professorId = document.getElementById('professor_id').value;
            const cursoId = document.getElementById('curso_id').value;
            
            if (!professorId || !cursoId) {
                showToast('warning', 'Campos obrigatórios', 'Selecione um professor e um curso para criar a atribuição.');
                return;
            }
            
            try {
                const response = await fetch('api/atribuicoes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        professor_id: professorId,
                        curso_id: cursoId
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    fecharModal();
                    showToast('success', 'Atribuição criada', 'O curso já aparecerá no dashboard do professor.');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast('error', 'Erro ao criar atribuição', result.error);
                }
            } catch (error) {
                showToast('error', 'Erro de conexão', error.message);
            }
        });

        function atribuirCursos(professorId, professorNome) {
            abrirModalComProfessor(professorId);
        }

        function atribuirProfessores(cursoId, cursoNome) {
            abrirModalComCurso(cursoId);
        }

        async function removerAtribuicao(atribuicaoId) {
            abrirConfirmacao(atribuicaoId);
        }

        document.getElementById('btnConfirmarRemocao').addEventListener('click', async function() {
            if (!atribuicaoParaRemover) return;

            try {
                const response = await fetch('api/atribuicoes.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: atribuicaoParaRemover
                    })
                });

                const result = await response.json();

                if (result.success) {
                    fecharConfirmacao();
                    showToast('success', 'Atribuição removida', 'O curso foi removido do dashboard do professor.');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast('error', 'Erro ao remover atribuição', result.error);
                }
            } catch (error) {
                showToast('error', 'Erro de conexão', error.message);
            }
        });

    </script>
    <script src="dark-mode.js"></script>
</body>
</html>







