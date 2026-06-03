<?php
echo "<h1>🔍 TESTE DA API DE ALUNOS</h1>";

// Teste 1: Verificar se a API responde
echo "<h3>1️⃣ Testando resposta da API:</h3>";
$url = 'http://localhost:8080/Sistema De Agendamento/public/api/usuarios.php?action=listar_alunos';
$response = file_get_contents($url);

if ($response === false) {
    echo "<p style='color: red;'>❌ Erro ao acessar a API</p>";
} else {
    echo "<p style='color: green;'>✅ API respondeu</p>";
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "<p>✅ JSON válido - Success: " . ($data['success'] ? 'true' : 'false') . "</p>";
        if (isset($data['data'])) {
            echo "<p>✅ Dados encontrados: " . count($data['data']) . " alunos</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ JSON inválido</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

// Teste 2: Testar edição de aluno
echo "<h3>2️⃣ Testando edição de aluno:</h3>";
$test_data = [
    'action' => 'editar_usuario',
    'id' => 30, // ID de um aluno existente
    'nome' => 'Teste Aluno',
    'email' => 'teste@email.com',
    'telefone' => '123456789'
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($test_data)
    ]
]);

$url = 'http://localhost:8080/Sistema De Agendamento/public/api/usuarios.php';
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "<p style='color: red;'>❌ Erro ao testar edição</p>";
} else {
    echo "<p style='color: green;'>✅ Edição testada</p>";
    $data = json_decode($response, true);
    if ($data) {
        echo "<p>Resposta: " . ($data['success'] ? '✅ Sucesso' : '❌ Erro') . "</p>";
        echo "<p>Mensagem: " . ($data['message'] ?? 'N/A') . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Resposta inválida</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

// Teste 3: Verificar estrutura da tabela
echo "<h3>3️⃣ Verificando estrutura da tabela usuarios:</h3>";
include 'db.php';
$result = $conn->query("DESCRIBE usuarios");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
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

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='alunos.php'>👨‍🎓 Ver Página de Alunos</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
?>









