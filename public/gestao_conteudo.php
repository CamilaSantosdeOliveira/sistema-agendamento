<?php
// Conectar ao banco de dados
include 'db.php';

// Buscar dados reais
$cursos_count = 0;
$materiais_count = 0;
$categorias_count = 0;

try {
    // Contar cursos ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $cursos_count = $result->fetch_assoc()['total'];
    }

    // Contar materiais (simulado - você pode criar uma tabela materiais)
    $materiais_count = $cursos_count * 3; // Simulado

    // Contar categorias únicas
    $result = $conn->query("SELECT COUNT(DISTINCT categoria) as total FROM cursos");
    if ($result) {
        $categorias_count = $result->fetch_assoc()['total'];
    }

    // Buscar cursos para exibir
    $cursos_result = $conn->query("SELECT id, nome, categoria, nivel, status, alunos_inscritos FROM cursos ORDER BY nome");

} catch (Exception $e) {
    // Em caso de erro, usar valores padrão
    $cursos_count = 0;
    $materiais_count = 0;
    $categorias_count = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Gestão de Conteúdo</title>
    
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
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 16px;
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

        .cursos-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cursos-table th,
        .cursos-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .cursos-table th {
            background-color: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.9rem;
        }

        .cursos-table td {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-ativo {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-em_breve {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-inativo {
            background-color: #fee2e2;
            color: #991b1b;
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
            <h1>📚 Gestão de Conteúdo</h1>
            <p>Gerencie cursos, materiais e categorias do sistema educacional</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div class="stat-value"><?php echo $cursos_count; ?></div>
                <div class="stat-label">Cursos Ativos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-value"><?php echo $materiais_count; ?></div>
                <div class="stat-label">Materiais</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-value"><?php echo $categorias_count; ?></div>
                <div class="stat-label">Categorias</div>
            </div>
        </div>

        <!-- Cursos Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-laptop-code"></i> Cursos Disponíveis</h3>
                <button class="btn btn-primary" onclick="adicionarCurso()">
                    <i class="fas fa-plus"></i> Novo Curso
                </button>
            </div>
            
            <?php if ($cursos_result && $cursos_result->num_rows > 0): ?>
                <table class="cursos-table">
                    <thead>
                        <tr>
                            <th>Nome do Curso</th>
                            <th>Categoria</th>
                            <th>Nível</th>
                            <th>Status</th>
                            <th>Alunos</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($curso = $cursos_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($curso['nome']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($curso['categoria']); ?></td>
                                <td><?php echo htmlspecialchars($curso['nivel']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $curso['status']; ?>">
                                        <?php echo ucfirst($curso['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $curso['alunos_inscritos']; ?> alunos</td>
                                <td>
                                    <button class="btn btn-outline btn-sm" onclick="editarCurso(<?php echo $curso['id']; ?>)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-outline btn-sm" onclick="verDetalhes(<?php echo $curso['id']; ?>)">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h4>Nenhum curso disponível</h4>
                    <p>Comece criando o primeiro curso do sistema</p>
                    <button class="btn btn-primary" onclick="adicionarCurso()">
                        <i class="fas fa-plus"></i> Criar Primeiro Curso
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Materiais Section -->
        <div class="content-section">
            <div class="section-header">
                <h3><i class="fas fa-file-alt"></i> Materiais de Apoio</h3>
                <button class="btn btn-success" onclick="adicionarMaterial()">
                    <i class="fas fa-plus"></i> Novo Material
                </button>
            </div>
            
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h4>Sistema de Materiais</h4>
                <p>Funcionalidade será implementada em breve</p>
                <button class="btn btn-success" onclick="adicionarMaterial()">
                    <i class="fas fa-plus"></i> Adicionar Material
                </button>
            </div>
        </div>
    </div>

    <script>
        function adicionarCurso() {
            // Redirecionar para o modal de novo curso no dashboard
            window.location.href = 'dashboard_corrigido.php#novo-curso';
        }

        function editarCurso(id) {
            alert(`🎯 Editando curso ID: ${id}\n\nFuncionalidade será implementada em breve!`);
        }

        function verDetalhes(id) {
            alert(`👁️ Visualizando detalhes do curso ID: ${id}\n\nFuncionalidade será implementada em breve!`);
        }

        function adicionarMaterial() {
            alert(`📚 Adicionando novo material\n\nFuncionalidade será implementada em breve!`);
        }
    </script>
</body>
</html>





































