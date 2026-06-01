
<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$agendamentos_count = 0;
$agendamentos_hoje = 0;
$agendamentos_semana = 0;
$agendamentos_futuros = 0;

try {
    // Contar agendamentos futuros
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento >= CURDATE() AND status != 'cancelado'");
    if ($result) {
        $agendamentos_futuros = $result->fetch_assoc()['total'];
    }

    // Contar agendamentos hoje
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = CURDATE() AND status != 'cancelado'");
    if ($result) {
        $agendamentos_hoje = $result->fetch_assoc()['total'];
    }

    // Contar agendamentos esta semana
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status != 'cancelado'");
    if ($result) {
        $agendamentos_semana = $result->fetch_assoc()['total'];
    }

    // Contar total de agendamentos
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE status != 'cancelado'");
    if ($result) {
        $agendamentos_count = $result->fetch_assoc()['total'];
    }

    // Buscar próximos agendamentos
    $agendamentos_result = $conn->query("
        SELECT a.id, a.data_agendamento as data, a.hora_inicio, a.hora_fim, a.status, a.observacoes,
               a.professor_id, a.curso_id, a.aluno_id,
               p.nome as professor_nome, p.email as professor_email,
               c.nome as curso_nome, c.categoria,
               u.nome as aluno_nome, u.email as aluno_email
        FROM agendamentos a
        LEFT JOIN usuarios p ON a.professor_id = p.id
        LEFT JOIN cursos c ON a.curso_id = c.id
        LEFT JOIN usuarios u ON a.aluno_id = u.id
        WHERE a.status != 'cancelado'
        ORDER BY a.data_agendamento ASC, a.hora_inicio ASC
        LIMIT 10
    ");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $agendamentos_count = 0;
    $agendamentos_hoje = 0;
    $agendamentos_semana = 0;
    $agendamentos_futuros = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Sistema de Agendamentos</title>
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: var(--transition);
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

        .stat-card.success .stat-icon { background: linear-gradient(135deg, #059669, #34d399); }
        .stat-card.warning .stat-icon { background: linear-gradient(135deg, #d97706, #fbbf24); }
        .stat-card.info .stat-icon    { background: linear-gradient(135deg, #0891b2, #22d3ee); }

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
        
        .stat-card.success {
            border-left-color: var(--success-color);
        }
        
        .stat-card.success::before {
            background: linear-gradient(90deg, var(--success-color), #34d399);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .stat-card.warning::before {
            background: linear-gradient(90deg, var(--warning-color), #fbbf24);
        }

        .stat-card.info {
            border-left-color: var(--info-color);
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

        .agendamentos-grid {
            display: grid;
            gap: 16px;
        }

        .agendamento-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .agendamento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .agendamento-title {
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

        .status-confirmado {
            background: #dcfce7;
            color: #166534;
        }

        .status-pendente {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelado {
            background: #fef2f2;
            color: #dc2626;
        }

        .agendamento-details {
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

        .agendamento-actions {
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
            margin: 2% auto;
            padding: 24px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-actions {
            display: flex !important;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding: 16px 0;
            border-top: 1px solid var(--border-color);
            position: relative;
            z-index: 10;
        }

        .form-actions .btn {
            min-width: 120px;
            justify-content: center;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
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

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(124, 58, 237, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(6, 182, 212, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #f5f3ff 100%) !important;
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
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
            margin-bottom: 28px;
        }

        .stat-card,
        .agendamento-card {
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
        .agendamento-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            transform: scaleX(1);
            background: linear-gradient(90deg, #7c3aed, #06b6d4, #2563eb) !important;
        }

        .stat-card {
            padding: 30px;
            transition: var(--transition);
        }

        .stat-card:hover,
        .agendamento-card:hover {
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
            background: linear-gradient(135deg, #7c3aed, #0891b2) !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        }

        .agendamentos-grid {
            gap: 24px;
        }

        .agendamento-card {
            padding: 28px;
            transition: var(--transition);
        }

        .agendamento-header {
            gap: 16px;
            align-items: flex-start;
        }

        .agendamento-title {
            color: #0f172a;
            font-size: 1.2rem;
            font-weight: 850;
            letter-spacing: -0.045em;
        }

        .agendamento-details {
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

        .status-confirmado {
            color: #065f46;
            background: #d1fae5;
            border: 1px solid #a7f3d0;
        }

        .status-pendente {
            color: #92400e;
            background: #fef3c7;
            border: 1px solid #fde68a;
        }

        .status-cancelado {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
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
            max-width: 620px;
            max-height: calc(100vh - 44px);
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            margin-bottom: 0;
            padding: 18px 22px;
            color: #ffffff;
            background: linear-gradient(135deg, #312e81, #7c3aed, #0891b2);
            flex-shrink: 0;
        }

        .modal-title {
            color: #ffffff;
            font-weight: 850;
            letter-spacing: -0.035em;
        }

        .close {
            color: rgba(255, 255, 255, 0.86);
        }

        .modal form {
            padding: 18px 22px 0;
            overflow-y: auto;
        }

        #formAgendamento {
            max-height: calc(100vh - 128px);
        }

        #formAgendamento .form-row {
            gap: 12px;
        }

        #formAgendamento .form-group {
            margin-bottom: 12px;
        }

        .form-label {
            color: #475569;
            font-size: 0.78rem;
            font-weight: 850;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .form-control {
            min-height: 42px;
            border-radius: 16px;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(203, 213, 225, 0.95);
        }

        .form-actions {
            position: sticky;
            bottom: 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin: 0 -22px;
            padding: 14px 22px;
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
        }

        .confirm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(10px);
        }

        .confirm-dialog {
            width: min(440px, 100%);
            overflow: hidden;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 34px 95px rgba(15, 23, 42, 0.32);
            animation: confirmIn 0.22s ease;
        }

        .confirm-dialog-header {
            padding: 26px 28px;
            color: #ffffff;
            background: linear-gradient(135deg, #312e81, #7c3aed, #0891b2);
        }

        .confirm-dialog-icon {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            margin-bottom: 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.24);
            font-size: 1.35rem;
        }

        .confirm-dialog-title {
            font-size: 1.25rem;
            font-weight: 850;
            letter-spacing: -0.04em;
            margin-bottom: 4px;
        }

        .confirm-dialog-subtitle {
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.92rem;
        }

        .confirm-dialog-body {
            padding: 24px 28px 28px;
        }

        .confirm-dialog-message {
            color: #475569;
            line-height: 1.65;
            margin-bottom: 22px;
        }

        .confirm-dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .confirm-dialog-actions .btn {
            justify-content: center;
            min-width: 120px;
        }

        @keyframes confirmIn {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .agendamento-details {
                grid-template-columns: 1fr;
            }
            
            .form-row {
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
/* ADMIN_SIDEBAR_OVERRIDE_END */

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
.dark-mode h2 {
    color: #ffffff !important;
}

.dark-mode .stat-card {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}

.dark-mode .stat-number {
    color: #ffffff !important;
}

.dark-mode .stat-label {
    color: #cbd5e1 !important;
}

.dark-mode .actions {
    background: rgba(30, 41, 59, 0.9) !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 22px 58px rgba(0, 0, 0, 0.25) !important;
}

.dark-mode .agendamento-card {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
}

.dark-mode .agendamento-header {
    background: rgba(255, 255, 255, 0.05) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .agendamento-title {
    color: #ffffff !important;
}

.dark-mode .agendamento-details {
    background: rgba(15, 23, 42, 0.6) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .detail-label {
    color: #cbd5e1 !important;
}

.dark-mode .detail-value {
    color: #ffffff !important;
}

.dark-mode .agendamento-observacoes,
.dark-mode .agendamento-details p {
    color: #cbd5e1 !important;
}

.dark-mode .modal-content {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #f8fafc !important;
    box-shadow: 0 32px 90px rgba(0, 0, 0, 0.5) !important;
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

.dark-mode .form-control:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

.dark-mode .form-actions {
    background: #1e293b !important;
    border-top-color: rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .close {
    color: #94a3b8 !important;
}

.dark-mode .close:hover {
    color: #ffffff !important;
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

.dark-mode .confirm-dialog {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 34px 95px rgba(0, 0, 0, 0.5) !important;
}

.dark-mode .confirm-dialog-message {
    color: #cbd5e1 !important;
}

.dark-mode .empty-state h3 {
    color: #ffffff !important;
}

/* Badges e contrastes no modo escuro */
.dark-mode .status-badge {
    box-shadow: none !important;
}

.dark-mode .status-confirmado,
.dark-mode .status-concluido,
.dark-mode .status-ativo {
    background: rgba(16, 185, 129, 0.25) !important;
    color: #34d399 !important;
    border: 1px solid rgba(16, 185, 129, 0.4) !important;
    font-weight: 700 !important;
}

.dark-mode .status-cancelado,
.dark-mode .status-inativo {
    background: rgba(239, 68, 68, 0.25) !important;
    color: #f87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.4) !important;
    font-weight: 700 !important;
}

.dark-mode .status-pendente,
.dark-mode .status-agendado {
    background: rgba(245, 158, 11, 0.25) !important;
    color: #fbbf24 !important;
    border: 1px solid rgba(245, 158, 11, 0.4) !important;
    font-weight: 700 !important;
}

.dark-mode .empty-state p {
    color: #94a3b8 !important;
}

        /* === Padronizar cores dos stat-cards === */
        .stat-card::before,
        .stat-card.primary::before,
        .stat-card.success::before,
        .stat-card.warning::before,
        .stat-card.info::before,
        .agendamento-card::before {
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
        }

        /* === Remover cores não-azuis === */
        .btn-primary { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }
        .btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }

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
                <h1>Sistema de Agendamentos</h1>
                <p>Gerencie aulas, eventos e compromissos do sistema educacional</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Agendamentos Futuros</h3>
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                </div>
                <div class="stat-number"><?php echo $agendamentos_futuros; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Próximas aulas</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($agendamentos_futuros / 20) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Aulas Hoje</h3>
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                </div>
                <div class="stat-number"><?php echo $agendamentos_hoje; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Aulas de hoje</div>
                <div class="progress-bar"><div class="progress-fill success" style="width: <?php echo min(($agendamentos_hoje / 10) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Esta Semana</h3>
                    <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                </div>
                <div class="stat-number"><?php echo $agendamentos_semana; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Aulas na semana</div>
                <div class="progress-bar"><div class="progress-fill warning" style="width: <?php echo min(($agendamentos_semana / 15) * 100, 100); ?>%"></div></div>
            </div>
            <div class="stat-card info">
                <div class="stat-header">
                    <h3 class="stat-title">Total de Agendamentos</h3>
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                </div>
                <div class="stat-number"><?php echo $agendamentos_count; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Total no sistema</div>
                <div class="progress-bar"><div class="progress-fill info" style="width: <?php echo min(($agendamentos_count / 30) * 100, 100); ?>%"></div></div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Novo Agendamento
            </button>
        </div>

        <!-- Próximos Agendamentos -->
        <h2 style="margin-bottom: 16px; color: var(--dark-color);">Próximos Agendamentos</h2>
        
        <div class="agendamentos-grid">
            <?php if ($agendamentos_result && $agendamentos_result->num_rows > 0): ?>
                <?php while ($agendamento = $agendamentos_result->fetch_assoc()): ?>
                    <div class="agendamento-card">
                        <div class="agendamento-header">
                            <div class="agendamento-title">
                                <?php echo htmlspecialchars($agendamento['professor_nome']); ?> → 
                                <?php echo htmlspecialchars($agendamento['aluno_nome']); ?>
                            </div>
                            <span class="status-badge status-<?php echo $agendamento['status']; ?>">
                                <?php echo ucfirst($agendamento['status']); ?>
                            </span>
                        </div>
                        
                        <div class="agendamento-details">
                            <div class="detail-item">
                                <span class="detail-label">Data</span>
                                <span class="detail-value"><?php echo date('d/m/Y', strtotime($agendamento['data'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Horário</span>
                                <span class="detail-value"><?php echo $agendamento['hora_inicio']; ?> - <?php echo $agendamento['hora_fim']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Professor</span>
                                <span class="detail-value"><?php echo htmlspecialchars($agendamento['professor_nome']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Aluno</span>
                                <span class="detail-value"><?php echo htmlspecialchars($agendamento['aluno_nome']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Curso</span>
                                <span class="detail-value"><?php echo htmlspecialchars($agendamento['curso_nome']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Categoria</span>
                                <span class="detail-value"><?php echo htmlspecialchars($agendamento['categoria']); ?></span>
                            </div>
                            <?php if ($agendamento['observacoes']): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Observações</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($agendamento['observacoes']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="agendamento-actions">
                            <button class="btn btn-primary btn-sm" onclick='editarAgendamento(
                                <?php echo (int)$agendamento["id"]; ?>,
                                <?php echo json_encode($agendamento["data"]); ?>,
                                <?php echo json_encode($agendamento["hora_inicio"]); ?>,
                                <?php echo json_encode($agendamento["hora_fim"]); ?>,
                                <?php echo (int)$agendamento["professor_id"]; ?>,
                                <?php echo (int)$agendamento["curso_id"]; ?>,
                                <?php echo (int)$agendamento["aluno_id"]; ?>,
                                <?php echo json_encode($agendamento["status"]); ?>,
                                <?php echo json_encode($agendamento["observacoes"] ?? ""); ?>
                            )'>
                                <i class="fas fa-pen"></i> Editar
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="removerAgendamento(<?php echo $agendamento['id']; ?>)">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Nenhum agendamento disponível</h3>
                    <p>Comece criando o primeiro agendamento do sistema.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="confirmOverlay" class="confirm-overlay">
        <div class="confirm-dialog">
            <div class="confirm-dialog-header">
                <div class="confirm-dialog-icon">
                    <i class="fas fa-calendar-xmark"></i>
                </div>
                <div class="confirm-dialog-title" id="confirmTitle">Remover agendamento?</div>
                <div class="confirm-dialog-subtitle" id="confirmSubtitle">Esta ação precisa de confirmação</div>
            </div>
            <div class="confirm-dialog-body">
                <p class="confirm-dialog-message" id="confirmMessage">Tem certeza que deseja remover este agendamento?</p>
                <div class="confirm-dialog-actions">
                    <button type="button" class="btn btn-success" onclick="closeConfirmDialog()">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmActionBtn">Remover</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Agendamento -->
    <div id="modalAgendamento" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Novo Agendamento</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form id="formAgendamento">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Data *</label>
                        <input type="date" class="form-control" id="data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hora de Início *</label>
                        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Hora de Fim *</label>
                        <input type="time" class="form-control" id="hora_fim" name="hora_fim" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="agendado">Agendado</option>
                            <option value="pendente">Pendente</option>
                            <option value="confirmado">Confirmado</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Professor *</label>
                    <select class="form-control" id="professor_id" name="professor_id" required>
                        <option value="">Selecione um professor</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Curso *</label>
                    <select class="form-control" id="curso_id" name="curso_id" required>
                        <option value="">Selecione um curso</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Aluno *</label>
                    <select class="form-control" id="aluno_id" name="aluno_id" required>
                        <option value="">Selecione um aluno</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="2" placeholder="Observações sobre o agendamento..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal()" class="btn btn-outline">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitAgendamentoBtn">Criar Agendamento</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editingAgendamentoId = null;

        // Carregar dados quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            carregarProfessores();
            carregarCursos();
            carregarAlunos();
            
            // Definir data mínima como hoje
            const hoje = new Date().toISOString().split('T')[0];
            document.getElementById('data').min = hoje;
        });

        // Modal functions
        function openModal() {
            editingAgendamentoId = null;
            document.getElementById('modalTitle').textContent = 'Novo Agendamento';
            document.getElementById('submitAgendamentoBtn').textContent = 'Criar Agendamento';
            document.getElementById('formAgendamento').reset();
            document.getElementById('status').value = 'confirmado';
            document.getElementById('modalAgendamento').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalAgendamento').style.display = 'none';
            document.getElementById('formAgendamento').reset();
            editingAgendamentoId = null;
        }

        function editarAgendamento(id, data, horaInicio, horaFim, professorId, cursoId, alunoId, status, observacoes) {
            editingAgendamentoId = id;
            document.getElementById('modalTitle').textContent = 'Editar Agendamento';
            document.getElementById('submitAgendamentoBtn').textContent = 'Salvar Alterações';
            document.getElementById('data').value = data || '';
            document.getElementById('hora_inicio').value = horaInicio || '';
            document.getElementById('hora_fim').value = horaFim || '';
            document.getElementById('professor_id').value = String(professorId || '');
            document.getElementById('curso_id').value = String(cursoId || '');
            document.getElementById('aluno_id').value = String(alunoId || '');
            document.getElementById('status').value = status || 'confirmado';
            document.getElementById('observacoes').value = observacoes || '';
            document.getElementById('modalAgendamento').style.display = 'block';
        }

        // Carregar professores
        function carregarProfessores() {
            fetch('api/usuarios.php?tipo=professor')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('professor_id');
                    select.innerHTML = '<option value="">Selecione um professor</option>';
                    
                    if (data.success && data.data) {
                        data.data.forEach(professor => {
                            const option = document.createElement('option');
                            option.value = professor.id;
                            option.textContent = professor.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar professores:', error);
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

        // Criar agendamento
        document.getElementById('formAgendamento').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                data: formData.get('data'),
                hora_inicio: formData.get('hora_inicio'),
                hora_fim: formData.get('hora_fim'),
                professor_id: formData.get('professor_id'),
                curso_id: formData.get('curso_id'),
                aluno_id: formData.get('aluno_id'),
                status: formData.get('status'),
                observacoes: formData.get('observacoes')
            };

            const url = editingAgendamentoId ? `api/agendamentos.php/${editingAgendamentoId}` : 'api/agendamentos.php';
            const method = editingAgendamentoId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const message = editingAgendamentoId
                        ? 'Agendamento atualizado com sucesso!'
                        : 'Agendamento criado com sucesso!';
                    showFeedbackDialog('success', message);
                    closeModal();
                } else {
                    const actionText = editingAgendamentoId ? 'atualizar' : 'criar';
                    showFeedbackDialog('error', `Erro ao ${actionText} agendamento: ` + (result.error || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                const actionText = editingAgendamentoId ? 'atualizar' : 'criar';
                showFeedbackDialog('error', `Erro ao ${actionText} agendamento`);
            });
        });

        function openConfirmDialog(config) {
            document.getElementById('confirmTitle').textContent = config.title;
            document.getElementById('confirmSubtitle').textContent = config.subtitle;
            document.getElementById('confirmMessage').textContent = config.message;
            document.getElementById('confirmActionBtn').textContent = config.actionText;
            document.getElementById('confirmActionBtn').onclick = config.onConfirm;
            document.getElementById('confirmOverlay').style.display = 'flex';
        }

        function closeConfirmDialog() {
            document.getElementById('confirmOverlay').style.display = 'none';
        }

        function showFeedbackDialog(type, message) {
            const isSuccess = type === 'success';
            document.getElementById('confirmTitle').textContent = isSuccess ? 'Tudo certo!' : 'Não foi possível concluir';
            document.getElementById('confirmSubtitle').textContent = isSuccess ? 'Operação realizada com sucesso' : 'Tente novamente em instantes';
            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmActionBtn').textContent = 'OK';
            document.getElementById('confirmActionBtn').className = isSuccess ? 'btn btn-success' : 'btn btn-danger';
            document.getElementById('confirmActionBtn').onclick = function() {
                closeConfirmDialog();
                if (isSuccess) {
                    location.reload();
                }
            };
            document.getElementById('confirmOverlay').style.display = 'flex';
        }

        function removerAgendamento(id) {
            const actionBtn = document.getElementById('confirmActionBtn');
            actionBtn.className = 'btn btn-danger';

            openConfirmDialog({
                title: 'Remover agendamento?',
                subtitle: 'Essa ação não poderá ser desfeita',
                message: 'Tem certeza que deseja remover este agendamento da agenda?',
                actionText: 'Sim, remover',
                onConfirm: function() {
                    actionBtn.disabled = true;
                    actionBtn.textContent = 'Removendo...';

                    fetch(`api/agendamentos.php/${id}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(result => {
                        actionBtn.disabled = false;

                        if (result.success) {
                            showFeedbackDialog('success', 'Agendamento removido com sucesso.');
                        } else {
                            showFeedbackDialog('error', 'Erro ao remover agendamento: ' + (result.error || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        actionBtn.disabled = false;
                        console.error('Erro:', error);
                        showFeedbackDialog('error', 'Erro de conexão ao remover agendamento.');
                    });
                }
            });
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('modalAgendamento');
            const confirmOverlay = document.getElementById('confirmOverlay');
            if (event.target == modal) {
                closeModal();
            }
            if (event.target == confirmOverlay) {
                closeConfirmDialog();
            }
        }
    </script>
    <script src="dark-mode.js"></script>
</body>
</html>












