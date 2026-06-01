<?php
session_start();
include 'db.php';

// Buscar estatÃ­sticas do sistema
$usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
$agendamentos = $conn->query("SELECT COUNT(*) as total FROM agendamentos")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <script>
        // Script de bloqueio para evitar o flash de luz (FOUC)
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
            // Aplicar estilos mínimos imediatamente
            document.write('<style>.dark-mode-init body { visibility: hidden; background: #0f172a !important; }</style>');
        }
    </script>
    <link rel="stylesheet" href="dark-mode.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Sistema - EduConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 32px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #1e40af;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #1e40af;
            transform: translateX(-4px);
        }

        .header h1 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            margin-top: 0;
        }

        .header p {
            color: #64748b;
            font-size: 1rem;
            margin: 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            padding: 32px;
            background: #f8fafc;
            margin-bottom: 32px;
            border-radius: 12px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: white;
            flex-shrink: 0;
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            margin: 6px 0 14px;
            color: #059669;
        }

        .stat-change-icon { font-size: 0.75rem; }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(226, 232, 240, 0.6);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #2563eb, #6366f1);
            transition: width 0.8s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #2563eb;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-value {
            font-size: 2.5em;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .content {
            padding: 32px;
        }

        .content h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 24px;
        }

        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .config-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            padding: 28px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .config-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .config-card:hover {
            border-color: #2563eb;
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .config-card:hover::before {
            opacity: 1;
        }

        .config-card h3 {
            color: #0f172a;
            margin-bottom: 12px;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .config-card h3 i {
            color: #1e40af;
        }

        .config-card p {
            color: #64748b;
            font-size: 0.95em;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .config-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9em;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .status-ativo {
            background: #dcfce7;
            color: #166534;
        }

        .status-desenvolvimento {
            background: #fef3c7;
            color: #92400e;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(15, 23, 42, 0.12), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(37, 99, 235, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%) !important;
            padding: 0;
        }

        .container {
            max-width: 1380px;
            padding: 24px;
        }

        .header {
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            border-radius: 30px !important;
            margin-bottom: 26px;
            padding: 72px 40px 48px !important;
            color: #ffffff !important;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
                radial-gradient(circle at 84% 26%, rgba(37, 99, 235, 0.28), transparent 34%),
                linear-gradient(135deg, #020617 0%, #1e293b 42%, #2563eb 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.42) !important;
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.2) !important;
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
        .header p,
        .back-btn {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            color: #ffffff !important;
            font-size: clamp(1.85rem, 3.6vw, 2.75rem);
            font-weight: 850;
            letter-spacing: -0.055em;
        }

        .header p {
            max-width: 720px;
            color: rgba(255, 255, 255, 0.82) !important;
            line-height: 1.65;
        }

        .back-btn {
            position: absolute;
            top: 24px;
            left: 24px;
            margin-bottom: 0;
            padding: 10px 16px;
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.24) !important;
            border-radius: 999px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(16px);
        }

        .stats,
        .content {
            isolation: isolate;
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .stats::before,
        .content::before,
        .stat-card::before,
        .config-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            opacity: 1;
            background: linear-gradient(90deg, #020617, #2563eb, #06b6d4) !important;
        }

        .stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
            padding: 28px;
            margin-bottom: 28px;
        }

        .stat-card,
        .config-card {
            isolation: isolate;
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .stat-card {
            padding: 30px;
        }

        .stat-card:hover,
        .config-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
            border-color: rgba(37, 99, 235, 0.24);
        }

        .stat-value {
            color: #0f172a;
            font-size: 2.35rem;
            font-weight: 850;
            letter-spacing: -0.06em;
        }

        .stat-label {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.08em;
        }

        .content {
            padding: 30px;
        }

        .content h2 {
            color: #0f172a;
            font-weight: 850;
            letter-spacing: -0.045em;
        }

        .config-grid {
            gap: 24px;
        }

        .config-card {
            padding: 30px;
        }

        .config-card h3 {
            color: #0f172a;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .config-card h3 i {
            width: 46px;
            height: 46px;
            display: inline-grid;
            place-items: center;
            color: #ffffff;
            border-radius: 16px;
            background: linear-gradient(135deg, #020617, #2563eb);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.22);
        }

        .status-badge {
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 850;
            letter-spacing: 0.05em;
        }

        .status-ativo {
            color: #065f46;
            background: #d1fae5;
            border: 1px solid #a7f3d0;
        }

        .btn {
            min-height: 40px;
            border-radius: 999px !important;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            color: #ffffff !important;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #64748b, #334155) !important;
            color: #ffffff !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #ffffff !important;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: #ffffff !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: #ffffff !important;
        }

        .actions {
            border-top: 1px solid rgba(226, 232, 240, 0.72);
        }

        @media (max-width: 900px) {
            .stats {
                grid-template-columns: 1fr;
            }
        }

        /* FORÇAR MODO ESCURO INTERNO */
        html.dark-mode body,
        body.dark-mode {
            background: #0f172a !important;
            color: #f8fafc !important;
        }

        .dark-mode .container {
            background: transparent !important;
        }

        .dark-mode .header {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.3) !important;
        }

        .dark-mode .header h1,
        .dark-mode .header p,
        .dark-mode h2,
        .dark-mode h3,
        .dark-mode h4,
        .dark-mode .content h2,
        .dark-mode .config-card h3 {
            color: #ffffff !important;
        }

        .dark-mode .stats,
        .dark-mode .content,
        .dark-mode .stat-card,
        .dark-mode .config-card {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 22px 58px rgba(0, 0, 0, 0.25) !important;
        }

        .dark-mode .stat-value {
            color: #ffffff !important;
        }

        .dark-mode .stat-label,
        .dark-mode .config-card p {
            color: #cbd5e1 !important;
        }

        .dark-mode .actions {
            border-top-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .status-badge {
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #cbd5e1 !important;
        }

        .dark-mode .status-ativo {
            background: rgba(16, 185, 129, 0.15) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: #34d399 !important;
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

/* === Padronizar header - igual professores.php === */
.header {
    position: relative !important;
    min-height: 250px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: center !important;
    text-align: center !important;
    overflow: hidden !important;
    border-radius: 30px !important;
    margin-bottom: 26px !important;
    padding: 72px 40px 48px !important;
    color: #ffffff !important;
    background:
        radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
        radial-gradient(circle at 84% 26%, rgba(255, 255, 255, 0.12), transparent 34%),
        linear-gradient(135deg, #172554 0%, #1e40af 52%, #2563eb 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.42) !important;
    box-shadow: 0 28px 80px rgba(37, 99, 235, 0.2) !important;
}
.header::before { display: none !important; }
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
.header h1, .header p, .back-btn { position: relative !important; z-index: 1 !important; }
.header h1 { color: #ffffff !important; font-size: clamp(1.85rem, 3.6vw, 2.75rem) !important; font-weight: 850 !important; letter-spacing: -0.055em !important; }
.header p { color: rgba(255, 255, 255, 0.82) !important; margin: 0 auto !important; }
.header .back-btn {
    position: relative !important; top: auto !important; left: auto !important;
    padding: 10px 18px !important; border-radius: 999px !important;
    color: #ffffff !important; background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.24) !important;
    backdrop-filter: blur(16px) !important; z-index: 10 !important;
}
.header .back-btn:hover { background: rgba(255, 255, 255, 0.25) !important; }
.header-top {
    position: absolute !important; top: 20px !important; left: 24px !important; right: 24px !important;
    display: flex !important; justify-content: space-between !important; align-items: center !important;
    width: auto !important; margin-bottom: 0 !important; z-index: 10 !important;
}
.header-content { display: block !important; text-align: center !important; padding: 0 !important; position: relative !important; z-index: 1 !important; }
.header-actions #darkModeToggle, .header #darkModeToggle {
    background: rgba(255, 255, 255, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: #ffffff !important;
}
.dark-mode .header { background: radial-gradient(circle at 10% 18%, rgba(255,255,255,0.24), transparent 30%), radial-gradient(circle at 84% 26%, rgba(255,255,255,0.12), transparent 34%), linear-gradient(135deg, #172554 0%, #1e40af 52%, #2563eb 100%) !important; }

/* === Padronizar cores dos stat-cards === */
.stat-card::before {
    background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
    transform: scaleX(1) !important;
    opacity: 1 !important;
}
.config-card::before {
    background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
}

/* === Remover laranja/amber === */
.btn-warning { background: #2563eb !important; color: white !important; }
.btn-warning:hover { background: #1e40af !important; }
.status-desenvolvimento { background: #eff6ff !important; color: #1e40af !important; }
.dark-mode .btn-warning { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }

/* === Remover verde/teal === */
.btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; color: #fff !important; }
.btn-success:hover { background: linear-gradient(135deg, #1e40af, #172554) !important; }
.dark-mode .btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }

/* === Container card - igual cursos_completo === */
body { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important; padding: 20px !important; min-height: 100vh !important; }
.container { background: white !important; border-radius: 24px !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08) !important; overflow: hidden !important; border: 1px solid #e2e8f0 !important; padding: 0 !important; }
.page-content { padding: 24px; }
.dark-mode body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; }
.dark-mode .container { background: #1e293b !important; border-color: rgba(255, 255, 255, 0.1) !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important; }

</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-top">
                <a href="dashboard_final.php" class="back-btn"><i class="fas fa-arrow-left"></i> Voltar ao Dashboard</a>
                <div class="header-actions">
                    <button id="darkModeToggle" title="Alternar tema" aria-label="Alternar Dark Mode"><i class="fas fa-moon"></i></button>
                </div>
            </div>
            <div class="header-content">
                <h1>Configurações do Sistema</h1>
                <p>Gerencie as configurações e preferências do sistema educacional</p>
            </div>
        </div>
        <div class="page-content">

        <div class="stats">
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Usuários</h3>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-value"><?php echo $usuarios; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Total de usuários</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($usuarios / 30) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Cursos</h3>
                    <div class="stat-icon"><i class="fas fa-laptop-code"></i></div>
                </div>
                <div class="stat-value"><?php echo $cursos; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Cursos ativos</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($cursos / 20) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Agendamentos</h3>
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                </div>
                <div class="stat-value"><?php echo $agendamentos; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Aulas agendadas</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($agendamentos / 20) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Status</h3>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="stat-value">Ativo</div>
                <div class="stat-change"><i class="fas fa-circle stat-change-icon"></i> Sistema operacional</div>
                <div class="progress-bar"><div class="progress-fill" style="width: 100%"></div></div>
            </div>
        </div>

        <div class="content">
            <h2>Configurações Disponíveis</h2>
            
            <div class="config-grid">
                <!-- Gestão de Usuários -->
                <div class="config-card">
                    <h3><i class="fas fa-users"></i> Gestão de Usuários</h3>
                    <p>Configurar permissões e tipos de usuário</p>
                    <div class="status-badge status-ativo">Disponível</div>
                    <div class="config-actions">
                        <a href="sistema_usuarios.php" class="btn btn-primary">Configurar</a>
                        <a href="sistema_usuarios.php" class="btn btn-secondary">Permissões</a>
                    </div>
                </div>

                <!-- Configurações de Cursos -->
                <div class="config-card">
                    <h3><i class="fas fa-book"></i> Configurações de Cursos</h3>
                    <p>Definir categorias e níveis de curso</p>
                    <div class="status-badge status-ativo">Disponível</div>
                    <div class="config-actions">
                        <a href="cursos_completo.php" class="btn btn-primary">Configurar</a>
                        <a href="cursos_completo.php" class="btn btn-secondary">Categorias</a>
                    </div>
                </div>

                <!-- Configurações do Sistema -->
                <div class="config-card">
                    <h3><i class="fas fa-cog"></i> Configurações do Sistema</h3>
                    <p>Preferências gerais e notificações</p>
                    <div class="status-badge status-ativo">Disponível</div>
                    <div class="config-actions">
                        <a href="configuracoes_sistema.php" class="btn btn-primary">Configurar</a>
                        <a href="configuracoes_sistema.php" class="btn btn-secondary">Notificações</a>
                    </div>
                </div>

                <!-- Backup e Restauração -->
                <div class="config-card">
                    <h3><i class="fas fa-database"></i> Backup e Restauração</h3>
                    <p>Gerenciar backups do banco de dados</p>
                    <div class="status-badge status-ativo">Disponível</div>
                    <div class="config-actions">
                        <a href="backup_completo_manual.php" class="btn btn-success">Backup</a>
                        <a href="download_backup.php" class="btn btn-warning">Restaurar</a>
                    </div>
                </div>

                <!-- Segurança -->
                <div class="config-card">
                    <h3><i class="fas fa-shield-alt"></i> Segurança</h3>
                    <p>Configurações de segurança e acesso</p>
                    <div class="status-badge status-ativo">Disponível</div>
                    <div class="config-actions">
                        <a href="seguranca_sistema.php" class="btn btn-primary">Configurar</a>
                        <a href="seguranca_sistema.php" class="btn btn-secondary">Acessos</a>
                    </div>
                </div>

                <!-- Logs do Sistema -->
                <div class="config-card">
                    <h3><i class="fas fa-chart-line"></i> Logs do Sistema</h3>
                    <p>Visualizar logs e auditoria</p>
                    <div class="status-badge status-ativo">Disponível</div>
                    <div class="config-actions">
                        <a href="logs_sistema.php" class="btn btn-primary">Ver Logs</a>
                        <a href="exportar_dados.php" class="btn btn-secondary">Exportar</a>
                    </div>
                </div>

                <!-- Manutenção do Sistema -->
                <div class="config-card">
                    <h3><i class="fas fa-tools"></i> Manutenção do Sistema</h3>
                    <p>Ferramentas de manutenção e controle</p>
                    <div class="status-badge status-ativo">Disponível</div>
                    <div class="config-actions">
                        <a href="modo_manutencao.php" class="btn btn-warning">Modo Manutenção</a>
                        <a href="modo_manutencao.php" class="btn btn-secondary">Ferramentas</a>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="teste_todos_botoes.php" class="btn btn-primary"><i class="fas fa-flask"></i> Testar Todas as Funcionalidades</a>
                <a href="dashboard_final.php" class="btn btn-secondary"><i class="fas fa-home"></i> Voltar ao Dashboard</a>
            </div>
        </div>
        </div><!-- /page-content -->
    </div>
    <script src="dark-mode.js"></script>
</body>
</html>








