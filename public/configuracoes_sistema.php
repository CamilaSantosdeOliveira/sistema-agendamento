<?php
// Sistema de Configurações Gerais - Versão Corrigida
session_start();
include 'db.php';

// Criar tabela de configurações se não existir
$create_config_table = "
CREATE TABLE IF NOT EXISTS configuracoes_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descricao TEXT,
    categoria VARCHAR(50) DEFAULT 'geral',
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
    $conn->query($create_config_table);
} catch (Exception $e) {
    // Continuar mesmo se houver erro
}

// Inserir configurações padrão se não existirem
$configuracoes_padrao = [
    ['nome_sistema', 'EduConnect', 'Nome do sistema educacional', 'geral'],
    ['versao_sistema', '1.0.0', 'Versão atual do sistema', 'geral'],
    ['email_contato', 'contato@educonnect.com', 'Email de contato', 'geral'],
    ['telefone_contato', '(11) 99999-9999', 'Telefone de contato', 'geral'],
    ['endereco_sistema', 'Rua das Flores, 123 - São Paulo/SP', 'Endereço da instituição', 'geral'],
    ['notificacoes_email', '1', 'Ativar notificações por email', 'notificacoes'],
    ['notificacoes_sms', '0', 'Ativar notificações por SMS', 'notificacoes'],
    ['backup_automatico', '1', 'Backup automático diário', 'backup'],
    ['retencao_backup', '30', 'Dias de retenção de backup', 'backup'],
    ['limite_usuarios', '1000', 'Limite máximo de usuários', 'limites'],
    ['limite_cursos', '500', 'Limite máximo de cursos', 'limites'],
    ['sessao_timeout', '30', 'Timeout da sessão (minutos)', 'seguranca'],
    ['tentativas_login', '3', 'Tentativas de login permitidas', 'seguranca'],
    ['manutencao_ativa', '0', 'Modo manutenção ativo', 'sistema'],
    ['log_detalhado', '1', 'Log detalhado de atividades', 'sistema']
];

foreach ($configuracoes_padrao as $config) {
    try {
        $sql = "INSERT IGNORE INTO configuracoes_sistema (chave, valor, descricao, categoria) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $config[0], $config[1], $config[2], $config[3]);
            $stmt->execute();
        }
    } catch (Exception $e) {
        // Continuar mesmo se houver erro
    }
}

// Processar formulário
if (isset($_POST['acao']) && $_POST['acao'] === 'salvar') {
    try {
        foreach ($_POST['config'] as $chave => $valor) {
            $sql = "UPDATE configuracoes_sistema SET valor = ? WHERE chave = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ss", $valor, $chave);
                $stmt->execute();
            }
        }
        $mensagem = "✅ Configurações salvas com sucesso!";
    } catch (Exception $e) {
        $mensagem = "❌ Erro ao salvar configurações: " . $e->getMessage();
    }
}

// Buscar configurações
$configuracoes = [];
try {
    $sql = "SELECT * FROM configuracoes_sistema ORDER BY categoria, chave";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $configuracoes[] = $row;
        }
    }
} catch (Exception $e) {
    // Se houver erro, usar array vazio
    $configuracoes = [];
}

