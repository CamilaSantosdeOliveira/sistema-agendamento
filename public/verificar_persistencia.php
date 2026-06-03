<?php
echo "<h1>🔍 VERIFICANDO PERSISTÊNCIA DOS DADOS</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar se o banco existe
$result = $conn->query("SELECT DATABASE() as db");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='info'>📊 Banco atual: {$row['db']}</div>";
}

// Verificar tabelas
echo "<h2>📋 Tabelas no banco:</h2>";
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        echo "<div class='success'>✅ Tabela: {$row[0]}</div>";
    }
}

// Verificar dados dos cursos
echo "<h2>📚 Cursos no banco:</h2>";
$result = $conn->query("SELECT COUNT(*) as total FROM cursos");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='info'>Total de cursos: {$row['total']}</div>";
    
    if ($row['total'] > 0) {
        $cursos = $conn->query("SELECT id, nome, preco FROM cursos ORDER BY id");
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Preço</th></tr>";
        while ($curso = $cursos->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$curso['id']}</td>";
            echo "<td>{$curso['nome']}</td>";
            echo "<td>R$ " . number_format($curso['preco'], 2, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='error'>❌ Nenhum curso encontrado!</div>";
    }
}

// Verificar dados dos usuários
echo "<h2>👥 Usuários no banco:</h2>";
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='info'>Total de usuários: {$row['total']}</div>";
}

// Verificar configuração do MySQL
echo "<h2>⚙️ Configuração do MySQL:</h2>";
$result = $conn->query("SHOW VARIABLES LIKE 'autocommit'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='info'>Autocommit: {$row['Value']}</div>";
}

$conn->close();

echo "<h2>🎯 DIAGNÓSTICO:</h2>";
echo "<p><strong>Se os dados aparecem aqui mas somem na página:</strong></p>";
echo "<ul>";
echo "<li>✅ Banco está funcionando</li>";
echo "<li>✅ Dados estão persistindo</li>";
echo "<li>❌ Problema pode ser na página PHP</li>";
echo "</ul>";

echo "<p><strong>Se os dados NÃO aparecem aqui:</strong></p>";
echo "<ul>";
echo "<li>❌ Banco foi resetado</li>";
echo "<li>❌ Dados não estão persistindo</li>";
echo "<li>❌ Precisa recarregar os dados</li>";
echo "</ul>";

echo "<p><a href='carregar_dados_final.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;'>🔄 Recarregar Dados</a></p>";
?>









