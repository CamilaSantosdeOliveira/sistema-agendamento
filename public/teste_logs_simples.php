<?php
// Teste Simples dos Logs
echo "<h1>🧪 Teste Simples dos Logs</h1>";

// Teste 1: Conexão com banco
echo "<h2>1. Teste de Conexão</h2>";
try {
    include 'db.php';
    echo "✅ Conexão com banco: OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
    exit;
}

// Teste 2: Criar tabela
echo "<h2>2. Teste de Criação da Tabela</h2>";
try {
    $create_logs_table = "
    CREATE TABLE IF NOT EXISTS logs_sistema (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        acao VARCHAR(100) NOT NULL,
        tabela_afetada VARCHAR(50),
        registro_id INT,
        dados_anteriores TEXT,
        dados_novos TEXT,
        ip_address VARCHAR(45),
        user_agent TEXT,
        data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_usuario (usuario_id),
        INDEX idx_acao (acao),
        INDEX idx_data (data_hora)
    )";
    
    $result = $conn->query($create_logs_table);
    if ($result) {
        echo "✅ Tabela criada/verificada: OK<br>";
    } else {
        echo "❌ Erro ao criar tabela: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro na criação da tabela: " . $e->getMessage() . "<br>";
}

// Teste 3: Inserir log de teste
echo "<h2>3. Teste de Inserção</h2>";
try {
    $sql = "INSERT INTO logs_sistema (acao, tabela_afetada, ip_address, user_agent) 
            VALUES ('TESTE_INSERCAO', 'teste', '127.0.0.1', 'Teste')";
    
    $result = $conn->query($sql);
    if ($result) {
        echo "✅ Inserção de teste: OK<br>";
    } else {
        echo "❌ Erro na inserção: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro na inserção: " . $e->getMessage() . "<br>";
}

// Teste 4: Consultar logs
echo "<h2>4. Teste de Consulta</h2>";
try {
    $sql = "SELECT COUNT(*) as total FROM logs_sistema";
    $result = $conn->query($sql);
    if ($result) {
        $total = $result->fetch_assoc()['total'];
        echo "✅ Consulta: OK - Total de logs: $total<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro na consulta: " . $e->getMessage() . "<br>";
}

// Teste 5: Verificar estrutura da tabela
echo "<h2>5. Estrutura da Tabela</h2>";
try {
    $result = $conn->query("DESCRIBE logs_sistema");
    if ($result) {
        echo "✅ Estrutura da tabela:<br>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Erro ao verificar estrutura: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar estrutura: " . $e->getMessage() . "<br>";
}

echo "<h2>✅ Teste Concluído!</h2>";
echo "<p><a href='logs_sistema.php'>🔗 Tentar acessar Logs do Sistema</a></p>";
?>









