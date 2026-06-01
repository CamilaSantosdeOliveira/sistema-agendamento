<?php
echo "<h1>🔍 VERIFICANDO CATEGORIAS DOS CURSOS</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar categorias únicas
echo "<h2>📊 Categorias únicas no banco:</h2>";
$result = $conn->query("SELECT DISTINCT categoria FROM cursos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
if ($result && $result->num_rows > 0) {
    echo "<div class='info'>Categorias encontradas:</div>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['categoria']}</div>";
    }
} else {
    echo "<div class='error'>❌ Nenhuma categoria encontrada</div>";
}

// Verificar todos os cursos com suas categorias
echo "<h2>📚 Todos os cursos e suas categorias:</h2>";
$result = $conn->query("SELECT id, nome, categoria FROM cursos ORDER BY nome");
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome do Curso</th><th>Categoria</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>" . ($row['categoria'] ?: 'Não definida') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Nenhum curso encontrado</div>";
}

$conn->close();
?>







