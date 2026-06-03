<?php
// Verificar dados específicos das aulas
include 'db.php';

echo "<h2>🔍 Verificação dos Dados das Aulas</h2>";

// Testar conexão
if ($conn->connect_error) {
    die("❌ Erro de conexão: " . $conn->connect_error);
}
echo "✅ Conexão com banco OK!<br><br>";

// Verificar estrutura da tabela agendamentos
echo "<h3>📋 Estrutura da Tabela Agendamentos:</h3>";
$result = $conn->query("DESCRIBE agendamentos");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Campo: {$row['Field']} | Tipo: {$row['Type']} | Null: {$row['Null']} | Default: {$row['Default']}<br>";
    }
}

echo "<br><h3>📅 Dados das Aulas Futuras:</h3>";
$result = $conn->query("SELECT * FROM agendamentos WHERE data >= CURDATE() ORDER BY data, hora");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>ID:</strong> {$row['id']}<br>";
        echo "<strong>Data:</strong> {$row['data']}<br>";
        echo "<strong>Hora:</strong> {$row['hora']}<br>";
        echo "<strong>Nome (Aluno):</strong> '" . ($row['nome'] ?: 'VAZIO') . "'<br>";
        echo "<strong>Professor:</strong> '" . ($row['professor'] ?: 'VAZIO') . "'<br>";
        echo "<strong>Serviço:</strong> '" . ($row['servico'] ?: 'VAZIO') . "'<br>";
        echo "<strong>Status:</strong> {$row['status']}<br>";
        echo "<strong>Email:</strong> '" . ($row['email'] ?: 'VAZIO') . "'<br>";
        echo "<strong>Telefone:</strong> '" . ($row['telefone'] ?: 'VAZIO') . "'<br>";
        echo "</div>";
    }
} else {
    echo "Nenhuma aula futura encontrada.<br>";
}

echo "<br><h3>🔧 Solução:</h3>";
echo "Se os campos 'nome' e 'professor' estão vazios, precisamos atualizar os dados no banco.<br>";
echo "Vou criar um script para corrigir isso automaticamente.";
?>


