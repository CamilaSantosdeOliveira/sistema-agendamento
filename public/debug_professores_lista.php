<?php
echo "<h1>🔍 DEBUG: Lista de Professores</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar se existem professores
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
if ($result) {
    $count = $result->fetch_assoc()['total'];
    echo "<div class='info'>📊 Total de professores: {$count}</div>";
}

// Buscar todos os professores
echo "<h2>👨‍🏫 Todos os Professores:</h2>";
$result = $conn->query("
    SELECT u.id, u.nome, u.email, u.ativo, u.criado_em,
           COUNT(DISTINCT a.id) as agendamentos_count
    FROM usuarios u
    LEFT JOIN agendamentos a ON u.id = a.usuario_id
    WHERE u.tipo_usuario = 'professor'
    GROUP BY u.id
    ORDER BY u.nome
");

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Ativo</th><th>Data Cadastro</th><th>Agendamentos</th></tr>";
    
    while ($professor = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$professor['id']}</td>";
        echo "<td><strong>{$professor['nome']}</strong></td>";
        echo "<td>{$professor['email']}</td>";
        echo "<td>" . ($professor['ativo'] ? '✅ Sim' : '❌ Não') . "</td>";
        echo "<td>{$professor['criado_em']}</td>";
        echo "<td>{$professor['agendamentos_count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Nenhum professor encontrado!</div>";
}

// Verificar estrutura da tabela
echo "<h2>🏗️ Estrutura da tabela 'usuarios':</h2>";
$result = $conn->query("DESCRIBE usuarios");
if ($result) {
    echo "<table>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar se há dados na tabela
echo "<h2>📋 Verificação de dados:</h2>";
$result = $conn->query("SELECT * FROM usuarios WHERE tipo_usuario = 'professor' LIMIT 3");
if ($result && $result->num_rows > 0) {
    echo "<div class='success'>✅ Dados encontrados na tabela!</div>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='info'>ID: {$row['id']} | Nome: {$row['nome']} | Email: {$row['email']}</div>";
    }
} else {
    echo "<div class='error'>❌ Nenhum dado encontrado!</div>";
}

$conn->close();

echo "<h2>🎯 DIAGNÓSTICO:</h2>";
echo "<p><strong>Se os dados aparecem aqui mas não na página:</strong></p>";
echo "<ul>";
echo "<li>✅ Banco está funcionando</li>";
echo "<li>✅ Dados estão presentes</li>";
echo "<li>❌ Problema na página PHP</li>";
echo "</ul>";

echo "<p><strong>Se os dados NÃO aparecem aqui:</strong></p>";
echo "<ul>";
echo "<li>❌ Banco não tem dados</li>";
echo "<li>❌ Precisa recarregar dados</li>";
echo "<li>❌ Problema na estrutura</li>";
echo "</ul>";

echo "<p><a href='carregar_dados_final.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;'>🔄 Recarregar Dados</a></p>";
?>









