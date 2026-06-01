<?php
echo "<h1>🔍 VERIFICANDO PROFESSORES NO BANCO DE DADOS</h1>";
include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>📊 Professores no banco de dados:</h3>";
$result = $conn->query("SELECT id, nome, email, tipo_usuario, ativo FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Ativo</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['tipo_usuario']}</td>";
        echo "<td>" . ($row['ativo'] ? 'Sim' : 'Não') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>🔗 Links para testar:</h3>";
    echo "<p><a href='professores.php'>👨‍🏫 Ver Página de Professores</a></p>";
    echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
} else {
    echo "<p style='color: red;'>❌ Nenhum professor encontrado no banco!</p>";
    echo "<p><a href='inserir_dados_reais.php'>🔄 Inserir Dados Reais</a></p>";
}

echo "<br><h3>📋 Estrutura da tabela usuarios:</h3>";
$result = $conn->query("DESCRIBE usuarios");
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
?>









