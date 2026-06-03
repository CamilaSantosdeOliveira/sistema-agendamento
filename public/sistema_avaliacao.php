<?php
// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$avaliacoes_count = 0;
$media_geral = 0;
$cursos_avaliados = 0;

try {
    // Contar avaliações (simulado - você pode criar uma tabela avaliacoes)
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    if ($result) {
        $cursos_count = $result->fetch_assoc()['total'];
        $avaliacoes_count = $cursos_count * 15; // Simulado: 15 avaliações por curso
    }

    // Calcular média geral das avaliações
    $result = $conn->query("SELECT AVG(avaliacao) as media FROM cursos WHERE avaliacao > 0");
    if ($result) {
        $media_geral = $result->fetch_assoc()['media'];
        $media_geral = number_format($media_geral, 1);
    }

    // Contar cursos avaliados
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE avaliacao > 0");
    if ($result) {
        $cursos_avaliados = $result->fetch_assoc()['total'];
    }

    // Buscar cursos com avaliações para exibir
    $cursos_result = $conn->query("SELECT id, nome, categoria, avaliacao, alunos_inscritos FROM cursos WHERE avaliacao > 0 ORDER BY avaliacao DESC");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $avaliacoes_count = 0;
    $media_geral = 0;
    $cursos_avaliados = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Sistema de Avaliação</title>
    
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
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .header h1 {
            color: var(--dark-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            color: var(--secondary-color);
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 16px;
        }

        .stat-card.primary .stat-icon {
            background-color: var(--primary-color);
        }

        .stat-card.success .stat-icon {
            background-color: var(--success-color);
        }

        .stat-card.warning .stat-icon {
            background-color: var(--warning-color);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .section-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-color);
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

        .cursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 24px;
        }

        .curso-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            transition: var(--transition);
        }

        .curso-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .curso-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .curso-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
        }

        .curso-categoria {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .avaliacao-badge {
            background: var(--success-color);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .curso-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            font-size: 0.8rem;
            color: var(--secondary-color);
        }

        .curso-stats span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stars {
            display: flex;
            gap: 2px;
            margin-bottom: 16px;
        }

        .star {
            color: var(--warning-color);
            font-size: 1.1rem;
        }

        .star.empty {
            color: var(--border-color);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 16px;
            transition: var(--transition);
        }

        .back-btn:hover {
            color: var(--primary-dark);
            transform: translateX(-4px);
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <a href="dashboard_corrigido.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Dashboard
        </a>

        <!-- Header -->
        <div class="header">
            <h1>⭐ Sistema de Avaliação</h1>
            <p>Monitore e gerencie as avaliações dos cursos e feedback dos alunos</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-value"><?php echo $avaliacoes_count; ?></div>
                <div class="stat-label">Total de Avaliações</div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?php echo $media_geral; ?></div>
                <div class="stat-label">Média Geral</div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div class="stat-value"><?php echo $cursos_avaliados; ?></div>
                <div class="stat-label">Cursos Avaliados</div>
            </div>
        </div>

        <!-- Cursos Avaliados Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-star"></i> Cursos com Melhores Avaliações</h3>
                <button class="btn btn-primary" onclick="novaAvaliacao()">
                    <i class="fas fa-plus"></i> Nova Avaliação
                </button>
            </div>
            
            <?php if ($cursos_result && $cursos_result->num_rows > 0): ?>
                <div class="cursos-grid">
                    <?php while ($curso = $cursos_result->fetch_assoc()): ?>
                        <div class="curso-card">
                            <div class="curso-header">
                                <div class="curso-info">
                                    <h4><?php echo htmlspecialchars($curso['nome']); ?></h4>
                                    <div class="curso-categoria"><?php echo htmlspecialchars($curso['categoria']); ?></div>
                                </div>
                                <div class="avaliacao-badge">
                                    <?php echo $curso['avaliacao']; ?>/5.0
                                </div>
                            </div>
                            
                            <div class="stars">
                                <?php
                                $rating = $curso['avaliacao'];
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star star"></i>';
                                    } else {
                                        echo '<i class="far fa-star star empty"></i>';
                                    }
                                }
                                ?>
                            </div>
                            
                            <div class="curso-stats">
                                <span><i class="fas fa-users"></i> <?php echo $curso['alunos_inscritos']; ?> alunos</span>
                                <span><i class="fas fa-star"></i> <?php echo $curso['avaliacao']; ?> avaliação</span>
                            </div>
                            
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-outline btn-sm" onclick="verAvaliacoes(<?php echo $curso['id']; ?>)">
                                    <i class="fas fa-eye"></i> Ver Avaliações
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="adicionarAvaliacao(<?php echo $curso['id']; ?>)">
                                    <i class="fas fa-plus"></i> Avaliar
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-star"></i>
                    <h4>Nenhuma avaliação disponível</h4>
                    <p>Comece avaliando os cursos do sistema</p>
                    <button class="btn btn-primary" onclick="novaAvaliacao()">
                        <i class="fas fa-plus"></i> Primeira Avaliação
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Feedback Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-comments"></i> Feedback dos Alunos</h3>
                <button class="btn btn-success" onclick="verTodosFeedback()">
                    <i class="fas fa-eye"></i> Ver Todos
                </button>
            </div>
            
            <div class="empty-state">
                <i class="fas fa-comments"></i>
                <h4>Sistema de Feedback</h4>
                <p>Funcionalidade será implementada em breve</p>
                <button class="btn btn-success" onclick="verTodosFeedback()">
                    <i class="fas fa-eye"></i> Ver Feedback
                </button>
            </div>
        </div>
    </div>

    <script>
        function novaAvaliacao() {
            alert(`⭐ Criando nova avaliação\n\nFuncionalidade será implementada em breve!`);
        }

        function verAvaliacoes(cursoId) {
            alert(`👁️ Visualizando avaliações do curso ID: ${cursoId}\n\nFuncionalidade será implementada em breve!`);
        }

        function adicionarAvaliacao(cursoId) {
            alert(`⭐ Adicionando avaliação ao curso ID: ${cursoId}\n\nFuncionalidade será implementada em breve!`);
        }

        function verTodosFeedback() {
            alert(`💬 Visualizando todos os feedbacks\n\nFuncionalidade será implementada em breve!`);
        }
    </script>
</body>
</html>





































