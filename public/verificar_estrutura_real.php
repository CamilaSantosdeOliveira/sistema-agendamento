<?php
echo "<h1>🔍 VERIFICANDO ESTRUTURA REAL DAS TABELAS</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados MySQL!</div>";

// 1. VERIFICAR TABELAS EXISTENTES
echo "<h2>📋 Tabelas no Banco:</h2>";
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        echo "<div class='info'>📄 Tabela: {$row[0]}</div>";
    }
}

// 2. VERIFICAR ESTRUTURA DA TABELA USUARIOS
echo "<h2>👥 Estrutura da tabela 'usuarios':</h2>";
$result = $conn->query("DESCRIBE usuarios");
if ($result) {
    echo "<table>";
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
    echo "<div class='error'>❌ Erro ao verificar estrutura: " . $conn->error . "</div>";
}

// 3. VERIFICAR ESTRUTURA DA TABELA CURSOS
echo "<h2>📚 Estrutura da tabela 'cursos':</h2>";
$result = $conn->query("DESCRIBE cursos");
if ($result) {
    echo "<table>";
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
    echo "<div class='error'>❌ Erro ao verificar estrutura: " . $conn->error . "</div>";
}

// 4. TESTAR CONSULTAS SIMPLES
echo "<h2>🧪 Testando Consultas:</h2>";

// Testar SELECT COUNT
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='success'>✅ SELECT COUNT usuarios: {$row['total']}</div>";
} else {
    echo "<div class='error'>❌ Erro SELECT COUNT usuarios: " . $conn->error . "</div>";
}

// Testar SELECT simples
$result = $conn->query("SELECT * FROM usuarios LIMIT 1");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='success'>✅ SELECT usuarios funciona - Primeiro registro: {$row['nome']}</div>";
} else {
    echo "<div class='error'>❌ Erro SELECT usuarios: " . $conn->error . "</div>";
}

// Testar SELECT COUNT cursos
$result = $conn->query("SELECT COUNT(*) as total FROM cursos");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='success'>✅ SELECT COUNT cursos: {$row['total']}</div>";
} else {
    echo "<div class='error'>❌ Erro SELECT COUNT cursos: " . $conn->error . "</div>";
}

$conn->close();
?>









