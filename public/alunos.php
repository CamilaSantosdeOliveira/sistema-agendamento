<?php
// ForÃ§ar atualizaÃ§Ã£o - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$alunos_count = 0;
$alunos_ativos = 0;
$cursos_inscritos = 0;

try {
    // Contar alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    if ($result) {
        $alunos_count = $result->fetch_assoc()['total'];
    }

    // Contar alunos ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
    if ($result) {
        $alunos_ativos = $result->fetch_assoc()['total'];
    }

    // Contar cursos inscritos (simulado)
    $cursos_inscritos = $alunos_ativos * 2; // Simulado

    // Buscar alunos para exibir
    $alunos_result = $conn->query("
        SELECT u.id, u.nome, u.email, u.ativo, u.criado_em,
               0 as agendamentos_count
        FROM usuarios u
        WHERE u.tipo_usuario = 'aluno'
        ORDER BY u.nome
    ");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrÃ£o
    $alunos_count = 0;
    $alunos_ativos = 0;
    $cursos_inscritos = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Alunos</title>
    <script>
        // Script de bloqueio para evitar o flash de luz (FOUC)
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
            // Aplicar estilos mínimos imediatamente vinculados à classe de inicialização
            document.write('<style>.dark-mode-init body { visibility: hidden; background: #0f172a !important; }</style>');
        }
    </script>
    <link rel="stylesheet" href="dark-mode.css">
    
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
            --secondary-color: #64748b;
            --success-color: #059669;
            --success-light: #10b981;
            --warning-color: #d97706;
            --warning-light: #f59e0b;
            --danger-color: #dc2626;
            --danger-light: #ef4444;
            --info-color: #0891b2;
            --info-light: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #0f172a;
            --border-color: #e2e8f0;
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            --gradient-success: linear-gradient(135deg, #059669 0%, #047857 100%);
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            margin-bottom: 32px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            transition: var(--transition);
        }
        
        .back-btn:hover {
            color: var(--primary-dark);
            transform: translateX(-4px);
        }

        /* ===== Modo escuro — Gestão de Alunos ===== */
        html.dark-mode body,
        body.dark-mode {
            background: #0f172a !important;
            color: #f8fafc !important;
        }

        .dark-mode .stat-card {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .stat-value {
            color: #f8fafc !important;
        }

        .dark-mode .stat-label {
            color: #94a3b8 !important;
        }

        .dark-mode .content-section {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .section-header {
            background: rgba(15, 23, 42, 0.75) !important;
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .section-header h3 {
            color: #f8fafc !important;
        }

        .dark-mode .section-header h3 i {
            color: #fcd34d !important;
        }

        .dark-mode .aluno-card {
            background: rgba(30, 41, 59, 0.95) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .aluno-info h4 {
            color: #f8fafc !important;
        }

        .dark-mode .aluno-email {
            color: #94a3b8 !important;
        }

        .dark-mode .aluno-stats .stat-item {
            background: rgba(15, 23, 42, 0.7) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #cbd5e1 !important;
        }

        .dark-mode .aluno-stats .stat-item i {
            color: #fcd34d !important;
        }

        .dark-mode .status-ativo {
            background: rgba(16, 185, 129, 0.22) !important;
            color: #6ee7b7 !important;
        }

        .dark-mode .status-inativo {
            background: rgba(239, 68, 68, 0.22) !important;
            color: #fca5a5 !important;
        }

        .dark-mode .btn-outline {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #93c5fd !important;
            border-color: rgba(96, 165, 250, 0.4) !important;
        }

        .dark-mode .btn-outline:hover {
            background: rgba(37, 99, 235, 0.35) !important;
            color: #ffffff !important;
        }

        .dark-mode .empty-state,
        .dark-mode .empty-state h4 {
            color: #cbd5e1 !important;
        }

        .dark-mode .empty-state p {
            color: #94a3b8 !important;
        }

        /* Dark mode para o empty-state da seção de Inscrições */
        .dark-mode .content-section:last-of-type .empty-state {
            background: rgba(15, 23, 42, 0.6) !important;
        }

        .dark-mode .content-section:last-of-type .section-header {
            background: rgba(15, 23, 42, 0.75) !important;
        }

        .dark-mode .content-section:last-of-type::before {
            opacity: 0.7;
        }

        .header h1 {
            color: #0f172a;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .header p {
            color: var(--secondary-color);
            font-size: 1.1rem;
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(226, 232, 240, 0.8);
            text-align: center;
            transition: var(--transition);
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
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-card.primary::before {
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
        }
        
        .stat-card.success::before {
            background: linear-gradient(90deg, var(--success-color), #34d399);
        }
        
        .stat-card.warning::before {
            background: linear-gradient(90deg, var(--warning-color), #fbbf24);
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
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

        .stat-change {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            margin: 6px 0 14px;
        }

        .stat-change.positive { color: #059669; }
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

        .progress-fill.success { background: linear-gradient(90deg, #059669, #34d399); }
        .progress-fill.warning { background: linear-gradient(90deg, #d97706, #fbbf24); }

        .stat-card.primary .stat-icon {
            background-color: var(--primary-color);
        }

        .stat-card.success .stat-icon {
            background-color: var(--success-color);
        }

        .stat-card.warning .stat-icon {
            background-color: var(--warning-color);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 6px;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-section {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 32px;
            overflow: hidden;
        }

        .section-header {
            padding: 24px 32px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #1e40af, #172554) !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .alunos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 24px;
        }

        .aluno-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            transition: var(--transition);
        }

        .aluno-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .aluno-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .aluno-avatar {
            width: 60px;
            height: 60px;
            background: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .aluno-info h4 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
        }

        .aluno-email {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .aluno-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--secondary-color);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 16px;
            display: inline-block;
        }

        .status-ativo {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-inativo {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 16px;
            transition: var(--transition);
        }

        .back-btn:hover {
            color: var(--primary-dark);
            transform: translateX(-4px);
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            color: var(--border-color);
        }

        .empty-state h4 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: var(--dark-color);
        }

        .empty-state p {
            margin-bottom: 16px;
        }

        body { background: #f1f5f9 !important; padding: 0 !important; }

        .container {
            max-width: 1380px;
            padding: 24px;
        }

        .header {
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
            border-radius: 30px !important;
            margin-bottom: 26px;
            padding: 72px 40px 48px !important;
            color: #ffffff !important;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
                radial-gradient(circle at 84% 26%, rgba(255, 255, 255, 0.12), transparent 34%),
                linear-gradient(135deg, #172554 0%, #1e40af 52%, #2563eb 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.42) !important;
            box-shadow: 0 28px 80px rgba(37, 99, 235, 0.2) !important;
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
            font-size: clamp(1.85rem, 3.6vw, 2.75rem);
            font-weight: 850;
            letter-spacing: -0.055em;
            color: #ffffff !important;
        }

        .header p {
            max-width: 720px;
            color: rgba(255, 255, 255, 0.82) !important;
            line-height: 1.65;
        }

        .back-btn {
            position: absolute !important;
            top: 20px;
            left: 20px;
            margin-bottom: 0;
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
            width: auto !important;
            max-width: max-content;
            padding: 10px 18px;
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.24) !important;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 10;
            white-space: nowrap;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
            transform: translateX(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .back-btn i {
            font-size: 12px;
        }

        .stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            margin-bottom: 28px;
        }

        .stat-card {
            isolation: isolate;
            padding: 30px;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .stat-card::before {
            height: 6px;
            transform: scaleX(1);
            background: linear-gradient(90deg, #2563eb, #1e40af) !important;
        }

        .stat-card::after { content: none; }

        .stat-card.primary .stat-icon,
        .stat-card.success .stat-icon,
        .stat-card.warning .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 20px;
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.16);
        }

        .stat-value {
            color: #0f172a;
            font-size: 2.45rem;
            font-weight: 850;
            letter-spacing: -0.06em;
        }

        .stat-label {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.08em;
        }

        .content-section {
            border-radius: 30px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
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

        .alunos-grid {
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 26px;
            padding: 32px;
        }

        .aluno-card {
            isolation: isolate;
            overflow: hidden;
            padding: 28px;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.94) !important;
            border: 1px solid rgba(226, 232, 240, 0.82) !important;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
        }

        .aluno-card::before {
            height: 6px;
            background: linear-gradient(90deg, #f59e0b, #d97706, #2563eb) !important;
        }

        .aluno-card::after { content: none; }

        .aluno-card:hover {
            transform: translateY(-9px);
            border-color: rgba(217, 119, 6, 0.35) !important;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .aluno-header {
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 20px;
        }

        .aluno-avatar {
            width: 66px;
            height: 66px;
            flex: 0 0 66px;
            border-radius: 22px;
            background: linear-gradient(135deg, #d97706, #2563eb) !important;
            box-shadow: 0 18px 38px rgba(217, 119, 6, 0.22);
        }

        .aluno-info h4 {
            color: #0f172a;
            font-size: 1.22rem;
            font-weight: 850;
            letter-spacing: -0.045em;
        }

        .aluno-email {
            color: #64748b;
            line-height: 1.45;
            word-break: break-word;
        }

        .status-badge {
            min-height: 30px;
            padding: 7px 13px;
            border-radius: 999px !important;
            font-size: 0.74rem;
            font-weight: 850;
            letter-spacing: 0.06em;
            border: 1px solid transparent;
        }

        .aluno-stats {
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .aluno-stats .stat-item {
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.75);
            color: #475569;
            font-weight: 650;
        }

        .aluno-stats .stat-item i {
            color: #d97706;
        }

        .aluno-card > div:last-child {
            flex-wrap: wrap;
        }

        .btn {
            min-height: 40px;
            border-radius: 999px !important;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #d97706, #2563eb) !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        .btn-outline {
            color: #0f172a;
            background: #ffffff;
            border: 1px solid rgba(203, 213, 225, 0.95);
        }

        .btn-outline:hover {
            color: #ffffff;
            background: #d97706;
            border-color: #d97706;
        }

        .empty-state {
            padding: 58px 24px;
        }

        .empty-state i {
            color: #f59e0b;
            opacity: 0.75;
        }

        .content-section:last-of-type {
            position: relative;
            overflow: hidden;
        }

        .content-section:last-of-type::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981);
        }

        .content-section:last-of-type .section-header {
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.98), rgba(238, 242, 255, 0.98)) !important;
        }

        .content-section:last-of-type .section-header h3 i {
            color: #2563eb;
        }

        .content-section:last-of-type .empty-state {
            background:
                radial-gradient(circle at 50% 0%, rgba(37, 99, 235, 0.08), transparent 34%),
                linear-gradient(180deg, rgba(239, 246, 255, 0.58), rgba(255, 255, 255, 0.92));
        }

        .content-section:last-of-type .empty-state i {
            width: 76px;
            height: 76px;
            display: inline-grid;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 24px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 18px 38px rgba(37, 99, 235, 0.22);
            opacity: 1;
        }

        .content-section:last-of-type .empty-state h4 {
            color: #0f172a;
            font-size: 1.28rem;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .content-section:last-of-type .empty-state p {
            color: #64748b;
            max-width: 520px;
            margin: 0 auto 22px;
            line-height: 1.65;
        }

        .content-section:last-of-type .btn-success {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            color: #ffffff;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 12px;
            }

            .header {
                min-height: 230px;
                padding: 76px 22px 42px !important;
                border-radius: 24px !important;
            }

            .back-btn {
                top: 18px;
                left: 18px;
            }

            .alunos-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
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

        /* === Padronizar cores - remover laranja/amber e verde === */
        /* Stat-cards e aluno-cards top strip */
        .stat-card::before,
        .stat-card.primary::before,
        .stat-card.success::before,
        .stat-card.warning::before,
        .stat-card.info::before,
        .aluno-card::before {
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
        }
        /* Decorações circulares */
        .stat-card::after { background: rgba(37, 99, 235, 0.07) !important; }
        .aluno-card::after { background: rgba(37, 99, 235, 0.06) !important; }
        /* Hover do card */
        .aluno-card:hover { border-color: rgba(37, 99, 235, 0.3) !important; }
        /* Section header */
        .section-header {
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.96), rgba(238, 242, 255, 0.96)) !important;
        }
        .section-header h3 i { color: #2563eb !important; }
        /* Avatar dos alunos */
        .aluno-avatar {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            box-shadow: 0 18px 38px rgba(37, 99, 235, 0.22) !important;
        }
        /* Botões */
        .btn-primary { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }
        .btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }
        /* Botão outline hover */
        .btn-outline:hover {
            background: #2563eb !important;
            border-color: #2563eb !important;
        }
        /* Ícones de stats e estados vazios */
        .aluno-stats .stat-item i,
        .empty-state i {
            color: #2563eb !important;
        }

        /* === Header profissional refatorado === */
        .header {
            position: relative !important;
            padding: 62px 40px 44px !important;
            text-align: center !important;
        }
        .header-top {
            position: absolute !important;
            top: 20px !important;
            left: 24px !important;
            right: 24px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: auto !important;
            margin-bottom: 0 !important;
            z-index: 10 !important;
        }
        .header .back-btn {
            position: relative !important;
            top: auto !important;
            left: auto !important;
        }
        .header-content {
            display: block !important;
            text-align: center !important;
            padding: 0 !important;
            position: relative;
            z-index: 1;
        }
        .header p { margin: 0 auto !important; text-align: center !important; }

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
                <h1>Gestão de Alunos</h1>
                <p>Gerencie os estudantes e suas inscrições nos cursos</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <h3 class="stat-title">Total de Alunos</h3>
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                </div>
                <div class="stat-value"><?php echo $alunos_count; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Total no sistema</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($alunos_count / 20) * 100, 100); ?>%"></div></div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Alunos Ativos</h3>
                    <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                </div>
                <div class="stat-value"><?php echo $alunos_ativos; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Alunos ativos</div>
                <div class="progress-bar"><div class="progress-fill success" style="width: <?php echo min(($alunos_ativos / 20) * 100, 100); ?>%"></div></div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Cursos Inscritos</h3>
                    <div class="stat-icon"><i class="fas fa-laptop-code"></i></div>
                </div>
                <div class="stat-value"><?php echo $cursos_inscritos; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Matrículas ativas</div>
                <div class="progress-bar"><div class="progress-fill warning" style="width: <?php echo min(($cursos_inscritos / 30) * 100, 100); ?>%"></div></div>
            </div>
        </div>

        <!-- Alunos Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-users"></i> Lista de Alunos</h3>
                <button class="btn btn-primary" onclick="adicionarAluno()">
                    <i class="fas fa-plus"></i> Novo Aluno
                </button>
            </div>
            
            <?php if ($alunos_result && $alunos_result->num_rows > 0): ?>
                <div class="alunos-grid">
                    <?php while ($aluno = $alunos_result->fetch_assoc()): ?>
                        <div class="aluno-card" data-id="<?php echo $aluno['id']; ?>" data-nome="<?php echo htmlspecialchars($aluno['nome']); ?>" data-email="<?php echo htmlspecialchars($aluno['email']); ?>">
                            <div class="aluno-header">
                                <div class="aluno-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="aluno-info">
                                    <h4><?php echo htmlspecialchars($aluno['nome']); ?></h4>
                                    <div class="aluno-email"><?php echo htmlspecialchars($aluno['email']); ?></div>
                                </div>
                            </div>
                            
                            <div class="status-badge status-<?php echo $aluno['ativo'] ? 'ativo' : 'inativo'; ?>">
                                <?php echo $aluno['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </div>
                            
                            <div class="aluno-stats">
                                <div class="stat-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span><?php echo $aluno['agendamentos_count']; ?> aulas</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo date('d/m/Y', strtotime($aluno['criado_em'])); ?></span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-outline btn-sm" onclick="editarAluno(<?php echo $aluno['id']; ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="verDetalhes(<?php echo $aluno['id']; ?>)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="inscreverCurso(<?php echo $aluno['id']; ?>)">
                                    <i class="fas fa-plus"></i> Inscrição
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-graduate"></i>
                    <h4>Nenhum aluno disponível</h4>
                    <p>Comece adicionando o primeiro aluno do sistema</p>
                    <button class="btn btn-primary" onclick="adicionarAluno()">
                        <i class="fas fa-plus"></i> Primeiro Aluno
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Inscrições Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-graduation-cap"></i> Inscrições em Cursos</h3>
                <a href="inscricoes_cursos.php" class="btn btn-success">
                    <i class="fas fa-cog"></i> Gerenciar
                </a>
            </div>

            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <h4>Sistema de Inscrições</h4>
                <p>Gerencie as inscrições dos alunos nos cursos disponíveis do sistema</p>
                <a href="inscricoes_cursos.php" class="btn btn-success">
                    <i class="fas fa-cog"></i> Gerenciar Inscrições
                </a>
            </div>
        </div>
    </div>

    <script>
        // FunÃ§Ã£o para adicionar aluno
        async function adicionarAluno() {
            try {
                showNovoAlunoModal();
            } catch (error) {
                showNotification('âŒ Erro ao abrir modal de aluno', 'error');
            }
        }

        // FunÃ§Ã£o para editar aluno
        async function editarAluno(id) {
            try {
                const response = await fetch(`api/usuarios.php?action=buscar_usuario&id=${id}&tipo=aluno`);
                const result = await response.json();
                
                if (result.success) {
                    showEditarAlunoModal(result.data);
                } else {
                    showNotification('âŒ Erro ao carregar aluno: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro de conexÃ£o ao carregar aluno', 'error');
            }
        }

        // FunÃ§Ã£o para ver detalhes do aluno
        async function verDetalhes(id) {
            try {
                const response = await fetch(`api/usuarios.php?action=buscar_usuario&id=${id}&tipo=aluno`);
                const result = await response.json();
                
                if (result.success) {
                    showDetalhesAlunoModal(result.data);
                } else {
                    showNotification('âŒ Erro ao carregar detalhes: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro de conexÃ£o ao carregar detalhes', 'error');
            }
        }

        // FunÃ§Ã£o para inscrever aluno em curso
        async function inscreverCurso(id) {
            try {
                showInscricaoCursoModal(id);
            } catch (error) {
                showNotification('âŒ Erro ao abrir modal de inscriÃ§Ã£o', 'error');
            }
        }

        // FunÃ§Ã£o para gerenciar inscriÃ§Ãµes
        function gerenciarInscricoes() {
            window.location.href = 'inscricoes_cursos.php';
        }

        // FunÃ§Ã£o para mostrar modal de novo aluno
        function showNovoAlunoModal() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>ðŸ‘¨â€ðŸŽ“ Novo Aluno</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="novoAlunoForm">
                            <div class="form-group">
                                <label>Nome Completo:</label>
                                <input type="text" name="nome" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Telefone:</label>
                                <input type="tel" name="telefone">
                            </div>
                            <div class="form-group">
                                <label>Data de Nascimento:</label>
                                <input type="date" name="data_nascimento">
                            </div>
                            <div class="form-group">
                                <label>EndereÃ§o:</label>
                                <textarea name="endereco" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Senha:</label>
                                <input type="password" name="senha" required>
                            </div>
                            <div class="form-group">
                                <label>Confirmar Senha:</label>
                                <input type="password" name="confirmar_senha" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button onclick="salvarNovoAluno()" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <button onclick="closeModal()" class="btn btn-outline">Cancelar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // FunÃ§Ã£o para mostrar modal de editar aluno
        function showEditarAlunoModal(aluno) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>âœï¸ Editar Aluno</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editarAlunoForm">
                            <input type="hidden" name="id" value="${aluno.id}">
                            <div class="form-group">
                                <label>Nome Completo:</label>
                                <input type="text" name="nome" value="${aluno.nome}" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" value="${aluno.email}" required>
                            </div>
                            <div class="form-group">
                                <label>Telefone:</label>
                                <input type="tel" name="telefone" value="${aluno.telefone || ''}">
                            </div>
                            <div class="form-group">
                                <label>Data de Nascimento:</label>
                                <input type="date" name="data_nascimento" value="${aluno.data_nascimento || ''}">
                            </div>
                            <div class="form-group">
                                <label>EndereÃ§o:</label>
                                <textarea name="endereco" rows="2">${aluno.endereco || ''}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="ativo" required>
                                    <option value="1" ${aluno.ativo == 1 ? 'selected' : ''}>Ativo</option>
                                    <option value="0" ${aluno.ativo == 0 ? 'selected' : ''}>Inativo</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button onclick="salvarEdicaoAluno()" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <button onclick="closeModal()" class="btn btn-outline">Cancelar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // FunÃ§Ã£o para mostrar modal de detalhes do aluno
        function showDetalhesAlunoModal(aluno) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>ðŸ‘ï¸ Detalhes do Aluno</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="aluno-details">
                            <div class="detail-row">
                                <span class="detail-label">Nome:</span>
                                <span class="detail-value">${aluno.nome}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${aluno.email}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Telefone:</span>
                                <span class="detail-value">${aluno.telefone || 'NÃ£o informado'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Data de Nascimento:</span>
                                <span class="detail-value">${aluno.data_nascimento ? new Date(aluno.data_nascimento).toLocaleDateString('pt-BR') : 'NÃ£o informado'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">EndereÃ§o:</span>
                                <span class="detail-value">${aluno.endereco || 'NÃ£o informado'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge ${aluno.ativo == 1 ? 'status-ativo' : 'status-inativo'}">
                                        ${aluno.ativo == 1 ? 'Ativo' : 'Inativo'}
                                    </span>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Data de Cadastro:</span>
                                <span class="detail-value">${new Date(aluno.criado_em).toLocaleDateString('pt-BR')}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="editarAluno(${aluno.id})" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button onclick="closeModal()" class="btn btn-outline">Fechar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // FunÃ§Ã£o para mostrar modal de inscriÃ§Ã£o em curso
        function showInscricaoCursoModal(alunoId) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>ðŸ“š InscriÃ§Ã£o em Curso</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="inscricaoCursoForm">
                            <input type="hidden" name="aluno_id" value="${alunoId}">
                            <div class="form-group">
                                <label>Curso:</label>
                                <select name="curso_id" required>
                                    <option value="">Selecione um curso</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Data de InÃ­cio:</label>
                                <input type="date" name="data_inicio" required>
                            </div>
                            <div class="form-group">
                                <label>ObservaÃ§Ãµes:</label>
                                <textarea name="observacoes" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button onclick="salvarInscricao()" class="btn btn-primary">
                            <i class="fas fa-save"></i> Inscrever
                        </button>
                        <button onclick="closeModal()" class="btn btn-outline">Cancelar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Carregar cursos disponÃ­veis
            carregarCursosDisponiveis();
        }

        // FunÃ§Ã£o para carregar cursos disponÃ­veis
        async function carregarCursosDisponiveis() {
            try {
                const response = await fetch('api/cursos_simples.php');
                const result = await response.json();
                
                if (result.success) {
                    const cursoSelect = document.querySelector('select[name="curso_id"]');
                    cursoSelect.innerHTML = '<option value="">Selecione um curso</option>';
                    result.data.forEach(curso => {
                        const option = document.createElement('option');
                        option.value = curso.id;
                        option.textContent = `${curso.nome} - ${curso.categoria}`;
                        cursoSelect.appendChild(option);
                    });
                } else {
                    showNotification('âŒ Erro ao carregar cursos: ' + result.error, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro ao carregar cursos: ' + error.message, 'error');
            }
        }

        // FunÃ§Ã£o para salvar novo aluno
        async function salvarNovoAluno() {
            const form = document.getElementById('novoAlunoForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Validar senhas
            if (data.senha !== data.confirmar_senha) {
                showNotification('âŒ As senhas nÃ£o coincidem', 'error');
                return;
            }
            
            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'criar_usuario',
                        tipo_usuario: 'aluno',
                        ...data
                    })
                });
                
                const result = await response.json();
                const errorMessage = result?.message || result?.error || 'Erro desconhecido';
                
                if (result.success) {
                    showNotification('âœ… Aluno criado com sucesso!', 'success');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('âŒ Erro ao criar aluno: ' + errorMessage, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro de conexÃ£o ao criar aluno', 'error');
            }
        }

        // FunÃ§Ã£o para salvar ediÃ§Ã£o de aluno
        async function salvarEdicaoAluno() {
            const form = document.getElementById('editarAlunoForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'editar_usuario',
                        tipo_usuario: 'aluno',
                        ...data
                    })
                });
                
                const result = await response.json();
                const errorMessage = result?.message || result?.error || 'Erro desconhecido';
                
                if (result.success) {
                    showNotification('âœ… Aluno atualizado com sucesso!', 'success');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('âŒ Erro ao atualizar aluno: ' + errorMessage, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro de conexÃ£o ao atualizar aluno', 'error');
            }
        }

        // FunÃ§Ã£o para salvar inscriÃ§Ã£o
        async function salvarInscricao() {
            const form = document.getElementById('inscricaoCursoForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Buscar dados do aluno
            const alunoId = data.aluno_id;
            const alunoCard = document.querySelector(`.aluno-card[data-id="${alunoId}"]`);
            const alunoNome = alunoCard ? alunoCard.getAttribute('data-nome') : '';
            const alunoEmail = alunoCard ? alunoCard.getAttribute('data-email') : '';
            
            try {
                const response = await fetch('api/inscricoes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        curso_id: data.curso_id,
                        aluno_nome: alunoNome,
                        aluno_email: alunoEmail,
                        telefone: '(11) 99999-9999', // Telefone padrÃ£o
                        data_inicio: data.data_inicio,
                        observacoes: data.observacoes || ''
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('âœ… InscriÃ§Ã£o realizada com sucesso!', 'success');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('âŒ Erro ao realizar inscriÃ§Ã£o: ' + result.error, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro de conexÃ£o ao realizar inscriÃ§Ã£o: ' + error.message, 'error');
            }
        }

        // FunÃ§Ã£o para fechar modal
        function closeModal() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
        }

        // FunÃ§Ã£o para mostrar notificaÃ§Ãµes
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="notification-close">&times;</button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remover apÃ³s 5 segundos
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Adicionar estilos para modal e notificaÃ§Ãµes
        const styles = `
            <style>
                .modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                }
                
                .modal-content {
                    background: white;
                    border-radius: 12px;
                    max-width: 600px;
                    width: 90%;
                    max-height: 80vh;
                    overflow-y: auto;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                }
                
                .modal-header {
                    padding: 20px;
                    border-bottom: 1px solid #e2e8f0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .modal-body {
                    padding: 20px;
                }
                
                .modal-footer {
                    padding: 20px;
                    border-top: 1px solid #e2e8f0;
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                }
                
                .close-btn {
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    color: #64748b;
                }
                
                .form-group {
                    margin-bottom: 15px;
                }
                
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: 600;
                    color: #374151;
                }
                
                .form-group input,
                .form-group select,
                .form-group textarea {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    font-size: 14px;
                }
                
                .form-group input:focus,
                .form-group select:focus,
                .form-group textarea:focus {
                    outline: none;
                    border-color: #3b82f6;
                    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                }
                
                .aluno-details {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 20px;
                }
                
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 12px;
                    padding-bottom: 8px;
                    border-bottom: 1px solid #e2e8f0;
                }
                
                .detail-label {
                    font-weight: 600;
                    color: #374151;
                }
                
                .detail-value {
                    color: #1f2937;
                }
                
                .status-badge {
                    padding: 4px 8px;
                    border-radius: 12px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                
                .status-ativo {
                    background: #dcfce7;
                    color: #166534;
                }
                
                .status-inativo {
                    background: #fef2f2;
                    color: #991b1b;
                }
                
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                    z-index: 10001;
                    min-width: 300px;
                    border-left: 4px solid #3b82f6;
                }
                
                .notification-success {
                    border-left-color: #10b981;
                }
                
                .notification-error {
                    border-left-color: #ef4444;
                }
                
                .notification-content {
                    padding: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .notification-close {
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    color: #64748b;
                    margin-left: 10px;
                }
            </style>
        `;
        
        // Adicionar estilos ao head
        if (!document.querySelector('#alunos-styles')) {
            const styleElement = document.createElement('div');
            styleElement.id = 'alunos-styles';
            styleElement.innerHTML = styles;
            document.head.appendChild(styleElement);
        }
    </script>
    <script src="dark-mode.js"></script>
</body>
</html>












