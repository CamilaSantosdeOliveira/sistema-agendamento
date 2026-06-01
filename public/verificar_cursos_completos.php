<?php
echo "<h1>🔍 VERIFICANDO CURSOS COM CATEGORIA E NÍVEL</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>📋 Todos os Cursos:</h3>";
$result = $conn->query("SELECT * FROM cursos ORDER BY nome");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Nível</th><th>Preço</th><th>Duração</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>" . ($row['categoria'] ?: 'VAZIO') . "</td>";
        echo "<td>" . ($row['nivel'] ?: 'VAZIO') . "</td>";
        echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
        echo "<td>{$row['duracao']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum curso encontrado.</p>";
}

echo "<br><h3>🔗 Links:</h3>";
echo "<p><a href='cursos.php' style='color: blue;'>📚 Ver Página de Cursos</a></p>";
echo "<p><a href='dashboard_final.php' style='color: blue;'>📊 Ver Dashboard</a></p>";
?>









