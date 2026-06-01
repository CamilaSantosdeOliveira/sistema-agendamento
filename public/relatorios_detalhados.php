<?php
// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$cursos_count = 0;
$professores_count = 0;
$alunos_count = 0;
$agendamentos_count = 0;
$receita_total = 0;

try {
    // Contar cursos ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $cursos_count = $result->fetch_assoc()['total'];
    }

    // Contar professores ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
    if ($result) {
        $professores_count = $result->fetch_assoc()['total'];
    }

    // Contar alunos ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
    if ($result) {
        $alunos_count = $result->fetch_assoc()['total'];
    }

    // Contar agendamentos futuros
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_aula >= CURDATE()");
    if ($result) {
        $agendamentos_count = $result->fetch_assoc()['total'];
    }

    // Calcular receita total (simulado)
    $result = $conn->query("SELECT SUM(preco * alunos_inscritos) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $receita_total = $result->fetch_assoc()['total'];
        $receita_total = number_format($receita_total, 2, ',', '.');
    }

    // Buscar dados para gráficos
    $cursos_por_categoria = $conn->query("SELECT categoria, COUNT(*) as total FROM cursos GROUP BY categoria");
    $cursos_por_nivel = $conn->query("SELECT nivel, COUNT(*) as total FROM cursos GROUP BY nivel");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $cursos_count = 0;
    $professores_count = 0;
    $alunos_count = 0;
    $agendamentos_count = 0;
    $receita_total = 0;
}
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
    <title>EduConnect - Relatórios Detalhados</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        
        .stat-card.info::before {
            background: linear-gradient(90deg, var(--info-color), #22d3ee);
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

        .progress-fill.success { background: linear-gradient(90deg, #059669, #34d399); }
        .progress-fill.warning { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .progress-fill.info    { background: linear-gradient(90deg, #0891b2, #22d3ee); }

        .stat-card.primary .stat-icon {
            background-color: var(--primary-color);
        }

        .stat-card.success .stat-icon {
            background-color: var(--success-color);
        }

        .stat-card.warning .stat-icon {
            background-color: var(--warning-color);
        }

        .stat-card.info .stat-icon {
            background-color: var(--info-color);
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

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .chart-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .chart-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-color);
        }

        .chart-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-content {
            padding: 24px;
            height: 300px;
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

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(37, 99, 235, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(16, 185, 129, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eff6ff 100%) !important;
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
            align-items: center;
            text-align: center;
            overflow: hidden;
            border-radius: 30px !important;
            margin-bottom: 26px;
            padding: 72px 40px 48px !important;
            color: #ffffff !important;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
                radial-gradient(circle at 84% 26%, rgba(16, 185, 129, 0.26), transparent 34%),
                linear-gradient(135deg, #1e3a8a 0%, #2563eb 46%, #059669 100%) !important;
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

        .stats-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 28px;
        }

        .stat-card,
        .chart-card {
            isolation: isolate;
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .stat-card::before,
        .chart-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            transform: scaleX(1);
            background: linear-gradient(90deg, #2563eb, #06b6d4, #10b981) !important;
        }

        .stat-card {
            padding: 30px 22px;
        }

        .stat-card:hover,
        .chart-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 20px;
            box-shadow: 0 16px 34px rgba(37, 99, 235, 0.2);
        }

        .stat-card.primary .stat-icon,
        .stat-card.success .stat-icon,
        .stat-card.warning .stat-icon,
        .stat-card.info .stat-icon {
            background: linear-gradient(135deg, #2563eb, #10b981);
        }

        .stat-value {
            color: #0f172a;
            font-size: 2rem;
            font-weight: 850;
            letter-spacing: -0.06em;
        }

        .stat-label {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.08em;
        }

        .charts-grid {
            gap: 24px;
            margin-bottom: 24px;
        }

        .chart-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.98), rgba(240, 253, 244, 0.96));
            border-bottom: 1px solid rgba(226, 232, 240, 0.72);
        }

        .chart-header h3 {
            color: #0f172a;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .chart-header h3 i {
            color: #2563eb;
        }

        .chart-content {
            padding: 28px;
            height: 330px;
        }

        .reports-panel {
            padding: 28px;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 18px;
        }

        .report-action {
            min-height: 150px;
            padding: 22px;
            color: #ffffff;
            text-align: left;
            border: 0;
            border-radius: 24px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.13);
        }

        .report-action:hover {
            transform: translateY(-6px);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.2);
        }

        .report-action i {
            display: grid;
            place-items: center;
            width: 52px;
            height: 52px;
            margin-bottom: 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.24);
            font-size: 1.35rem;
        }

        .report-action strong {
            display: block;
            font-size: 1.02rem;
            letter-spacing: -0.03em;
        }

        .report-action p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.9rem;
        }

        .report-action.primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .report-action.success {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .report-action.warning {
            background: linear-gradient(135deg, #d97706, #b45309);
        }

        .report-action.info {
            background: linear-gradient(135deg, #0891b2, #0e7490);
        }

        .toast {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 2000;
            display: none;
            max-width: 360px;
            padding: 16px 18px;
            color: #ffffff;
            border-radius: 18px;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
            font-weight: 750;
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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

/* FORÇAR MODO ESCURO INTERNO (PRIORIDADE MÁXIMA) */
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
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .header h1, 
.dark-mode .header p,
.dark-mode h2,
.dark-mode h3 {
    color: #ffffff !important;
}

.dark-mode .stat-card,
.dark-mode .chart-card {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 22px 58px rgba(0, 0, 0, 0.25) !important;
}

.dark-mode .stat-value {
    color: #ffffff !important;
}

.dark-mode .stat-label {
    color: #cbd5e1 !important;
}

.dark-mode .chart-header {
    background: rgba(255, 255, 255, 0.05) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .chart-header h3 {
    color: #ffffff !important;
}

.dark-mode .chart-header h3 i {
    color: #3b82f6 !important;
}

.dark-mode .report-action p {
    color: rgba(255, 255, 255, 0.8) !important;
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

/* === Container simples (sem card) === */
body { background: #f1f5f9 !important; padding: 0 !important; }
.container { background: transparent !important; border-radius: 0 !important; box-shadow: none !important; overflow: visible !important; border: none !important; padding: 24px !important; }
.page-content { padding: 0; }

/* === Padronizar cores dos stat-cards === */
.stat-card::before, .stat-card.primary::before, .stat-card.success::before,
.stat-card.warning::before, .stat-card.info::before {
    background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
    transform: scaleX(1) !important;
    opacity: 1 !important;
}

/* === Remover laranja do botão warning === */
.report-action.warning { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }

/* === Remover verde/teal === */
.btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; color: #fff !important; }

</style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <a href="dashboard_final.php" class="back-btn"><i class="fas fa-arrow-left"></i> Voltar ao Dashboard</a>
                <div class="header-actions">
                    <button id="darkModeToggle" title="Alternar tema" aria-label="Alternar Dark Mode"><i class="fas fa-moon"></i></button>
                </div>
            </div>
            <div class="header-content">
                <h1>Relatórios Detalhados</h1>
                <p>Análises completas e estatísticas do sistema educacional</p>
            </div>
        </div>
        <div class="page-content">

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <h3 class="stat-title">Cursos Ativos</h3>
                    <div class="stat-icon"><i class="fas fa-laptop-code"></i></div>
                </div>
                <div class="stat-value"><?php echo $cursos_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Cursos disponíveis</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($cursos_count / 20) * 100, 100); ?>%"></div></div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Professores</h3>
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                </div>
                <div class="stat-value"><?php echo $professores_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Professores ativos</div>
                <div class="progress-bar"><div class="progress-fill success" style="width: <?php echo min(($professores_count / 15) * 100, 100); ?>%"></div></div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Alunos</h3>
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                </div>
                <div class="stat-value"><?php echo $alunos_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Alunos cadastrados</div>
                <div class="progress-bar"><div class="progress-fill warning" style="width: <?php echo min(($alunos_count / 30) * 100, 100); ?>%"></div></div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <h3 class="stat-title">Aulas</h3>
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                </div>
                <div class="stat-value"><?php echo $agendamentos_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Aulas agendadas</div>
                <div class="progress-bar"><div class="progress-fill info" style="width: <?php echo min(($agendamentos_count / 20) * 100, 100); ?>%"></div></div>
            </div>

            <div class="stat-card primary">
                <div class="stat-header">
                    <h3 class="stat-title">Receita</h3>
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
                <div class="stat-value">R$ <?php echo $receita_total; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Receita total</div>
                <div class="progress-bar"><div class="progress-fill" style="width: 70%"></div></div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- Cursos por Categoria -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-pie"></i> Cursos por Categoria</h3>
                </div>
                <div class="chart-content">
                    <canvas id="categoriaChart"></canvas>
                </div>
            </div>

            <!-- Cursos por Nível -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar"></i> Cursos por Nível</h3>
                </div>
                <div class="chart-content">
                    <canvas id="nivelChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Relatórios Section -->
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-file-alt"></i> Relatórios Disponíveis</h3>
            </div>
            <div class="reports-panel">
                <div class="reports-grid">
                    <button onclick="gerarRelatorio('cursos')" class="report-action primary">
                        <i class="fas fa-laptop-code"></i>
                        <strong>Relatório de Cursos</strong>
                        <p>Análise completa dos cursos</p>
                    </button>
                    
                    <button onclick="gerarRelatorio('usuarios')" class="report-action success">
                        <i class="fas fa-users"></i>
                        <strong>Relatório de Usuários</strong>
                        <p>Estatísticas dos usuários</p>
                    </button>
                    
                    <button onclick="gerarRelatorio('financeiro')" class="report-action warning">
                        <i class="fas fa-chart-line"></i>
                        <strong>Relatório Financeiro</strong>
                        <p>Análise financeira</p>
                    </button>
                    
                    <button onclick="gerarRelatorio('agendamentos')" class="report-action info">
                        <i class="fas fa-calendar"></i>
                        <strong>Relatório de Agendamentos</strong>
                        <p>Análise de aulas</p>
                    </button>
                </div>
            </div>
        </div>
        </div><!-- /page-content -->
    </div>

    <div id="toast" class="toast"></div>

    <script>
        // Configuração inicial de cores do ChartJS com base no tema ativo
        const isDarkTheme = document.body.classList.contains('dark-mode');
        const textColor = isDarkTheme ? '#cbd5e1' : '#64748b';
        const gridColor = isDarkTheme ? 'rgba(255, 255, 255, 0.1)' : '#e2e8f0';

        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = textColor;
            Chart.defaults.borderColor = gridColor;
        }

        let categoriaChartObj = null;
        let nivelChartObj = null;

        // Gráfico de Cursos por Categoria
        const categoriaCtx = document.getElementById('categoriaChart');
        if (categoriaCtx) {
            categoriaChartObj = new Chart(categoriaCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Programação', 'Design', 'Marketing', 'Negócios', 'Tecnologia'],
                    datasets: [{
                        data: [3, 2, 1, 1, 2],
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor
                            }
                        }
                    }
                }
            });
        }

        // Gráfico de Cursos por Nível
        const nivelCtx = document.getElementById('nivelChart');
        if (nivelCtx) {
            nivelChartObj = new Chart(nivelCtx, {
                type: 'bar',
                data: {
                    labels: ['Iniciante', 'Intermediário', 'Avançado'],
                    datasets: [{
                        label: 'Quantidade de Cursos',
                        data: [2, 4, 1],
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                stepSize: 1,
                                color: textColor
                            }
                        }
                    }
                }
            });
        }

        // Escutar a mudança de tema para atualizar as cores dos gráficos dinamicamente
        window.addEventListener('themeChanged', function() {
            const isDark = document.body.classList.contains('dark-mode');
            const newTextColor = isDark ? '#cbd5e1' : '#64748b';
            const newGridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : '#e2e8f0';

            if (typeof Chart !== 'undefined') {
                Chart.defaults.color = newTextColor;
                Chart.defaults.borderColor = newGridColor;
            }

            if (categoriaChartObj) {
                categoriaChartObj.options.plugins.legend.labels.color = newTextColor;
                categoriaChartObj.update();
            }
            if (nivelChartObj) {
                nivelChartObj.options.scales.x.ticks.color = newTextColor;
                nivelChartObj.options.scales.y.ticks.color = newTextColor;
                nivelChartObj.options.scales.x.grid.color = newGridColor;
                nivelChartObj.options.scales.y.grid.color = newGridColor;
                nivelChartObj.update();
            }
        });

        function gerarRelatorio(tipo) {
            switch(tipo) {
                case 'cursos':
                    window.location.href = 'relatorio_cursos.php';
                    break;
                case 'usuarios':
                    window.location.href = 'relatorio_usuarios.php';
                    break;
                case 'financeiro':
                    window.location.href = 'relatorio_financeiro.php';
                    break;
                case 'agendamentos':
                    window.location.href = 'relatorio_agendamentos.php';
                    break;
                default:
                    showToast('Relatório não encontrado.');
            }
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.display = 'block';

            setTimeout(function() {
                toast.style.display = 'none';
            }, 3200);
        }
    </script>
    <script src="dark-mode.js"></script>
</body>
</html>




























