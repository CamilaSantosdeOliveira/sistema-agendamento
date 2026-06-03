<?php
echo "<h1>🔍 VERIFICANDO CURSOS NO BANCO DE DADOS</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>📊 Cursos no banco de dados:</h3>";
$result = $conn->query("SELECT id, nome, categoria, nivel FROM cursos ORDER BY id");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Nível</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>" . ($row['categoria'] ?: 'NULL') . "</td>";
        echo "<td>" . ($row['nivel'] ?: 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>🔗 Links para testar:</h3>";
    $result->data_seek(0); // Voltar ao início
    while ($row = $result->fetch_assoc()) {
        echo "<p><a href='ver_detalhes_curso.php?id={$row['id']}' target='_blank'>Ver detalhes do curso: {$row['nome']} (ID: {$row['id']})</a></p>";
    }
} else {
    echo "<p style='color: red;'>❌ Nenhum curso encontrado no banco!</p>";
    echo "<p><a href='inserir_cursos_final.php'>🔄 Inserir Cursos</a></p>";
}

echo "<br><h3>🔗 Páginas do sistema:</h3>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
echo "<p><a href='cursos_completo.php'>📚 Página de Cursos</a></p>";
?>











