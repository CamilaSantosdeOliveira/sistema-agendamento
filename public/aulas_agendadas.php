<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Conectar ao banco de dados
include 'db.php';

// Buscar informações do usuário logado
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT nome, email, tipo_usuario FROM usuarios WHERE id = ?");
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$usuario_logado = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aulas Agendadas - EduConnect Tech</title>
    
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
            background: #f1f5f9;
            color: var(--dark-color);
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border-color);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .page-subtitle {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .filter-input {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
        }

        .filter-select {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            background: white;
        }

        .aulas-list {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .aula-item {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .aula-item:hover {
            background: var(--light-color);
        }

        .aula-item:last-child {
            border-bottom: none;
        }

        .aula-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .aula-info {
            flex: 1;
        }

        .aula-data {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .aula-horario {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .aula-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmado {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-cancelado {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .status-pendente {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .aula-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-icon {
            color: var(--primary-color);
            width: 16px;
        }

        .detail-text {
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .aula-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-warning {
            background: var(--warning-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-info {
            background: var(--info-color);
            color: white;
        }

        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--secondary-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
                margin: 1rem auto;
            }
            
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .aula-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .aula-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <i class="fas fa-calendar-check"></i> Aulas Agendadas
            </div>
            <div class="header-actions">
                <a href="dashboard_corrigido.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                </a>
                <a href="agendar_aula.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Aula
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">📅 Gerenciar Aulas Agendadas</h1>
            <p class="page-subtitle">Visualize, edite e gerencie todas as suas aulas agendadas</p>
        </div>

        <div class="filters">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select" id="filterStatus">
                        <option value="">Todos os status</option>
                        <option value="confirmado">Confirmado</option>
                        <option value="pendente">Pendente</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Data Início</label>
                    <input type="date" class="filter-input" id="filterDataInicio">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Data Fim</label>
                    <input type="date" class="filter-input" id="filterDataFim">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Professor</label>
                    <select class="filter-select" id="filterProfessor">
                        <option value="">Todos os professores</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Curso</label>
                    <select class="filter-select" id="filterCurso">
                        <option value="">Todos os cursos</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Buscar</label>
                    <input type="text" class="filter-input" id="filterBusca" placeholder="Buscar por aluno, curso...">
                </div>
            </div>
        </div>

        <div class="aulas-list" id="aulasList">
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Carregando aulas agendadas...</p>
            </div>
        </div>
    </div>

    <script>
        // Carregar aulas agendadas
        async function loadAulas() {
            try {
                const response = await fetch('api/agendamentos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        acao: 'listar',
                        listar_todas: true 
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    displayAulas(result.data);
                } else {
                    showError('Erro ao carregar aulas: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro:', error);
                showError('Erro de conexão ao carregar aulas');
            }
        }

        // Exibir aulas na interface
        function displayAulas(aulas) {
            const container = document.getElementById('aulasList');
            
            if (!aulas || aulas.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>Nenhuma aula agendada</h3>
                        <p>Você ainda não tem aulas agendadas. Que tal agendar a primeira?</p>
                        <a href="agendar_aula.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Agendar Aula
                        </a>
                    </div>
                `;
                return;
            }

            container.innerHTML = aulas.map(aula => `
                <div class="aula-item">
                    <div class="aula-header">
                        <div class="aula-info">
                            <div class="aula-data">${formatarData(aula.data)}</div>
                            <div class="aula-horario">${aula.hora_inicio} - ${aula.hora_fim}</div>
                        </div>
                        <span class="aula-status status-${aula.status}">${aula.status}</span>
                    </div>
                    
                    <div class="aula-details">
                        <div class="detail-item">
                            <i class="fas fa-user-tie detail-icon"></i>
                            <span class="detail-text">${aula.professor_nome}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-book detail-icon"></i>
                            <span class="detail-text">${aula.curso_nome}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-user detail-icon"></i>
                            <span class="detail-text">${aula.aluno_nome}</span>
                        </div>
                    </div>
                    
                    ${aula.observacoes ? `<p style="margin: 1rem 0; padding: 1rem; background: var(--light-color); border-radius: var(--border-radius);">${aula.observacoes}</p>` : ''}
                    
                    <div class="aula-actions">
                        ${aula.status === 'cancelado' ? 
                            `<button class="btn btn-sm btn-success" onclick="reativarAula(${aula.id})">
                                <i class="fas fa-undo"></i> Reativar
                            </button>` : ''
                        }
                        ${aula.status === 'confirmado' ? 
                            `<button class="btn btn-sm btn-warning" onclick="cancelarAula(${aula.id})">
                                <i class="fas fa-times"></i> Cancelar
                            </button>` : ''
                        }
                        <button class="btn btn-sm btn-info" onclick="editarAula(${aula.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Formatar data
        function formatarData(data) {
            if (!data || data === '0000-00-00' || data === '0000-00-00 00:00:00') {
                return 'Data não definida';
            }
            
            const dataObj = new Date(data);
            
            // Verificar se a data é válida
            if (isNaN(dataObj.getTime())) {
                return 'Data inválida';
            }
            
            const hoje = new Date();
            const amanha = new Date(hoje);
            amanha.setDate(amanha.getDate() + 1);
            
            if (dataObj.toDateString() === hoje.toDateString()) {
                return 'Hoje';
            } else if (dataObj.toDateString() === amanha.toDateString()) {
                return 'Amanhã';
            } else {
                return dataObj.toLocaleDateString('pt-BR', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
        }

        // Funções de ação
        async function reativarAula(id) {
            if (confirm('Deseja reativar esta aula?')) {
                try {
                    const response = await fetch('api/agendamentos.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            acao: 'reativar',
                            agendamento_id: id 
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Aula reativada com sucesso!');
                        loadAulas(); // Recarregar a lista
                    } else {
                        alert('Erro ao reativar aula: ' + (result.error || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro de conexão ao reativar aula');
                }
            }
        }

        async function cancelarAula(id) {
            if (confirm('Deseja cancelar esta aula?')) {
                try {
                    const response = await fetch('api/agendamentos.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            acao: 'cancelar',
                            agendamento_id: id 
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Aula cancelada com sucesso!');
                        loadAulas(); // Recarregar a lista
                    } else {
                        alert('Erro ao cancelar aula: ' + (result.error || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro de conexão ao cancelar aula');
                }
            }
        }

        function editarAula(id) {
            // Redirecionar para a página de edição
            window.location.href = `editar_aula.php?id=${id}`;
        }

        function showError(message) {
            const container = document.getElementById('aulasList');
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Erro</h3>
                    <p>${message}</p>
                </div>
            `;
        }

        // Carregar aulas ao iniciar
        document.addEventListener('DOMContentLoaded', loadAulas);

        // Aplicar filtros
        document.getElementById('filterStatus').addEventListener('change', loadAulas);
        document.getElementById('filterDataInicio').addEventListener('change', loadAulas);
        document.getElementById('filterDataFim').addEventListener('change', loadAulas);
        document.getElementById('filterProfessor').addEventListener('change', loadAulas);
        document.getElementById('filterCurso').addEventListener('change', loadAulas);
        document.getElementById('filterBusca').addEventListener('input', loadAulas);
    </script>
</body>
</html>
