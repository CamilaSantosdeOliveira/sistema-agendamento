<?php
echo "<h1>🔍 VERIFICANDO ESTRUTURA DA TABELA AGENDAMENTOS</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>📋 Estrutura da Tabela Agendamentos:</h3>";
$result = $conn->query("DESCRIBE agendamentos");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Erro ao verificar estrutura: " . $conn->error . "</p>";
}

echo "<br><h3>📊 Dados na Tabela Agendamentos:</h3>";
$result = $conn->query("SELECT * FROM agendamentos LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    $first = true;
    while ($row = $result->fetch_assoc()) {
        if ($first) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<th>$key</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum dado encontrado na tabela agendamentos.</p>";
}

echo "<br><h3>🔧 SOLUÇÃO:</h3>";
echo "<p>Vou corrigir o arquivo api/agendamentos.php para usar as colunas corretas!</p>";
?>















