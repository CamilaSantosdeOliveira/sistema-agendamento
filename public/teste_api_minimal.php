<?php
echo "<h1>🔍 TESTE API MINIMAL</h1>";

// Teste 1: Verificar se o arquivo existe
echo "<h3>1️⃣ Verificando arquivo da API:</h3>";
if (file_exists('api/usuarios.php')) {
    echo "<p style='color: green;'>✅ Arquivo api/usuarios.php existe</p>";
} else {
    echo "<p style='color: red;'>❌ Arquivo api/usuarios.php não existe</p>";
    exit;
}

// Teste 2: Testar requisição GET simples
echo "<h3>2️⃣ Testando requisição GET simples:</h3>";
$url = 'http://localhost:8080/Sistema De Agendamento/public/api/usuarios.php';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

$response = @file_get_contents($url, false, $context);
if ($response === false) {
    echo "<p style='color: red;'>❌ Erro ao acessar API</p>";
    echo "<p>Erro: " . error_get_last()['message'] ?? 'Desconhecido' . "</p>";
} else {
    echo "<p style='color: green;'>✅ API respondeu</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// Teste 3: Testar com ação específica
echo "<h3>3️⃣ Testando com ação listar_alunos:</h3>";
$url = 'http://localhost:8080/Sistema De Agendamento/public/api/usuarios.php?action=listar_alunos';
$response = @file_get_contents($url, false, $context);
if ($response === false) {
    echo "<p style='color: red;'>❌ Erro ao acessar API com ação</p>";
    echo "<p>Erro: " . error_get_last()['message'] ?? 'Desconhecido' . "</p>";
} else {
    echo "<p style='color: green;'>✅ API respondeu com ação</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// Teste 4: Verificar se o db.php está sendo incluído corretamente
echo "<h3>4️⃣ Verificando inclusão do db.php:</h3>";
$db_content = file_get_contents('db.php');
if (strpos($db_content, '$conn = new mysqli') !== false) {
    echo "<p style='color: green;'>✅ db.php contém conexão mysqli</p>";
} else {
    echo "<p style='color: red;'>❌ db.php não contém conexão mysqli</p>";
}

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='alunos.php'>👨‍🎓 Ver Página de Alunos</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard Principal</a></p>";
?>









