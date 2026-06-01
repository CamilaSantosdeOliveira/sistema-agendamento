<?php
echo "<h1>🔍 VERIFICANDO ESTRUTURA DO BANCO</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar tabelas existentes
echo "<h2>📋 Tabelas no banco:</h2>";
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        echo "<div class='info'>📄 Tabela: {$row[0]}</div>";
    }
} else {
    echo "<div class='error'>❌ Erro ao listar tabelas</div>";
}

// Verificar estrutura da tabela usuarios (se existir)
echo "<h2>👥 Estrutura da tabela 'usuarios':</h2>";
$result = $conn->query("DESCRIBE usuarios");
if ($result) {
    echo "<table border='1' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Tabela 'usuarios' não existe!</div>";
}

// Verificar estrutura da tabela cursos (se existir)
echo "<h2>📚 Estrutura da tabela 'cursos':</h2>";
$result = $conn->query("DESCRIBE cursos");
if ($result) {
    echo "<table border='1' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Tabela 'cursos' não existe!</div>";
}

$conn->close();
?>







