<?php
echo "<h1>🔍 DEBUG: Dados dos Professores</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;}</style>";

// Conectar ao banco
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento');

if ($conn->connect_error) {
    die("<div class='error'>❌ Erro na conexão: " . $conn->connect_error . "</div>");
}

echo "<div class='success'>✅ Conectado ao banco de dados!</div>";

// Executar a mesma consulta da página professores.php
echo "<h2>📋 Consulta SQL da página:</h2>";
$query = "
    SELECT u.id, u.nome, u.email, u.ativo, u.criado_em,
           0 as agendamentos_count
    FROM usuarios u
    WHERE u.tipo_usuario = 'professor'
    ORDER BY u.nome
";

echo "<div class='info'>🔍 Query: " . htmlspecialchars($query) . "</div>";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<div class='success'>✅ Consulta executada com sucesso!</div>";
    echo "<div class='info'>📊 Professores encontrados: {$result->num_rows}</div>";
    
    echo "<h2>📋 Dados retornados:</h2>";
    echo "<table style='border-collapse:collapse;width:100%;'>";
    echo "<tr style='background:#f2f2f2;'>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>ID</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Nome</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Email</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Ativo</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Criado em (RAW)</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Criado em (Formatted)</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Empty Check</th>";
    echo "</tr>";
    
    while ($professor = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$professor['id']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'><strong>{$professor['nome']}</strong></td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$professor['email']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>" . ($professor['ativo'] ? '✅' : '❌') . "</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>" . ($professor['criado_em'] ?: '<span style="color:red;">NULL</span>') . "</td>";
        
        // Testar formatação
        $formatted_date = '';
        $empty_check = '';
        
        if (!empty($professor['criado_em'])) {
            $formatted_date = date('d/m/Y', strtotime($professor['criado_em']));
            $empty_check = '✅ Não vazio';
        } else {
            $formatted_date = '<span style="color:red;">Data não informada</span>';
            $empty_check = '❌ Vazio';
        }
        
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$formatted_date}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$empty_check}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} else {
    echo "<div class='error'>❌ Erro na consulta ou nenhum resultado</div>";
    if ($result === false) {
        echo "<div class='error'>Erro SQL: " . $conn->error . "</div>";
    }
}

// Testar a lógica da página
echo "<h2>🧪 Teste da lógica da página:</h2>";
$result = $conn->query($query);
if ($result) {
    while ($professor = $result->fetch_assoc()) {
        echo "<div style='border:1px solid #ddd;padding:10px;margin:5px 0;border-radius:5px;'>";
        echo "<strong>{$professor['nome']}</strong><br>";
        echo "Criado em (RAW): " . ($professor['criado_em'] ?: 'NULL') . "<br>";
        echo "Empty check: " . (!empty($professor['criado_em']) ? 'FALSE' : 'TRUE') . "<br>";
        echo "Resultado: " . (!empty($professor['criado_em']) ? date('d/m/Y', strtotime($professor['criado_em'])) : 'Data não informada');
        echo "</div>";
    }
}

$conn->close();

echo "<h2>🎯 PRÓXIMOS PASSOS:</h2>";
echo "<div style='margin:20px 0;'>";
echo "<a href='professores.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>📊 Ver Página Professores</a>";
echo "<a href='verificar_data_professores.php' style='background:blue;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>🔍 Verificar Datas</a>";
echo "</div>";
?>









