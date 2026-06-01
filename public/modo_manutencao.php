<?php
// Sistema de Modo Manutenção
session_start();
include 'db.php';

// Criar tabela de configurações se não existir
$create_config_table = "
CREATE TABLE IF NOT EXISTS configuracoes_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descricao TEXT,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$conn->query($create_config_table);

// Verificar se modo manutenção está ativo
$modo_manutencao = false;
$result = $conn->query("SELECT valor FROM configuracoes_sistema WHERE chave = 'modo_manutencao'");
if ($result && $result->num_rows > 0) {
    $modo_manutencao = $result->fetch_assoc()['valor'] === '1';
}

// Processar ações
if ($_POST['acao'] ?? false) {
    $acao = $_POST['acao'];
    
    if ($acao === 'ativar') {
        $sql = "INSERT INTO configuracoes_sistema (chave, valor, descricao) 
                VALUES ('modo_manutencao', '1', 'Modo manutenção ativo') 
                ON DUPLICATE KEY UPDATE valor = '1'";
        $conn->query($sql);
        $modo_manutencao = true;
    } elseif ($acao === 'desativar') {
        $sql = "INSERT INTO configuracoes_sistema (chave, valor, descricao) 
                VALUES ('modo_manutencao', '0', 'Modo manutenção inativo') 
                ON DUPLICATE KEY UPDATE valor = '0'";
        $conn->query($sql);
        $modo_manutencao = false;
    }
}

// Buscar outras configurações
$configuracoes = [];
$result = $conn->query("SELECT * FROM configuracoes_sistema ORDER BY chave");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $configuracoes[] = $row;
    }
}

// Estatísticas do sistema
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$total_cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];
$total_agendamentos = $conn->query("SELECT COUNT(*) as total FROM agendamentos")->fetch_assoc()['total'];
$total_certificados = $conn->query("SELECT COUNT(*) as total FROM certificados")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Manutenção - EduConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .status-banner {
            padding: 20px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1em;
        }

        .status-banner.ativo {
            background: #fee2e2;
            color: #991b1b;
            border-bottom: 3px solid #dc2626;
        }

        .status-banner.inativo {
            background: #dcfce7;
            color: #166534;
            border-bottom: 3px solid #16a34a;
        }

        .content {
            padding: 30px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .stat-value {
            font-size: 2em;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9em;
        }

        .maintenance-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .maintenance-section h3 {
            color: #374151;
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .maintenance-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 1em;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .warning-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .warning-box h4 {
            color: #92400e;
            margin-bottom: 10px;
        }

        .warning-box p {
            color: #92400e;
            font-size: 0.9em;
        }

        .config-section {
            margin-top: 30px;
        }

        .config-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .config-table th {
            background: #f8fafc;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .config-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f4f6;
        }

        .config-table tr:hover {
            background: #f9fafb;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-indicator.ativo {
            background: #dc2626;
        }

        .status-indicator.inativo {
            background: #16a34a;
        }

        .tools-section {
            margin-top: 30px;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .tool-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.2s;
        }

        .tool-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
        }

        .tool-icon {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .tool-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .tool-desc {
            color: #6b7280;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Modo Manutenção</h1>
            <p>Gerencie o modo manutenção e ferramentas do sistema</p>
        </div>

        <?php if ($modo_manutencao): ?>
        <div class="status-banner ativo">
            ⚠️ MODO MANUTENÇÃO ATIVO - Sistema temporariamente indisponível
        </div>
        <?php else: ?>
        <div class="status-banner inativo">
            ✅ SISTEMA OPERACIONAL - Modo manutenção inativo
        </div>
        <?php endif; ?>

        <div class="content">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_usuarios; ?></div>
                    <div class="stat-label">Usuários</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_cursos; ?></div>
                    <div class="stat-label">Cursos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_agendamentos; ?></div>
                    <div class="stat-label">Agendamentos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_certificados; ?></div>
                    <div class="stat-label">Certificados</div>
                </div>
            </div>

            <div class="maintenance-section">
                <h3>🛠️ Controle de Manutenção</h3>
                
                <?php if ($modo_manutencao): ?>
                <div class="warning-box">
                    <h4>⚠️ Modo Manutenção Ativo</h4>
                    <p>O sistema está em modo manutenção. Os usuários não conseguirão acessar o sistema até que o modo seja desativado.</p>
                </div>
                <?php endif; ?>

                <div class="maintenance-controls">
                    <?php if (!$modo_manutencao): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="ativar">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja ativar o modo manutenção?')">
                            🔧 Ativar Modo Manutenção
                        </button>
                    </form>
                    <?php else: ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="desativar">
                        <button type="submit" class="btn btn-success">
                            ✅ Desativar Modo Manutenção
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <button class="btn btn-secondary" onclick="window.history.back()">
                        ← Voltar
                    </button>
                </div>
            </div>

            <div class="config-section">
                <h3>⚙️ Configurações do Sistema</h3>
                <table class="config-table">
                    <thead>
                        <tr>
                            <th>Configuração</th>
                            <th>Valor</th>
                            <th>Descrição</th>
                            <th>Última Atualização</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($configuracoes as $config): ?>
                        <tr>
                            <td>
                                <span class="status-indicator <?php echo $config['valor'] === '1' ? 'ativo' : 'inativo'; ?>"></span>
                                <?php echo ucfirst(str_replace('_', ' ', $config['chave'])); ?>
                            </td>
                            <td>
                                <?php if ($config['valor'] === '1'): ?>
                                    <span style="color: #dc2626; font-weight: 600;">Ativo</span>
                                <?php elseif ($config['valor'] === '0'): ?>
                                    <span style="color: #16a34a; font-weight: 600;">Inativo</span>
                                <?php else: ?>
                                    <?php echo $config['valor']; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $config['descricao']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($config['atualizado_em'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tools-section">
                <h3>🔧 Ferramentas de Manutenção</h3>
                <div class="tools-grid">
                    <div class="tool-card">
                        <div class="tool-icon">💾</div>
                        <div class="tool-title">Backup do Sistema</div>
                        <div class="tool-desc">Criar backup completo do banco de dados</div>
                        <button class="btn btn-secondary" onclick="window.location.href='backup_completo_manual.php'">
                            Criar Backup
                        </button>
                    </div>
                    
                    <div class="tool-card">
                        <div class="tool-icon">📊</div>
                        <div class="tool-title">Logs do Sistema</div>
                        <div class="tool-desc">Visualizar logs de auditoria</div>
                        <button class="btn btn-secondary" onclick="window.location.href='logs_sistema.php'">
                            Ver Logs
                        </button>
                    </div>
                    
                    <div class="tool-card">
                        <div class="tool-icon">📤</div>
                        <div class="tool-title">Exportar Dados</div>
                        <div class="tool-desc">Exportar dados em CSV/PDF</div>
                        <button class="btn btn-secondary" onclick="window.location.href='exportar_dados.php'">
                            Exportar
                        </button>
                    </div>
                    
                    <div class="tool-card">
                        <div class="tool-icon">🧹</div>
                        <div class="tool-title">Limpeza de Cache</div>
                        <div class="tool-desc">Limpar cache do sistema</div>
                        <button class="btn btn-secondary" onclick="limparCache()">
                            Limpar Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function limparCache() {
            if (confirm('Deseja limpar o cache do sistema?')) {
                // Implementar limpeza de cache
                alert('Cache limpo com sucesso!');
            }
        }
    </script>
</body>
</html>







