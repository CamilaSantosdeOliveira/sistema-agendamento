<?php
// Conectar ao banco de dados
include 'db.php';

// Buscar estatísticas reais
$stats = [];

try {
    // Contar cursos
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $stats['cursos'] = $result->fetch_assoc()['total'];
    }
    
    // Contar professores
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
    if ($result) {
        $stats['professores'] = $result->fetch_assoc()['total'];
    }
    
    // Contar alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
    if ($result) {
        $stats['alunos'] = $result->fetch_assoc()['total'];
    }
    
    // Contar agendamentos
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_aula >= CURDATE()");
    if ($result) {
        $stats['agendamentos'] = $result->fetch_assoc()['total'];
    }
    
    // Contar pagamentos
    $result = $conn->query("SELECT COUNT(*) as total FROM pagamentos");
    if ($result) {
        $stats['pagamentos'] = $result->fetch_assoc()['total'];
    }
    
    // Buscar próximos agendamentos
    $proximos_agendamentos = [];
    $result = $conn->query("
        SELECT 
            a.id,
            a.data_aula,
            a.hora_inicio,
            a.status,
            u1.nome as aluno,
            u2.nome as professor,
            c.nome as curso
        FROM agendamentos a
        JOIN usuarios u1 ON a.aluno_id = u1.id
        JOIN usuarios u2 ON a.professor_id = u2.id
        JOIN cursos c ON a.curso_id = c.id
        WHERE a.data_aula >= CURDATE()
        ORDER BY a.data_aula, a.hora_inicio
        LIMIT 5
    ");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $proximos_agendamentos[] = $row;
        }
    }
    
    // Buscar cursos populares
    $cursos_populares = [];
    $result = $conn->query("
        SELECT nome, alunos_inscritos, avaliacao, categoria
        FROM cursos 
        WHERE status = 'ativo'
        ORDER BY alunos_inscritos DESC, avaliacao DESC
        LIMIT 4
    ");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cursos_populares[] = $row;
        }
    }
    
    // Buscar professores ativos
    $professores_ativos = [];
    $result = $conn->query("
        SELECT nome, formacao, valor_hora
        FROM usuarios 
        WHERE tipo_usuario = 'professor' AND ativo = 1
        ORDER BY nome
        LIMIT 4
    ");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $professores_ativos[] = $row;
        }
    }
    
} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $stats = [
        'cursos' => 0,
        'professores' => 0,
        'alunos' => 0,
        'agendamentos' => 0,
        'pagamentos' => 0
    ];
    $proximos_agendamentos = [];
    $cursos_populares = [];
    $professores_ativos = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Agendamento</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h2 {
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            opacity: 0.8;
            font-size: 0.9em;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 10px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid #60a5fa;
        }

        .nav-icon {
            margin-right: 12px;
            font-size: 1.2em;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2em;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .header p {
            color: #6b7280;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #3b82f6;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stat-card.cursos { border-left-color: #10b981; }
        .stat-card.professores { border-left-color: #f59e0b; }
        .stat-card.alunos { border-left-color: #8b5cf6; }
        .stat-card.agendamentos { border-left-color: #ef4444; }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            color: white;
        }

        .stat-card.cursos .stat-icon { background: #10b981; }
        .stat-card.professores .stat-icon { background: #f59e0b; }
        .stat-card.alunos .stat-icon { background: #8b5cf6; }
        .stat-card.agendamentos .stat-icon { background: #ef4444; }

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

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .content-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-card h3 {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .agendamento-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .agendamento-item:last-child {
            border-bottom: none;
        }

        .agendamento-date {
            background: #f3f4f6;
            padding: 8px 12px;
            border-radius: 8px;
            text-align: center;
            min-width: 80px;
            margin-right: 15px;
        }

        .agendamento-date .day {
            font-size: 1.2em;
            font-weight: 700;
            color: #1f2937;
        }

        .agendamento-date .month {
            font-size: 0.8em;
            color: #6b7280;
            text-transform: uppercase;
        }

        .agendamento-info {
            flex: 1;
        }

        .agendamento-time {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .agendamento-details {
            color: #6b7280;
            font-size: 0.9em;
        }

        .agendamento-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmado {
            background: #dcfce7;
            color: #166534;
        }

        .status-agendado {
            background: #fef3c7;
            color: #92400e;
        }

        .curso-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .curso-item:last-child {
            border-bottom: none;
        }

        .curso-icon {
            width: 40px;
            height: 40px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2em;
        }

        .curso-info {
            flex: 1;
        }

        .curso-nome {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .curso-meta {
            color: #6b7280;
            font-size: 0.9em;
        }

        .curso-stats {
            text-align: right;
        }

        .curso-alunos {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .curso-avaliacao {
            color: #f59e0b;
            font-size: 0.9em;
        }

        .professor-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .professor-item:last-child {
            border-bottom: none;
        }

        .professor-avatar {
            width: 40px;
            height: 40px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-weight: 600;
        }

        .professor-info {
            flex: 1;
        }

        .professor-nome {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .professor-formacao {
            color: #6b7280;
            font-size: 0.9em;
        }

        .professor-valor {
            text-align: right;
            font-weight: 600;
            color: #10b981;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .actions-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>🎯 Sistema</h2>
                <p>Agendamento de Aulas</p>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="cursos_completo.php" class="nav-link">
                        <span class="nav-icon">📚</span>
                        Cursos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">👥</span>
                        Usuários
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📅</span>
                        Agendamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📈</span>
                        Relatórios
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">⚙️</span>
                        Configurações
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>🎯 Dashboard Principal</h1>
                <p>Bem-vindo ao sistema de agendamento de aulas</p>
            </div>

            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card cursos">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo $stats['cursos']; ?></div>
                            <div class="stat-label">Cursos Ativos</div>
                        </div>
                        <div class="stat-icon">📚</div>
                    </div>
                </div>

                <div class="stat-card professores">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo $stats['professores']; ?></div>
                            <div class="stat-label">Professores</div>
                        </div>
                        <div class="stat-icon">👨‍🏫</div>
                    </div>
                </div>

                <div class="stat-card alunos">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo $stats['alunos']; ?></div>
                            <div class="stat-label">Alunos</div>
                        </div>
                        <div class="stat-icon">👨‍🎓</div>
                    </div>
                </div>

                <div class="stat-card agendamentos">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo $stats['agendamentos']; ?></div>
                            <div class="stat-label">Aulas Agendadas</div>
                        </div>
                        <div class="stat-icon">📅</div>
                    </div>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="actions-bar">
                <a href="#" class="btn" onclick="showNovoCursoModal()">📚 Novo Curso</a>
                <a href="#" class="btn" onclick="showNovoAgendamentoModal()">📅 Novo Agendamento</a>
                <a href="#" class="btn btn-secondary" onclick="showRelatoriosModal()">📊 Relatórios</a>
                <a href="#" class="btn btn-secondary" onclick="showUsuariosModal()">👥 Usuários</a>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Left Column -->
                <div class="content-card">
                    <h3>📅 Próximos Agendamentos</h3>
                    <?php if (empty($proximos_agendamentos)): ?>
                        <p style="color: #6b7280; text-align: center; padding: 40px;">Nenhum agendamento para os próximos dias</p>
                    <?php else: ?>
                        <?php foreach ($proximos_agendamentos as $agendamento): ?>
                            <div class="agendamento-item">
                                <div class="agendamento-date">
                                    <div class="day"><?php echo date('d', strtotime($agendamento['data_aula'])); ?></div>
                                    <div class="month"><?php echo date('M', strtotime($agendamento['data_aula'])); ?></div>
                                </div>
                                <div class="agendamento-info">
                                    <div class="agendamento-time"><?php echo substr($agendamento['hora_inicio'], 0, 5); ?></div>
                                    <div class="agendamento-details">
                                        <?php echo $agendamento['aluno']; ?> • <?php echo $agendamento['curso']; ?>
                                    </div>
                                </div>
                                <span class="agendamento-status status-<?php echo $agendamento['status']; ?>">
                                    <?php echo ucfirst($agendamento['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Column -->
                <div class="content-card">
                    <h3>📚 Cursos Populares</h3>
                    <?php if (empty($cursos_populares)): ?>
                        <p style="color: #6b7280; text-align: center; padding: 40px;">Nenhum curso disponível</p>
                    <?php else: ?>
                        <?php foreach ($cursos_populares as $curso): ?>
                            <div class="curso-item">
                                <div class="curso-icon">📚</div>
                                <div class="curso-info">
                                    <div class="curso-nome"><?php echo htmlspecialchars($curso['nome']); ?></div>
                                    <div class="curso-meta"><?php echo htmlspecialchars($curso['categoria']); ?></div>
                                </div>
                                <div class="curso-stats">
                                    <div class="curso-alunos"><?php echo $curso['alunos_inscritos']; ?></div>
                                    <div class="curso-avaliacao">⭐ <?php echo $curso['avaliacao']; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="content-card" style="margin-top: 30px;">
                <h3>👨‍🏫 Professores Ativos</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <?php if (empty($professores_ativos)): ?>
                        <p style="color: #6b7280; text-align: center; padding: 40px;">Nenhum professor disponível</p>
                    <?php else: ?>
                        <?php foreach ($professores_ativos as $professor): ?>
                            <div class="professor-item">
                                <div class="professor-avatar">
                                    <?php echo strtoupper(substr($professor['nome'], 0, 1)); ?>
                                </div>
                                <div class="professor-info">
                                    <div class="professor-nome"><?php echo htmlspecialchars($professor['nome']); ?></div>
                                    <div class="professor-formacao"><?php echo htmlspecialchars($professor['formacao']); ?></div>
                                </div>
                                <div class="professor-valor">
                                    R$ <?php echo number_format($professor['valor_hora'], 2, ',', '.'); ?>/h
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funções dos botões de ação
        function showNovoCursoModal() {
            alert('🎯 Funcionalidade de novo curso será implementada!');
        }
        
        function showNovoAgendamentoModal() {
            alert('📅 Funcionalidade de novo agendamento será implementada!');
        }
        
        function showRelatoriosModal() {
            alert('📊 Funcionalidade de relatórios será implementada!');
        }
        
        function showUsuariosModal() {
            alert('👥 Funcionalidade de usuários será implementada!');
        }
        
        // Navegação ativa
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>





































