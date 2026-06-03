<?php
echo "<h1>🔍 VERIFICANDO DATAS DOS PROFESSORES</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar estrutura da tabela
echo "<h2>🏗️ Campos de data na tabela 'usuarios':</h2>";
$result = $conn->query("DESCRIBE usuarios");
if ($result) {
    echo "<table>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        if (strpos($row['Type'], 'timestamp') !== false || strpos($row['Type'], 'date') !== false) {
            echo "<tr>";
            echo "<td><strong>{$row['Field']}</strong></td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
}

// Verificar dados dos professores
echo "<h2>👨‍🏫 Datas dos Professores:</h2>";
$result = $conn->query("
    SELECT id, nome, email, criado_em, atualizado_em
    FROM usuarios 
    WHERE tipo_usuario = 'professor'
    ORDER BY nome
");

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Criado em</th><th>Atualizado em</th></tr>";
    
    while ($professor = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$professor['id']}</td>";
        echo "<td><strong>{$professor['nome']}</strong></td>";
        echo "<td>{$professor['email']}</td>";
        echo "<td>" . ($professor['criado_em'] ? $professor['criado_em'] : '<span style="color:red;">NULL</span>') . "</td>";
        echo "<td>" . ($professor['atualizado_em'] ? $professor['atualizado_em'] : '<span style="color:red;">NULL</span>') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Nenhum professor encontrado!</div>";
}

// Verificar se há valores NULL
echo "<h2>🔍 Verificando valores NULL:</h2>";
$result = $conn->query("
    SELECT COUNT(*) as total_null_criado
    FROM usuarios 
    WHERE tipo_usuario = 'professor' AND criado_em IS NULL
");
if ($result) {
    $null_count = $result->fetch_assoc()['total_null_criado'];
    echo "<div class='info'>📊 Professores com 'criado_em' NULL: {$null_count}</div>";
}

$result = $conn->query("
    SELECT COUNT(*) as total_null_atualizado
    FROM usuarios 
    WHERE tipo_usuario = 'professor' AND atualizado_em IS NULL
");
if ($result) {
    $null_count = $result->fetch_assoc()['total_null_atualizado'];
    echo "<div class='info'>📊 Professores com 'atualizado_em' NULL: {$null_count}</div>";
}

$conn->close();

echo "<h2>🎯 DIAGNÓSTICO:</h2>";
echo "<p><strong>Se as datas aparecem como NULL:</strong></p>";
echo "<ul>";
echo "<li>❌ Campo não tem valor padrão</li>";
echo "<li>❌ Dados foram inseridos sem data</li>";
echo "<li>✅ Pode ser corrigido com UPDATE</li>";
echo "</ul>";

echo "<p><strong>Se as datas aparecem corretamente:</strong></p>";
echo "<ul>";
echo "<li>✅ Dados estão corretos</li>";
echo "<li>✅ Problema pode ser na exibição</li>";
echo "<li>✅ Verificar formato da data</li>";
echo "</ul>";

echo "<p><a href='professores.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;'>📊 Ver Página Professores</a></p>";
?>









