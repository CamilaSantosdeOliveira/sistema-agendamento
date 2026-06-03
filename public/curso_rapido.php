<?php
echo "<h1>🚀 INSERINDO CURSO RÁPIDO</h1>";
include 'db.php';
if (!$conn) { echo "<p style='color: red;'>❌ Erro de conexão</p>"; exit; }

echo "<h3>Inserindo 1 curso de teste...</h3>";

$sql = "INSERT INTO cursos (nome, descricao, preco, duracao, status) VALUES ('Desenvolvimento Web', 'Aprenda HTML, CSS e JavaScript', 299.90, '3 meses', 'ativo')";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Curso inserido com sucesso!</p>";
    echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🚀 Ver Dashboard</a></p>";
} else {
    echo "<p style='color: red;'>❌ Erro: " . $conn->error . "</p>";
}

$conn->close();
?>


