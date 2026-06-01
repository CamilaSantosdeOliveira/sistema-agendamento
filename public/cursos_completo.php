<?php
header('Content-Type: text/html; charset=UTF-8');
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar cursos do banco de dados com consulta específica
$cursos = [];
try {
    $result = $conn->query("SELECT id, nome, descricao, categoria, nivel, preco, duracao_horas, status FROM cursos ORDER BY nome");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Garantir que categoria e nível tenham valores
            $row['categoria'] = $row['categoria'] ?: 'Tecnologia';
            $row['nivel'] = $row['nivel'] ?: 'Intermediário';
            $cursos[] = $row;
        }
    }
} catch (Exception $e) {
    $cursos = [];
}

// Buscar categorias únicas para o filtro
$categorias = [];
try {
    $cat_result = $conn->query("SELECT DISTINCT categoria FROM cursos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
    if ($cat_result) {
        while ($row = $cat_result->fetch_assoc()) {
            $categorias[] = $row['categoria'];
        }
    }
} catch (Exception $e) {
    $categorias = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode-init">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Cursos de Tecnologia - Sistema de Agendamento</title>
    <script>
        // Script de bloqueio para evitar o flash de luz (FOUC)
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
            // Aplicar estilos mínimos imediatamente vinculados à classe de inicialização
            document.write('<style>.dark-mode-init body { visibility: hidden; background: #0f172a !important; }</style>');
        }
    </script>
    <link rel="stylesheet" href="dark-mode.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 20px;
            color: #1e293b;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
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

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 12px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            font-weight: 400;
        }

        .actions {
            padding: 32px 40px;
            background: #ffffff;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
        }

        .btn {
            padding: 12px 24px;
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
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            color: white;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(59, 130, 246, 0.35);
        }

        .btn-secondary {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.1);
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
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select, .filter-group input {
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            min-width: 160px;
            background: white;
            transition: all 0.3s ease;
            color: #1e293b;
        }
        
        .filter-group select:focus, .filter-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .search-box {
            padding: 12px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            min-width: 280px;
            outline: none;
            background: white;
            transition: all 0.3s ease;
            color: #1e293b;
        }

        .search-box:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .search-box::placeholder {
            color: #94a3b8;
        }

        .courses-grid {
            padding: 48px 40px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 32px;
            background: #f8fafc;
        }

        .course-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #2563eb;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .course-card:hover::before {
            opacity: 1;
        }

        .course-header {
            padding: 25px;
            border-bottom: 1px solid #f3f4f6;
        }

        .course-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .status-ativo {
            background: #dcfce7;
            color: #166534;
        }

        .status-em_breve {
            background: #fef3c7;
            color: #92400e;
        }

        .status-inativo {
            background: #fee2e2;
            color: #991b1b;
        }

        .course-title {
            font-size: 1.3em;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .course-description {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .course-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4b5563;
            font-size: 14px;
        }

        .meta-icon {
            color: #2563eb;
            margin-right: 8px;
            font-size: 14px;
            width: auto;
            height: auto;
            background: none;
            border-radius: 0;
            display: inline-block;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #6b7280;
        }

        .course-footer {
            padding: 20px 25px;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-price {
            font-size: 1.5em;
            font-weight: 800;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .course-actions {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn-success {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.25);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #1e40af 0%, #172554 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(37, 99, 235, 0.35);
        }

        .btn-outline {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-outline:hover {
            background: #f1f5f9;
            color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.1);
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-item {
            text-align: center;
            color: #6b7280;
        }

        .stat-value {
            font-size: 1.2em;
            font-weight: 700;
            color: #1f2937;
        }

        .no-courses {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .no-courses h3 {
            color: #374151;
            margin-bottom: 8px;
        }

        body {
            background:
                radial-gradient(circle at 12% 8%, rgba(37, 99, 235, 0.14), transparent 28%),
                radial-gradient(circle at 88% 14%, rgba(16, 185, 129, 0.12), transparent 30%),
                linear-gradient(135deg, #f8fafc 0%, #eef2ff 46%, #f8fafc 100%);
            background-attachment: fixed;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.78);
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(18px);
        }

        .header {
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.22), transparent 34%),
                linear-gradient(135deg, #172554 0%, #1e40af 52%, #2563eb 100%);
        }

        .header h1 {
            letter-spacing: -0.055em;
        }

        .btn-back,
        .btn,
        button,
        input,
        select,
        textarea {
            border-radius: 999px;
        }

        input,
        select,
        textarea {
            border-color: rgba(203, 213, 225, 0.9);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .curso-card,
        .course-card,
        .card,
        .filter-section,
        .filters,
        .stats-card,
        .stat-card {
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(255, 255, 255, 0.78) !important;
            border-radius: 24px !important;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(18px);
        }

        .curso-card:hover,
        .course-card:hover,
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 70px rgba(15, 23, 42, 0.14) !important;
        }

        body { background: #f1f5f9 !important; padding: 0 !important; }

        .container {
            max-width: 1380px;
            background: transparent !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
            border: none !important;
            padding: 24px !important;
        }

        .header {
            position: relative;
            min-height: 250px;
            border-radius: 30px;
            margin-bottom: 24px;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
                radial-gradient(circle at 84% 26%, rgba(255, 255, 255, 0.12), transparent 34%),
                linear-gradient(135deg, #172554 0%, #1e40af 52%, #2563eb 100%) !important;
            box-shadow: 0 28px 80px rgba(37, 99, 235, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.42);
        }

        .header::before {
            opacity: 0.18;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px),
                linear-gradient(rgba(255, 255, 255, 0.28) 1px, transparent 1px);
            background-size: 42px 42px;
        }

        .header h1 {
            font-size: clamp(1.85rem, 3.6vw, 2.85rem);
            font-weight: 850;
            letter-spacing: -0.055em;
        }

        .header p {
            max-width: 720px;
            margin: 0 auto;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.65;
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

        .header .btn-back {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            padding: 10px 18px !important;
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.24) !important;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
        }

        .header-content {
            display: block !important;
            text-align: center !important;
            padding: 0 !important;
            position: relative !important;
            z-index: 1 !important;
        }

        .actions {
            position: sticky;
            top: 16px;
            z-index: 20;
            margin-bottom: 24px;
            padding: 22px;
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(255, 255, 255, 0.82);
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09);
            backdrop-filter: blur(20px);
        }

        .filters {
            flex: 1;
            gap: 16px;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
        }

        .filter-group label {
            color: #475569;
            font-size: 0.72rem;
            font-weight: 850;
            letter-spacing: 0.09em;
        }

        .filter-group select,
        .filter-group input,
        .search-box {
            min-height: 48px;
            border-radius: 16px !important;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(203, 213, 225, 0.95);
            color: #0f172a;
        }

        .filter-group select:focus,
        .filter-group input:focus,
        .search-box:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .actions > .btn {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 14px 34px rgba(37, 99, 235, 0.16);
        }

        .courses-grid {
            padding: 0 0 48px;
            background: transparent;
            grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
            gap: 26px;
        }

        .course-card {
            isolation: isolate;
            display: flex;
            flex-direction: column;
            min-height: 430px;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.94) !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
        }

        .course-card::before {
            height: 6px;
            opacity: 1;
            background: linear-gradient(90deg, #2563eb, #6366f1, #10b981);
        }

        .course-card::after { content: none; }

        .course-card:hover {
            transform: translateY(-9px);
            border-color: rgba(37, 99, 235, 0.3) !important;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .course-header {
            flex: 1;
            padding: 30px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.72);
        }

        .course-status {
            margin-bottom: 18px;
            letter-spacing: 0.06em;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .course-title {
            color: #0f172a;
            font-size: 1.42rem;
            font-weight: 850;
            letter-spacing: -0.045em;
        }

        .course-description {
            min-height: 74px;
            color: #64748b;
            line-height: 1.7;
        }

        .stats {
            padding: 14px;
            border-radius: 18px;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.75);
        }

        .stat-value {
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .course-meta {
            margin-bottom: 0;
        }

        .meta-item {
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.88);
            border: 1px solid rgba(226, 232, 240, 0.75);
            color: #475569;
            font-weight: 650;
        }

        .meta-icon {
            color: #2563eb;
            background: #dbeafe;
            width: 28px;
            height: 28px;
            margin-right: 0;
        }

        .course-footer {
            padding: 22px 30px 30px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.6), rgba(255, 255, 255, 0.96));
            gap: 16px;
        }

        .course-price {
            font-size: 1.65rem;
            letter-spacing: -0.055em;
        }

        .course-actions {
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            border-radius: 999px !important;
            font-weight: 850;
        }

        .btn-sm {
            min-height: 38px;
            padding: 9px 15px;
        }

        @media (max-width: 768px) {
            body {
                padding: 12px;
            }

            .header {
                min-height: 230px;
                padding: 78px 20px 42px;
                border-radius: 24px;
            }

            .header .btn-back {
                top: 18px;
                left: 18px;
            }

            .actions {
                position: static;
            }

            .filter-group,
            .filter-group select,
            .filter-group input,
            .search-box {
                width: 100%;
                min-width: 100%;
            }

            .course-footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .course-actions {
                justify-content: flex-start;
                width: 100%;
            }
        }

        .course-modal-overlay {
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

        .course-modal {
            width: min(440px, 100%);
            overflow: hidden;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 34px 95px rgba(15, 23, 42, 0.32);
        }

        .course-modal-header {
            padding: 20px 24px;
            color: #ffffff;
            background: linear-gradient(135deg, #064e3b, #2563eb, #7c3aed);
        }

        .course-modal-title {
            font-size: 1.08rem;
            font-weight: 850;
            letter-spacing: -0.04em;
        }

        .course-modal-subtitle {
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.92rem;
        }

        .course-modal-body {
            padding: 20px 24px 24px;
        }

        .course-form-group {
            margin-bottom: 16px;
        }

        .course-form-group label {
            display: block;
            margin-bottom: 7px;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 850;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .course-form-group input {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(203, 213, 225, 0.95);
            font-size: 0.92rem;
        }

        .course-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 22px;
        }

        .course-toast {
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

        .course-toast.success {
            border-left-color: #10b981;
        }

        .course-toast.error {
            border-left-color: #ef4444;
        }

        .course-detail-list {
            display: grid;
            gap: 8px;
        }

        .course-detail-item {
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.88);
            border: 1px solid rgba(226, 232, 240, 0.78);
        }

        .course-detail-label {
            display: block;
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 850;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .course-detail-value {
            display: block;
            margin-top: 2px;
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 750;
        }

        /* ===== Modo escuro — página de cursos ===== */
        .dark-mode body,
        body.dark-mode {
            background: #0f172a !important;
            color: #f8fafc !important;
        }

        .dark-mode .container {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .dark-mode .actions {
            background: rgba(30, 41, 59, 0.95) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .filter-group label {
            color: #94a3b8 !important;
        }

        .dark-mode .filter-group select,
        .dark-mode .filter-group input,
        .dark-mode .search-box {
            background: rgba(15, 23, 42, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }

        .dark-mode .filter-group select option {
            background: #1e293b;
            color: #f8fafc;
        }

        .dark-mode .courses-grid {
            background: transparent !important;
        }

        .dark-mode .course-card,
        .dark-mode .curso-card {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .course-header {
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .course-title {
            color: #f8fafc !important;
        }

        .dark-mode .course-description {
            color: #94a3b8 !important;
        }

        .dark-mode .stats {
            background: rgba(15, 23, 42, 0.65) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .stat-item {
            color: #94a3b8 !important;
        }

        .dark-mode .stat-value {
            color: #f8fafc !important;
        }

        .dark-mode .meta-item {
            background: rgba(15, 23, 42, 0.55) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #cbd5e1 !important;
        }

        .dark-mode .meta-icon {
            background: rgba(37, 99, 235, 0.25) !important;
            color: #93c5fd !important;
        }

        .dark-mode .course-footer {
            background: rgba(15, 23, 42, 0.8) !important;
        }

        .dark-mode .course-price {
            background: linear-gradient(135deg, #34d399, #10b981) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }

        .dark-mode .btn-outline,
        .dark-mode .btn-secondary {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #e2e8f0 !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
        }

        .dark-mode .btn-outline:hover {
            background: rgba(37, 99, 235, 0.35) !important;
            color: #ffffff !important;
        }

        .dark-mode .status-ativo {
            background: rgba(16, 185, 129, 0.22) !important;
            color: #6ee7b7 !important;
        }

        .dark-mode .status-em_breve {
            background: rgba(245, 158, 11, 0.22) !important;
            color: #fcd34d !important;
        }

        .dark-mode .status-inativo {
            background: rgba(239, 68, 68, 0.22) !important;
            color: #fca5a5 !important;
        }

        .dark-mode .no-courses,
        .dark-mode .no-courses h3,
        .dark-mode .no-courses p {
            color: #cbd5e1 !important;
        }

        .dark-mode .course-modal {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dark-mode .course-modal-body {
            color: #f8fafc !important;
        }

        .dark-mode .course-form-group label,
        .dark-mode .course-detail-label {
            color: #94a3b8 !important;
        }

        .dark-mode .course-form-group input {
            background: rgba(15, 23, 42, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }

        .dark-mode .course-detail-item {
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .dark-mode .course-detail-value {
            color: #f8fafc !important;
        }

        .dark-mode .course-toast {
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

</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-top">
                <a href="dashboard_final.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                </a>
                <div class="header-actions">
                    <button id="darkModeToggle" title="Alternar tema" aria-label="Alternar Dark Mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
            <div class="header-content">
                <h1>Cursos de Tecnologia</h1>
                <p>Explore nossa coleção completa de cursos profissionalizantes</p>
            </div>
        </div>

        <div class="actions">
            <div class="filters">
                <div class="filter-group">
                    <label>Categoria</label>
                    <select id="filterCategory">
                        <option value="">Todas as categorias</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo htmlspecialchars($categoria); ?>"><?php echo htmlspecialchars($categoria); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Nível</label>
                    <select id="filterLevel">
                        <option value="">Todos os níveis</option>
                        <option value="Básico">Básico</option>
                        <option value="Intermediário">Intermediário</option>
                        <option value="Avançado">Avançado</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Preço</label>
                    <select id="filterPrice">
                        <option value="">Qualquer preço</option>
                        <option value="0-200">Até R$ 200</option>
                        <option value="200-400">R$ 200 - R$ 400</option>
                        <option value="400+">Acima de R$ 400</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Duração</label>
                    <select id="filterDuration">
                        <option value="">Qualquer duração</option>
                        <option value="0-50">Até 50 horas</option>
                        <option value="50-80">50 - 80 horas</option>
                        <option value="80+">Acima de 80 horas</option>
                    </select>
                </div>
            </div>

            <input type="text" id="searchBox" class="search-box" placeholder="Pesquisar cursos...">
        </div>

        <div class="courses-grid" id="coursesGrid">
            <?php if (empty($cursos)): ?>
                <div class="no-courses">
                    <h3><i class="fas fa-book"></i> Nenhum curso encontrado</h3>
                    <p>Não há cursos disponíveis no momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card" 
                         data-category="<?php echo htmlspecialchars($curso['categoria'] ?: 'Tecnologia'); ?>"
                         data-level="<?php echo htmlspecialchars($curso['nivel'] ?: 'Intermediário'); ?>"
                         data-price="<?php echo $curso['preco']; ?>"
                         data-duration="<?php echo $curso['duracao_horas']; ?>"
                         data-name="<?php echo strtolower(htmlspecialchars($curso['nome'])); ?>">
                        
                        <div class="course-header">
                            <span class="course-status status-<?php echo $curso['status']; ?>">
                                <?php echo ucfirst($curso['status']); ?>
                            </span>
                            
                            <h3 class="course-title"><?php echo htmlspecialchars($curso['nome']); ?></h3>
                            <p class="course-description"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                            
                            <div class="stats">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $curso['duracao_horas']; ?></div>
                                    <div>horas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo isset($curso['alunos_inscritos']) ? $curso['alunos_inscritos'] : '0'; ?></div>
                                    <div>alunos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo isset($curso['avaliacao']) ? $curso['avaliacao'] : '4.5'; ?>/5.0</div>
                                    <div>avaliação</div>
                                </div>
                            </div>
                            
                            <div class="course-meta">
                                <div class="meta-item">
                                    <i class="fas fa-book meta-icon"></i>
                                    <span><?php echo htmlspecialchars($curso['categoria'] ?: 'Tecnologia'); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-bullseye meta-icon"></i>
                                    <span><?php echo htmlspecialchars($curso['nivel'] ?: 'Intermediário'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="course-footer">
                            <div class="course-price">R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></div>
                            <div class="course-actions">
                                <?php if ($curso['status'] === 'ativo'): ?>
                                    <button class="btn btn-sm btn-success" onclick="inscreverCurso(<?php echo $curso['id']; ?>, '<?php echo htmlspecialchars($curso['nome']); ?>')">Inscrever-se</button>
                                <?php elseif ($curso['status'] === 'em_breve'): ?>
                                    <button class="btn btn-sm btn-outline" onclick="adicionarListaEspera(<?php echo $curso['id']; ?>, '<?php echo htmlspecialchars($curso['nome']); ?>')">Lista de Espera</button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline" onclick="verDetalhesCurso(<?php echo $curso['id']; ?>)">Ver Detalhes</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="courseModalOverlay" class="course-modal-overlay">
        <div class="course-modal">
            <div class="course-modal-header">
                <div class="course-modal-title" id="courseModalTitle">Cursos Tech</div>
                <div class="course-modal-subtitle" id="courseModalSubtitle">EduConnect</div>
            </div>
            <div class="course-modal-body" id="courseModalBody"></div>
        </div>
    </div>

    <script>
        // Filtros funcionais
        function filterCourses() {
            const category = document.getElementById('filterCategory').value;
            const level = document.getElementById('filterLevel').value;
            const price = document.getElementById('filterPrice').value;
            const duration = document.getElementById('filterDuration').value;
            const search = document.getElementById('searchBox').value.toLowerCase();
            
            const courses = document.querySelectorAll('.course-card');
            
            courses.forEach(course => {
                let show = true;
                
                // Filtro por categoria
                if (category && course.dataset.category !== category) {
                    show = false;
                }
                
                // Filtro por nível
                if (level && course.dataset.level !== level) {
                    show = false;
                }
                
                // Filtro por preço
                if (price) {
                    const coursePrice = parseFloat(course.dataset.price);
                    if (price === '0-200' && coursePrice > 200) show = false;
                    if (price === '200-400' && (coursePrice < 200 || coursePrice > 400)) show = false;
                    if (price === '400+' && coursePrice <= 400) show = false;
                }
                
                // Filtro por duração
                if (duration) {
                    const courseDuration = parseInt(course.dataset.duration);
                    if (duration === '0-50' && courseDuration > 50) show = false;
                    if (duration === '50-80' && (courseDuration < 50 || courseDuration > 80)) show = false;
                    if (duration === '80+' && courseDuration <= 80) show = false;
                }
                
                // Filtro por busca
                if (search && !course.dataset.name.includes(search)) {
                    show = false;
                }
                
                course.style.display = show ? 'block' : 'none';
            });
        }
        
        // Aplicar filtros em tempo real
        document.getElementById('filterCategory').addEventListener('change', filterCourses);
        document.getElementById('filterLevel').addEventListener('change', filterCourses);
        document.getElementById('filterPrice').addEventListener('change', filterCourses);
        document.getElementById('filterDuration').addEventListener('change', filterCourses);
        document.getElementById('searchBox').addEventListener('input', filterCourses);
        
        // Função para inscrever em curso
        async function inscreverCurso(cursoId, cursoNome) {
            openCourseFormModal({
                title: 'Inscrever-se no curso',
                subtitle: cursoNome,
                includePhone: true,
                submitText: 'Confirmar inscrição',
                onSubmit: async function(data) {
                    try {
                        const response = await fetch('api/inscricoes_simples.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                curso_id: cursoId,
                                aluno_nome: data.nome,
                                aluno_email: data.email,
                                telefone: data.telefone || ''
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            closeCourseModal();
                            showCourseToast(`Inscrição realizada com sucesso para ${cursoNome}.`, 'success');
                        } else {
                            showCourseToast('Erro ao realizar inscrição: ' + result.error, 'error');
                        }
                    } catch (error) {
                        showCourseToast('Erro de conexão: ' + error.message, 'error');
                    }
                }
            });
        }

        // Função para adicionar à lista de espera
        async function adicionarListaEspera(cursoId, cursoNome) {
            openCourseFormModal({
                title: 'Entrar na lista de espera',
                subtitle: cursoNome,
                includePhone: false,
                submitText: 'Entrar na lista',
                onSubmit: async function(data) {
                    try {
                        const response = await fetch('api/lista_espera.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                curso_id: cursoId,
                                aluno_nome: data.nome,
                                aluno_email: data.email
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            closeCourseModal();
                            showCourseToast(`Você entrou na lista de espera de ${cursoNome}.`, 'success');
                        } else {
                            showCourseToast('Erro ao adicionar à lista de espera: ' + result.error, 'error');
                        }
                    } catch (error) {
                        showCourseToast('Erro de conexão: ' + error.message, 'error');
                    }
                }
            });
        }

        // Função para ver detalhes do curso
        async function verDetalhesCurso(cursoId) {
            try {
                // Buscar dados do curso diretamente do banco
                const response = await fetch('api/cursos_simples.php');
                const result = await response.json();
                
                if (result.success) {
                    const curso = result.data.find(c => c.id == cursoId);
                    if (curso) {
                        openCourseDetailsModal(curso);
                    } else {
                        showCourseToast('Curso não encontrado.', 'error');
                    }
                } else {
                    showCourseToast('Erro ao carregar detalhes: ' + result.error, 'error');
                }
            } catch (error) {
                showCourseToast('Erro de conexão: ' + error.message, 'error');
            }
        }

        function openCourseFormModal(config) {
            document.getElementById('courseModalTitle').textContent = config.title;
            document.getElementById('courseModalSubtitle').textContent = config.subtitle;
            document.getElementById('courseModalBody').innerHTML = `
                <form id="courseActionForm">
                    <div class="course-form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" required placeholder="Digite seu nome completo">
                    </div>
                    <div class="course-form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="Digite seu email">
                    </div>
                    ${config.includePhone ? `
                        <div class="course-form-group">
                            <label>Telefone</label>
                            <input type="text" name="telefone" placeholder="Digite seu telefone">
                        </div>
                    ` : ''}
                    <div class="course-modal-actions">
                        <button type="button" class="btn btn-outline" onclick="closeCourseModal()">Cancelar</button>
                        <button type="submit" class="btn btn-success">${config.submitText}</button>
                    </div>
                </form>
            `;
            document.getElementById('courseActionForm').onsubmit = async function(event) {
                event.preventDefault();
                const formData = new FormData(event.target);
                await config.onSubmit({
                    nome: formData.get('nome'),
                    email: formData.get('email'),
                    telefone: formData.get('telefone')
                });
            };
            document.getElementById('courseModalOverlay').style.display = 'flex';
        }

        function openCourseDetailsModal(curso) {
            document.getElementById('courseModalTitle').textContent = 'Detalhes do curso';
            document.getElementById('courseModalSubtitle').textContent = curso.nome;
            document.getElementById('courseModalBody').innerHTML = `
                <div class="course-detail-list">
                    <div class="course-detail-item"><span class="course-detail-label">Nome</span><span class="course-detail-value">${curso.nome}</span></div>
                    <div class="course-detail-item"><span class="course-detail-label">Categoria</span><span class="course-detail-value">${curso.categoria || 'Tecnologia'}</span></div>
                    <div class="course-detail-item"><span class="course-detail-label">Nível</span><span class="course-detail-value">${curso.nivel || 'Intermediário'}</span></div>
                    <div class="course-detail-item"><span class="course-detail-label">Duração</span><span class="course-detail-value">${curso.duracao_horas} horas</span></div>
                    <div class="course-detail-item"><span class="course-detail-label">Preço</span><span class="course-detail-value">R$ ${curso.preco}</span></div>
                    <div class="course-detail-item"><span class="course-detail-label">Status</span><span class="course-detail-value">${curso.status === 'ativo' ? 'Ativo' : 'Em breve'}</span></div>
                    <div class="course-detail-item"><span class="course-detail-label">Descrição</span><span class="course-detail-value">${curso.descricao || 'Não informado'}</span></div>
                </div>
                <div class="course-modal-actions">
                    <button type="button" class="btn btn-primary" onclick="closeCourseModal()">Fechar</button>
                </div>
            `;
            document.getElementById('courseModalOverlay').style.display = 'flex';
        }

        function closeCourseModal() {
            document.getElementById('courseModalOverlay').style.display = 'none';
        }

        function showCourseToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `course-toast ${type}`;
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















