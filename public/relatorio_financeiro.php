<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar estatísticas financeiras
$receita_total = 0;
$receita_mes_atual = 0;
$receita_mes_anterior = 0;
$total_inscricoes = 0;
$inscricoes_mes_atual = 0;
$valor_medio_curso = 0;
$cursos_mais_vendidos = [];
$receita_por_categoria = [];

try {
    // Receita total
    $result = $conn->query("
        SELECT SUM(c.preco) as total 
        FROM inscricoes i 
        JOIN cursos c ON i.curso_id = c.id 
        WHERE i.status = 'ativa'
    ");
    if ($result) {
        $row = $result->fetch_assoc();
        $receita_total = $row['total'] ?? 0;
    }

    // Receita do mês atual
    $result = $conn->query("
        SELECT SUM(c.preco) as total 
        FROM inscricoes i 
        JOIN cursos c ON i.curso_id = c.id 
        WHERE i.status = 'ativa'
        AND MONTH(i.criado_em) = MONTH(CURRENT_DATE())
        AND YEAR(i.criado_em) = YEAR(CURRENT_DATE())
    ");
    if ($result) {
        $row = $result->fetch_assoc();
        $receita_mes_atual = $row['total'] ?? 0;
    }

    // Receita do mês anterior
    $result = $conn->query("
        SELECT SUM(c.preco) as total 
        FROM inscricoes i 
        JOIN cursos c ON i.curso_id = c.id 
        WHERE i.status = 'ativa'
        AND MONTH(i.criado_em) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(i.criado_em) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    ");
    if ($result) {
        $row = $result->fetch_assoc();
        $receita_mes_anterior = $row['total'] ?? 0;
    }

    // Total de inscrições
    $result = $conn->query("SELECT COUNT(*) as total FROM inscricoes WHERE status = 'ativa'");
    if ($result) {
        $total_inscricoes = $result->fetch_assoc()['total'];
    }

    // Inscrições do mês atual
    $result = $conn->query("
        SELECT COUNT(*) as total 
        FROM inscricoes 
        WHERE status = 'ativa'
        AND MONTH(criado_em) = MONTH(CURRENT_DATE())
        AND YEAR(criado_em) = YEAR(CURRENT_DATE())
    ");
    if ($result) {
        $inscricoes_mes_atual = $result->fetch_assoc()['total'];
    }

    // Valor médio por curso
    $result = $conn->query("SELECT AVG(preco) as media FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $row = $result->fetch_assoc();
        $valor_medio_curso = $row['media'] ?? 0;
    }

    // Cursos mais vendidos
    $result = $conn->query("
        SELECT 
            c.nome,
            c.categoria,
            c.preco,
            COUNT(i.id) as vendas,
            SUM(c.preco) as receita_total
        FROM cursos c
        LEFT JOIN inscricoes i ON c.id = i.curso_id AND i.status = 'ativa'
        WHERE c.status = 'ativo'
        GROUP BY c.id
        ORDER BY vendas DESC, receita_total DESC
        LIMIT 5
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cursos_mais_vendidos[] = $row;
        }
    }

    // Receita por categoria
    $result = $conn->query("
        SELECT 
            c.categoria,
            COUNT(i.id) as inscricoes,
            SUM(c.preco) as receita
        FROM cursos c
        LEFT JOIN inscricoes i ON c.id = i.curso_id AND i.status = 'ativa'
        WHERE c.status = 'ativo'
        GROUP BY c.categoria
        ORDER BY receita DESC
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $receita_por_categoria[] = $row;
        }
    }

    // Lista de inscrições com detalhes
    $inscricoes_result = $conn->query("
        SELECT 
            i.id,
            i.criado_em,
            c.nome as curso_nome,
            c.categoria,
            c.preco,
            u.nome as aluno_nome,
            u.email as aluno_email
        FROM inscricoes i
        JOIN cursos c ON i.curso_id = c.id
        JOIN usuarios u ON i.aluno_id = u.id
        WHERE i.status = 'ativa'
        ORDER BY i.criado_em DESC
    ");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
}

// Calcular crescimento
$crescimento = 0;
if ($receita_mes_anterior > 0) {
    $crescimento = (($receita_mes_atual - $receita_mes_anterior) / $receita_mes_anterior) * 100;
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
    <link rel="stylesheet" href="dark-mode.css?v=3">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Relatório Financeiro</title>
    
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

        .stat-change {
            font-size: 0.875rem;
            margin-top: 8px;
        }

        .stat-change.positive {
            color: var(--success-color);
        }

        .stat-change.negative {
            color: var(--danger-color);
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

        .categoria-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            background: #dbeafe;
            color: #1e40af;
        }

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(37, 99, 235, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(16, 185, 129, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #ecfeff 100%) !important;
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
                radial-gradient(circle at 84% 26%, rgba(16, 185, 129, 0.25), transparent 34%),
                linear-gradient(135deg, #0f172a 0%, #2563eb 48%, #0891b2 100%);
            border: 1px solid rgba(255, 255, 255, 0.42);
            box-shadow: 0 28px 80px rgba(37, 99, 235, 0.18);
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
            background: linear-gradient(135deg, #2563eb, #0891b2);
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
            background: linear-gradient(90deg, #2563eb, #0891b2, #10b981);
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
            background: linear-gradient(135deg, #2563eb, #0891b2);
            box-shadow: 0 16px 34px rgba(37, 99, 235, 0.22);
            font-size: 1.18rem;
        }

        .stat-number {
            color: #0f172a;
            font-size: 2.18rem;
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
            background: linear-gradient(135deg, rgba(236, 253, 245, 0.98), rgba(239, 246, 255, 0.96));
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
            background: rgba(236, 253, 245, 0.58);
        }

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
            <h1><i class="fas fa-chart-line"></i> Relatório Financeiro</h1>
            <p>Análise financeira completa do sistema educacional</p>
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
                <div class="stat-icon"><i class="fas fa-sack-dollar"></i></div>
                <div class="stat-number">R$ <?php echo number_format($receita_total, 2, ',', '.'); ?></div>
                <div class="stat-label">Receita Total</div>
            </div>
            <div class="stat-card info">
                <div class="stat-icon"><i class="fas fa-calendar-days"></i></div>
                <div class="stat-number">R$ <?php echo number_format($receita_mes_atual, 2, ',', '.'); ?></div>
                <div class="stat-label">Receita Mês Atual</div>
                <?php if ($crescimento != 0): ?>
                    <div class="stat-change <?php echo $crescimento > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $crescimento > 0 ? '+' : ''; ?><?php echo number_format($crescimento, 1); ?>% vs mês anterior
                    </div>
                <?php endif; ?>
            </div>
            <div class="stat-card warning">
                <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                <div class="stat-number"><?php echo $total_inscricoes; ?></div>
                <div class="stat-label">Total Inscrições</div>
            </div>
            <div class="stat-card info">
                <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="stat-number">R$ <?php echo number_format($valor_medio_curso, 2, ',', '.'); ?></div>
                <div class="stat-label">Valor Médio Curso</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="chart-title">Cursos Mais Vendidos</h3>
                <?php if (!empty($cursos_mais_vendidos)): ?>
                    <?php foreach ($cursos_mais_vendidos as $curso): ?>
                        <div class="chart-item">
                            <div>
                                <div class="chart-label"><?php echo htmlspecialchars($curso['nome']); ?></div>
                                <small style="color: var(--secondary-color);"><?php echo htmlspecialchars($curso['categoria']); ?></small>
                            </div>
                            <div style="text-align: right;">
                                <div class="chart-value"><?php echo $curso['vendas']; ?> vendas</div>
                                <small style="color: var(--secondary-color);">R$ <?php echo number_format($curso['receita_total'], 2, ',', '.'); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--secondary-color); text-align: center; padding: 20px;">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>

            <div class="chart-card">
                <h3 class="chart-title">Receita por Categoria</h3>
                <?php if (!empty($receita_por_categoria)): ?>
                    <?php foreach ($receita_por_categoria as $categoria): ?>
                        <div class="chart-item">
                            <span class="chart-label"><?php echo htmlspecialchars($categoria['categoria']); ?></span>
                            <div style="text-align: right;">
                                <div class="chart-value">R$ <?php echo number_format($categoria['receita'], 2, ',', '.'); ?></div>
                                <small style="color: var(--secondary-color);"><?php echo $categoria['inscricoes']; ?> inscrições</small>
                            </div>
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
                <h3 class="table-title">Histórico de Inscrições</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Aluno</th>
                        <th>Curso</th>
                        <th>Categoria</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($inscricoes_result && $inscricoes_result->num_rows > 0): ?>
                        <?php while ($inscricao = $inscricoes_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($inscricao['criado_em'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($inscricao['aluno_nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($inscricao['curso_nome']); ?></td>
                                <td>
                                    <span class="categoria-badge">
                                        <?php echo htmlspecialchars($inscricao['categoria']); ?>
                                    </span>
                                </td>
                                <td><strong>R$ <?php echo number_format($inscricao['preco'], 2, ',', '.'); ?></strong></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--secondary-color);">
                                Nenhuma inscrição encontrada
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


