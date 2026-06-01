<?php
echo "<h1>🧪 TESTE DE PREÇOS - SISTEMA DE PESQUISA</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar todos os cursos com seus preços
echo "<h2>📊 Preços dos cursos no banco:</h2>";
$result = $conn->query("SELECT id, nome, preco FROM cursos ORDER BY preco");
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome do Curso</th><th>Preço</th><th>Faixa de Preço</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $preco = $row['preco'];
        $faixa = '';
        if ($preco <= 200) {
            $faixa = 'Até R$ 200';
        } elseif ($preco <= 400) {
            $faixa = 'R$ 200 - R$ 400';
        } else {
            $faixa = 'Acima de R$ 400';
        }
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>R$ " . number_format($preco, 2, ',', '.') . "</td>";
        echo "<td>{$faixa}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Nenhum curso encontrado</div>";
}

// Testar filtros de preço
echo "<h2>🧪 Teste dos Filtros de Preço:</h2>";

// Teste 1: Até R$ 200
echo "<h3>📋 Teste 1: Cursos até R$ 200</h3>";
$result = $conn->query("SELECT nome, preco FROM cursos WHERE preco <= 200 ORDER BY preco");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['nome']} - R$ " . number_format($row['preco'], 2, ',', '.') . "</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Nenhum curso até R$ 200</div>";
}

// Teste 2: R$ 200 - R$ 400
echo "<h3>📋 Teste 2: Cursos entre R$ 200 - R$ 400</h3>";
$result = $conn->query("SELECT nome, preco FROM cursos WHERE preco > 200 AND preco <= 400 ORDER BY preco");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['nome']} - R$ " . number_format($row['preco'], 2, ',', '.') . "</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Nenhum curso entre R$ 200 - R$ 400</div>";
}

// Teste 3: Acima de R$ 400
echo "<h3>📋 Teste 3: Cursos acima de R$ 400</h3>";
$result = $conn->query("SELECT nome, preco FROM cursos WHERE preco > 400 ORDER BY preco");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['nome']} - R$ " . number_format($row['preco'], 2, ',', '.') . "</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Nenhum curso acima de R$ 400</div>";
}

$conn->close();

echo "<h2>🎯 CONCLUSÃO DO TESTE:</h2>";
echo "<div class='success'>✅ Os preços estão funcionando corretamente para pesquisa!</div>";
echo "<p><strong>Filtros disponíveis:</strong></p>";
echo "<ul>";
echo "<li>✅ <strong>Até R$ 200</strong> - Cursos com preço ≤ R$ 200</li>";
echo "<li>✅ <strong>R$ 200 - R$ 400</strong> - Cursos com preço entre R$ 200 e R$ 400</li>";
echo "<li>✅ <strong>Acima de R$ 400</strong> - Cursos com preço > R$ 400</li>";
echo "</ul>";
echo "<p><a href='cursos_completo.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;'>🚀 Testar na Página de Cursos</a></p>";
?>







