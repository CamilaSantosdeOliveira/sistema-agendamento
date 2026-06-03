<?php
// Conectar ao banco de dados
include 'db.php';

// Buscar cursos do banco
$cursos_result = null;
$total_cursos = 0;

try {
    // Contar total de cursos
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $total_cursos = $result->fetch_assoc()['total'];
    }

    // Buscar todos os cursos ativos
    $cursos_result = $conn->query("SELECT id, nome, descricao, categoria, nivel, preco, duracao, status FROM cursos WHERE status = 'ativo' ORDER BY nome");
    
} catch (Exception $e) {
    $total_cursos = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Cursos de Tecnologia</title>
    
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

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 16px;
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: var(--secondary-color);
            margin-bottom: 32px;
        }

        .page-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.8rem;
        }

        /* Filtros */
        .filters-section {
            background: white;
            padding: 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 32px;
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
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .filter-select, .filter-input {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            background: white;
        }

        .search-container {
            display: flex;
            gap: 12px;
            align-items: end;
        }

        .search-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
        }

        .search-btn {
            padding: 12px 24px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        /* Cursos Grid */
        .cursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
        }

        .curso-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 24px;
            transition: var(--transition);
            position: relative;
        }

        .curso-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .curso-status {
            position: absolute;
            top: 16px;
            right: 16px;
            background: var(--success-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .curso-header {
            margin-bottom: 16px;
        }

        .curso-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .curso-descricao {
            color: var(--secondary-color);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .curso-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .info-item i {
            color: var(--primary-color);
            width: 16px;
        }

        .curso-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .stats-summary {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
            text-align: center;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .stats-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .page-actions {
                flex-direction: column;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .cursos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <header class="page-header">
            <h1 class="page-title">Cursos de Tecnologia</h1>
            <p class="page-subtitle">Explore nossa coleção completa de cursos profissionalizantes</p>
            
            <div class="page-actions">
                <button class="btn btn-primary" onclick="showNovoCursoModal()">
                    <i class="fas fa-plus"></i> Novo Curso
                </button>
                <button class="btn btn-success" onclick="window.location.href='agendamentos-eventos.html'">
                    <i class="fas fa-calendar-plus"></i> Agendar Aula
                </button>
                <a href="dashboard_corrigido.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                </a>
            </div>
        </header>

        <!-- Stats Summary -->
        <div class="stats-summary">
            <div class="stats-number"><?php echo $total_cursos; ?></div>
            <div class="stats-label">Cursos Disponíveis</div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Categoria</label>
                    <select class="filter-select" id="categoriaFilter">
                        <option value="">Todas as categorias</option>
                        <option value="Programação Web">Programação Web</option>
                        <option value="Data Science">Data Science</option>
                        <option value="Desenvolvimento Backend">Desenvolvimento Backend</option>
                        <option value="DevOps">DevOps</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Nível</label>
                    <select class="filter-select" id="nivelFilter">
                        <option value="">Todos os níveis</option>
                        <option value="Iniciante ao Avançado">Iniciante ao Avançado</option>
                        <option value="Intermediário ao Avançado">Intermediário ao Avançado</option>
                        <option value="Avançado">Avançado</option>
                        <option value="Intermediário">Intermediário</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Preço</label>
                    <select class="filter-select" id="precoFilter">
                        <option value="">Qualquer preço</option>
                        <option value="0-500">Até R$ 500</option>
                        <option value="500-800">R$ 500 - R$ 800</option>
                        <option value="800+">Acima de R$ 800</option>
                    </select>
                </div>
            </div>
            
            <div class="search-container">
                <input type="text" class="search-input" id="searchInput" placeholder="Pesquisar cursos...">
                <button class="search-btn" onclick="filtrarCursos()">
                    <i class="fas fa-search"></i> Pesquisar
                </button>
            </div>
        </div>

        <!-- Cursos Grid -->
        <div class="cursos-grid" id="cursosGrid">
            <?php if ($cursos_result && $cursos_result->num_rows > 0): ?>
                <?php while ($curso = $cursos_result->fetch_assoc()): ?>
                    <div class="curso-card" data-categoria="<?php echo htmlspecialchars($curso['categoria'] ?: 'Tecnologia'); ?>" 
                         data-nivel="<?php echo htmlspecialchars($curso['nivel'] ?: 'Intermediário'); ?>" 
                         data-preco="<?php echo $curso['preco']; ?>" 
                         data-duracao="<?php echo htmlspecialchars($curso['duracao']); ?>"
                         data-nome="<?php echo htmlspecialchars(strtolower($curso['nome'])); ?>">
                        
                        <div class="curso-status"><?php echo ucfirst($curso['status']); ?></div>
                        
                        <div class="curso-header">
                            <h3 class="curso-title"><?php echo htmlspecialchars($curso['nome']); ?></h3>
                            <p class="curso-descricao"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                        </div>
                        
                        <div class="curso-info">
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo htmlspecialchars($curso['duracao']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-signal"></i>
                                <span><?php echo htmlspecialchars($curso['nivel'] ?: 'Intermediário'); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-tag"></i>
                                <span><?php echo htmlspecialchars($curso['categoria'] ?: 'Tecnologia'); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-dollar-sign"></i>
                                <span>R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></span>
                            </div>
                        </div>
                        
                        <div class="curso-actions">
                            <a href="ver_detalhes_curso.php?id=<?php echo $curso['id']; ?>" class="btn btn-primary btn-sm">
                                Ver Detalhes
                            </a>
                            <button class="btn btn-outline btn-sm" onclick="editarCurso(<?php echo $curso['id']; ?>)">
                                Editar
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="excluirCurso(<?php echo $curso['id']; ?>)">
                                Excluir
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="curso-card" style="grid-column: 1 / -1; text-align: center; padding: 60px 24px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--warning-color); margin-bottom: 20px;"></i>
                    <h3>Nenhum curso encontrado</h3>
                    <p>Não há cursos cadastrados no sistema ou os filtros não retornaram resultados.</p>
                    <div style="margin-top: 20px;">
                        <button class="btn btn-primary" onclick="window.location.href='criar_tabela_cursos.php'">
                            <i class="fas fa-database"></i> Criar Tabela de Cursos
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Função para filtrar cursos
        function filtrarCursos() {
            const categoria = document.getElementById('categoriaFilter').value;
            const nivel = document.getElementById('nivelFilter').value;
            const preco = document.getElementById('precoFilter').value;
            const duracao = document.getElementById('duracaoFilter').value;
            const search = document.getElementById('searchInput').value.toLowerCase();
            
            const cursos = document.querySelectorAll('.curso-card');
            
            cursos.forEach(curso => {
                let mostrar = true;
                
                // Filtro por categoria
                if (categoria && curso.dataset.categoria !== categoria) {
                    mostrar = false;
                }
                
                // Filtro por nível
                if (nivel && curso.dataset.nivel !== nivel) {
                    mostrar = false;
                }
                
                // Filtro por preço
                if (preco) {
                    const cursoPreco = parseFloat(curso.dataset.preco);
                    if (preco === '0-200' && cursoPreco > 200) mostrar = false;
                    if (preco === '200-400' && (cursoPreco <= 200 || cursoPreco > 400)) mostrar = false;
                    if (preco === '400+' && cursoPreco <= 400) mostrar = false;
                }
                
                // Filtro por duração
                if (duracao) {
                    const cursoDuracao = parseInt(curso.dataset.duracao);
                    if (duracao === '0-50' && cursoDuracao > 50) mostrar = false;
                    if (duracao === '50-80' && (cursoDuracao <= 50 || cursoDuracao > 80)) mostrar = false;
                    if (duracao === '80+' && cursoDuracao <= 80) mostrar = false;
                }
                
                // Filtro por pesquisa
                if (search && !curso.dataset.nome.includes(search)) {
                    mostrar = false;
                }
                
                curso.style.display = mostrar ? 'block' : 'none';
            });
        }

        // Função para mostrar modal de novo curso
        async function showNovoCursoModal() {
            try {
                const modal = document.createElement('div');
                modal.className = 'modal-overlay';
                modal.innerHTML = `
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>📚 Novo Curso</h3>
                            <button onclick="closeModal()" class="close-btn">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="novoCursoForm">
                                <div class="form-group">
                                    <label>Nome do Curso:</label>
                                    <input type="text" name="nome" required>
                                </div>
                                <div class="form-group">
                                    <label>Categoria:</label>
                                    <select name="categoria" required>
                                        <option value="">Selecione uma categoria</option>
                                        <option value="programacao">Programação</option>
                                        <option value="design">Design</option>
                                        <option value="marketing">Marketing Digital</option>
                                        <option value="negocios">Negócios</option>
                                        <option value="tecnologia">Tecnologia</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nível:</label>
                                    <select name="nivel" required>
                                        <option value="">Selecione o nível</option>
                                        <option value="iniciante">Iniciante</option>
                                        <option value="intermediario">Intermediário</option>
                                        <option value="avancado">Avançado</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Duração (horas):</label>
                                    <input type="number" name="duracao_horas" min="1" max="200" required>
                                </div>
                                <div class="form-group">
                                    <label>Preço (R$):</label>
                                    <input type="number" name="preco" min="0" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Descrição:</label>
                                    <textarea name="descricao" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select name="status" required>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button onclick="salvarNovoCurso()" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                            <button onclick="closeModal()" class="btn btn-outline">Cancelar</button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
            } catch (error) {
                showNotification('❌ Erro ao abrir modal de curso', 'error');
            }
        }

        // Função para ver detalhes do curso
        async function verDetalhes(id) {
            try {
                // Buscar dados do curso diretamente do banco
                const response = await fetch('api/cursos_simples.php');
                const result = await response.json();
                
                if (result.success) {
                    const curso = result.data.find(c => c.id == id);
                    if (curso) {
                        showDetalhesCursoModal(curso);
                    } else {
                        showNotification('❌ Curso não encontrado', 'error');
                    }
                } else {
                    showNotification('❌ Erro ao carregar detalhes: ' + result.error, 'error');
                }
            } catch (error) {
                showNotification('❌ Erro de conexão ao carregar curso', 'error');
            }
        }

        // Função para editar curso
        async function editarCurso(id) {
            try {
                const response = await fetch(`api/cursos.php?action=buscar_curso&id=${id}`);
                const result = await response.json();
                
                if (result.success) {
                    showEditarCursoModal(result.data);
                } else {
                    showNotification('❌ Erro ao carregar curso: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('❌ Erro de conexão ao carregar curso', 'error');
            }
        }

        // Função para excluir curso
        async function excluirCurso(id) {
            showConfirmModal(
                'Excluir curso',
                'Tem certeza que deseja excluir este curso? Esta ação não pode ser desfeita.',
                async () => {
                    try {
                        const response = await fetch('api/cursos.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: 'excluir_curso',
                                id: id
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showNotification('✅ Curso excluído com sucesso!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('❌ Erro ao excluir curso: ' + result.message, 'error');
                        }
                    } catch (error) {
                        showNotification('❌ Erro de conexão ao excluir curso', 'error');
                    }
                }
            );
        }

        // Função para mostrar modal de detalhes do curso
        function showDetalhesCursoModal(curso) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>👁️ Detalhes do Curso</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="curso-details">
                            <div class="detail-row">
                                <span class="detail-label">Nome:</span>
                                <span class="detail-value">${curso.nome}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Descrição:</span>
                                <span class="detail-value">${curso.descricao || 'Não informado'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Duração:</span>
                                <span class="detail-value">${curso.duracao}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Preço:</span>
                                <span class="detail-value">R$ ${parseFloat(curso.preco).toFixed(2)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">${curso.status === 'ativo' ? 'Ativo' : 'Em breve'}</span>
                            </div>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Avaliação:</span>
                                <span class="detail-value">${curso.avaliacao ? curso.avaliacao + '/5' : 'Sem avaliações'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge ${curso.status === 'ativo' ? 'status-ativo' : 'status-inativo'}">
                                        ${curso.status === 'ativo' ? 'Ativo' : 'Inativo'}
                                    </span>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Descrição:</span>
                                <span class="detail-value">${curso.descricao || 'Sem descrição'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="editarCurso(${curso.id})" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button onclick="closeModal()" class="btn btn-outline">Fechar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Função para mostrar modal de editar curso
        function showEditarCursoModal(curso) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>✏️ Editar Curso</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editarCursoForm">
                            <input type="hidden" name="id" value="${curso.id}">
                            <div class="form-group">
                                <label>Nome do Curso:</label>
                                <input type="text" name="nome" value="${curso.nome}" required>
                            </div>
                            <div class="form-group">
                                <label>Categoria:</label>
                                <select name="categoria" required>
                                    <option value="programacao" ${curso.categoria === 'programacao' ? 'selected' : ''}>Programação</option>
                                    <option value="design" ${curso.categoria === 'design' ? 'selected' : ''}>Design</option>
                                    <option value="marketing" ${curso.categoria === 'marketing' ? 'selected' : ''}>Marketing Digital</option>
                                    <option value="negocios" ${curso.categoria === 'negocios' ? 'selected' : ''}>Negócios</option>
                                    <option value="tecnologia" ${curso.categoria === 'tecnologia' ? 'selected' : ''}>Tecnologia</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nível:</label>
                                <select name="nivel" required>
                                    <option value="iniciante" ${curso.nivel === 'iniciante' ? 'selected' : ''}>Iniciante</option>
                                    <option value="intermediario" ${curso.nivel === 'intermediario' ? 'selected' : ''}>Intermediário</option>
                                    <option value="avancado" ${curso.nivel === 'avancado' ? 'selected' : ''}>Avançado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Duração (horas):</label>
                                <input type="number" name="duracao_horas" value="${curso.duracao_horas}" min="1" max="200" required>
                            </div>
                            <div class="form-group">
                                <label>Preço (R$):</label>
                                <input type="number" name="preco" value="${curso.preco}" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label>Descrição:</label>
                                <textarea name="descricao" rows="4" required>${curso.descricao || ''}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status" required>
                                    <option value="ativo" ${curso.status === 'ativo' ? 'selected' : ''}>Ativo</option>
                                    <option value="inativo" ${curso.status === 'inativo' ? 'selected' : ''}>Inativo</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button onclick="salvarEdicaoCurso()" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <button onclick="closeModal()" class="btn btn-outline">Cancelar</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Função para salvar novo curso
        async function salvarNovoCurso() {
            const form = document.getElementById('novoCursoForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('api/cursos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'criar_curso',
                        ...data
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('✅ Curso criado com sucesso!', 'success');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('❌ Erro ao criar curso: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('❌ Erro de conexão ao criar curso', 'error');
            }
        }

        // Função para salvar edição de curso
        async function salvarEdicaoCurso() {
            const form = document.getElementById('editarCursoForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('api/cursos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'atualizar_curso',
                        ...data
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('✅ Curso atualizado com sucesso!', 'success');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('❌ Erro ao atualizar curso: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('❌ Erro de conexão ao atualizar curso', 'error');
            }
        }

        // Função para fechar modal
        function closeModal() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
        }

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

        function showConfirmModal(title, message, onConfirm) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay confirm-modal-overlay';
            modal.innerHTML = `
                <div class="modal-content" style="max-width: 460px;">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button onclick="closeConfirmModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p style="margin-bottom: 16px; color: #475569;">${message}</p>
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" onclick="closeConfirmModal()">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            document.getElementById('confirmDeleteBtn').onclick = async function() {
                closeConfirmModal();
                await onConfirm();
            };
        }

        function closeConfirmModal() {
            const modal = document.querySelector('.confirm-modal-overlay');
            if (modal) modal.remove();
        }

        // Adicionar estilos para modal e notificações
        const styles = `
            <style>
                .modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                }
                
                .modal-content {
                    background: white;
                    border-radius: 12px;
                    max-width: 600px;
                    width: 90%;
                    max-height: 80vh;
                    overflow-y: auto;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                }
                
                .modal-header {
                    padding: 20px;
                    border-bottom: 1px solid #e2e8f0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .modal-body {
                    padding: 20px;
                }
                
                .modal-footer {
                    padding: 20px;
                    border-top: 1px solid #e2e8f0;
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                }
                
                .close-btn {
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    color: #64748b;
                }
                
                .form-group {
                    margin-bottom: 15px;
                }
                
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: 600;
                    color: #374151;
                }
                
                .form-group input,
                .form-group select,
                .form-group textarea {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    font-size: 14px;
                }
                
                .form-group input:focus,
                .form-group select:focus,
                .form-group textarea:focus {
                    outline: none;
                    border-color: #3b82f6;
                    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                }
                
                .curso-details {
                    background: #f8fafc;
                    border-radius: 8px;
                    padding: 20px;
                }
                
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 12px;
                    padding-bottom: 8px;
                    border-bottom: 1px solid #e2e8f0;
                }
                
                .detail-label {
                    font-weight: 600;
                    color: #374151;
                }
                
                .detail-value {
                    color: #1f2937;
                }
                
                .status-badge {
                    padding: 4px 8px;
                    border-radius: 12px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                
                .status-ativo {
                    background: #dcfce7;
                    color: #166534;
                }
                
                .status-inativo {
                    background: #fef2f2;
                    color: #991b1b;
                }
                
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                    z-index: 10001;
                    min-width: 300px;
                    border-left: 4px solid #3b82f6;
                }
                
                .notification-success {
                    border-left-color: #10b981;
                }
                
                .notification-error {
                    border-left-color: #ef4444;
                }
                
                .notification-content {
                    padding: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .notification-close {
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    color: #64748b;
                    margin-left: 10px;
                }
            </style>
        `;
        
        // Adicionar estilos ao head
        if (!document.querySelector('#cursos-styles')) {
            const styleElement = document.createElement('div');
            styleElement.id = 'cursos-styles';
            styleElement.innerHTML = styles;
            document.head.appendChild(styleElement);
        }
    </script>
</body>
</html>


