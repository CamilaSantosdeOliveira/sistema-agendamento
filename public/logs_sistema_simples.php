<?php
// Logs do Sistema - Versão Simplificada
session_start();

echo "<h1>📊 Logs do Sistema - Teste</h1>";

// Teste de conexão
try {
    include 'db.php';
    echo "✅ Conexão com banco: OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
    exit;
}

// Criar tabela se não existir
try {
    $conn->query("CREATE TABLE IF NOT EXISTS logs_sistema (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NULL,
        acao VARCHAR(100) NOT NULL,
        tabela_afetada VARCHAR(50) NULL,
        ip_address VARCHAR(45) NULL,
        data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabela logs_sistema: OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na tabela: " . $e->getMessage() . "<br>";
}

// Inserir log de teste
try {
    $sql = "INSERT INTO logs_sistema (acao, tabela_afetada, ip_address) VALUES ('TESTE_ACESSO', 'teste', '127.0.0.1')";
    $conn->query($sql);
    echo "✅ Inserção de log: OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na inserção: " . $e->getMessage() . "<br>";
}

// Buscar logs
try {
    $result = $conn->query("SELECT * FROM logs_sistema ORDER BY data_hora DESC LIMIT 10");
    echo "✅ Busca de logs: OK<br>";
    
    echo "<h3>Logs Recentes:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Ação</th><th>Tabela</th><th>IP</th><th>Data/Hora</th></tr>";
    
    while ($log = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $log['id'] . "</td>";
        echo "<td>" . htmlspecialchars($log['acao']) . "</td>";
        echo "<td>" . htmlspecialchars($log['tabela_afetada']) . "</td>";
        echo "<td>" . htmlspecialchars($log['ip_address']) . "</td>";
        echo "<td>" . $log['data_hora'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "❌ Erro na busca: " . $e->getMessage() . "<br>";
}

// Estatísticas
try {
    $total = $conn->query("SELECT COUNT(*) as total FROM logs_sistema")->fetch_assoc()['total'];
    $hoje = $conn->query("SELECT COUNT(*) as total FROM logs_sistema WHERE DATE(data_hora) = CURDATE()")->fetch_assoc()['total'];
    
    echo "<h3>Estatísticas:</h3>";
    echo "<p>Total de logs: $total</p>";
    echo "<p>Logs hoje: $hoje</p>";
    
} catch (Exception $e) {
    echo "❌ Erro nas estatísticas: " . $e->getMessage() . "<br>";
}

echo "<p><a href='configuracoes.php'>← Voltar às Configurações</a></p>";
?>







