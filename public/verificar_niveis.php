<?php
echo "<h1>🔍 VERIFICANDO NÍVEIS DOS CURSOS</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar níveis únicos
echo "<h2>📊 Níveis únicos no banco:</h2>";
$result = $conn->query("SELECT DISTINCT nivel FROM cursos WHERE nivel IS NOT NULL AND nivel != '' ORDER BY nivel");
if ($result && $result->num_rows > 0) {
    echo "<div class='info'>Níveis encontrados:</div>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['nivel']}</div>";
    }
} else {
    echo "<div class='error'>❌ Nenhum nível encontrado</div>";
}

// Verificar todos os cursos com seus níveis
echo "<h2>📚 Todos os cursos e seus níveis:</h2>";
$result = $conn->query("SELECT id, nome, nivel FROM cursos ORDER BY nome");
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome do Curso</th><th>Nível</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>" . ($row['nivel'] ?: 'Não definido') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Nenhum curso encontrado</div>";
}

$conn->close();
?>







