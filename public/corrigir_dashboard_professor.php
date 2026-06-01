<?php
session_start();

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Buscar dados do professor
$professor_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$professor = $stmt->get_result()->fetch_assoc();

// Contar cursos do professor
$cursos_query = "SELECT COUNT(DISTINCT a.curso_id) as count FROM agendamentos a 
                 WHERE a.professor_id = ?";
$stmt = $conn->prepare($cursos_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$cursos_count = $stmt->get_result()->fetch_assoc()['count'];

// Contar alunos do professor
$alunos_query = "SELECT COUNT(DISTINCT a.aluno_id) as count FROM agendamentos a 
                 WHERE a.professor_id = ?";
$stmt = $conn->prepare($alunos_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$alunos_count = $stmt->get_result()->fetch_assoc()['count'];

// Contar aulas agendadas
$aulas_query = "SELECT COUNT(*) as count FROM agendamentos WHERE professor_id = ? AND data_agendamento >= CURDATE()";
$stmt = $conn->prepare($aulas_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$aulas_count = $stmt->get_result()->fetch_assoc()['count'];

// Buscar cursos do professor
$cursos_professor_query = "SELECT DISTINCT c.* FROM cursos c 
                          JOIN agendamentos a ON c.id = a.curso_id 
                          WHERE a.professor_id = ? 
                          ORDER BY c.nome";
$stmt = $conn->prepare($cursos_professor_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$cursos_professor = $stmt->get_result();

// Buscar próximas aulas
$aulas_query = "SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome 
                FROM agendamentos a 
                JOIN cursos c ON a.curso_id = c.id 
                JOIN usuarios u ON a.aluno_id = u.id 
                WHERE a.professor_id = ? AND a.data_agendamento >= CURDATE() 
                ORDER BY a.data_agendamento, a.hora_inicio 
                LIMIT 5";
$stmt = $conn->prepare($aulas_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$proximas_aulas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Dashboard Professor</title>
    
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
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid var(--border-color);
            padding: 24px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 24px 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 0 16px;
        }

        .sidebar-item {
            margin-bottom: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--secondary-color);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-weight: 500;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: var(--primary-color);
            color: white;
        }

        .sidebar-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .logout-btn {
            background: var(--danger-color);
            color: white;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        /* Stats Grid */
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
        }

        .stat-card .icon {
            width: 48px;
            height: 48px;
            background: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 16px;
        }

        .stat-card h3 {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 4px;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .content-card {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .content-card h2 {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .content-card h2 i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: var(--transition);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard_professor.php" class="logo">EduConnect</a>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="sidebar-item">
                        <a href="dashboard_professor.php" class="sidebar-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="cursos_professor.php" class="sidebar-link">
                            <i class="fas fa-book"></i>
                            <span>Meus Cursos</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="aulas_professor.php" class="sidebar-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Aulas</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="alunos_professor.php" class="sidebar-link">
                            <i class="fas fa-users"></i>
                            <span>Alunos</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="relatorios_professor.php" class="sidebar-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="configuracoes_professor.php" class="sidebar-link">
                            <i class="fas fa-cog"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1>👨‍🏫 Dashboard Professor</h1>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($professor['nome'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?php echo $professor['nome']; ?></div>
                        <div style="font-size: 0.875rem; color: var(--secondary-color);">Professor</div>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </header>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Meus Cursos</h3>
                    <div class="value"><?php echo $cursos_count; ?></div>
                    <div style="font-size: 0.875rem; color: var(--secondary-color);">Cursos ativos</div>
                </div>

                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Alunos</h3>
                    <div class="value"><?php echo $alunos_count; ?></div>
                    <div style="font-size: 0.875rem; color: var(--secondary-color);">Estudantes inscritos</div>
                </div>

                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Próximas Aulas</h3>
                    <div class="value"><?php echo $aulas_count; ?></div>
                    <div style="font-size: 0.875rem; color: var(--secondary-color);">Aulas agendadas</div>
                </div>

                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Avaliação</h3>
                    <div class="value">4.8</div>
                    <div style="font-size: 0.875rem; color: var(--secondary-color);">Média dos alunos</div>
                </div>
            </div>

            <!-- Content -->
            <div class="content-grid">
                <!-- Meus Cursos -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-book"></i>
                        Meus Cursos
                    </h2>
                    
                    <?php if ($cursos_professor->num_rows > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Categoria</th>
                                    <th>Alunos</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($curso = $cursos_professor->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;"><?php echo $curso['nome']; ?></div>
                                            <div style="font-size: 0.875rem; color: var(--secondary-color);">
                                                <?php echo $curso['nivel']; ?> • <?php echo $curso['duracao_horas']; ?>h
                                            </div>
                                        </td>
                                        <td><?php echo $curso['categoria']; ?></td>
                                        <td>
                                            <?php
                                            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM inscricoes WHERE curso_id = ?");
                                            $stmt->bind_param("i", $curso['id']);
                                            $stmt->execute();
                                            $alunos_curso = $stmt->get_result()->fetch_assoc()['count'];
                                            echo $alunos_curso;
                                            ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-active">Ativo</span>
                                        </td>
                                        <td>
                                            <a href="detalhes_curso_professor.php?id=<?php echo $curso['id']; ?>" 
                                               style="background: var(--primary-color); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.875rem; text-decoration: none;">
                                                Ver Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px; color: var(--secondary-color);">
                            <i class="fas fa-book" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.5;"></i>
                            <p>Você ainda não tem cursos cadastrados.</p>
                            <a href="cursos_professor.php" style="background: var(--primary-color); color: white; border: none; padding: 12px 24px; border-radius: var(--border-radius); cursor: pointer; margin-top: 16px; text-decoration: none; display: inline-block;">
                                Criar Primeiro Curso
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Próximas Aulas -->
                <div class="content-card">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        Próximas Aulas
                    </h2>
                    
                    <?php if ($proximas_aulas->num_rows > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <?php while ($aula = $proximas_aulas->fetch_assoc()): ?>
                                <div style="border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 16px;">
                                    <div style="font-weight: 600; margin-bottom: 4px;"><?php echo $aula['curso_nome']; ?></div>
                                    <div style="font-size: 0.875rem; color: var(--secondary-color); margin-bottom: 8px;">
                                        <i class="fas fa-user"></i> <?php echo $aula['aluno_nome']; ?>
                                    </div>
                                    <div style="font-size: 0.875rem; color: var(--primary-color); font-weight: 600;">
                                        <i class="fas fa-clock"></i> 
                                        <?php echo date('d/m/Y', strtotime($aula['data_agendamento'])); ?> às <?php echo $aula['hora_inicio']; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px; color: var(--secondary-color);">
                            <i class="fas fa-calendar" style="font-size: 2rem; margin-bottom: 12px; opacity: 0.5;"></i>
                            <p>Nenhuma aula agendada.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Adicionar JavaScript para melhorar a experiência
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar efeitos hover nos cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = 'var(--shadow-md)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'var(--shadow-sm)';
                });
            });

            // Adicionar confirmação no logout
            const logoutBtn = document.querySelector('.logout-btn');
            logoutBtn.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja sair?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>






