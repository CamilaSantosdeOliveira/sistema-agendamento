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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Certificados - EduConnect</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="dark-mode.css?v=3">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .stats-card {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-action {
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 14px;
            margin: 2px;
        }
        .certificate-item {
            border-left: 4px solid #3b82f6;
            transition: all 0.3s ease;
        }
        .certificate-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            font-size: 12px;
            padding: 4px 8px;
        }
        .search-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .filter-buttons {
            margin-bottom: 20px;
        }
        .filter-btn {
            margin: 5px;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 14px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #d1d5db;
        }

        body {
            background:
                radial-gradient(circle at 8% 4%, rgba(37, 99, 235, 0.16), transparent 24%),
                radial-gradient(circle at 92% 8%, rgba(16, 185, 129, 0.14), transparent 26%),
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 42%, #ecfeff 100%) !important;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            margin: 24px auto 0;
            width: min(1320px, calc(100% - 48px));
            min-height: 170px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border-radius: 30px;
            overflow: hidden;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, 0.24), transparent 30%),
                radial-gradient(circle at 84% 26%, rgba(37, 99, 235, 0.28), transparent 34%),
                linear-gradient(135deg, #0f172a 0%, #2563eb 48%, #0891b2 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.42);
            box-shadow: 0 28px 80px rgba(37, 99, 235, 0.18);
        }

        .navbar::after {
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

        .navbar .container {
            position: relative;
            z-index: 1;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            gap: 18px;
            padding: 28px;
        }

        .navbar-brand {
            position: absolute !important;
            top: 24px;
            left: 28px;
            margin: 0;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.24);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(16px);
            font-size: 0.92rem;
            font-weight: 800;
        }

        .navbar-text {
            color: #ffffff !important;
            font-size: clamp(1.85rem, 3.6vw, 2.65rem);
            font-weight: 850;
            letter-spacing: -0.055em;
        }

        .container.mt-4 {
            max-width: 1320px;
            margin-top: 26px !important;
        }

        .stats-card,
        .search-box,
        .certificate-item,
        .empty-state {
            isolation: isolate;
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            background: rgba(255, 255, 255, 0.92) !important;
            color: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.84) !important;
            box-shadow: 0 22px 58px rgba(15, 23, 42, 0.09) !important;
            backdrop-filter: blur(20px);
        }

        .stats-card::before,
        .search-box::before,
        .certificate-item::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            background: linear-gradient(90deg, #2563eb, #0891b2, #10b981);
        }

        .stats-card {
            padding: 28px 22px;
            transition: all 0.3s ease;
        }

        .stats-card:hover,
        .certificate-item:hover {
            transform: translateY(-7px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.15) !important;
        }

        .stats-card h3 {
            color: #0f172a;
            font-size: 2.35rem;
            font-weight: 850;
            letter-spacing: -0.06em;
        }

        .stats-card p {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.78rem;
        }

        .search-box {
            padding: 24px;
        }

        .form-control,
        .input-group-text {
            min-height: 48px;
            border-radius: 16px;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(203, 213, 225, 0.95);
        }

        .input-group-text {
            color: #d97706;
        }

        .btn {
            min-height: 40px;
            border-radius: 999px !important;
            font-weight: 850;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #0891b2) !important;
            border: 0 !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            border: 0 !important;
        }

        .btn-warning {
            color: #ffffff !important;
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            border: 0 !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            border: 0 !important;
        }

        .btn-info {
            color: #ffffff !important;
            background: linear-gradient(135deg, #0891b2, #2563eb) !important;
            border: 0 !important;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.86) !important;
            border: 1px solid rgba(203, 213, 225, 0.95) !important;
        }

        .filter-btn.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #d97706, #2563eb) !important;
        }

        .certificate-item {
            border-left: 0 !important;
            margin-bottom: 18px;
            transition: all 0.3s ease;
        }

        .certificate-item .card-body {
            padding: 26px;
        }

        .certificate-item h6 {
            color: #64748b;
            font-weight: 850;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 0.78rem;
        }

        .certificate-item .fw-bold {
            color: #0f172a;
            font-weight: 800 !important;
        }

        .status-badge {
            padding: 8px 13px;
            border-radius: 999px;
            font-weight: 850;
            letter-spacing: 0.05em;
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
            z-index: 2500;
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
            background: linear-gradient(135deg, #0f172a, #2563eb, #0891b2);
        }

        .confirm-dialog-title {
            font-size: 1.25rem;
            font-weight: 850;
            letter-spacing: -0.04em;
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

        /* Dark Mode Overrides */
        body.dark-mode {
            background: #0f172a !important;
            color: #f8fafc !important;
        }

        body.dark-mode .stats-card,
        body.dark-mode .search-box,
        body.dark-mode .certificate-item,
        body.dark-mode .empty-state {
            background: #1e293b !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 22px 58px rgba(0, 0, 0, 0.25) !important;
        }

        body.dark-mode .stats-card h3 {
            color: #ffffff !important;
        }

        body.dark-mode .stats-card p {
            color: #94a3b8 !important;
        }

        body.dark-mode .form-control {
            background: rgba(15, 23, 42, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }

        body.dark-mode .form-control::placeholder {
            color: #64748b !important;
        }

        body.dark-mode .input-group-text {
            background: rgba(15, 23, 42, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f59e0b !important;
        }

        body.dark-mode .filter-btn {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #cbd5e1 !important;
        }

        body.dark-mode .filter-btn:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }

        body.dark-mode .filter-btn.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #d97706, #2563eb) !important;
        }

        body.dark-mode .certificate-item h6 {
            color: #94a3b8 !important;
        }

        body.dark-mode .certificate-item .fw-bold {
            color: #ffffff !important;
        }

        body.dark-mode .certificate-item .text-muted {
            color: #94a3b8 !important;
        }

        body.dark-mode .certificate-item code {
            background: rgba(15, 23, 42, 0.6) !important;
            color: #60a5fa !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        body.dark-mode .confirm-dialog {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 34px 95px rgba(0, 0, 0, 0.5) !important;
        }

        body.dark-mode .confirm-dialog-body {
            color: #cbd5e1 !important;
        }

        body.dark-mode .confirm-dialog-body p {
            color: #cbd5e1 !important;
        }

        body.dark-mode .toast-custom {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.4) !important;
        }

        body.dark-mode .empty-state h4 {
            color: #ffffff !important;
        }

        body.dark-mode .empty-state p {
            color: #94a3b8 !important;
        }

        body.dark-mode .alert-info {
            background: rgba(59, 130, 246, 0.15) !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
            color: #93c5fd !important;
        }

        body.dark-mode .btn-outline-secondary {
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.25) !important;
        }

        body.dark-mode .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
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

        .btn-action-danger {
            background: rgba(239, 68, 68, 0.08) !important;
            color: #dc2626 !important;
            border-color: rgba(239, 68, 68, 0.18) !important;
        }
        .btn-action-danger:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
        }

        .btn-action-warning {
            background: rgba(245, 158, 11, 0.08) !important;
            color: #d97706 !important;
            border-color: rgba(245, 158, 11, 0.18) !important;
        }
        .btn-action-warning:hover {
            background: #f59e0b !important;
            color: #ffffff !important;
            border-color: #f59e0b !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2) !important;
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

        body.dark-mode .btn-action-danger {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
            border-color: rgba(239, 68, 68, 0.25) !important;
        }
        body.dark-mode .btn-action-danger:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
        }

        body.dark-mode .btn-action-warning {
            background: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
            border-color: rgba(245, 158, 11, 0.25) !important;
        }
        body.dark-mode .btn-action-warning:hover {
            background: #f59e0b !important;
            color: #ffffff !important;
            border-color: #f59e0b !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="certificados.php">
                <i class="fas fa-arrow-left"></i> Voltar aos Certificados
            </a>
            <button id="darkModeToggle" title="Alternar tema" aria-label="Alternar Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
            <span class="navbar-text">
                <i class="fas fa-shield-alt"></i> Sistema de Validação
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Estatísticas -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3 id="totalValidados">0</h3>
                    <p class="mb-0">Validados</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h3 id="totalPendentes">0</h3>
                    <p class="mb-0">Pendentes</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h3 id="totalRevogados">0</h3>
                    <p class="mb-0">Revogados</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <h3 id="totalCertificados">0</h3>
                    <p class="mb-0">Total</p>
                </div>
            </div>
        </div>

        <!-- Busca e Filtros -->
        <div class="search-box">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar por nome do aluno, código ou curso...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button class="btn btn-success flex-fill" onclick="validarTodosPendentes()">
                            <i class="fas fa-check-double"></i> Validar Todos Pendentes
                        </button>
                        <button class="btn btn-primary" onclick="emitirNovoCertificado()">
                            <i class="fas fa-plus"></i> Emitir
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="filter-buttons mt-3">
                <button class="btn btn-outline-primary filter-btn active" onclick="filtrarCertificados('todos')">
                    Todos
                </button>
                <button class="btn btn-outline-warning filter-btn" onclick="filtrarCertificados('pendentes')">
                    Pendentes
                </button>
                <button class="btn btn-outline-success filter-btn" onclick="filtrarCertificados('validados')">
                    Validados
                </button>
                <button class="btn btn-outline-danger filter-btn" onclick="filtrarCertificados('revogados')">
                    Revogados
                </button>
                <button class="btn btn-outline-secondary filter-btn" onclick="limparFiltros()">
                    <i class="fas fa-times"></i> Limpar
                </button>
            </div>
        </div>

        <!-- Lista de Certificados -->
        <div id="certificadosList">
            <div class="empty-state">
                <i class="fas fa-certificate"></i>
                <h4>Carregando certificados...</h4>
                <p>Aguarde enquanto buscamos os certificados no sistema</p>
            </div>
        </div>
    </div>

    <div id="confirmOverlay" class="confirm-overlay">
        <div class="confirm-dialog">
            <div class="confirm-dialog-header">
                <div class="confirm-dialog-title" id="confirmTitle">Confirmar ação</div>
            </div>
            <div class="confirm-dialog-body">
                <p id="confirmMessage">Deseja continuar?</p>
                <div class="confirm-dialog-actions">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeConfirmDialog()">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let certificados = [];
        let filtroAtual = 'todos';

        // Carregar certificados ao iniciar
        document.addEventListener('DOMContentLoaded', function() {
            carregarCertificados();
        });

        // Função para carregar certificados
        async function carregarCertificados() {
            try {
                const response = await fetch('api/certificados.php?action=listar_certificados');
                const data = await response.json();
                
                if (data.success) {
                    certificados = data.data || data.certificados || [];
                    atualizarEstatisticas();
                    exibirCertificados();
                } else {
                    mostrarErro('Erro ao carregar certificados: ' + data.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                mostrarErro('Erro de conexão ao carregar certificados');
            }
        }

        // Função para atualizar estatísticas
        function atualizarEstatisticas() {
            const validados = certificados.filter(c => c.status === 'validado').length;
            const pendentes = certificados.filter(c => c.status === 'pendente').length;
            const revogados = certificados.filter(c => c.status === 'revogado').length;
            const total = certificados.length;

            document.getElementById('totalValidados').textContent = validados;
            document.getElementById('totalPendentes').textContent = pendentes;
            document.getElementById('totalRevogados').textContent = revogados;
            document.getElementById('totalCertificados').textContent = total;
        }

        // Função para exibir certificados
        function exibirCertificados() {
            const container = document.getElementById('certificadosList');
            
            if (certificados.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h4>Nenhum certificado encontrado</h4>
                        <p>Não há certificados para validação no momento</p>
                    </div>
                `;
                return;
            }

            // Verificar se há muitos certificados pendentes
            const pendentesCount = certificados.filter(c => c.status === 'pendente').length;
            let alertHtml = '';
            
            if (pendentesCount > 3) {
                alertHtml = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Dica:</strong> Você tem ${pendentesCount} certificados pendentes. 
                        Use o botão <strong>"Validar Todos"</strong> para aprovar todos de uma vez, 
                        ou filtre por "Pendentes" para ver apenas os que precisam de validação.
                    </div>
                `;
            }

            let html = alertHtml;
            certificados.forEach(certificado => {
                const statusClass = getStatusClass(certificado.status);
                const statusText = getStatusText(certificado.status);
                
                html += `
                    <div class="card certificate-item">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h6 class="mb-1"><i class="fas fa-user"></i> Aluno</h6>
                                    <p class="mb-0 fw-bold">${certificado.aluno_nome || 'N/A'}</p>
                                    <small class="text-muted">${certificado.aluno_email || 'N/A'}</small>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1"><i class="fas fa-graduation-cap"></i> Curso</h6>
                                    <p class="mb-0 fw-bold">${certificado.curso_nome || 'N/A'}</p>
                                    <small class="text-muted">${certificado.carga_horaria || 0} horas</small>
                                </div>
                                <div class="col-md-2">
                                    <h6 class="mb-1"><i class="fas fa-key"></i> Código</h6>
                                    <code class="small">${certificado.codigo_verificacao || 'N/A'}</code>
                                </div>
                                <div class="col-md-2">
                                    <h6 class="mb-1"><i class="fas fa-calendar"></i> Emissão</h6>
                                    <p class="mb-0">${certificado.data_emissao || 'N/A'}</p>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="badge ${statusClass} status-badge mb-2">${statusText}</span>
                                        <div class="d-flex gap-2 justify-content-end flex-wrap">
                                            ${getActionButtons(certificado)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Função para obter classe do status
        function getStatusClass(status) {
            switch(status) {
                case 'validado': return 'bg-success';
                case 'pendente': return 'bg-warning';
                case 'revogado': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }

        // Função para obter texto do status
        function getStatusText(status) {
            switch(status) {
                case 'validado': return 'Validado';
                case 'pendente': return 'Pendente';
                case 'revogado': return 'Revogado';
                default: return 'Desconhecido';
            }
        }

        // Função para obter botões de ação
        function getActionButtons(certificado) {
            let buttons = '';
            
            // Mostrar botões baseado no status
            if (certificado.status === 'emitido' || certificado.status === 'pendente') {
                buttons += `<button class="btn btn-action btn-action-success" onclick="validarCertificado(${certificado.id})">
                    <i class="fas fa-check"></i> Validar
                </button>`;
            }
            
            if (certificado.status === 'validado') {
                buttons += `<button class="btn btn-action btn-action-danger" onclick="revogarCertificado(${certificado.id})">
                    <i class="fas fa-ban"></i> Revogar
                </button>`;
            }
            
            if (certificado.status === 'revogado') {
                buttons += `<button class="btn btn-action btn-action-warning" onclick="desrevogarCertificado(${certificado.id})">
                    <i class="fas fa-undo"></i> Reativar
                </button>`;
            }
            
            buttons += `<button class="btn btn-action btn-action-info" onclick="verCertificado(${certificado.id})">
                <i class="fas fa-eye"></i> Ver
            </button>`;
            
            return buttons;
        }

        // Função para validar certificado
        async function validarCertificado(id) {
            showConfirmDialog('Validar certificado?', 'Tem certeza que deseja validar este certificado?', async function() {
                try {
                    const response = await fetch('api/certificados.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'validar_certificado',
                            certificado_id: id
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast('Certificado validado com sucesso!', 'success');
                        carregarCertificados();
                    } else {
                        showToast('Erro ao validar certificado: ' + data.message, 'error');
                    }
                } catch (error) {
                    showToast('Erro de conexão ao validar certificado', 'error');
                }
            });
        }

        // Função para revogar certificado
        async function revogarCertificado(id) {
            showConfirmDialog('Revogar certificado?', 'Tem certeza que deseja revogar este certificado?', async function() {
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
                        showToast('Certificado revogado com sucesso!', 'success');
                        carregarCertificados();
                    } else {
                        showToast('Erro ao revogar certificado: ' + data.message, 'error');
                    }
                } catch (error) {
                    showToast('Erro de conexão ao revogar certificado', 'error');
                }
            });
        }

        // Função para desrevogar certificado
        async function desrevogarCertificado(id) {
            showConfirmDialog('Reativar certificado?', 'Tem certeza que deseja reativar este certificado revogado?', async function() {
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
                        showToast('Certificado reativado com sucesso!', 'success');
                        carregarCertificados();
                    } else {
                        showToast('Erro ao reativar certificado: ' + data.message, 'error');
                    }
                } catch (error) {
                    showToast('Erro de conexão ao reativar certificado', 'error');
                }
            });
        }

        // Função para validar todos os pendentes
        async function validarTodosPendentes() {
            showConfirmDialog('Validar pendentes?', 'Tem certeza que deseja validar todos os certificados pendentes?', async function() {
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
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast(`${data.count || 'Todos os'} certificados validados com sucesso!`, 'success');
                        carregarCertificados();
                    } else {
                        showToast('Erro ao validar certificados: ' + data.message, 'error');
                    }
                } catch (error) {
                    showToast('Erro de conexão ao validar certificados', 'error');
                }
            });
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

        // Função para ver certificado
        function verCertificado(id) {
            window.open(`gerar_pdf_certificado.php?id=${id}`, '_blank');
        }

        // Função para emitir novo certificado
        function emitirNovoCertificado() {
            // Abrir página de certificados em nova aba
            window.open('certificados.php', '_blank');
        }

        // Função para filtrar certificados
        function filtrarCertificados(filtro) {
            filtroAtual = filtro;
            
            // Atualizar botões
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Aplicar filtro
            let certificadosFiltrados = certificados;
            
            switch(filtro) {
                case 'pendentes':
                    certificadosFiltrados = certificados.filter(c => c.status === 'pendente');
                    break;
                case 'validados':
                    certificadosFiltrados = certificados.filter(c => c.status === 'validado');
                    break;
                case 'revogados':
                    certificadosFiltrados = certificados.filter(c => c.status === 'revogado');
                    break;
            }
            
            exibirCertificadosFiltrados(certificadosFiltrados);
        }

        // Função para exibir certificados filtrados
        function exibirCertificadosFiltrados(certificadosFiltrados) {
            const container = document.getElementById('certificadosList');
            
            if (certificadosFiltrados.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-filter"></i>
                        <h4>Nenhum certificado encontrado</h4>
                        <p>Não há certificados com o filtro selecionado</p>
                    </div>
                `;
                return;
            }

            // Usar a mesma lógica de exibição
            certificados = certificadosFiltrados;
            exibirCertificados();
        }

        // Função para limpar filtros
        function limparFiltros() {
            document.getElementById('searchInput').value = '';
            carregarCertificados();
            
            // Resetar botões
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.filter-btn').classList.add('active');
        }

        // Função para mostrar erro
        function mostrarErro(mensagem) {
            document.getElementById('certificadosList').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <h4>Erro</h4>
                    <p>${mensagem}</p>
                    <button class="btn btn-primary" onclick="carregarCertificados()">
                        <i class="fas fa-redo"></i> Tentar Novamente
                    </button>
                </div>
            `;
        }

        // Busca em tempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const termo = this.value.toLowerCase();
            
            if (termo === '') {
                exibirCertificados();
                return;
            }
            
            const certificadosFiltrados = certificados.filter(certificado => 
                (certificado.aluno_nome && certificado.aluno_nome.toLowerCase().includes(termo)) ||
                (certificado.codigo_verificacao && certificado.codigo_verificacao.toLowerCase().includes(termo)) ||
                (certificado.curso_nome && certificado.curso_nome.toLowerCase().includes(termo))
            );
            
            exibirCertificadosFiltrados(certificadosFiltrados);
        });
    </script>
    <script src="dark-mode.js"></script>
</body>
</html>


