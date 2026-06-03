<?php
header('Content-Type: text/html; charset=UTF-8');
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Adicionar timestamp para forçar atualização
$timestamp = time();

// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$professores_count = 0;
$professores_ativos = 0;
$cursos_ministrados = 0;

try {
    // Contar professores
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
    if ($result) {
        $professores_count = $result->fetch_assoc()['total'];
    }

    // Contar professores ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
    if ($result) {
        $professores_ativos = $result->fetch_assoc()['total'];
    }

    // Contar cursos ministrados (simulado)
    $cursos_ministrados = $professores_ativos * 2; // Simulado

    // Buscar professores para exibir (consulta simplificada)
    $professores_result = $conn->query("
        SELECT u.id, u.nome, u.email, u.ativo, u.criado_em,
               0 as agendamentos_count
        FROM usuarios u
        WHERE u.tipo_usuario = 'professor'
        ORDER BY u.nome
    ");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $professores_count = 0;
    $professores_ativos = 0;
    $cursos_ministrados = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Professores</title>
    <script>
        // Script de bloqueio para evitar o flash de luz (FOUC)
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
            // Aplicar estilos mínimos imediatamente vinculados à classe de inicialização
            document.write('<style>.dark-mode-init body { visibility: hidden; background: #0f172a !important; }</style>');
        }
    </script>
    <link rel="stylesheet" href="dark-mode.css?v=3">
    
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

        .header .back-btn {
            position: absolute !important;
            top: 20px;
            left: 20px;
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
            width: auto !important;
            max-width: max-content;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.24);
            z-index: 10;
            white-space: nowrap;
        }

        .header .back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff !important;
            transform: translateX(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .header .back-btn i {
            font-size: 12px;
        }
        
        .header h1 {
            color: #ffffff !important;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .header p {
            color: #ffffff !important;
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
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
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

        .professores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            padding: 32px;
        }

        .professor-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            padding: 24px;
            transition: var(--transition);
            position: relative;
        }

        .professor-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--primary-color);
        }

        .professor-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .professor-avatar {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .professor-info h4 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
        }

        .professor-email {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .professor-stats {
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

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(16, 185, 129, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(59, 130, 246, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #ecfdf5 100%) !important;
        }

        .container {
            max-width: 1380px;
            padding: 24px;
        }

        .header {
            position: relative;
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
            margin-left: auto;
            margin-right: auto;
            color: rgba(255, 255, 255, 0.82) !important;
            line-height: 1.65;
        }

        .header .back-btn {
            position: absolute;
            top: 24px;
            left: 24px;
            margin: 0;
            padding: 10px 18px;
            width: auto;
            max-width: max-content;
            flex: none;
            align-self: auto;
            white-space: nowrap;
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.24) !important;
            border-radius: 999px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(16px);
            z-index: 10;
        }

        .header .back-btn:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.25) !important;
            transform: translateX(-4px);
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
            background: linear-gradient(90deg, #2563eb, #10b981) !important;
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
            background: linear-gradient(135deg, rgba(236, 253, 245, 0.96), rgba(239, 246, 255, 0.96)) !important;
        }

        .section-header h3 {
            color: #0f172a;
            font-size: 1.18rem;
            font-weight: 850;
            letter-spacing: -0.035em;
        }

        .section-header h3 i {
            color: #059669;
        }

        .professores-grid {
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 26px;
            padding: 32px;
        }

        .professor-card {
            isolation: isolate;
            overflow: hidden;
            padding: 28px;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.94) !important;
            border: 1px solid rgba(226, 232, 240, 0.82) !important;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08) !important;
        }

        .professor-card::before {
            height: 6px;
            background: linear-gradient(90deg, #059669, #10b981, #2563eb) !important;
        }

        .professor-card::after { content: none; }

        .professor-card:hover {
            transform: translateY(-9px);
            border-color: rgba(5, 150, 105, 0.35) !important;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .professor-header {
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 20px;
        }

        .professor-avatar {
            width: 66px;
            height: 66px;
            flex: 0 0 66px;
            border-radius: 22px;
            background: linear-gradient(135deg, #059669, #2563eb) !important;
            box-shadow: 0 18px 38px rgba(5, 150, 105, 0.22);
        }

        .professor-info h4 {
            color: #0f172a;
            font-size: 1.22rem;
            font-weight: 850;
            letter-spacing: -0.045em;
        }

        .professor-email {
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

        .status-ativo {
            color: #065f46;
            background: #d1fae5;
            border-color: #a7f3d0;
        }

        .status-inativo {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }

        .professor-stats {
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .professor-stats .stat-item {
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.75);
            color: #475569;
            font-weight: 650;
        }

        .professor-stats .stat-item i {
            color: #059669;
        }

        .professor-card > div:last-child {
            flex-wrap: wrap;
        }

        .btn {
            min-height: 40px;
            border-radius: 999px !important;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #059669, #2563eb) !important;
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
            background: #059669;
            border-color: #059669;
        }

        .empty-state {
            padding: 58px 24px;
        }

        .empty-state i {
            color: #10b981;
            opacity: 0.75;
        }

        .teacher-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2500;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(10px);
        }

        .teacher-modal {
            width: min(440px, 100%);
            overflow: hidden;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 34px 95px rgba(15, 23, 42, 0.32);
        }

        .teacher-modal-header {
            padding: 20px 24px;
            color: #ffffff;
            background: linear-gradient(135deg, #064e3b, #2563eb, #10b981);
        }

        .teacher-modal-title {
            font-size: 1.08rem;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .teacher-modal-subtitle {
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.92rem;
        }

        .teacher-modal-body {
            padding: 20px 24px 24px;
        }

        .teacher-form-group {
            margin-bottom: 16px;
        }

        .teacher-form-group label {
            display: block;
            margin-bottom: 7px;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 850;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .teacher-form-group input {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(203, 213, 225, 0.95);
        }

        .teacher-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 22px;
        }

        .teacher-toast {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 3000;
            min-width: 300px;
            max-width: 440px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.86);
            border-left: 6px solid #3b82f6;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
            color: #0f172a;
            font-weight: 750;
            backdrop-filter: blur(18px);
        }

        .teacher-toast.success {
            border-left-color: #10b981;
        }

        .teacher-toast.error {
            border-left-color: #ef4444;
        }

        .teacher-detail-list {
            display: grid;
            gap: 8px;
        }

        .teacher-detail-item {
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.88);
            border: 1px solid rgba(226, 232, 240, 0.78);
        }

        .teacher-detail-label {
            display: block;
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 850;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .teacher-detail-value {
            display: block;
            margin-top: 2px;
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 750;
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

            .header .back-btn {
                top: 18px;
                left: 18px;
            }

            .professores-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }
        }
    
        /* ===== Modo escuro — Gestão de Professores ===== */
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
            color: #6ee7b7 !important;
        }

        .dark-mode .professor-card {
            background: rgba(30, 41, 59, 0.95) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .professor-info h4 {
            color: #f8fafc !important;
        }

        .dark-mode .professor-email {
            color: #94a3b8 !important;
        }

        .dark-mode .professor-stats .stat-item {
            background: rgba(15, 23, 42, 0.7) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #cbd5e1 !important;
        }

        .dark-mode .professor-stats .stat-item i {
            color: #6ee7b7 !important;
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

        .dark-mode .teacher-modal {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .teacher-modal-body {
            color: #f8fafc !important;
        }

        .dark-mode .teacher-form-group label,
        .dark-mode .teacher-detail-label {
            color: #94a3b8 !important;
        }

        .dark-mode .teacher-form-group input,
        .dark-mode .teacher-form-group select {
            background: rgba(15, 23, 42, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }

        .dark-mode .teacher-detail-item {
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .teacher-detail-value {
            color: #f8fafc !important;
        }

        .dark-mode .teacher-toast {
            background: #1e293b !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
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

        /* === Padronizar cores dos stat-cards === */
        .stat-card.primary::before,
        .stat-card.success::before,
        .stat-card.warning::before,
        .stat-card.info::before {
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
        }

        /* === Remover verde - padronizar em azul === */
        .professor-avatar {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            box-shadow: 0 18px 38px rgba(37, 99, 235, 0.22) !important;
        }
        .professor-card::before {
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
        }
        .professor-card::after { content: none !important; }
        .professor-card:hover { border-color: rgba(37, 99, 235, 0.3) !important; }
        .professor-stats .stat-item i { color: #2563eb !important; }
        .section-header {
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.96), rgba(238, 242, 255, 0.96)) !important;
        }
        .section-header h3 i { color: #2563eb !important; }
        .btn-primary { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }
        .btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }
        .btn-outline:hover { background: #2563eb !important; border-color: #2563eb !important; color: #ffffff !important; }

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
            align-items: center !important;
            z-index: 10 !important;
        }
        .header .back-btn {
            position: relative !important;
            top: auto !important;
            left: auto !important;
        }
        .header > #darkModeToggle {
            position: absolute !important;
            top: 20px !important;
            right: 24px !important;
            z-index: 100 !important;
        }
        .header-content {
            display: block !important;
            text-align: center !important;
            padding: 0 !important;
        }

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
            </div>
            <button id="darkModeToggle" title="Alternar tema" aria-label="Alternar Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
            <div class="header-content">
                <h1>Gestão de Professores</h1>
                <p>Gerencie o corpo docente e suas atribuições no sistema</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <h3 class="stat-title">Total de Professores</h3>
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $professores_count; ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up stat-change-icon"></i>
                    Total no sistema
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo min(($professores_count / 15) * 100, 100); ?>%"></div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Professores Ativos</h3>
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $professores_ativos; ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up stat-change-icon"></i>
                    Professores ativos
                </div>
                <div class="progress-bar">
                    <div class="progress-fill success" style="width: <?php echo min(($professores_ativos / 15) * 100, 100); ?>%"></div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Cursos Ministrados</h3>
                    <div class="stat-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $cursos_ministrados; ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up stat-change-icon"></i>
                    Cursos atribuídos
                </div>
                <div class="progress-bar">
                    <div class="progress-fill warning" style="width: <?php echo min(($cursos_ministrados / 20) * 100, 100); ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Professores Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-users"></i> Lista de Professores</h3>
                <button class="btn btn-primary" onclick="adicionarProfessor()">
                    <i class="fas fa-plus"></i> Novo Professor
                </button>
            </div>
            
            <?php if ($professores_result && $professores_result->num_rows > 0): ?>
                <div class="professores-grid">
                    <?php while ($professor = $professores_result->fetch_assoc()): ?>
                        <div class="professor-card">
                            <div class="professor-header">
                                <div class="professor-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="professor-info">
                                    <h4><?php echo htmlspecialchars($professor['nome']); ?></h4>
                                    <div class="professor-email"><?php echo htmlspecialchars($professor['email']); ?></div>
                                </div>
                            </div>
                            
                            <div class="status-badge status-<?php echo $professor['ativo'] ? 'ativo' : 'inativo'; ?>">
                                <?php echo $professor['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </div>
                            
                            <div class="professor-stats">
                                <div class="stat-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span><?php echo $professor['agendamentos_count']; ?> aulas</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo date('d/m/Y', strtotime($professor['criado_em'])); ?></span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-outline btn-sm" onclick="editarProfessor(<?php echo $professor['id']; ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="verDetalhes(<?php echo $professor['id']; ?>)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="toggleStatus(<?php echo $professor['id']; ?>, <?php echo $professor['ativo']; ?>)">
                                    <i class="fas fa-<?php echo $professor['ativo'] ? 'pause' : 'play'; ?>"></i>
                                    <?php echo $professor['ativo'] ? 'Pausar' : 'Ativar'; ?>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h4>Nenhum professor disponível</h4>
                    <p>Comece adicionando o primeiro professor do sistema</p>
                    <button class="btn btn-primary" onclick="adicionarProfessor()">
                        <i class="fas fa-plus"></i> Primeiro Professor
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Atribuições Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-tasks"></i> Atribuições de Cursos</h3>
                <button class="btn btn-success" onclick="gerenciarAtribuicoes()">
                    <i class="fas fa-cog"></i> Gerenciar
                </button>
            </div>
            
            <div class="empty-state">
                <i class="fas fa-tasks"></i>
                <h4>Sistema de Atribuições</h4>
                <p>Gerencie quais cursos cada professor ministra</p>
                <a href="atribuicoes_cursos.php" class="btn btn-success">
                    <i class="fas fa-cog"></i> Gerenciar Atribuições
                </a>
            </div>
        </div>
    </div>

    <div id="teacherModalOverlay" class="teacher-modal-overlay">
        <div class="teacher-modal">
            <div class="teacher-modal-header">
                <div class="teacher-modal-title" id="teacherModalTitle">Professores</div>
                <div class="teacher-modal-subtitle" id="teacherModalSubtitle">EduConnect</div>
            </div>
            <div class="teacher-modal-body" id="teacherModalBody"></div>
        </div>
    </div>

    <script>
        // Função para adicionar professor
        async function adicionarProfessor() {
            openTeacherFormModal({
                title: 'Novo Professor',
                subtitle: 'Cadastre um novo docente',
                submitText: 'Adicionar professor',
                onSubmit: async function(data) {
                    try {
                        const response = await fetch('api/professores_simples.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                nome: data.nome,
                                email: data.email,
                                senha: 'senha123'
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            closeTeacherModal();
                            showTeacherToast('Professor adicionado com sucesso!', 'success');
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            showTeacherToast('Erro ao adicionar professor: ' + result.error, 'error');
                        }
                    } catch (error) {
                        showTeacherToast('Erro de conexão: ' + error.message, 'error');
                    }
                }
            });
        }

        // Função para editar professor
        async function editarProfessor(id) {
            openTeacherFormModal({
                title: 'Editar Professor',
                subtitle: 'Atualize os dados do docente',
                submitText: 'Salvar alterações',
                onSubmit: async function(data) {
                    try {
                        const response = await fetch(`api/professores_simples.php?id=${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                nome: data.nome,
                                email: data.email
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            closeTeacherModal();
                            showTeacherToast('Professor editado com sucesso!', 'success');
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            showTeacherToast('Erro ao editar professor: ' + result.error, 'error');
                        }
                    } catch (error) {
                        showTeacherToast('Erro de conexão: ' + error.message, 'error');
                    }
                }
            });
        }

        // Função para ver detalhes do professor
        async function verDetalhes(id) {
            try {
                const response = await fetch(`api/professores_simples.php?id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    const prof = result.data;
                    openTeacherDetailsModal(prof);
                } else {
                    showTeacherToast('Erro ao carregar detalhes: ' + result.error, 'error');
                }
            } catch (error) {
                showTeacherToast('Erro de conexão: ' + error.message, 'error');
            }
        }

        // Função para pausar/ativar professor
        async function toggleStatus(id, ativo) {
            const acao = ativo ? 'pausar' : 'ativar';
            openTeacherConfirmModal({
                title: `${ativo ? 'Pausar' : 'Ativar'} professor?`,
                subtitle: 'Confirme a alteração de status',
                message: `Tem certeza que deseja ${acao} este professor?`,
                actionText: ativo ? 'Sim, pausar' : 'Sim, ativar',
                onConfirm: async function() {
                try {
                    const response = await fetch(`api/professores_simples.php?id=${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ativo: !ativo
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        closeTeacherModal();
                        showTeacherToast(`Professor ${acao}do com sucesso!`, 'success');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showTeacherToast('Erro ao alterar status: ' + result.error, 'error');
                    }
                } catch (error) {
                    showTeacherToast('Erro de conexão: ' + error.message, 'error');
                }
                }
            });
        }

        function gerenciarAtribuicoes() {
            window.location.href = 'atribuicoes_cursos.php';
        }

        function openTeacherFormModal(config) {
            document.getElementById('teacherModalTitle').textContent = config.title;
            document.getElementById('teacherModalSubtitle').textContent = config.subtitle;
            document.getElementById('teacherModalBody').innerHTML = `
                <form id="teacherActionForm">
                    <div class="teacher-form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" required placeholder="Digite o nome do professor">
                    </div>
                    <div class="teacher-form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="Digite o email do professor">
                    </div>
                    <div class="teacher-modal-actions">
                        <button type="button" class="btn btn-outline" onclick="closeTeacherModal()">Cancelar</button>
                        <button type="submit" class="btn btn-success">${config.submitText}</button>
                    </div>
                </form>
            `;
            document.getElementById('teacherActionForm').onsubmit = async function(event) {
                event.preventDefault();
                const formData = new FormData(event.target);
                await config.onSubmit({
                    nome: formData.get('nome'),
                    email: formData.get('email')
                });
            };
            document.getElementById('teacherModalOverlay').style.display = 'flex';
        }

        function openTeacherDetailsModal(prof) {
            document.getElementById('teacherModalTitle').textContent = 'Detalhes do professor';
            document.getElementById('teacherModalSubtitle').textContent = prof.nome;
            document.getElementById('teacherModalBody').innerHTML = `
                <div class="teacher-detail-list">
                    <div class="teacher-detail-item"><span class="teacher-detail-label">Nome</span><span class="teacher-detail-value">${prof.nome}</span></div>
                    <div class="teacher-detail-item"><span class="teacher-detail-label">Email</span><span class="teacher-detail-value">${prof.email}</span></div>
                    <div class="teacher-detail-item"><span class="teacher-detail-label">Status</span><span class="teacher-detail-value">${prof.ativo ? 'Ativo' : 'Inativo'}</span></div>
                    <div class="teacher-detail-item"><span class="teacher-detail-label">Data de cadastro</span><span class="teacher-detail-value">${prof.data_cadastro || 'Não informado'}</span></div>
                    <div class="teacher-detail-item"><span class="teacher-detail-label">Total de aulas</span><span class="teacher-detail-value">${prof.agendamentos_count || 0}</span></div>
                </div>
                <div class="teacher-modal-actions">
                    <button type="button" class="btn btn-primary" onclick="closeTeacherModal()">Fechar</button>
                </div>
            `;
            document.getElementById('teacherModalOverlay').style.display = 'flex';
        }

        function openTeacherConfirmModal(config) {
            document.getElementById('teacherModalTitle').textContent = config.title;
            document.getElementById('teacherModalSubtitle').textContent = config.subtitle;
            document.getElementById('teacherModalBody').innerHTML = `
                <p style="color: #475569; line-height: 1.65;">${config.message}</p>
                <div class="teacher-modal-actions">
                    <button type="button" class="btn btn-outline" onclick="closeTeacherModal()">Cancelar</button>
                    <button type="button" class="btn btn-success" id="teacherConfirmButton">${config.actionText}</button>
                </div>
            `;
            document.getElementById('teacherConfirmButton').onclick = config.onConfirm;
            document.getElementById('teacherModalOverlay').style.display = 'flex';
        }

        function closeTeacherModal() {
            document.getElementById('teacherModalOverlay').style.display = 'none';
        }

        function showTeacherToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `teacher-toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(function() {
                toast.remove();
            }, 3600);
        }
    </script>
    <script src="sidebar.js"></script>
    <script src="dark-mode.js"></script>
</body>
</html>


