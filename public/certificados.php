<?php
// Conectar ao banco de dados
include 'db.php';

// Verificar se Ã© uma requisiÃ§Ã£o AJAX para buscar alunos
if (isset($_GET['action']) && $_GET['action'] === 'buscar_alunos') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    
    try {
        $sql = "
            SELECT 
                u.id,
                u.nome,
                u.email,
                u.criado_em,
                COUNT(DISTINCT c.id) as certificados_count
            FROM usuarios u
            LEFT JOIN certificados c ON u.id = c.aluno_id
            WHERE u.tipo_usuario = 'aluno' AND u.ativo = 1
            GROUP BY u.id
            ORDER BY u.nome
        ";
        
        $result = $conn->query($sql);
        $alunos = [];
        
        while ($row = $result->fetch_assoc()) {
            $alunos[] = [
                'id' => $row['id'],
                'nome' => $row['nome'],
                'email' => $row['email'],
                'certificados_count' => $row['certificados_count']
            ];
        }
        
        echo json_encode([
            'success' => true, 
            'data' => $alunos
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao buscar alunos: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Buscar dados reais do banco de dados
$certificados_count = 0;
$certificados_emitidos = 0;
$certificados_pendentes = 0;
$certificados_validados = 0;
$certificados_revogados = 0;

try {
    // Verificar se a tabela certificados existe
    $check_table = $conn->query("SHOW TABLES LIKE 'certificados'");
    
    if ($check_table && $check_table->num_rows > 0) {
        // Buscar estatÃ­sticas reais dos certificados
        $stats_sql = "
        SELECT 
            COUNT(*) as total_certificados,
            SUM(CASE WHEN status = 'emitido' THEN 1 ELSE 0 END) as emitidos,
            SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
            SUM(CASE WHEN status = 'validado' THEN 1 ELSE 0 END) as validados,
            SUM(CASE WHEN status = 'revogado' THEN 1 ELSE 0 END) as revogados
        FROM certificados
        ";
        
        $stats_result = $conn->query($stats_sql);
        if ($stats_result && $stats_result->num_rows > 0) {
            $stats = $stats_result->fetch_assoc();
            $certificados_count = $stats['total_certificados'];
            $certificados_emitidos = $stats['emitidos'];
            $certificados_pendentes = $stats['pendentes'];
            $certificados_validados = $stats['validados'];
            $certificados_revogados = $stats['revogados'];
        }

        // Buscar certificados com dados completos
        $certificados_sql = "
        SELECT 
            c.id,
            c.codigo_verificacao,
            c.status,
            c.data_emissao,
            c.data_conclusao,
            c.carga_horaria,
            u.nome as aluno_nome,
            u.email as aluno_email,
            u.criado_em as aluno_cadastro,
            cur.nome as curso_nome,
            cur.descricao as curso_descricao
        FROM certificados c
        INNER JOIN usuarios u ON c.aluno_id = u.id
        INNER JOIN cursos cur ON c.curso_id = cur.id
        WHERE u.tipo_usuario = 'aluno' AND u.ativo = 1
        ORDER BY c.data_emissao DESC
        LIMIT 20
        ";
        
        $certificados_result = $conn->query($certificados_sql);
        
    } else {
        // Se a tabela nÃ£o existe, usar dados simulados temporariamente
        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
        if ($result) {
            $alunos_ativos = $result->fetch_assoc()['total'];
            $certificados_count = $alunos_ativos * 3;
            $certificados_emitidos = $alunos_ativos * 2;
            $certificados_pendentes = $alunos_ativos;
        }

        // Buscar alunos para exibir certificados simulados
        $certificados_result = $conn->query("
            SELECT u.id, u.nome, u.email, u.criado_em,
                   COUNT(DISTINCT a.id) as agendamentos_count
            FROM usuarios u
            LEFT JOIN agendamentos a ON u.id = a.aluno_id
            WHERE u.tipo_usuario = 'aluno' AND u.ativo = 1
            GROUP BY u.id
            ORDER BY u.nome
            LIMIT 10
        ");
    }

} catch (Exception $e) {
    // Em caso de erro, usar valores padrÃ£o
    $certificados_count = 0;
    $certificados_emitidos = 0;
    $certificados_pendentes = 0;
    $certificados_validados = 0;
    $certificados_revogados = 0;
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
    <title>EduConnect - Certificados</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
        
        .stat-card.danger::before {
            background: linear-gradient(90deg, var(--danger-color), #f87171);
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
        .progress-fill.info    { background: linear-gradient(90deg, #0891b2, #22d3ee); }
        .progress-fill.danger  { background: linear-gradient(90deg, #dc2626, #f87171); }

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
        
        .stat-card.danger .stat-icon {
            background-color: var(--danger-color);
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

        .certificados-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            padding: 32px;
        }

        .certificado-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            padding: 24px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .certificado-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--primary-color);
        }

        .certificado-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        }

        .certificado-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .certificado-icon {
            width: 60px;
            height: 60px;
            background: var(--warning-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .certificado-info h4 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
            padding-right: 85px; /* Prevent overlap with status badge */
            line-height: 1.3;
        }

        .certificado-aluno {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .certificado-status {
            position: absolute;
            top: 16px;
            right: 16px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-emitido {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pendente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .certificado-details {
            margin-bottom: 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .detail-label {
            color: var(--secondary-color);
            font-weight: 500;
        }

        .detail-value {
            color: var(--dark-color);
            font-weight: 600;
            text-align: right;
            max-width: 65%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        .container { max-width: 1380px; background: transparent !important; border-radius: 0 !important; box-shadow: none !important; overflow: visible !important; border: none !important; padding: 24px !important; }
        .page-content { padding: 0; }

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
        .content-section,
        .certificado-card {
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
        .content-section::before,
        .certificado-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            transform: scaleX(1);
            background: linear-gradient(90deg, #f59e0b, #d97706, #2563eb) !important;
        }

        .stat-card {
            padding: 30px 22px;
        }

        .stat-card:hover,
        .certificado-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 20px;
            background: linear-gradient(135deg, #d97706, #2563eb) !important;
            box-shadow: 0 16px 34px rgba(217, 119, 6, 0.2);
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

        .section-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, rgba(255, 251, 235, 0.98), rgba(239, 246, 255, 0.96));
            border-bottom: 1px solid rgba(226, 232, 240, 0.72);
        }

        .section-header h3 {
            color: #0f172a;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .section-header h3 i {
            color: #d97706;
        }

        .btn {
            min-height: 40px;
            border-radius: 999px !important;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #d97706, #2563eb) !important;
            color: #ffffff !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #ffffff !important;
        }

        .btn-outline {
            color: #2563eb !important;
            background: rgba(239, 246, 255, 0.88) !important;
            border: 1px solid rgba(37, 99, 235, 0.22) !important;
        }

        .certificados-grid {
            gap: 24px;
            padding: 28px;
        }

        .certificado-card {
            padding: 28px;
        }

        .certificado-icon {
            border-radius: 22px;
            background: linear-gradient(135deg, #d97706, #2563eb) !important;
            box-shadow: 0 18px 38px rgba(217, 119, 6, 0.22);
        }

        .certificado-info h4 {
            color: #0f172a;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .certificado-details {
            padding: 18px;
            border-radius: 20px;
            background: rgba(248, 250, 252, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.75);
        }

        .certificado-status {
            top: 18px;
            right: 18px;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 850;
        }

        .status-emitido,
        .status-validado {
            color: #065f46;
            background: #d1fae5;
            border: 1px solid #a7f3d0;
        }

        .status-pendente {
            color: #92400e;
            background: #fef3c7;
            border: 1px solid #fde68a;
        }

        .status-revogado {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        .empty-state {
            background:
                radial-gradient(circle at 50% 0%, rgba(245, 158, 11, 0.12), transparent 34%),
                linear-gradient(180deg, rgba(255, 251, 235, 0.58), rgba(255, 255, 255, 0.92));
        }

        .empty-state i {
            width: 76px;
            height: 76px;
            display: inline-grid;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 24px;
            color: #ffffff;
            background: linear-gradient(135deg, #d97706, #2563eb);
            box-shadow: 0 18px 38px rgba(217, 119, 6, 0.22);
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modern Premium Action Buttons Styling */
        .btn-action {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            min-height: auto !important;
            padding: 6px 14px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            border-radius: 999px !important;
            border: 1px solid transparent !important;
            box-shadow: none !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-transform: none !important;
            letter-spacing: normal !important;
        }

        .btn-action-success {
            background: rgba(16, 185, 129, 0.08) !important;
            color: #059669 !important;
            border-color: rgba(16, 185, 129, 0.18) !important;
        }
        .btn-action-success:hover {
            background: #10b981 !important;
            color: #ffffff !important;
            border-color: #10b981 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }

        .btn-action-info {
            background: rgba(37, 99, 235, 0.08) !important;
            color: #2563eb !important;
            border-color: rgba(37, 99, 235, 0.18) !important;
        }
        .btn-action-info:hover {
            background: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
        }

        /* Dark Mode Specific Action Buttons */
        body.dark-mode .btn-action-success {
            background: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
            border-color: rgba(16, 185, 129, 0.25) !important;
        }
        body.dark-mode .btn-action-success:hover {
            background: #10b981 !important;
            color: #ffffff !important;
            border-color: #10b981 !important;
        }

        body.dark-mode .btn-action-info {
            background: rgba(96, 165, 250, 0.12) !important;
            color: #60a5fa !important;
            border-color: rgba(96, 165, 250, 0.22) !important;
        }
        body.dark-mode .btn-action-info:hover {
            background: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
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
        .dark-mode .section-header h3,
        .dark-mode .certificado-info h4 {
            color: #ffffff !important;
        }

        .dark-mode .stat-card,
        .dark-mode .content-section,
        .dark-mode .certificado-card {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 22px 58px rgba(0, 0, 0, 0.25) !important;
        }

        .dark-mode .stat-value,
        .dark-mode .detail-value {
            color: #ffffff !important;
        }

        .dark-mode .stat-label,
        .dark-mode .detail-label,
        .dark-mode .certificado-aluno {
            color: #cbd5e1 !important;
        }

        .dark-mode .section-header {
            background: rgba(255, 255, 255, 0.03) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .dark-mode .certificado-details {
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        .dark-mode .btn-outline {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .btn-outline:hover {
            background: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
        }

        /* Badges status */
        .dark-mode .status-emitido,
        .dark-mode .status-validado {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #34d399 !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        .dark-mode .status-pendente {
            background: rgba(245, 158, 11, 0.2) !important;
            color: #fbbf24 !important;
            border-color: rgba(245, 158, 11, 0.3) !important;
        }

        .dark-mode .status-revogado {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #f87171 !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }

        .dark-mode .empty-state {
            background: rgba(255, 255, 255, 0.02) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
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
            padding: 10px 18px !important;
        }
        .header-content {
            display: block !important;
            text-align: center !important;
            padding: 0 !important;
            position: relative;
            z-index: 1;
        }
        .header p { margin: 0 auto !important; text-align: center !important; }

        /* === Remover laranja/amber === */
        .stat-card::before, .content-section::before, .certificado-card::before {
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
        }
        .stat-icon {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            box-shadow: 0 16px 34px rgba(37, 99, 235, 0.22) !important;
        }
        .section-header h3 i { color: #2563eb !important; }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
        }
        .certificado-icon {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            box-shadow: 0 18px 38px rgba(37, 99, 235, 0.22) !important;
        }
        .status-pendente {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            border-color: rgba(37, 99, 235, 0.3) !important;
        }
        .empty-state {
            background:
                radial-gradient(circle at 50% 0%, rgba(37, 99, 235, 0.10), transparent 34%),
                linear-gradient(180deg, rgba(239, 246, 255, 0.58), rgba(255, 255, 255, 0.92)) !important;
        }
        .empty-state i {
            background: linear-gradient(135deg, #2563eb, #1e40af) !important;
            box-shadow: 0 18px 38px rgba(37, 99, 235, 0.22) !important;
        }
        .dark-mode .status-pendente {
            background: rgba(37, 99, 235, 0.2) !important;
            color: #60a5fa !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
        }

        /* === Remover verde === */
        .btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; color: #fff !important; }
        .btn-success:hover { background: linear-gradient(135deg, #1e40af, #172554) !important; }


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
                <h1>Sistema de Certificados</h1>
                <p>Gerencie a emissão e validação de certificados dos alunos</p>
            </div>
        </div>
        <div class="page-content">

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <h3 class="stat-title">Total de Certificados</h3>
                    <div class="stat-icon"><i class="fas fa-certificate"></i></div>
                </div>
                <div class="stat-value"><?php echo $certificados_count; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Total emitidos</div>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min(($certificados_count / 30) * 100, 100); ?>%"></div></div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <h3 class="stat-title">Emitidos</h3>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="stat-value"><?php echo $certificados_emitidos; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Certificados emitidos</div>
                <div class="progress-bar"><div class="progress-fill success" style="width: <?php echo $certificados_count > 0 ? min(($certificados_emitidos / $certificados_count) * 100, 100) : 0; ?>%"></div></div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <h3 class="stat-title">Pendentes</h3>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-value"><?php echo $certificados_pendentes; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Aguardando emissão</div>
                <div class="progress-bar"><div class="progress-fill warning" style="width: <?php echo $certificados_count > 0 ? min(($certificados_pendentes / $certificados_count) * 100, 100) : 0; ?>%"></div></div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <h3 class="stat-title">Validados</h3>
                    <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
                </div>
                <div class="stat-value"><?php echo $certificados_validados; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Certificados válidos</div>
                <div class="progress-bar"><div class="progress-fill info" style="width: <?php echo $certificados_count > 0 ? min(($certificados_validados / $certificados_count) * 100, 100) : 0; ?>%"></div></div>
            </div>

            <div class="stat-card danger">
                <div class="stat-header">
                    <h3 class="stat-title">Revogados</h3>
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                </div>
                <div class="stat-value"><?php echo $certificados_revogados; ?></div>
                <div class="stat-change positive"><i class="fas fa-arrow-up stat-change-icon"></i> Certificados revogados</div>
                <div class="progress-bar"><div class="progress-fill danger" style="width: <?php echo $certificados_count > 0 ? min(($certificados_revogados / $certificados_count) * 100, 100) : 0; ?>%"></div></div>
            </div>
        </div>

        <!-- Certificados Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-certificate"></i> Certificados Disponíveis</h3>
                <button class="btn btn-primary" onclick="emitirCertificado()">
                    <i class="fas fa-plus"></i> Emitir Certificado
                </button>
            </div>
            
            <?php if ($certificados_result && $certificados_result->num_rows > 0): ?>
                <div class="certificados-grid">
                    <?php while ($certificado = $certificados_result->fetch_assoc()): ?>
                        <div class="certificado-card">
                            <div class="certificado-status status-<?php echo isset($certificado['status']) ? $certificado['status'] : 'pendente'; ?>">
                                <?php echo ucfirst(isset($certificado['status']) ? $certificado['status'] : 'pendente'); ?>
                            </div>
                            
                            <div class="certificado-header">
                                <div class="certificado-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div class="certificado-info">
                                    <h4><?php echo htmlspecialchars(isset($certificado['curso_nome']) ? $certificado['curso_nome'] : 'Curso não definido'); ?></h4>
                                    <div class="certificado-aluno"><?php echo htmlspecialchars(isset($certificado['aluno_nome']) ? $certificado['aluno_nome'] : 'Aluno não definido'); ?></div>
                                </div>
                            </div>
                            
                            <div class="certificado-details">
                                <div class="detail-row">
                                    <span class="detail-label">Aluno:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars(isset($certificado['aluno_nome']) ? $certificado['aluno_nome'] : 'Não definido'); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Email:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars(isset($certificado['aluno_email']) ? $certificado['aluno_email'] : 'Não definido'); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Curso:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars(isset($certificado['curso_nome']) ? $certificado['curso_nome'] : 'Não definido'); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Carga Horária:</span>
                                    <span class="detail-value"><?php echo isset($certificado['carga_horaria']) ? $certificado['carga_horaria'] : '0'; ?> horas</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Data de Conclusão:</span>
                                    <span class="detail-value"><?php echo isset($certificado['data_conclusao']) && $certificado['data_conclusao'] ? date('d/m/Y', strtotime($certificado['data_conclusao'])) : 'Não definida'; ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Código:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars(isset($certificado['codigo_verificacao']) ? $certificado['codigo_verificacao'] : 'Não definido'); ?></span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button class="btn btn-action btn-action-info" onclick="verCertificado(<?php echo isset($certificado['id']) ? $certificado['id'] : 0; ?>)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn btn-action btn-action-success" onclick="baixarCertificado(<?php echo isset($certificado['id']) ? $certificado['id'] : 0; ?>)">
                                    <i class="fas fa-download"></i> Baixar
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-certificate"></i>
                    <h4>Nenhum certificado disponível</h4>
                    <p>Comece emitindo o primeiro certificado do sistema</p>
                    <button class="btn btn-primary" onclick="emitirCertificado()">
                        <i class="fas fa-plus"></i> Primeiro Certificado
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Validação Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-shield-alt"></i> Validação de Certificados</h3>
                <button class="btn btn-success" onclick="gerenciarValidacao()">
                    <i class="fas fa-cog"></i> Gerenciar
                </button>
            </div>
            
            <div class="empty-state">
                <i class="fas fa-shield-alt"></i>
                <h4>Sistema de Validação</h4>
                <p>Sistema de validação de certificados funcionando</p>
                <button class="btn btn-success" onclick="gerenciarValidacao()">
                    <i class="fas fa-cog"></i> Gerenciar Validação
                </button>
            </div>
        </div>
        </div><!-- /page-content -->
    </div>

    <script>
        // FunÃ§Ã£o para emitir certificado
        async function emitirCertificado() {
            try {
                // Buscar alunos do banco de dados
                const response = await fetch('certificados.php?action=buscar_alunos');
                const data = await response.json();
                
                if (!data.success) {
                    showNotification('Erro ao buscar alunos: ' + data.message, 'error');
                    return;
                }
                
                // Criar modal com alunos reais
                let alunosHtml = '';
                data.data.forEach(aluno => {
                    alunosHtml += `
                        <button type="button" class="list-group-item list-group-item-action" onclick="emitirCertificadoParaAluno(${aluno.id})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${aluno.nome}</strong><br>
                                    <small class="text-muted">${aluno.email}</small>
                                </div>
                                <span class="badge bg-primary">ID: ${aluno.id}</span>
                            </div>
                        </button>
                    `;
                });
                
                const modalHtml = `
                    <div class="modal fade" id="selecaoAlunoModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">ðŸŽ“ Selecionar Aluno</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Selecione o aluno que receberÃ¡ o certificado:</p>
                                    <div class="list-group">
                                        ${alunosHtml}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remover modal anterior se existir
                const modalAnterior = document.getElementById('selecaoAlunoModal');
                if (modalAnterior) {
                    modalAnterior.remove();
                }
                
                // Adicionar novo modal
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('selecaoAlunoModal'));
                modal.show();
                
            } catch (error) {
                console.error('Erro ao buscar alunos:', error);
                showNotification('Erro ao carregar lista de alunos', 'error');
            }
        }



        // FunÃ§Ã£o para emitir certificado para aluno especÃ­fico
        async function emitirCertificadoParaAluno(alunoId) {
            try {
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('selecaoAlunoModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Primeiro, buscar cursos disponÃ­veis
                const cursosResponse = await fetch('api/cursos.php?action=listar');
                const cursosData = await cursosResponse.json();
                
                if (!cursosData.success || cursosData.data.length === 0) {
                    showNotification('âŒ Nenhum curso disponÃ­vel para certificado', 'error');
                    return;
                }
                
                // Selecionar o primeiro curso disponÃ­vel
                const curso = cursosData.data[0];
                
                // Emitir certificado via API
                const response = await fetch('api/certificados.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'emitir_certificado_individual',
                        aluno_id: alunoId,
                        curso_id: curso.id,
                        data_conclusao: new Date().toISOString().split('T')[0]
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('âœ… Certificado emitido com sucesso!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('âŒ Erro ao emitir certificado: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('âŒ Erro de conexÃ£o ao emitir certificado', 'error');
            }
        }

        // FunÃ§Ã£o para visualizar certificado
        async function verCertificado(id) {
            try {
                const response = await fetch(`api/certificados.php?action=ver_certificado&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    // Abrir modal com detalhes do certificado
                    showCertificadoModal(result.data);
                } else {
                    showNotification('âŒ Erro ao carregar certificado: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro de conexÃ£o ao carregar certificado', 'error');
            }
        }

        // FunÃ§Ã£o para baixar certificado
        function baixarCertificado(id) {
            try {
                // Abrir em nova aba para visualizaÃ§Ã£o/impressÃ£o
                const url = `gerar_pdf_simples.php?id=${id}`;
                window.open(url, '_blank');
                showNotification('âœ… Certificado aberto! Use Ctrl+P para salvar como PDF', 'success');
            } catch (error) {
                showNotification('âŒ Erro ao abrir certificado', 'error');
            }
        }

        // FunÃ§Ã£o para validar certificado
        async function validarCertificado(id) {
            try {
                const response = await fetch('api/certificados.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'validar_certificado',
                        id: id
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('âœ… Certificado validado com sucesso!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('âŒ Erro ao validar certificado: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('âŒ Erro de conexÃ£o ao validar certificado', 'error');
            }
        }

        // FunÃ§Ã£o para gerenciar validaÃ§Ã£o
        function gerenciarValidacao() {
            // Redirecionar para pÃ¡gina de validaÃ§Ã£o
            window.location.href = 'validacao_certificados.php';
        }
        
        // FunÃ§Ã£o alternativa para gerenciar validaÃ§Ã£o (se o arquivo nÃ£o existir)
        function gerenciarValidacaoAlternativa() {
            // Mostrar modal com opÃ§Ãµes de validaÃ§Ã£o
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>ðŸ” Sistema de ValidaÃ§Ã£o</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-shield-alt" style="font-size: 3rem; color: #3b82f6; margin-bottom: 20px;"></i>
                            <h4>Sistema de ValidaÃ§Ã£o de Certificados</h4>
                            <p>Funcionalidade em desenvolvimento</p>
                            <div style="margin-top: 30px;">
                                <button onclick="validarTodosCertificados()" class="btn btn-success" style="margin: 10px;">
                                    <i class="fas fa-check-double"></i> Validar Todos
                                </button>
                                <button onclick="closeModal()" class="btn btn-outline" style="margin: 10px;">
                                    <i class="fas fa-times"></i> Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }
        
        // FunÃ§Ã£o para validar todos os certificados
        async function validarTodosCertificados() {
            showConfirmDialog({
                title: 'Validar certificados?',
                message: 'Tem certeza que deseja validar todos os certificados pendentes?',
                actionText: 'Sim, validar',
                onConfirm: async function() {
                    closeModal();
                    
                    try {
                        const response = await fetch('api/certificados.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: 'validar_todos_pendentes'
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showNotification(`${result.count || 'Todos os'} certificados validados com sucesso!`, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('Erro ao validar certificados: ' + result.message, 'error');
                        }
                    } catch (error) {
                        showNotification('Erro de conexÃ£o ao validar certificados', 'error');
                    }
                }
            });
        }

        // Função para mostrar modal de certificado
        function showCertificadoModal(certificado) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-file-contract"></i> Certificado de Conclusão</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="certificado-preview">
                            <div class="certificado-header">
                                <h2><i class="fas fa-graduation-cap"></i> EduConnect</h2>
                                <p>Sistema Educacional Profissional</p>
                            </div>
                            <div class="certificado-content">
                                <p>Certificamos que</p>
                                <h3>${certificado.aluno_nome}</h3>
                                <p>concluiu com êxito o curso</p>
                                <h4>${certificado.curso_nome}</h4>
                                <p>com carga horária de ${certificado.carga_horaria} horas</p>
                                <p>em ${certificado.data_conclusao}</p>
                            </div>
                            <div class="certificado-footer">
                                <p>Código de Validação: ${certificado.codigo_verificacao}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="baixarCertificado(${certificado.id})" class="btn btn-primary">
                            <i class="fas fa-download"></i> Baixar PDF
                        </button>
                        <button onclick="closeModal()" class="btn btn-outline">Fechar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Função para fechar modal
        function closeModal() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
        }

        function showConfirmDialog(config) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content confirm-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-shield-check"></i> ${config.title}</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>${config.message}</p>
                    </div>
                    <div class="modal-footer">
                        <button onclick="closeModal()" class="btn btn-outline">Cancelar</button>
                        <button id="confirmActionButton" class="btn btn-primary">${config.actionText}</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            document.getElementById('confirmActionButton').onclick = config.onConfirm;
        }

        // Função para mostrar notificações
        // Função para mostrar notificações
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
            
            // Auto-remover após 5 segundos
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Adicionar estilos para modal e notificações
        const styles = `
            <style>
                .modal-overlay {
                    position: fixed;
                    inset: 0;
                    padding: 20px;
                    background: rgba(15, 23, 42, 0.62);
                    backdrop-filter: blur(10px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                }
                
                .modal-content {
                    background: rgba(255, 255, 255, 0.96);
                    border-radius: 28px;
                    border: 1px solid rgba(255, 255, 255, 0.86);
                    max-width: 600px;
                    width: 90%;
                    max-height: 80vh;
                    overflow-y: auto;
                    box-shadow: 0 34px 95px rgba(15, 23, 42, 0.32);
                }
                
                .modal-header {
                    padding: 24px 28px;
                    color: #ffffff;
                    background: linear-gradient(135deg, #172554, #1e40af, #2563eb);
                    border-bottom: 0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .modal-header h3,
                .modal-header .modal-title {
                    color: #ffffff;
                    font-weight: 850;
                    letter-spacing: -0.04em;
                    margin: 0;
                }
                
                .modal-body {
                    padding: 28px;
                }
                
                .modal-footer {
                    padding: 20px 28px 28px;
                    border-top: 1px solid rgba(226, 232, 240, 0.72);
                    display: flex;
                    gap: 12px;
                    justify-content: flex-end;
                }
                
                .close-btn {
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    color: rgba(255, 255, 255, 0.86);
                }
                
                .certificado-preview {
                    border: 2px solid rgba(217, 119, 6, 0.32);
                    border-radius: 24px;
                    padding: 30px;
                    text-align: center;
                    background: linear-gradient(135deg, #fff7ed 0%, #eff6ff 100%);
                }
                
                .certificado-header h2 {
                    color: #d97706;
                    margin-bottom: 5px;
                }
                
                .certificado-content h3 {
                    color: #1e293b;
                    margin: 20px 0;
                    font-size: 1.5rem;
                }
                
                .certificado-content h4 {
                    color: #2563eb;
                    margin: 15px 0;
                }
                
                .notification {
                    position: fixed;
                    right: 24px;
                    bottom: 24px;
                    background: rgba(255, 255, 255, 0.96);
                    border-radius: 18px;
                    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
                    z-index: 10001;
                    min-width: 300px;
                    border: 1px solid rgba(255, 255, 255, 0.86);
                    border-left: 6px solid #3b82f6;
                    backdrop-filter: blur(18px);
                }
                
                .notification-success {
                    border-left-color: #10b981;
                }
                
                .notification-error {
                    border-left-color: #ef4444;
                }
                
                .notification-content {
                    padding: 16px 18px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 14px;
                    color: #0f172a;
                    font-weight: 750;
                }
                
                .notification-close {
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    color: #64748b;
                    margin-left: 10px;
                }
                
                /* Dark Mode styles inside injected stylesheet */
                .dark-mode .modal-content {
                    background: #1e293b !important;
                    border-color: rgba(255, 255, 255, 0.15) !important;
                    color: #f8fafc !important;
                }
                .dark-mode .modal-header {
                    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
                }
                .dark-mode .modal-footer {
                    border-top-color: rgba(255, 255, 255, 0.1) !important;
                }
                .dark-mode .certificado-preview {
                    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
                    border-color: rgba(217, 119, 6, 0.4) !important;
                }
                .dark-mode .certificado-content h3 {
                    color: #ffffff !important;
                }
                .dark-mode .notification {
                    background: #1e293b !important;
                    color: #f8fafc !important;
                    border-color: rgba(255, 255, 255, 0.15) !important;
                }
                .dark-mode .notification-content {
                    color: #ffffff !important;
                }
                .dark-mode .list-group-item-action {
                    background-color: rgba(255, 255, 255, 0.03) !important;
                    color: #f8fafc !important;
                    border-color: rgba(255, 255, 255, 0.08) !important;
                }
                .dark-mode .list-group-item-action:hover {
                    background-color: rgba(255, 255, 255, 0.08) !important;
                    color: #ffffff !important;
                }
                .dark-mode .list-group-item-action .text-muted {
                    color: #cbd5e1 !important;
                }
            </style>
        `;
        
        // Função para revogar certificado
        async function revogarCertificado(id) {
            showConfirmDialog({
                title: 'Revogar certificado?',
                message: 'Tem certeza que deseja revogar este certificado?',
                actionText: 'Sim, revogar',
                onConfirm: async function() {
                    closeModal();
                    
                    try {
                        const response = await fetch('api/certificados.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: 'revogar_certificado',
                                certificado_id: id
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            showNotification('Certificado revogado com sucesso!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('Erro ao revogar certificado: ' + data.message, 'error');
                        }
                    } catch (error) {
                        showNotification('Erro de conexão ao revogar certificado', 'error');
                    }
                }
            });
        }

        // Função para desrevogar certificado
        async function desrevogarCertificado(id) {
            showConfirmDialog({
                title: 'Reativar certificado?',
                message: 'Tem certeza que deseja reativar este certificado revogado?',
                actionText: 'Sim, reativar',
                onConfirm: async function() {
                    closeModal();
                    
                    try {
                        const response = await fetch('api/certificados.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: 'desrevogar_certificado',
                                certificado_id: id
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            showNotification('Certificado reativado com sucesso!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('Erro ao reativar certificado: ' + data.message, 'error');
                        }
                    } catch (error) {
                        showNotification('Erro de conexão ao reativar certificado', 'error');
                    }
                }
            });
        }

        // Adicionar estilos ao head
        if (!document.querySelector('#certificados-styles')) {
            const styleElement = document.createElement('div');
            styleElement.id = 'certificados-styles';
            styleElement.innerHTML = styles;
            document.head.appendChild(styleElement);
        }
    </script>
    
    <script src="dark-mode.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>













