<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

// Buscar cursos do banco de dados com consulta específica
$cursos = [];
try {
    $result = $conn->query("SELECT id, nome, descricao, categoria, nivel, preco, duracao, status FROM cursos ORDER BY nome");
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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos de Tecnologia - Sistema de Agendamento</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 50px 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .filters {
            padding: 30px 40px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #374151;
        }

        .filter-select {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            font-size: 14px;
        }

        .search-container {
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-btn {
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }

        .courses-grid {
            padding: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .course-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .course-status {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .course-title {
            font-size: 1.25em;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1f2937;
        }

        .course-description {
            color: #6b7280;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .course-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-weight: 600;
            color: #1f2937;
        }

        .course-meta {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #6b7280;
        }

        .meta-icon {
            font-size: 16px;
        }

        .course-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-price {
            font-size: 1.25em;
            font-weight: 600;
            color: #059669;
        }

        .course-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: #3b82f6;
            border: 1px solid #3b82f6;
        }

        .btn-outline:hover {
            background: #3b82f6;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cursos de Tecnologia</h1>
            <p>Explore nossa coleção completa de cursos profissionalizantes</p>
        </div>

        <div class="filters">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Categoria</label>
                    <select class="filter-select" id="filterCategory">
                        <option value="">Todas as categorias</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Nível</label>
                    <select class="filter-select" id="filterLevel">
                        <option value="">Todos os níveis</option>
                        <option value="Iniciante ao Avançado">Iniciante ao Avançado</option>
                        <option value="Intermediário ao Avançado">Intermediário ao Avançado</option>
                        <option value="Avançado">Avançado</option>
                        <option value="Intermediário">Intermediário</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Preço</label>
                    <select class="filter-select" id="filterPrice">
                        <option value="">Qualquer preço</option>
                        <option value="0-500">Até R$ 500</option>
                        <option value="500-800">R$ 500 - R$ 800</option>
                        <option value="800+">Acima de R$ 800</option>
                    </select>
                </div>
            </div>
            
            <div class="search-container">
                <input type="text" class="search-input" id="searchBox" placeholder="Pesquisar cursos...">
                <button class="search-btn" onclick="filterCourses()">
                    <i class="fas fa-search"></i> Pesquisar
                </button>
            </div>
        </div>

        <div class="courses-grid">
            <?php if (!empty($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card" 
                         data-category="<?php echo htmlspecialchars($curso['categoria']); ?>" 
                         data-level="<?php echo htmlspecialchars($curso['nivel']); ?>" 
                         data-price="<?php echo $curso['preco']; ?>" 
                         data-name="<?php echo htmlspecialchars(strtolower($curso['nome'])); ?>">
                        
                        <div class="course-status"><?php echo ucfirst($curso['status']); ?></div>
                        
                        <div class="course-title"><?php echo htmlspecialchars($curso['nome']); ?></div>
                        <div class="course-description"><?php echo htmlspecialchars($curso['descricao']); ?></div>
                        
                        <div class="course-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $curso['duracao']; ?></div>
                                <div>horas</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">0</div>
                                <div>alunos</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">5.0/5.0</div>
                                <div>avaliação</div>
                            </div>
                        </div>
                        
                        <div class="course-meta">
                            <div class="meta-item">
                                <div class="meta-icon">📚</div>
                                <span><?php echo htmlspecialchars($curso['categoria']); ?></span>
                            </div>
                            <div class="meta-item">
                                <div class="meta-icon">🎯</div>
                                <span><?php echo htmlspecialchars($curso['nivel']); ?></span>
                            </div>
                        </div>
                        
                        <div class="course-footer">
                            <div class="course-price">R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></div>
                            <div class="course-actions">
                                <?php if ($curso['status'] === 'ativo'): ?>
                                    <button class="btn btn-success" onclick="inscreverCurso(<?php echo $curso['id']; ?>, '<?php echo htmlspecialchars($curso['nome']); ?>')">Inscrever-se</button>
                                <?php endif; ?>
                                <button class="btn btn-outline" onclick="verDetalhesCurso(<?php echo $curso['id']; ?>)">Ver Detalhes</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                    <h3>Nenhum curso encontrado</h3>
                    <p>Não há cursos disponíveis no momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filterCourses() {
            const category = document.getElementById('filterCategory').value;
            const level = document.getElementById('filterLevel').value;
            const price = document.getElementById('filterPrice').value;
            const search = document.getElementById('searchBox').value.toLowerCase();
            
            const courses = document.querySelectorAll('.course-card');
            
            courses.forEach(course => {
                let show = true;
                
                if (category && course.dataset.category !== category) {
                    show = false;
                }
                
                if (level && course.dataset.level !== level) {
                    show = false;
                }
                
                if (price) {
                    const coursePrice = parseFloat(course.dataset.price);
                    if (price === '0-500' && coursePrice > 500) show = false;
                    if (price === '500-800' && (coursePrice < 500 || coursePrice > 800)) show = false;
                    if (price === '800+' && coursePrice <= 800) show = false;
                }
                
                if (search && !course.dataset.name.includes(search)) {
                    show = false;
                }
                
                course.style.display = show ? 'block' : 'none';
            });
        }
        
        function inscreverCurso(cursoId, cursoNome) {
            alert('Funcionalidade de inscrição será implementada!\n\nCurso: ' + cursoNome);
        }
        
        function verDetalhesCurso(cursoId) {
            window.open('ver_detalhes_curso.php?id=' + cursoId, '_blank');
        }
        
        // Aplicar filtros em tempo real
        document.getElementById('filterCategory').addEventListener('change', filterCourses);
        document.getElementById('filterLevel').addEventListener('change', filterCourses);
        document.getElementById('filterPrice').addEventListener('change', filterCourses);
        document.getElementById('searchBox').addEventListener('input', filterCourses);
    </script>
</body>
</html>











