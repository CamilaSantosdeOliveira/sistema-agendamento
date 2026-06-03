<?php
// Logs Ultra Simples - Teste
echo "<h1>📊 Logs - Teste Ultra Simples</h1>";

// Teste 1: Conexão
echo "<h2>1. Teste de Conexão</h2>";
try {
    include 'db.php';
    echo "✅ Conexão: OK<br>";
} catch (Exception $e) {
    echo "❌ Conexão: " . $e->getMessage() . "<br>";
    exit;
}

// Teste 2: Criar tabela
echo "<h2>2. Teste da Tabela</h2>";
try {
    $conn->query("CREATE TABLE IF NOT EXISTS logs_sistema (
        id INT AUTO_INCREMENT PRIMARY KEY,
        acao VARCHAR(100) NOT NULL,
        data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabela: OK<br>";
} catch (Exception $e) {
    echo "❌ Tabela: " . $e->getMessage() . "<br>";
}

// Teste 3: Inserir log
echo "<h2>3. Teste de Inserção</h2>";
try {
    $conn->query("INSERT INTO logs_sistema (acao) VALUES ('TESTE_ULTRA_SIMPLES')");
    echo "✅ Inserção: OK<br>";
} catch (Exception $e) {
    echo "❌ Inserção: " . $e->getMessage() . "<br>";
}

// Teste 4: Buscar logs
echo "<h2>4. Teste de Busca</h2>";
try {
    $result = $conn->query("SELECT * FROM logs_sistema ORDER BY data_hora DESC LIMIT 5");
    echo "✅ Busca: OK<br>";
    
    echo "<h3>Logs:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Ação</th><th>Data/Hora</th></tr>";
    
    while ($log = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $log['id'] . "</td>";
        echo "<td>" . $log['acao'] . "</td>";
        echo "<td>" . $log['data_hora'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "❌ Busca: " . $e->getMessage() . "<br>";
}

echo "<p><a href='configuracoes.php'>← Voltar</a></p>";
?>


