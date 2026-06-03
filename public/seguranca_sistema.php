<?php
// Sistema de Segurança
session_start();
include 'db.php';

// Criar tabela de logs de segurança se não existir
$create_security_logs = "
CREATE TABLE IF NOT EXISTS logs_seguranca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    detalhes TEXT,
    nivel_risco ENUM('baixo', 'medio', 'alto', 'critico') DEFAULT 'baixo',
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario_id),
    INDEX idx_acao (acao),
    INDEX idx_nivel_risco (nivel_risco),
    INDEX idx_data (data_hora)
)";

$conn->query($create_security_logs);

// Criar tabela de tentativas de login se não existir
$create_login_attempts = "
CREATE TABLE IF NOT EXISTS tentativas_login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    sucesso BOOLEAN DEFAULT FALSE,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_ip (ip_address),
    INDEX idx_data (data_hora)
)";

$conn->query($create_login_attempts);

// Função para registrar log de segurança
function registrarLogSeguranca($acao, $nivel_risco = 'baixo', $detalhes = null) {
    global $conn;
    
    try {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'desconhecido';
        
        $sql = "INSERT INTO logs_seguranca (usuario_id, acao, ip_address, user_agent, detalhes, nivel_risco) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("isssss", $usuario_id, $acao, $ip, $user_agent, $detalhes, $nivel_risco);
            $stmt->execute();
        }
    } catch (Exception $e) {
        // Silenciar erros de log
    }
}

// Registrar acesso à página de segurança
registrarLogSeguranca('ACESSO_PAGINA_SEGURANCA', 'baixo');

// Buscar estatísticas de segurança
$stats = [];
try {
    // Total de logs de segurança
    $total_logs = $conn->query("SELECT COUNT(*) as total FROM logs_seguranca")->fetch_assoc()['total'] ?? 0;
    
    // Logs por nível de risco
    $logs_baixo = $conn->query("SELECT COUNT(*) as total FROM logs_seguranca WHERE nivel_risco = 'baixo'")->fetch_assoc()['total'] ?? 0;
    $logs_medio = $conn->query("SELECT COUNT(*) as total FROM logs_seguranca WHERE nivel_risco = 'medio'")->fetch_assoc()['total'] ?? 0;
    $logs_alto = $conn->query("SELECT COUNT(*) as total FROM logs_seguranca WHERE nivel_risco = 'alto'")->fetch_assoc()['total'] ?? 0;
    $logs_critico = $conn->query("SELECT COUNT(*) as total FROM logs_seguranca WHERE nivel_risco = 'critico'")->fetch_assoc()['total'] ?? 0;
    
    // Tentativas de login
    $tentativas_total = $conn->query("SELECT COUNT(*) as total FROM tentativas_login")->fetch_assoc()['total'] ?? 0;
    $tentativas_falha = $conn->query("SELECT COUNT(*) as total FROM tentativas_login WHERE sucesso = 0")->fetch_assoc()['total'] ?? 0;
    $tentativas_sucesso = $conn->query("SELECT COUNT(*) as total FROM tentativas_login WHERE sucesso = 1")->fetch_assoc()['total'] ?? 0;
    
    // IPs suspeitos (muitas tentativas de falha)
    $ips_suspeitos = $conn->query("SELECT ip_address, COUNT(*) as tentativas FROM tentativas_login WHERE sucesso = 0 GROUP BY ip_address HAVING tentativas > 5")->num_rows ?? 0;
    
    $stats = [
        'total_logs' => $total_logs,
        'logs_baixo' => $logs_baixo,
        'logs_medio' => $logs_medio,
        'logs_alto' => $logs_alto,
        'logs_critico' => $logs_critico,
        'tentativas_total' => $tentativas_total,
        'tentativas_falha' => $tentativas_falha,
        'tentativas_sucesso' => $tentativas_sucesso,
        'ips_suspeitos' => $ips_suspeitos
    ];
} catch (Exception $e) {
    $stats = [
        'total_logs' => 0, 'logs_baixo' => 0, 'logs_medio' => 0, 'logs_alto' => 0, 'logs_critico' => 0,
        'tentativas_total' => 0, 'tentativas_falha' => 0, 'tentativas_sucesso' => 0, 'ips_suspeitos' => 0
    ];
}