// Agrupar por categoria
$categorias = [];
foreach ($configuracoes as $config) {
    $categoria = $config['categoria'];
    if (!isset($categorias[$categoria])) {
        $categorias[$categoria] = [];
    }
    $categorias[$categoria][] = $config;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Sistema - EduConnect</title>
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

        .content {
            padding: 30px;
        }

        .mensagem {
            background: #dcfce7;
            border: 1px solid #16a34a;
            color: #166534;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .categoria {
            margin-bottom: 30px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
        }

        .categoria h3 {
            color: #374151;
            margin-bottom: 15px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .config-item {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .config-label {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .config-label strong {
            color: #374151;
            font-weight: 600;
        }

        .config-label small {
            color: #6b7280;
            font-size: 0.9em;
        }

        .config-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .config-input input[type="text"],
        .config-input input[type="email"],
        .config-input input[type="number"],
        .config-input select {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9em;
        }

        .config-input input[type="checkbox"] {
            width: 20px;
            height: 20px;
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

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .icon-geral { color: #3b82f6; }
        .icon-notificacoes { color: #f59e0b; }
        .icon-backup { color: #10b981; }
        .icon-limites { color: #ef4444; }
        .icon-seguranca { color: #8b5cf6; }
        .icon-sistema { color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Configurações do Sistema</h1>
            <p>Gerencie as preferências e configurações gerais do sistema</p>
        </div>

        <div class="content">
            <?php if (isset($mensagem)): ?>
            <div class="mensagem">
                <?php echo $mensagem; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="acao" value="salvar">

                <!-- Configurações Gerais -->
                <div class="categoria">
                    <h3><span class="icon-geral">🏢</span> Configurações Gerais</h3>
                    <?php foreach ($categorias['geral'] ?? [] as $config): ?>
                    <div class="config-item">
                        <div class="config-label">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $config['chave'])); ?></strong>
                            <small><?php echo $config['descricao']; ?></small>
                        </div>
                        <div class="config-input">
                            <input type="text" name="config[<?php echo $config['chave']; ?>]" 
                                   value="<?php echo htmlspecialchars($config['valor']); ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Notificações -->
                <div class="categoria">
                    <h3><span class="icon-notificacoes">🔔</span> Notificações</h3>
                    <?php foreach ($categorias['notificacoes'] ?? [] as $config): ?>
                    <div class="config-item">
                        <div class="config-label">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $config['chave'])); ?></strong>
                            <small><?php echo $config['descricao']; ?></small>
                        </div>
                        <div class="config-input">
                            <select name="config[<?php echo $config['chave']; ?>]">
                                <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Ativado</option>
                                <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Desativado</option>
                            </select>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Backup -->
                <div class="categoria">
                    <h3><span class="icon-backup">💾</span> Backup</h3>
                    <?php foreach ($categorias['backup'] ?? [] as $config): ?>
                    <div class="config-item">
                        <div class="config-label">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $config['chave'])); ?></strong>
                            <small><?php echo $config['descricao']; ?></small>
                        </div>
                        <div class="config-input">
                            <?php if ($config['chave'] === 'backup_automatico'): ?>
                            <select name="config[<?php echo $config['chave']; ?>]">
                                <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Ativado</option>
                                <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Desativado</option>
                            </select>
                            <?php else: ?>
                            <input type="number" name="config[<?php echo $config['chave']; ?>]" 
                                   value="<?php echo htmlspecialchars($config['valor']); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Limites -->
                <div class="categoria">
                    <h3><span class="icon-limites">📊</span> Limites do Sistema</h3>
                    <?php foreach ($categorias['limites'] ?? [] as $config): ?>
                    <div class="config-item">
                        <div class="config-label">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $config['chave'])); ?></strong>
                            <small><?php echo $config['descricao']; ?></small>
                        </div>
                        <div class="config-input">
                            <input type="number" name="config[<?php echo $config['chave']; ?>]" 
                                   value="<?php echo htmlspecialchars($config['valor']); ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Segurança -->
                <div class="categoria">
                    <h3><span class="icon-seguranca">🔒</span> Segurança</h3>
                    <?php foreach ($categorias['seguranca'] ?? [] as $config): ?>
                    <div class="config-item">
                        <div class="config-label">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $config['chave'])); ?></strong>
                            <small><?php echo $config['descricao']; ?></small>
                        </div>
                        <div class="config-input">
                            <input type="number" name="config[<?php echo $config['chave']; ?>]" 
                                   value="<?php echo htmlspecialchars($config['valor']); ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Sistema -->
                <div class="categoria">
                    <h3><span class="icon-sistema">⚙️</span> Sistema</h3>
                    <?php foreach ($categorias['sistema'] ?? [] as $config): ?>
                    <div class="config-item">
                        <div class="config-label">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $config['chave'])); ?></strong>
                            <small><?php echo $config['descricao']; ?></small>
                        </div>
                        <div class="config-input">
                            <?php if ($config['chave'] === 'manutencao_ativa' || $config['chave'] === 'log_detalhado'): ?>
                            <select name="config[<?php echo $config['chave']; ?>]">
                                <option value="1" <?php echo $config['valor'] == '1' ? 'selected' : ''; ?>>Ativado</option>
                                <option value="0" <?php echo $config['valor'] == '0' ? 'selected' : ''; ?>>Desativado</option>
                            </select>
                            <?php else: ?>
                            <input type="text" name="config[<?php echo $config['chave']; ?>]" 
                                   value="<?php echo htmlspecialchars($config['valor']); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Salvar Todas as Configurações
                    </button>
                    <a href="teste_todos_botoes.php" class="btn btn-secondary">
                        ← Voltar ao Teste
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


