<?php
echo "<h1>🔍 VERIFICANDO DADOS REAIS DO BANCO</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>📋 Estrutura da Tabela Cursos:</h3>";
$result = $conn->query("DESCRIBE cursos");
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

echo "<br><h3>📊 Dados Reais dos Cursos:</h3>";
$result = $conn->query("SELECT * FROM cursos ORDER BY nome");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Nível</th><th>Preço</th><th>Duração</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>" . ($row['categoria'] ?: 'NULL') . "</td>";
        echo "<td>" . ($row['nivel'] ?: 'NULL') . "</td>";
        echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
        echo "<td>{$row['duracao']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum curso encontrado.</p>";
}

echo "<br><h3>🔧 SOLUÇÃO:</h3>";
echo "<p>Se categoria e nível estão NULL, precisamos executar o script de atualização novamente.</p>";
echo "<p><a href='adicionar_categoria_nivel.php' style='color: blue;'>🔄 Executar Atualização de Categoria e Nível</a></p>";
?>