// Buscar logs de segurança recentes
$logs_recentes = [];
try {
    $sql = "SELECT l.*, u.nome as usuario_nome 
            FROM logs_seguranca l 
            LEFT JOIN usuarios u ON l.usuario_id = u.id 
            ORDER BY l.data_hora DESC 
            LIMIT 20";
    
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $logs_recentes[] = $row;
        }
    }
} catch (Exception $e) {
    $logs_recentes = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segurança do Sistema - EduConnect</title>
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
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
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
            margin-bottom: 5px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9em;
        }

        .stat-value.baixo { color: #10b981; }
        .stat-value.medio { color: #f59e0b; }
        .stat-value.alto { color: #ef4444; }
        .stat-value.critico { color: #dc2626; }

        .security-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .security-section h3 {
            color: #374151;
            margin-bottom: 20px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .security-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .security-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
        }

        .security-card:hover {
            border-color: #8b5cf6;
            transform: translateY(-2px);
        }

        .security-card h4 {
            color: #374151;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .security-card p {
            color: #6b7280;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .security-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .status-seguro { background: #dcfce7; color: #166534; }
        .status-atencao { background: #fef3c7; color: #92400e; }
        .status-risco { background: #fee2e2; color: #991b1b; }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logs-table th {
            background: #f8fafc;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .logs-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f4f6;
        }

        .logs-table tr:hover {
            background: #f9fafb;
        }

        .risk-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .risk-baixo { background: #dcfce7; color: #166534; }
        .risk-medio { background: #fef3c7; color: #92400e; }
        .risk-alto { background: #fee2e2; color: #991b1b; }
        .risk-critico { background: #fecaca; color: #7f1d1d; }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            background: #6b7280;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }

        .btn:hover {
            background: #4b5563;
        }

        .btn-primary {
            background: #8b5cf6;
        }

        .btn-primary:hover {
            background: #7c3aed;
        }

        .no-logs {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Segurança do Sistema</h1>
            <p>Monitoramento e configurações de segurança</p>
        </div>

        <div class="content">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_logs']; ?></div>
                    <div class="stat-label">Total de Logs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value baixo"><?php echo $stats['logs_baixo']; ?></div>
                    <div class="stat-label">Risco Baixo</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value medio"><?php echo $stats['logs_medio']; ?></div>
                    <div class="stat-label">Risco Médio</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value alto"><?php echo $stats['logs_alto']; ?></div>
                    <div class="stat-label">Risco Alto</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value critico"><?php echo $stats['logs_critico']; ?></div>
                    <div class="stat-label">Risco Crítico</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['ips_suspeitos']; ?></div>
                    <div class="stat-label">IPs Suspeitos</div>
                </div>
            </div>

            <div class="security-section">
                <h3>🛡️ Status de Segurança</h3>
                <div class="security-grid">
                    <div class="security-card">
                        <h4>🔐 Autenticação</h4>
                        <p>Monitoramento de tentativas de login e acesso</p>
                        <div class="security-status status-seguro">Seguro</div>
                        <p><small>Total: <?php echo $stats['tentativas_total']; ?> | Sucesso: <?php echo $stats['tentativas_sucesso']; ?> | Falha: <?php echo $stats['tentativas_falha']; ?></small></p>
                    </div>
                    
                    <div class="security-card">
                        <h4>🌐 Acesso por IP</h4>
                        <p>Controle de acesso por endereço IP</p>
                        <div class="security-status status-seguro">Ativo</div>
                        <p><small>IPs suspeitos detectados: <?php echo $stats['ips_suspeitos']; ?></small></p>
                    </div>
                    
                    <div class="security-card">
                        <h4>📊 Auditoria</h4>
                        <p>Logs detalhados de todas as atividades</p>
                        <div class="security-status status-seguro">Ativo</div>
                        <p><small>Logs de segurança: <?php echo $stats['total_logs']; ?></small></p>
                    </div>
                    
                    <div class="security-card">
                        <h4>🔒 Sessões</h4>
                        <p>Controle de sessões e timeouts</p>
                        <div class="security-status status-seguro">Configurado</div>
                        <p><small>Timeout automático ativo</small></p>
                    </div>
                </div>
            </div>

            <div class="security-section">
                <h3>📋 Logs de Segurança Recentes</h3>
                <?php if (empty($logs_recentes)): ?>
                <div class="no-logs">
                    <h3>📝 Nenhum log de segurança encontrado</h3>
                    <p>Os logs de segurança aparecerão aqui conforme as atividades do sistema.</p>
                </div>
                <?php else: ?>
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>IP</th>
                            <th>Nível de Risco</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs_recentes as $log): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['data_hora'])); ?></td>
                            <td><?php echo $log['usuario_nome'] ?: 'Sistema'; ?></td>
                            <td><?php echo htmlspecialchars($log['acao']); ?></td>
                            <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                            <td>
                                <span class="risk-badge risk-<?php echo $log['nivel_risco']; ?>">
                                    <?php echo ucfirst($log['nivel_risco']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <a href="teste_todos_botoes.php" class="btn btn-primary">← Voltar ao Teste</a>
                <a href="configuracoes_sistema.php" class="btn">⚙️ Configurações</a>
                <a href="logs_sistema.php" class="btn">📊 Logs Gerais</a>
            </div>
        </div>
    </div>
</body>
</html>









