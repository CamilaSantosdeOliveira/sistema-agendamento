<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acesso negado');
}

include 'db.php';

try {
    $professor_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $professor = $stmt->get_result()->fetch_assoc();

    if (!$professor) {
        header('HTTP/1.1 404 Not Found');
        exit('Professor não encontrado');
    }

    $stmt = $conn->prepare("SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome
                           FROM agendamentos a
                           JOIN cursos c ON a.curso_id = c.id
                           JOIN usuarios u ON a.aluno_id = u.id
                           WHERE a.professor_id = ?
                           ORDER BY a.data_agendamento DESC
                           LIMIT 20");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $agendamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $total_agendamentos = count($agendamentos);
    $concluidos = count(array_filter($agendamentos, fn($a) => ($a['status'] ?? '') === 'concluido'));
    $agendados  = count(array_filter($agendamentos, fn($a) => ($a['status'] ?? '') === 'agendado'));
    $cancelados = count(array_filter($agendamentos, fn($a) => ($a['status'] ?? '') === 'cancelado'));

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Erro interno do servidor');
}

function h($v) { return htmlspecialchars((string)($v ?? '-'), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório do Professor - EduConnect</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        /* Barra de ações (não aparece no PDF) */
        .action-bar {
            background: #0f172a;
            color: white;
            padding: 12px 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
        }
        .action-bar span { opacity: 0.7; flex: 1; }
        .btn-action {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-action:hover { background: #1d4ed8; }
        .btn-close {
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        /* Página */
        .page {
            max-width: 820px;
            margin: 24px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }

        /* Cabeçalho */
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 52%, #2563eb 100%);
            color: white;
            padding: 40px 48px 36px;
            position: relative;
        }
        .header-brand {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.6;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .header .meta {
            font-size: 12px;
            opacity: 0.65;
        }
        .header-circle {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            border: 2px solid rgba(255,255,255,0.12);
        }

        /* Conteúdo */
        .content { padding: 40px 48px; }

        /* Seção */
        .section { margin-bottom: 36px; }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #2563eb;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 14px;
            background: #2563eb;
            border-radius: 2px;
        }

        /* Grade de dados */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 32px;
        }
        .info-item label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            display: block;
            margin-bottom: 3px;
        }
        .info-item span {
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
        }

        /* Cards de resumo */
        .stats-row {
            display: flex;
            gap: 16px;
        }
        .stat-card {
            flex: 1;
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px 16px;
            text-align: center;
            border-top: 3px solid #cbd5e1;
        }
        .stat-card.total   { border-top-color: #2563eb; }
        .stat-card.ok      { border-top-color: #10b981; }
        .stat-card.pending { border-top-color: #f59e0b; }
        .stat-card.cancel  { border-top-color: #ef4444; }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        /* Tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        thead tr {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
        }
        thead th {
            padding: 11px 14px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 10px 14px; color: #334155; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-concluido { background: #d1fae5; color: #065f46; }
        .badge-agendado  { background: #fef3c7; color: #92400e; }
        .badge-cancelado { background: #fee2e2; color: #991b1b; }
        .badge-default   { background: #f1f5f9; color: #475569; }

        /* Rodapé */
        .footer {
            background: #0f172a;
            color: rgba(255,255,255,0.4);
            text-align: center;
            padding: 16px 48px;
            font-size: 11px;
            letter-spacing: 0.3px;
        }

        /* Impressão */
        @page {
            margin: 0;
            size: A4;
        }
        @media print {
            body { background: white; }
            .action-bar { display: none !important; }
            .page {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="action-bar no-print">
    <span>Relatório pronto &mdash; clique em <strong>Salvar como PDF</strong> para baixar</span>
    <button class="btn-action" onclick="window.print()">&#128438; Salvar como PDF</button>
    <button class="btn-close" onclick="window.close()">&#10005; Fechar</button>
</div>

<div class="page">

    <!-- Cabeçalho -->
    <div class="header">
        <div class="header-brand">EduConnect Tech</div>
        <h1>Relatório do Professor</h1>
        <div class="meta">Exportado em <?php echo date('d/m/Y \à\s H:i'); ?></div>
        <div class="header-circle"><?php echo mb_strtoupper(mb_substr($professor['nome'] ?? 'P', 0, 1)); ?></div>
    </div>

    <div class="content">

        <!-- Dados do Professor -->
        <div class="section">
            <div class="section-title">Dados do Professor</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Nome</label>
                    <span><?php echo h($professor['nome']); ?></span>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <span><?php echo h($professor['email']); ?></span>
                </div>
                <div class="info-item">
                    <label>Formação</label>
                    <span><?php echo h($professor['formacao']); ?></span>
                </div>
                <div class="info-item">
                    <label>Valor por hora</label>
                    <span>R$ <?php echo number_format((float)($professor['valor_hora'] ?? 0), 2, ',', '.'); ?></span>
                </div>
                <div class="info-item">
                    <label>Telefone</label>
                    <span><?php echo h($professor['telefone'] ?? 'Não informado'); ?></span>
                </div>
            </div>
        </div>

        <!-- Resumo -->
        <div class="section">
            <div class="section-title">Resumo</div>
            <div class="stats-row">
                <div class="stat-card total">
                    <div class="stat-number"><?php echo $total_agendamentos; ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card ok">
                    <div class="stat-number"><?php echo $concluidos; ?></div>
                    <div class="stat-label">Concluídas</div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-number"><?php echo $agendados; ?></div>
                    <div class="stat-label">Agendadas</div>
                </div>
                <div class="stat-card cancel">
                    <div class="stat-number"><?php echo $cancelados; ?></div>
                    <div class="stat-label">Canceladas</div>
                </div>
            </div>
        </div>

        <!-- Agendamentos -->
        <div class="section">
            <div class="section-title">Últimos Agendamentos</div>
            <?php if (empty($agendamentos)): ?>
                <p style="color:#94a3b8; font-size:13px; padding: 20px 0;">Nenhum agendamento encontrado.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Aluno</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $aula): ?>
                    <?php
                        $status = $aula['status'] ?? '';
                        $badge_class = match($status) {
                            'concluido' => 'badge-concluido',
                            'agendado'  => 'badge-agendado',
                            'cancelado' => 'badge-cancelado',
                            default     => 'badge-default'
                        };
                        $data = !empty($aula['data_agendamento'])
                            ? date('d/m/Y', strtotime($aula['data_agendamento']))
                            : '-';
                    ?>
                    <tr>
                        <td><?php echo h($aula['curso_nome']); ?></td>
                        <td><?php echo h($aula['aluno_nome']); ?></td>
                        <td><?php echo $data; ?></td>
                        <td><?php echo h($aula['hora_inicio']); ?></td>
                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($status ?: '-'); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    </div>

    <div class="footer">
        Relatório gerado automaticamente pelo EduConnect Tech &bull; <?php echo date('d/m/Y \à\s H:i'); ?>
    </div>

</div>

</body>
</html>
