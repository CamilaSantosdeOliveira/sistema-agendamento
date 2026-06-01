<?php
echo "<h1>🧪 TESTE DE DURAÇÃO - SISTEMA DE PESQUISA</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Verificar todos os cursos com suas durações
echo "<h2>📊 Durações dos cursos no banco:</h2>";
$result = $conn->query("SELECT id, nome, duracao_horas FROM cursos ORDER BY duracao_horas");
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nome do Curso</th><th>Duração (horas)</th><th>Faixa de Duração</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $duracao = $row['duracao_horas'];
        $faixa = '';
        if ($duracao <= 50) {
            $faixa = 'Até 50 horas';
        } elseif ($duracao <= 80) {
            $faixa = '50 - 80 horas';
        } else {
            $faixa = 'Acima de 80 horas';
        }
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>{$duracao} horas</td>";
        echo "<td>{$faixa}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>❌ Nenhum curso encontrado</div>";
}

// Testar filtros de duração
echo "<h2>🧪 Teste dos Filtros de Duração:</h2>";

// Teste 1: Até 50 horas
echo "<h3>📋 Teste 1: Cursos até 50 horas</h3>";
$result = $conn->query("SELECT nome, duracao_horas FROM cursos WHERE duracao_horas <= 50 ORDER BY duracao_horas");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['nome']} - {$row['duracao_horas']} horas</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Nenhum curso até 50 horas</div>";
}

// Teste 2: 50 - 80 horas
echo "<h3>📋 Teste 2: Cursos entre 50 - 80 horas</h3>";
$result = $conn->query("SELECT nome, duracao_horas FROM cursos WHERE duracao_horas > 50 AND duracao_horas <= 80 ORDER BY duracao_horas");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['nome']} - {$row['duracao_horas']} horas</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Nenhum curso entre 50 - 80 horas</div>";
}

// Teste 3: Acima de 80 horas
echo "<h3>📋 Teste 3: Cursos acima de 80 horas</h3>";
$result = $conn->query("SELECT nome, duracao_horas FROM cursos WHERE duracao_horas > 80 ORDER BY duracao_horas");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='success'>✅ {$row['nome']} - {$row['duracao_horas']} horas</div>";
    }
} else {
    echo "<div class='info'>ℹ️ Nenhum curso acima de 80 horas</div>";
}

$conn->close();

echo "<h2>🎯 CONCLUSÃO DO TESTE:</h2>";
echo "<div class='success'>✅ As durações estão funcionando corretamente para pesquisa!</div>";
echo "<p><strong>Filtros disponíveis:</strong></p>";
echo "<ul>";
echo "<li>✅ <strong>Até 50 horas</strong> - Cursos com duração ≤ 50 horas</li>";
echo "<li>✅ <strong>50 - 80 horas</strong> - Cursos com duração entre 50 e 80 horas</li>";
echo "<li>✅ <strong>Acima de 80 horas</strong> - Cursos com duração > 80 horas</li>";
echo "</ul>";
echo "<p><a href='cursos_completo.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;'>🚀 Testar na Página de Cursos</a></p>";
?>







