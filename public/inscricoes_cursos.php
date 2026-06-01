<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$inscricoes_count = 0;
$inscricoes_ativas = 0;
$alunos_count = 0;
$cursos_count = 0;

try {
    // Contar inscrições
    $result = $conn->query("SELECT COUNT(*) as total FROM inscricoes");
    if ($result) {
        $inscricoes_count = $result->fetch_assoc()['total'];
    }

    // Contar inscrições ativas
    $result = $conn->query("SELECT COUNT(*) as total FROM inscricoes WHERE status = 'ativa'");
    if ($result) {
        $inscricoes_ativas = $result->fetch_assoc()['total'];
    }

    // Contar alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
    if ($result) {
        $alunos_count = $result->fetch_assoc()['total'];
    }

    // Contar cursos
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $cursos_count = $result->fetch_assoc()['total'];
    }

    // Buscar inscrições para exibir
    $inscricoes_result = $conn->query("
        SELECT i.id, i.data_inicio, i.observacoes, i.status, i.criado_em,
               c.nome as curso_nome, c.categoria, c.nivel,
               u.nome as aluno_nome, u.email as aluno_email
        FROM inscricoes i
        LEFT JOIN cursos c ON i.curso_id = c.id
        LEFT JOIN usuarios u ON i.aluno_id = u.id
        ORDER BY i.criado_em DESC
    ");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $inscricoes_count = 0;
    $inscricoes_ativas = 0;
    $alunos_count = 0;
    $cursos_count = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Gerenciar Inscrições</title>
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
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }

        .header h1 {
            color: var(--dark-color);
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            color: var(--secondary-color);
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
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
        }

        .stat-card .stat-icon { background: linear-gradient(135deg, #2563eb, #1e40af); }
        .stat-card.success .stat-icon { background: linear-gradient(135deg, #059669, #34d399); }
        .stat-card.info .stat-icon    { background: linear-gradient(135deg, #0891b2, #22d3ee); }
        .stat-card.warning .stat-icon { background: linear-gradient(135deg, #d97706, #fbbf24); }

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
        .progress-fill.info    { background: linear-gradient(90deg, #0891b2, #22d3ee); }
        .progress-fill.warning { background: linear-gradient(90deg, #d97706, #fbbf24); }

        .stat-card.success {
            border-left-color: var(--success-color);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }

        .stat-card.info {
            border-left-color: var(--info-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .actions {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
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

        .inscricoes-grid {
            display: grid;
            gap: 16px;
        }

        .inscricao-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .inscricao-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 16px;
        }

        .inscricao-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-ativa {
            background: #dcfce7;
            color: #166534;
        }

        .status-cancelada {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-concluida {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .inscricao-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 0.875rem;
            color: var(--dark-color);
            font-weight: 500;
        }

        .inscricao-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.75rem;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 32px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .close {
            color: var(--secondary-color);
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: var(--dark-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            margin-bottom: 8px;
            color: var(--dark-color);
        }

        .empty-state p {
            font-size: 0.875rem;
        }

        body { background: #f1f5f9 !important; padding: 0 !important; }

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
            align-items: center;
            text-align: center;
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
        .header p {
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

        .header .btn-back {
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

        .header .btn-back:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff !important;
            transform: translateX(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .header .btn-back i {
            font-size: 12px;
        }

        .stats-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
            margin-bottom: 28px;
        }

        .stat-card,
        .inscricao-card {
            isolation: isolate;
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            border-left: 0 !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .stat-card::before,
        .inscricao-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981);
        }

        .stat-card {
            padding: 30px;
            transition: var(--transition);
        }

        .stat-card:hover,
        .inscricao-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .stat-number {
            color: #0f172a;
            font-size: 2.45rem;
            font-weight: 850;
            letter-spacing: -0.06em;
        }

        .stat-label {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .actions {
            padding: 22px;
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(255, 255, 255, 0.82);
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09);
            backdrop-filter: blur(20px);
        }

        .btn {
            min-height: 40px;
            border-radius: 999px !important;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        }

        .inscricoes-grid {
            gap: 24px;
        }

        .inscricao-card {
            padding: 28px;
            transition: var(--transition);
        }

        .inscricao-header {
            justify-content: space-between;
            gap: 16px;
        }

        .inscricao-title {
            color: #0f172a;
            font-size: 1.2rem;
            font-weight: 850;
            letter-spacing: -0.045em;
        }

        .inscricao-details {
            padding: 18px;
            border-radius: 20px;
            background: rgba(248, 250, 252, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.75);
        }

        .detail-label {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .detail-value {
            color: #0f172a;
            font-weight: 700;
        }

        .status-badge {
            min-height: 30px;
            display: inline-flex;
            align-items: center;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 850;
            letter-spacing: 0.05em;
        }

        .status-ativa {
            color: #065f46;
            background: #d1fae5;
            border: 1px solid #a7f3d0;
        }

        .status-cancelada {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }

            .header {
                min-height: 230px;
                padding: 54px 22px 42px !important;
                border-radius: 24px !important;
            }

            .actions {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .inscricao-actions {
                flex-direction: column;
            }

            .inscricao-details {
                grid-template-columns: 1fr;
            }
        }
        /* ===== Dark Mode - Inscrições em Cursos ===== */
        html.dark-mode body,
        body.dark-mode {
            background: #0f172a !important;
            color: #f8fafc !important;
        }

        .dark-mode .stat-card,
        .dark-mode .inscricao-card {
            background: rgba(30, 41, 59, 0.95) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .stat-number {
            color: #f8fafc !important;
        }

        .dark-mode .stat-label {
            color: #94a3b8 !important;
        }

        .dark-mode .actions {
            background: rgba(30, 41, 59, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .inscricao-title {
            color: #f8fafc !important;
        }

        .dark-mode .inscricao-details {
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .detail-label {
            color: #94a3b8 !important;
        }

        .dark-mode .detail-value {
            color: #e2e8f0 !important;
        }

        .dark-mode .status-ativa {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #6ee7b7 !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        .dark-mode .status-cancelada {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #fca5a5 !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }

        .dark-mode .empty-state {
            color: #94a3b8 !important;
        }

        .dark-mode .empty-state h3 {
            color: #cbd5e1 !important;
        }

        .dark-mode .modal-content {
            background: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .dark-mode .modal-title,
        .dark-mode .form-label {
            color: #f8fafc !important;
        }

        .dark-mode .form-control {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }

        .dark-mode .close {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="dashboard_final.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
            <button id="darkModeToggle" title="Alternar tema" style="position: absolute; top: 20px; right: 20px; width: 42px; height: 42px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; backdrop-filter: blur(16px); z-index: 100; transition: all 0.3s ease;">
                <i class="fas fa-moon"></i>
            </button>
            <h1><i class="fas fa-graduation-cap"></i> Inscrições em Cursos</h1>
            <p>Gerencie as inscrições dos alunos nos cursos do sistema</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Total de Inscrições</h3>
                    <div class="stat-icon"><i class="fas fa-list-alt"></i></div>
                </div>
                <div class="stat-number"><?php echo $inscricoes_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Total no sistema</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($inscricoes_count / 50) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Inscrições Ativas</h3>
                    <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                </div>
                <div class="stat-number"><?php echo $inscricoes_ativas; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Matrículas ativas</div>
                <div class="progress-bar"><div class="progress-fill success" style="width: <?php echo $inscricoes_count > 0 ? min(($inscricoes_ativas / $inscricoes_count) * 100, 100) : 0; ?>%"></div></div>
            </div>
            <div class="stat-card info">
                <div class="stat-header">
                    <h3 class="stat-title">Alunos Disponíveis</h3>
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                </div>
                <div class="stat-number"><?php echo $alunos_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Alunos cadastrados</div>
                <div class="progress-bar"><div class="progress-fill info" style="width: <?php echo min(($alunos_count / 30) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Cursos Disponíveis</h3>
                    <div class="stat-icon"><i class="fas fa-laptop-code"></i></div>
                </div>
                <div class="stat-number"><?php echo $cursos_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Cursos ativos</div>
                <div class="progress-bar"><div class="progress-fill warning" style="width: <?php echo min(($cursos_count / 20) * 100, 100); ?>%"></div></div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Nova Inscrição
            </button>
            <a href="dashboard_final.php" class="btn btn-success">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>

        <!-- Inscrições List -->
        <div class="inscricoes-grid">
            <?php if ($inscricoes_result && $inscricoes_result->num_rows > 0): ?>
                <?php while ($inscricao = $inscricoes_result->fetch_assoc()): ?>
                    <div class="inscricao-card">
                        <div class="inscricao-header">
                            <div class="inscricao-title">
                                <?php echo htmlspecialchars($inscricao['aluno_nome']); ?> → 
                                <?php echo htmlspecialchars($inscricao['curso_nome']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $inscricao['status']; ?>">
                                <?php echo ucfirst($inscricao['status']); ?>
                            </span>
                        </div>
                        
                        <div class="inscricao-details">
                            <div class="detail-item">
                                <span class="detail-label">Aluno</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inscricao['aluno_nome']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inscricao['aluno_email']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Curso</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inscricao['curso_nome']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Categoria</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inscricao['categoria']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Nível</span>
                                <span class="detail-value"><?php echo htmlspecialchars($inscricao['nivel']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Data de Início</span>
                                <span class="detail-value">
                                    <?php echo $inscricao['data_inicio'] ? date('d/m/Y', strtotime($inscricao['data_inicio'])) : 'Não definida'; ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Data de Inscrição</span>
                                <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($inscricao['criado_em'])); ?></span>
                            </div>
                            <?php if ($inscricao['observacoes']): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Observações</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($inscricao['observacoes']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="inscricao-actions">
                            <button class="btn btn-danger btn-sm" onclick="removerInscricao(<?php echo $inscricao['id']; ?>)">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Nenhuma inscrição encontrada</h3>
                    <p>Comece criando a primeira inscrição de um aluno em um curso.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Nova Inscrição -->
    <div id="modalInscricao" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nova Inscrição</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form id="formInscricao">
                <div class="form-group">
                    <label class="form-label">Aluno *</label>
                    <select class="form-control" id="aluno_id" name="aluno_id" required>
                        <option value="">Selecione um aluno</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Curso *</label>
                    <select class="form-control" id="curso_id" name="curso_id" required>
                        <option value="">Selecione um curso</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Data de Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Observações sobre a inscrição..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Inscrição</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Carregar dados quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            carregarAlunos();
            carregarCursos();
        });

        // Modal functions
        function openModal() {
            document.getElementById('modalInscricao').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalInscricao').style.display = 'none';
            document.getElementById('formInscricao').reset();
        }

        // Carregar alunos
        function carregarAlunos() {
            fetch('api/usuarios.php?tipo=aluno')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('aluno_id');
                    select.innerHTML = '<option value="">Selecione um aluno</option>';
                    
                    if (data.success && data.data) {
                        data.data.forEach(aluno => {
                            const option = document.createElement('option');
                            option.value = aluno.id;
                            option.textContent = aluno.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar alunos:', error);
                });
        }

        // Carregar cursos
        function carregarCursos() {
            fetch('api/cursos_simples.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('curso_id');
                    select.innerHTML = '<option value="">Selecione um curso</option>';
                    
                    if (data.success && data.data) {
                        data.data.forEach(curso => {
                            const option = document.createElement('option');
                            option.value = curso.id;
                            option.textContent = curso.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar cursos:', error);
                });
        }

        // Criar inscrição
        document.getElementById('formInscricao').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                aluno_id: formData.get('aluno_id'),
                curso_id: formData.get('curso_id'),
                data_inicio: formData.get('data_inicio'),
                observacoes: formData.get('observacoes')
            };

            fetch('api/inscricoes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Inscrição criada com sucesso!');
                    closeModal();
                    location.reload();
                } else {
                    alert('Erro ao criar inscrição: ' + (result.error || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao criar inscrição');
            });
        });

        // Remover inscrição
        function removerInscricao(id) {
            if (confirm('Tem certeza que deseja remover esta inscrição?')) {
                fetch(`api/inscricoes.php?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Inscrição removida com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro ao remover inscrição: ' + (result.error || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao remover inscrição');
                });
            }
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('modalInscricao');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
    <script src="dark-mode.js"></script>
    <script>
        // Tornar o body visível após o carregamento (previne FOUC)
        document.body.style.visibility = 'visible';
    </script>
</body>
</html>







