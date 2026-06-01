<?php
// Conectar ao banco de dados
include 'db.php';

// Buscar usuÃ¡rios do banco de dados
$usuarios = [];
$filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$filtro_status = isset($_GET['status']) ? $_GET['status'] : '';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

try {
    $where_conditions = [];
    $params = [];
    
    if ($filtro_tipo) {
        $where_conditions[] = "tipo_usuario = ?";
        $params[] = $filtro_tipo;
    }
    
    if ($filtro_status !== '') {
        $where_conditions[] = "ativo = ?";
        $params[] = $filtro_status;
    }
    
    if ($busca) {
        $where_conditions[] = "(nome LIKE ? OR email LIKE ? OR formacao LIKE ?)";
        $params[] = "%$busca%";
        $params[] = "%$busca%";
        $params[] = "%$busca%";
    }
    
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    $sql = "SELECT * FROM usuarios $where_clause ORDER BY nome";
    $stmt = $conn->prepare($sql);
    
    if ($stmt && !empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        $stmt->close();
    }
    
    // Buscar estatÃ­sticas
    $stats = [];
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    if ($result) {
        $stats['total'] = $result->fetch_assoc()['total'];
    }
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
    if ($result) {
        $stats['professores'] = $result->fetch_assoc()['total'];
    }
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
    if ($result) {
        $stats['alunos'] = $result->fetch_assoc()['total'];
    }
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 0");
    if ($result) {
        $stats['inativos'] = $result->fetch_assoc()['total'];
    }
    
} catch (Exception $e) {
    $usuarios = [];
    $stats = ['total' => 0, 'professores' => 0, 'alunos' => 0, 'inativos' => 0];
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
    <title>Gestão de Usuários - Sistema de Agendamento</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            background: #f8fafc;
            color: #1f2937;
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

        .header h1 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            margin-top: 0;
        }

        .header p {
            color: #6b7280;
            font-size: 1.2em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
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

        .stat-card.professores .stat-icon { background: linear-gradient(135deg, #d97706, #fbbf24); }
        .stat-card.alunos .stat-icon      { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
        .stat-card.inativos .stat-icon    { background: linear-gradient(135deg, #dc2626, #f87171); }

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

        .progress-fill.warning  { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .progress-fill.purple   { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
        .progress-fill.danger   { background: linear-gradient(90deg, #dc2626, #f87171); }

        .stat-value {
            font-size: 2.5em;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .actions-bar {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .filter-group select, .filter-group input {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            min-width: 150px;
        }

        .search-box {
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            border-radius: 25px;
            font-size: 14px;
            min-width: 250px;
            outline: none;
        }

        .search-box:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #1e40af;
            color: white;
        }

        .btn-primary:hover {
            background: #1e40af;
            transform: translateY(-2px);
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
        
        .btn-outline {
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }
        
        .btn-outline:hover {
            background: #f9fafb;
            color: #374151;
        }

        .users-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-header {
            background: #f9fafb;
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-header h3 {
            font-size: 1.3em;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th, td {
            padding: 18px 14px;
            text-align: left;
            border-bottom: 1px solid #f3f4f6;
        }

        th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: #f9fafb;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #1e40af;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1em;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-details h4 {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .user-email {
            color: #6b7280;
            font-size: 0.85em;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
        }

        .user-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .type-professor {
            background: #fef3c7;
            color: #92400e;
        }

        .type-aluno {
            background: #e0e7ff;
            color: #3730a3;
        }

        .type-admin {
            background: #f1f5f9;
            color: #475569;
        }

        .user-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-ativo {
            background: #dcfce7;
            color: #166534;
        }

        .status-inativo {
            background: #fee2e2;
            color: #991b1b;
        }

        .user-actions {
            display: flex;
            gap: 6px;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 650;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-action i { font-size: 0.85rem; }

        .btn-action-edit {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }
        .btn-action-edit:hover { background: #10b981; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); }

        .btn-action-disable {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
        }
        .btn-action-disable:hover { background: #f59e0b; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25); }

        .btn-action-delete {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
        }
        .btn-action-delete:hover { background: #ef4444; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25); }

        .no-users {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .no-users h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #374151;
        }

        .empty-dash {
            color: #94a3b8;
            font-weight: 400;
        }
        
        /* Estilos para o modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            padding: 0;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            background: #1e40af;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.2em;
        }
        
        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #374151;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #2563eb;
            transform: translateX(-4px);
        }
        
        .header {
            position: relative;
        }

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(37, 99, 235, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(139, 92, 246, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #eef2ff 100%) !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
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
                radial-gradient(circle at 84% 26%, rgba(139, 92, 246, 0.28), transparent 34%),
                linear-gradient(135deg, #1e3a8a 0%, #2563eb 46%, #7c3aed 100%) !important;
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
        .back-link {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            color: #ffffff !important;
            font-size: clamp(1.85rem, 3.6vw, 2.75rem);
            font-weight: 750;
            letter-spacing: -0.02em;
            margin-bottom: 10px;
        }

        .header p {
            max-width: 720px;
            margin-inline: auto;
            color: rgba(255, 255, 255, 0.82) !important;
            line-height: 1.65;
        }

        .back-link {
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
        .actions-bar,
        .users-table {
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
        .actions-bar::before,
        .users-table::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            background: linear-gradient(90deg, #2563eb, #8b5cf6, #06b6d4);
        }

        .stat-card {
            padding: 30px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .stat-value {
            color: #0f172a;
            font-size: 2.45rem;
            font-weight: 750;
            letter-spacing: -0.02em;
        }

        .stat-label {
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .actions-bar {
            padding: 24px;
        }

        .filter-group label {
            color: #64748b;
            font-weight: 650;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .filter-group select,
        .filter-group input,
        .search-box,
        .form-group input {
            min-height: 46px;
            border-radius: 16px;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(203, 213, 225, 0.95);
        }

        .btn {
            min-height: 40px;
            border-radius: 999px !important;
            font-weight: 650;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #7c3aed) !important;
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

        .btn-outline {
            background: rgba(248, 250, 252, 0.92) !important;
            color: #475569 !important;
            border: 1px solid rgba(203, 213, 225, 0.95) !important;
        }

        .table-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.98), rgba(245, 243, 255, 0.96));
            border-bottom: 1px solid rgba(226, 232, 240, 0.72);
        }

        .table-header h3 {
            color: #0f172a;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        th {
            background: rgba(248, 250, 252, 0.9);
            color: #64748b;
            font-weight: 650;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        td {
            color: #334155;
            vertical-align: middle;
        }

        tr:hover {
            background: rgba(239, 246, 255, 0.58);
        }

        .user-avatar {
            background: linear-gradient(135deg, #2563eb, #7c3aed) !important;
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.22);
        }

        .user-details h4 {
            color: #0f172a;
            font-weight: 850;
        }

        .user-type,
        .user-status {
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 850;
            white-space: nowrap;
        }

        .modal-overlay {
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(10px);
            z-index: 2500;
        }

        .modal-content {
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 34px 95px rgba(15, 23, 42, 0.32);
        }

        .modal-header {
            background: linear-gradient(135deg, #1e3a8a, #2563eb, #7c3aed);
            border-radius: 28px 28px 0 0;
            padding: 24px 28px;
        }

        .modal-header h3 {
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .modal-body {
            padding: 28px;
        }

        .form-group label {
            color: #64748b;
            font-weight: 650;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            font-size: 0.78rem;
        }

        .toast-custom {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 3000;
            min-width: 300px;
            max-width: 420px;
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

        .toast-custom.success {
            border-left-color: #10b981;
        }

        .toast-custom.error {
            border-left-color: #ef4444;
        }

        .confirm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2600;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(10px);
        }

        .confirm-dialog {
            width: min(460px, 100%);
            overflow: hidden;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 34px 95px rgba(15, 23, 42, 0.32);
        }

        .confirm-dialog-header {
            padding: 26px 28px;
            color: #ffffff;
            background: linear-gradient(135deg, #1e3a8a, #2563eb, #7c3aed);
        }

        .confirm-dialog-title {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .confirm-dialog-body {
            padding: 24px 28px 28px;
        }

        .confirm-dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 22px;
        }

        @media (max-width: 768px) {
            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filters {
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                font-size: 0.9em;
            }
            
            th, td {
                padding: 10px 15px;
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
        .dark-mode .table-header h3,
        .dark-mode .user-details h4 {
            color: #ffffff !important;
        }

        .dark-mode .stat-card,
        .dark-mode .actions-bar,
        .dark-mode .users-table {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 22px 58px rgba(0, 0, 0, 0.25) !important;
        }

        .dark-mode .stat-value,
        .dark-mode .detail-value {
            color: #ffffff !important;
        }

        .dark-mode .stat-label,
        .dark-mode .filter-group label,
        .dark-mode .user-details .text-muted,
        .dark-mode .no-users {
            color: #cbd5e1 !important;
        }

        .dark-mode .table-header {
            background: rgba(255, 255, 255, 0.02) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        }

        .dark-mode th {
            background: rgba(255, 255, 255, 0.04) !important;
            color: #94a3b8 !important;
        }

        .dark-mode td {
            color: #f8fafc !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        .dark-mode tr:hover {
            background: rgba(255, 255, 255, 0.02) !important;
        }

        /* Badges Dark Mode */
        .dark-mode .type-professor {
            background: rgba(245, 158, 11, 0.2) !important;
            color: #fcd34d !important;
            border: 1px solid rgba(245, 158, 11, 0.3) !important;
        }
        .dark-mode .type-aluno {
            background: rgba(99, 102, 241, 0.2) !important;
            color: #a5b4fc !important;
            border: 1px solid rgba(99, 102, 241, 0.3) !important;
        }
        .dark-mode .type-admin {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }
        .dark-mode .status-ativo {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #6ee7b7 !important;
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
        }
        .dark-mode .status-inativo {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #fca5a5 !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
        }
        .dark-mode .empty-dash {
            color: rgba(255, 255, 255, 0.2) !important;
        }

        /* Botões de Ação Dark Mode */
        .dark-mode .btn-action-edit {
            background: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
        }
        .dark-mode .btn-action-edit:hover { background: #10b981 !important; color: #fff !important; }
        
        .dark-mode .btn-action-disable {
            background: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
        }
        .dark-mode .btn-action-disable:hover { background: #f59e0b !important; color: #fff !important; }

        .dark-mode .btn-action-enable {
            background: rgba(59, 130, 246, 0.15) !important;
            color: #60a5fa !important;
        }
        .dark-mode .btn-action-enable:hover { background: #3b82f6 !important; color: #fff !important; }

        .dark-mode .btn-action-delete {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
        }
        .dark-mode .btn-action-delete:hover { background: #ef4444 !important; color: #fff !important; }

        .dark-mode .btn-outline {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .btn-outline:hover {
            background: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
        }

        .dark-mode .filter-group select,
        .dark-mode .filter-group input,
        .dark-mode .search-box,
        .dark-mode .form-group input,
        .dark-mode .form-group select {
            background: #0f172a !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Modals and Dialogs */
        .dark-mode .modal-content,
        .dark-mode .confirm-dialog {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }

        .dark-mode .modal-header,
        .dark-mode .confirm-dialog-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .form-group label {
            color: #cbd5e1 !important;
        }

        .dark-mode .toast-custom {
            background: #1e293b !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
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
.header .back-btn, .header .back-link {
    position: relative !important; top: auto !important; left: auto !important;
    padding: 10px 18px !important; border-radius: 999px !important;
    color: #ffffff !important; background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.24) !important;
    backdrop-filter: blur(16px) !important; z-index: 10 !important;
    display: inline-flex !important; align-items: center !important; gap: 8px !important;
    text-decoration: none !important; font-weight: 600 !important;
}
.header .back-btn:hover, .header .back-link:hover { background: rgba(255, 255, 255, 0.25) !important; }
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
.stat-card {
    border-left: none !important;
    position: relative !important;
    overflow: hidden !important;
}
.stat-card::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important; left: 0 !important; right: 0 !important;
    height: 4px !important;
    background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
}
.stat-card.professores, .stat-card.alunos, .stat-card.inativos {
    border-left: none !important;
}

/* === Remover laranja/amber === */
.stat-card.professores { border-left-color: #2563eb !important; }
.btn-warning { background: #2563eb !important; }
.btn-warning:hover { background: #1e40af !important; }
.type-professor { background: #eff6ff !important; color: #1e40af !important; }
.btn-action-disable { background: rgba(37, 99, 235, 0.12) !important; color: #2563eb !important; }
.btn-action-disable:hover { background: #2563eb !important; color: #fff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important; }
.dark-mode .btn-action-disable { background: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
.dark-mode .btn-action-disable:hover { background: #3b82f6 !important; color: #fff !important; }
.dark-mode .btn-warning { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }

/* === Remover verde/teal === */
.actions-bar::before, .users-table::before {
    background: linear-gradient(90deg, #2563eb, #6366f1, #10b981) !important;
}
.btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; color: #fff !important; }
.btn-success:hover { background: linear-gradient(135deg, #1e40af, #172554) !important; }
.btn-action-edit { background: rgba(37, 99, 235, 0.12) !important; color: #2563eb !important; }
.btn-action-edit:hover { background: #2563eb !important; color: #fff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important; }
.dark-mode .btn-action-edit { background: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
.dark-mode .btn-action-edit:hover { background: #3b82f6 !important; color: #fff !important; }
.dark-mode .btn-success { background: linear-gradient(135deg, #2563eb, #1e40af) !important; }

/* === Container simples (sem card) === */
body { background: #f1f5f9 !important; padding: 0 !important; }
.container { background: transparent !important; border-radius: 0 !important; box-shadow: none !important; overflow: visible !important; border: none !important; padding: 24px !important; }
.page-content { padding: 0; }

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
                <h1>Gestão de Usuários</h1>
                <p>Gerencie professores, alunos e permissões do sistema</p>
            </div>
        </div>
        <div class="page-content">

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <h3 class="stat-title">Total de Usuários</h3>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Total no sistema</div>
                <div class="progress-bar"><div class="progress-fill" style="width: 100%"></div></div>
            </div>
            <div class="stat-card professores">
                <div class="stat-header">
                    <h3 class="stat-title">Professores Ativos</h3>
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['professores']; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Professores ativos</div>
                <div class="progress-bar"><div class="progress-fill warning" style="width: <?php echo $stats['total'] > 0 ? min(($stats['professores'] / $stats['total']) * 100, 100) : 0; ?>%"></div></div>
            </div>
            <div class="stat-card alunos">
                <div class="stat-header">
                    <h3 class="stat-title">Alunos Ativos</h3>
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['alunos']; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Alunos cadastrados</div>
                <div class="progress-bar"><div class="progress-fill purple" style="width: <?php echo $stats['total'] > 0 ? min(($stats['alunos'] / $stats['total']) * 100, 100) : 0; ?>%"></div></div>
            </div>
            <div class="stat-card inativos">
                <div class="stat-header">
                    <h3 class="stat-title">Inativos</h3>
                    <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
                </div>
                <div class="stat-value"><?php echo $stats['inativos']; ?></div>
                <div class="stat-change"><i class="fas fa-arrow-up stat-change-icon"></i> Usuários inativos</div>
                <div class="progress-bar"><div class="progress-fill danger" style="width: <?php echo $stats['total'] > 0 ? min(($stats['inativos'] / $stats['total']) * 100, 100) : 0; ?>%"></div></div>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="actions-bar">
            <div class="filters">
                <div class="filter-group">
                    <label>Tipo de Usuário</label>
                    <select id="filterType" onchange="applyFilters()">
                        <option value="">Todos os tipos</option>
                        <option value="professor" <?php echo $filtro_tipo === 'professor' ? 'selected' : ''; ?>>Professores</option>
                        <option value="aluno" <?php echo $filtro_tipo === 'aluno' ? 'selected' : ''; ?>>Alunos</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Status</label>
                    <select id="filterStatus" onchange="applyFilters()">
                        <option value="">Todos os status</option>
                        <option value="1" <?php echo $filtro_status === '1' ? 'selected' : ''; ?>>Ativos</option>
                        <option value="0" <?php echo $filtro_status === '0' ? 'selected' : ''; ?>>Inativos</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="searchBox">Buscar</label>
                    <input type="text" id="searchBox" class="search-box" placeholder="Buscar usuários..." 
                           value="<?php echo htmlspecialchars($busca); ?>" oninput="applyFilters()">
                </div>
            </div>

            <a href="#" class="btn btn-primary" onclick="showNovoUsuarioModal()"><i class="fas fa-user-plus"></i> Novo Usuário</a>
        </div>

        <!-- Users Table -->
        <div class="users-table">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Lista de Usuários (<?php echo count($usuarios); ?> encontrados)</h3>
            </div>
            
            <div class="table-container">
                <?php if (empty($usuarios)): ?>
                    <div class="no-users">
                        <h3><i class="fas fa-users-slash"></i> Nenhum usuário encontrado</h3>
                        <p>Não há usuários que correspondam aos filtros aplicados.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 28%;">Usuário</th>
                                <th style="width: 12%;">Tipo</th>
                                <th style="width: 18%;">Formação/Especialidade</th>
                                <th style="width: 12%;">Valor/Hora</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 10%;">Data de Criação</th>
                                <th style="width: 10%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($usuario['nome'], 0, 1)); ?>
                                            </div>
                                            <div class="user-details">
                                                <h4><?php echo htmlspecialchars($usuario['nome']); ?></h4>
                                                <div class="user-email"><?php echo htmlspecialchars($usuario['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="user-type type-<?php echo $usuario['tipo_usuario']; ?>">
                                            <?php echo ucfirst($usuario['tipo_usuario']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($usuario['tipo_usuario'] === 'professor'): ?>
                                            <?php echo htmlspecialchars($usuario['formacao'] ?: 'Não informado'); ?>
                                        <?php else: ?>
                                            <span class="empty-dash">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($usuario['tipo_usuario'] === 'professor'): ?>
                                            R$ <?php echo number_format($usuario['valor_hora'], 2, ',', '.'); ?>/h
                                        <?php else: ?>
                                            <span class="empty-dash">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="user-status status-<?php echo $usuario['ativo'] ? 'ativo' : 'inativo'; ?>">
                                            <?php echo $usuario['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($usuario['criado_em'])); ?>
                                    </td>
                                    <td>
                                        <div class="user-actions">
                                            <button class="btn-action btn-action-edit" onclick="editarUsuario(<?php echo $usuario['id']; ?>)">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <?php if ($usuario['ativo']): ?>
                                                <button class="btn-action btn-action-disable" onclick="desativarUsuario(<?php echo $usuario['id']; ?>)">
                                                    <i class="fas fa-ban"></i> Desativar
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action btn-action-enable" onclick="ativarUsuario(<?php echo $usuario['id']; ?>)">
                                                    <i class="fas fa-play"></i> Ativar
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn-action btn-action-delete" onclick="excluirUsuario(<?php echo $usuario['id']; ?>)">
                                                <i class="fas fa-trash-alt"></i> Excluir
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        </div><!-- /page-content -->
    </div>

    <div id="confirmOverlay" class="confirm-overlay">
        <div class="confirm-dialog">
            <div class="confirm-dialog-header">
                <div class="confirm-dialog-title" id="confirmTitle">Confirmar ação</div>
            </div>
            <div class="confirm-dialog-body">
                <p id="confirmMessage">Deseja continuar?</p>
                <div class="confirm-dialog-actions">
                    <button type="button" class="btn btn-outline" onclick="closeConfirmDialog()">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Aplicar filtros
        function applyFilters() {
            const tipo = document.getElementById('filterType').value;
            const status = document.getElementById('filterStatus').value;
            const busca = document.getElementById('searchBox').value;
            
            const params = new URLSearchParams();
            if (tipo) params.append('tipo', tipo);
            if (status !== '') params.append('status', status);
            if (busca) params.append('busca', busca);
            
            const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.location.href = url;
        }

        // FunÃ§Ãµes dos botÃµes de aÃ§Ã£o
        function showNovoUsuarioModal() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>ðŸ‘¤ Novo UsuÃ¡rio</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="novoUsuarioForm">
                            <div class="form-group">
                                <label>Nome:</label>
                                <input type="text" name="nome" required>
                            </div>
                            <div class="form-group">
                                <label>E-mail:</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Senha:</label>
                                <input type="password" name="senha" minlength="6" required>
                            </div>
                            <div class="form-group">
                                <label>Tipo de usuário:</label>
                                <select name="tipo_usuario" id="novoTipoUsuario" required onchange="toggleNovoUsuarioCampos()">
                                    <option value="aluno">Aluno</option>
                                    <option value="professor">Professor</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Telefone:</label>
                                <input type="text" name="telefone" placeholder="(11) 99999-9999">
                            </div>
                            <div id="novoUsuarioCamposProfessor" style="display:none;">
                                <div class="form-group">
                                    <label>Formação:</label>
                                    <input type="text" name="formacao" placeholder="Ex: Matemática, Engenharia...">
                                </div>
                                <div class="form-group">
                                    <label>Valor/Hora (R$):</label>
                                    <input type="number" name="valor_hora" step="0.01" min="0" placeholder="Ex: 80.00">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Criar Usuário</button>
                                <button type="button" onclick="closeModal()" class="btn btn-outline"><i class="fas fa-times"></i> Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            toggleNovoUsuarioCampos();

            document.getElementById('novoUsuarioForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                await salvarNovoUsuario();
            });
        }

        function toggleNovoUsuarioCampos() {
            const tipo = document.getElementById('novoTipoUsuario')?.value;
            const camposProfessor = document.getElementById('novoUsuarioCamposProfessor');
            if (!camposProfessor) return;
            camposProfessor.style.display = tipo === 'professor' ? 'block' : 'none';
        }

        async function salvarNovoUsuario() {
            const form = document.getElementById('novoUsuarioForm');
            if (!form) return;
            const formData = new FormData(form);

            const payload = {
                action: 'criar_usuario',
                nome: formData.get('nome'),
                email: formData.get('email'),
                senha: formData.get('senha'),
                tipo_usuario: formData.get('tipo_usuario'),
                telefone: formData.get('telefone') || '',
                formacao: formData.get('formacao') || '',
                valor_hora: formData.get('valor_hora') || ''
            };

            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                const errorMessage = result?.message || result?.error || 'Erro desconhecido';

                if (result.success) {
                    showToast('Usuário criado com sucesso!', 'success');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                } else {
                    showToast('Erro ao criar usuário: ' + errorMessage, 'error');
                }
            } catch (error) {
                showToast('Erro de conexão ao criar usuário: ' + error.message, 'error');
            }
        }
        
        // Função para editar usuário
        async function editarUsuario(id) {
            try {
                const response = await fetch(`api/usuarios_fallback.php?action=buscar_usuario&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    const usuario = result.data;
                    showEditarUsuarioModal(usuario);
                } else {
                    showToast('Erro ao buscar dados do usuário: ' + result.message, 'error');
                }
            } catch (error) {
                showToast('Erro de conexão ao editar usuário', 'error');
            }
        }
        
        // Função para desativar usuário
        async function desativarUsuario(id) {
            showConfirmDialog('Desativar usuário?', 'Tem certeza que deseja desativar este usuário?', async function() {
                try {
                    console.log('Enviando requisição para desativar usuário ID:', id);
                    
                    const requestData = {
                        action: 'desativar_usuario',
                        id: id
                    };
                    
                    console.log('Dados enviados:', requestData);
                    
                    const response = await fetch('api/usuarios_fallback.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    console.log('Resposta recebida:', response);
                    
                    const result = await response.json();
                    console.log('Resultado:', result);
                    
                    if (result.success) {
                        showToast('Usuário desativado com sucesso!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('Erro ao desativar usuário: ' + result.message, 'error');
                        if (result.debug) {
                            console.log('Debug da API:', result.debug);
                        }
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showToast('Erro de conexão ao desativar usuário: ' + error.message, 'error');
                }
            });
        }
        
        // Função para ativar usuário
        async function ativarUsuario(id) {
            showConfirmDialog('Ativar usuário?', 'Tem certeza que deseja ativar este usuário?', async function() {
                try {
                    console.log('Enviando requisição para ativar usuário ID:', id);
                    
                    const requestData = {
                        action: 'ativar_usuario',
                        id: id
                    };
                    
                    console.log('Dados enviados:', requestData);
                    
                    const response = await fetch('api/usuarios_fallback.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    console.log('Resposta recebida:', response);
                    
                    const result = await response.json();
                    console.log('Resultado:', result);
                    
                    if (result.success) {
                        showToast('Usuário ativado com sucesso!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('Erro ao ativar usuário: ' + result.message, 'error');
                        if (result.debug) {
                            console.log('Debug da API:', result.debug);
                        }
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showToast('Erro de conexão ao ativar usuário: ' + error.message, 'error');
                }
            });
        }
        
        // Função para excluir usuário
        async function excluirUsuario(id) {
            showConfirmDialog('Excluir usuário?', 'Atenção: esta ação é irreversível. Tem certeza que deseja excluir este usuário?', async function() {
                try {
                    console.log('Enviando requisição para excluir usuário ID:', id);
                    
                    const requestData = {
                        action: 'excluir_usuario',
                        id: id
                    };
                    
                    console.log('Dados enviados:', requestData);
                    
                    const response = await fetch('api/usuarios.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    console.log('Resposta recebida:', response);
                    
                    const result = await response.json();
                    console.log('Resultado:', result);
                    
                    if (result.success) {
                        showToast('Usuário excluído com sucesso!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast('Erro ao excluir usuário: ' + result.message, 'error');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showToast('Erro de conexão ao excluir usuário: ' + error.message, 'error');
                }
            });
        }
        
        // Função para mostrar modal de edição
        function showEditarUsuarioModal(usuario) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-edit"></i> Editar Usuário</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editarUsuarioForm">
                            <div class="form-group">
                                <label>Nome:</label>
                                <input type="text" name="nome" value="${usuario.nome}" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" value="${usuario.email}" required>
                            </div>
                            <div class="form-group">
                                <label>Telefone:</label>
                                <input type="text" name="telefone" value="${usuario.telefone || ''}">
                            </div>
                            ${usuario.tipo_usuario === 'professor' ? `
                                <div class="form-group">
                                    <label>Formação:</label>
                                    <input type="text" name="formacao" value="${usuario.formacao || ''}">
                                </div>
                                <div class="form-group">
                                    <label>Valor/Hora:</label>
                                    <input type="number" name="valor_hora" value="${usuario.valor_hora || ''}" step="0.01">
                                </div>
                            ` : ''}
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                                <button type="button" onclick="closeModal()" class="btn btn-outline"><i class="fas fa-times"></i> Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Configurar envio do formulário
            document.getElementById('editarUsuarioForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                await salvarEdicaoUsuario(usuario.id);
            });
        }
        
        // Função para salvar edição do usuário
        async function salvarEdicaoUsuario(id) {
            const form = document.getElementById('editarUsuarioForm');
            const formData = new FormData(form);
            
            try {
                const response = await fetch('api/usuarios_fallback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'editar_usuario',
                        id: id,
                        nome: formData.get('nome'),
                        email: formData.get('email'),
                        telefone: formData.get('telefone'),
                        formacao: formData.get('formacao'),
                        valor_hora: formData.get('valor_hora')
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Usuário atualizado com sucesso!', 'success');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast('Erro ao atualizar usuário: ' + result.message, 'error');
                }
            } catch (error) {
                showToast('Erro de conexão ao atualizar usuário', 'error');
            }
        }
        
        // Função para fechar modal
        function closeModal() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
        }

        function showConfirmDialog(title, message, onConfirm) {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmActionBtn').onclick = async function() {
                closeConfirmDialog();
                await onConfirm();
            };
            document.getElementById('confirmOverlay').style.display = 'flex';
        }

        function closeConfirmDialog() {
            document.getElementById('confirmOverlay').style.display = 'none';
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast-custom ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(function() {
                toast.remove();
            }, 3600);
        }
    </script>
    <script src="dark-mode.js"></script>
</body>
</html>



















