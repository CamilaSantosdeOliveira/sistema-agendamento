<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar estatísticas dos agendamentos
$total_agendamentos = 0;
$agendamentos_hoje = 0;
$agendamentos_semana = 0;
$agendamentos_mes = 0;
$agendamentos_por_status = [];
$agendamentos_por_professor = [];
$agendamentos_por_curso = [];

try {
    // Estatísticas gerais
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
    if ($result) {
        $total_agendamentos = $result->fetch_assoc()['total'];
    }

    $result = $conn->query("
        SELECT COUNT(*) as total 
        FROM agendamentos 
        WHERE DATE(data_agendamento) = CURDATE()
    ");
    if ($result) {
        $agendamentos_hoje = $result->fetch_assoc()['total'];
    }

    $result = $conn->query("
        SELECT COUNT(*) as total 
        FROM agendamentos 
        WHERE data_agendamento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ");
    if ($result) {
        $agendamentos_semana = $result->fetch_assoc()['total'];
    }

    $result = $conn->query("
        SELECT COUNT(*) as total 
        FROM agendamentos 
        WHERE MONTH(data_agendamento) = MONTH(CURRENT_DATE())
        AND YEAR(data_agendamento) = YEAR(CURRENT_DATE())
    ");
    if ($result) {
        $agendamentos_mes = $result->fetch_assoc()['total'];
    }

    // Agendamentos por status
    $result = $conn->query("
        SELECT status, COUNT(*) as total 
        FROM agendamentos 
        GROUP BY status 
        ORDER BY total DESC
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $agendamentos_por_status[] = $row;
        }
    }

    // Agendamentos por professor
    $result = $conn->query("
        SELECT 
            p.nome as professor_nome,
            COUNT(a.id) as total_agendamentos
        FROM agendamentos a
        JOIN usuarios p ON a.professor_id = p.id
        GROUP BY a.professor_id
        ORDER BY total_agendamentos DESC
        LIMIT 5
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $agendamentos_por_professor[] = $row;
        }
    }

    // Agendamentos por curso
    $result = $conn->query("
        SELECT 
            c.nome as curso_nome,
            c.categoria,
            COUNT(a.id) as total_agendamentos
        FROM agendamentos a
        JOIN cursos c ON a.curso_id = c.id
        GROUP BY a.curso_id
        ORDER BY total_agendamentos DESC
        LIMIT 5
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $agendamentos_por_curso[] = $row;
        }
    }

    // Lista de agendamentos com detalhes
    $agendamentos_result = $conn->query("
        SELECT 
            a.id,
            a.data_agendamento,
            a.hora_inicio,
            a.hora_fim,
            a.status,
            a.observacoes,
            a.criado_em,
            p.nome as professor_nome,
            al.nome as aluno_nome,
            c.nome as curso_nome,
            c.categoria
        FROM agendamentos a
        JOIN usuarios p ON a.professor_id = p.id
        JOIN usuarios al ON a.aluno_id = al.id
        JOIN cursos c ON a.curso_id = c.id
        ORDER BY a.data_agendamento DESC, a.hora_inicio DESC
    ");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
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
    <title>EduConnect - Relatório de Agendamentos</title>
    
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
            background: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .header p {
            color: var(--secondary-color);
            font-size: 1.125rem;
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
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .stat-card.success {
            border-left: 4px solid var(--success-color);
        }

        .stat-card.warning {
            border-left: 4px solid var(--warning-color);
        }

        .stat-card.info {
            border-left: 4px solid var(--info-color);
        }

        .stat-card.danger {
            border-left: 4px solid var(--danger-color);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--secondary-color);
            font-weight: 500;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-card {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 16px;
        }

        .chart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .chart-item:last-child {
            border-bottom: none;
        }

        .chart-label {
            font-weight: 500;
            color: var(--dark-color);
        }

        .chart-value {
            font-weight: 600;
            color: var(--primary-color);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            text-decoration: none;
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

        .table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .table-header {
            background: var(--light-color);
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
        }

        tr:hover {
            background: #f8fafc;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-agendado {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-confirmado {
            background: #dcfce7;
            color: #166534;
        }

        .status-cancelado {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-concluido {
            background: #fef3c7;
            color: #92400e;
        }

        .categoria-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            background: #f3e8ff;
            color: #7c3aed;
        }

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(124, 58, 237, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(8, 145, 178, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #f5f3ff 100%) !important;
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
            border-radius: 30px;
            margin-bottom: 24px;
            padding: 52px 40px;
            color: #ffffff;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
                radial-gradient(circle at 84% 26%, rgba(8, 145, 178, 0.25), transparent 34%),
                linear-gradient(135deg, #312e81 0%, #7c3aed 48%, #0891b2 100%);
            border: 1px solid rgba(255, 255, 255, 0.42);
            box-shadow: 0 28px 80px rgba(124, 58, 237, 0.18);
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
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            z-index: 100;
        }
        .back-btn:hover {
            color: #ffffff !important;
            transform: translateX(-4px);
            background: rgba(255, 255, 255, 0.25) !important;
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .header h1, 
        .dark-mode .header p,
        .dark-mode h2,
        .dark-mode h3,
        .dark-mode .table-title,
        .dark-mode .chart-title {
            color: #ffffff !important;
        }

        .dark-mode .stat-card,
        .dark-mode .chart-card,
        .dark-mode .table-container {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 22px 58px rgba(0, 0, 0, 0.25) !important;
        }

        .dark-mode .stat-number,
        .dark-mode td,
        .dark-mode th,
        .dark-mode .chart-label {
            color: #ffffff !important;
        }

        /* Badges e contrastes no modo escuro */
        .dark-mode .status-badge {
            box-shadow: none !important;
        }

        .dark-mode .status-ativo,
        .dark-mode .status-confirmado,
        .dark-mode .status-concluido,
        .dark-mode .status-active,
        .dark-mode .status-completed,
        .dark-mode .status-concluida {
            background: rgba(16, 185, 129, 0.25) !important;
            color: #34d399 !important;
            font-weight: 700 !important;
        }

        .dark-mode .status-inativo,
        .dark-mode .status-cancelado,
        .dark-mode .status-cancelada {
            background: rgba(239, 68, 68, 0.25) !important;
            color: #f87171 !important;
            font-weight: 700 !important;
        }

        .dark-mode .status-agendado,
        .dark-mode .status-pending {
            background: rgba(245, 158, 11, 0.25) !important;
            color: #fbbf24 !important;
            font-weight: 700 !important;
        }

        .dark-mode .categoria-badge {
            background: rgba(124, 58, 237, 0.25) !important;
            color: #c084fc !important;
            font-weight: 700 !important;
        }

        .dark-mode .stat-label {
            color: #cbd5e1 !important;
        }

        .dark-mode .table-header {
            background: rgba(255, 255, 255, 0.05) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode th {
            background: rgba(255, 255, 255, 0.02) !important;
        }

        .dark-mode tr:hover {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .dark-mode .chart-item {
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        }

        .header::after {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.14;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
            background-size: 42px 42px;
            pointer-events: none;
        }

        .header h1,
        .header p,
        .header .actions {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            color: #ffffff;
            font-size: clamp(1.85rem, 3.6vw, 2.75rem);
            font-weight: 850;
            letter-spacing: -0.055em;
        }

        .header p {
            max-width: 720px;
            color: rgba(255, 255, 255, 0.82);
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .header .actions {
            justify-content: flex-start;
            margin-top: 26px;
            margin-bottom: 0;
        }

        .btn {
            min-height: 42px;
            border-radius: 999px;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .header .btn {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.24);
            backdrop-filter: blur(16px);
        }

        .header .btn:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.24);
        }

        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #0891b2);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .stat-card,
        .chart-card,
        .table-container {
            isolation: isolate;
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.84);
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09);
            backdrop-filter: blur(20px);
        }

        .stat-card::before,
        .chart-card::before,
        .table-container::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            background: linear-gradient(90deg, #7c3aed, #0891b2, #10b981);
        }

        .stat-card {
            padding: 30px;
            border-left: 0 !important;
            text-align: left;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            color: #ffffff;
            border-radius: 18px;
            background: linear-gradient(135deg, #7c3aed, #0891b2);
            box-shadow: 0 16px 34px rgba(124, 58, 237, 0.22);
            font-size: 1.18rem;
        }

        .stat-number {
            color: #0f172a;
            font-size: 2.35rem;
            font-weight: 850;
            letter-spacing: -0.06em;
        }

        .stat-label {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.78rem;
        }

        .chart-title,
        .table-title {
            color: #0f172a;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .table-header {
            padding: 22px 26px;
            background: linear-gradient(135deg, rgba(245, 243, 255, 0.98), rgba(236, 254, 255, 0.96));
        }

        th {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            font-size: 0.76rem;
        }

        td {
            color: #334155;
        }

        tr:hover {
            background: rgba(245, 243, 255, 0.58);
        }

        .status-badge,
        .categoria-badge {
            border-radius: 999px;
            font-weight: 850;
        }

        @media print {
            body {
                background: #ffffff !important;
            }

            .actions {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="dashboard_final.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
            <button id="darkModeToggle" title="Alternar tema" aria-label="Alternar Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
            <h1><i class="fas fa-calendar-alt"></i> Relatório de Agendamentos</h1>
            <p>Análise completa dos agendamentos e aulas</p>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir Relatório
            </button>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-number"><?php echo $total_agendamentos; ?></div>
                <div class="stat-label">Total de Agendamentos</div>
            </div>
            <div class="stat-card info">
                <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-number"><?php echo $agendamentos_hoje; ?></div>
                <div class="stat-label">Agendamentos Hoje</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                <div class="stat-number"><?php echo $agendamentos_semana; ?></div>
                <div class="stat-label">Esta Semana</div>
            </div>
            <div class="stat-card info">
                <div class="stat-icon"><i class="fas fa-calendar-days"></i></div>
                <div class="stat-number"><?php echo $agendamentos_mes; ?></div>
                <div class="stat-label">Este Mês</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="chart-title">Agendamentos por Status</h3>
                <?php if (!empty($agendamentos_por_status)): ?>
                    <?php foreach ($agendamentos_por_status as $status): ?>
                        <div class="chart-item">
                            <span class="chart-label"><?php echo ucfirst(htmlspecialchars($status['status'])); ?></span>
                            <span class="chart-value"><?php echo $status['total']; ?> agendamentos</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--secondary-color); text-align: center; padding: 20px;">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>

            <div class="chart-card">
                <h3 class="chart-title">Professores Mais Ativos</h3>
                <?php if (!empty($agendamentos_por_professor)): ?>
                    <?php foreach ($agendamentos_por_professor as $professor): ?>
                        <div class="chart-item">
                            <span class="chart-label"><?php echo htmlspecialchars($professor['professor_nome']); ?></span>
                            <span class="chart-value"><?php echo $professor['total_agendamentos']; ?> aulas</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--secondary-color); text-align: center; padding: 20px;">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="chart-title">Cursos Mais Agendados</h3>
                <?php if (!empty($agendamentos_por_curso)): ?>
                    <?php foreach ($agendamentos_por_curso as $curso): ?>
                        <div class="chart-item">
                            <div>
                                <div class="chart-label"><?php echo htmlspecialchars($curso['curso_nome']); ?></div>
                                <small style="color: var(--secondary-color);"><?php echo htmlspecialchars($curso['categoria']); ?></small>
                            </div>
                            <span class="chart-value"><?php echo $curso['total_agendamentos']; ?> aulas</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--secondary-color); text-align: center; padding: 20px;">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">Histórico de Agendamentos</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Professor</th>
                        <th>Aluno</th>
                        <th>Curso</th>
                        <th>Status</th>
                        <th>Observações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($agendamentos_result && $agendamentos_result->num_rows > 0): ?>
                        <?php while ($agendamento = $agendamentos_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                                <td><?php echo substr($agendamento['hora_inicio'], 0, 5) . ' - ' . substr($agendamento['hora_fim'], 0, 5); ?></td>
                                <td><strong><?php echo htmlspecialchars($agendamento['professor_nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($agendamento['aluno_nome']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($agendamento['curso_nome']); ?></div>
                                    <small style="color: var(--secondary-color);"><?php echo htmlspecialchars($agendamento['categoria']); ?></small>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $agendamento['status']; ?>">
                                        <?php echo ucfirst($agendamento['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $agendamento['observacoes'] ? htmlspecialchars($agendamento['observacoes']) : '-'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--secondary-color);">
                                Nenhum agendamento encontrado
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="dark-mode.js"></script>
</body>
</html>







