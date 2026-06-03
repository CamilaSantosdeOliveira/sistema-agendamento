<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>🔍 Verificando Estrutura da Tabela Agendamentos</h2>";

try {
    include 'db.php';
    echo "✅ Conexão com banco OK!<br>";
    
    // Verificar estrutura da tabela
    $result = $conn->query("DESCRIBE agendamentos");
    if ($result) {
        echo "<h3>📋 Colunas da tabela agendamentos:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Erro ao verificar estrutura: " . $conn->error . "<br>";
    }
    
    // Verificar alguns registros
    $result = $conn->query("SELECT * FROM agendamentos LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h3>📄 Exemplo de registro:</h3>";
        echo "<pre>" . print_r($row, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎯 Próximo passo:</h3>";
echo "<p>Com essas informações, posso corrigir as consultas SQL!</p>";
?>




