<?php
echo "<h1>🔍 DEBUG - PÁGINA DE PROFESSORES</h1>";
include 'db.php';

echo "<h3>1️⃣ Testando consulta básica:</h3>";
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
if ($result) {
    $count = $result->fetch_assoc()['total'];
    echo "<p>✅ Total de professores: {$count}</p>";
} else {
    echo "<p style='color: red;'>❌ Erro na consulta básica: " . $conn->error . "</p>";
}

echo "<h3>2️⃣ Testando consulta completa:</h3>";
$sql = "
    SELECT u.id, u.nome, u.email, u.ativo, u.criado_em,
           COUNT(DISTINCT a.id) as agendamentos_count
    FROM usuarios u
    LEFT JOIN agendamentos a ON u.id = a.usuario_id
    WHERE u.tipo_usuario = 'professor'
    GROUP BY u.id
    ORDER BY u.nome
";

echo "<p><strong>SQL:</strong> " . htmlspecialchars($sql) . "</p>";

$professores_result = $conn->query($sql);

if ($professores_result) {
    echo "<p style='color: green;'>✅ Consulta executada com sucesso!</p>";
    echo "<p><strong>Número de linhas:</strong> {$professores_result->num_rows}</p>";
    
    if ($professores_result->num_rows > 0) {
        echo "<h3>📊 Dados retornados:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Ativo</th><th>Agendamentos</th></tr>";
        
        while ($row = $professores_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>" . ($row['ativo'] ? 'Sim' : 'Não') . "</td>";
            echo "<td>{$row['agendamentos_count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Nenhum professor retornado!</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Erro na consulta: " . $conn->error . "</p>";
}

echo "<h3>3️⃣ Verificando estrutura da tabela agendamentos:</h3>";
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
}

echo "<h3>🔗 Links para testar:</h3>";
echo "<p><a href='professores.php'>👨‍🏫 Ver Página de Professores</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
?>











