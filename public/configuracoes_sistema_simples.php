<?php
// Configurações do Sistema - Versão Simplificada
session_start();
include 'db.php';

// Criar tabela se não existir
$conn->query("CREATE TABLE IF NOT EXISTS configuracoes_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descricao TEXT,
    categoria VARCHAR(50) DEFAULT 'geral'
)");

// Inserir configuração de teste
$conn->query("INSERT IGNORE INTO configuracoes_sistema (chave, valor, descricao, categoria) 
              VALUES ('teste', 'funcionando', 'Teste de configuração', 'teste')");

// Buscar configurações
$configs = $conn->query("SELECT * FROM configuracoes_sistema LIMIT 10");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Configurações do Sistema - Teste</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>⚙️ Configurações do Sistema - Teste</h1>
    
    <div class="success">
        <h2>✅ Sistema Funcionando!</h2>
        <p>Esta página confirma que as configurações do sistema estão funcionando.</p>
    </div>

    <h3>Configurações Cadastradas:</h3>
    <table>
        <tr>
            <th>Chave</th>
            <th>Valor</th>
            <th>Descrição</th>
            <th>Categoria</th>
        </tr>
        <?php while ($config = $configs->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($config['chave']); ?></td>
            <td><?php echo htmlspecialchars($config['valor']); ?></td>
            <td><?php echo htmlspecialchars($config['descricao']); ?></td>
            <td><?php echo htmlspecialchars($config['categoria']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="configuracoes.php">← Voltar às Configurações</a></p>
</body>
</html>







