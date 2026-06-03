<?php
echo "<h1>🔍 TESTE DIRETO SIMPLES</h1>";

// Teste 1: Verificar se conseguimos acessar a API diretamente
echo "<h3>1️⃣ Testando acesso direto à API:</h3>";
$url = 'http://localhost:8080/Sistema De Agendamento/public/api/usuarios.php?action=listar_alunos';

echo "<p>URL: " . $url . "</p>";

$response = file_get_contents($url);
if ($response === false) {
    $error = error_get_last();
    echo "<p style='color: red;'>❌ Erro: " . $error['message'] . "</p>";
} else {
    echo "<p style='color: green;'>✅ Resposta recebida:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// Teste 2: Verificar se conseguimos acessar a página de alunos
echo "<h3>2️⃣ Testando acesso à página de alunos:</h3>";
$url = 'http://localhost:8080/Sistema De Agendamento/public/alunos.php';

echo "<p>URL: " . $url . "</p>";

$response = file_get_contents($url);
if ($response === false) {
    $error = error_get_last();
    echo "<p style='color: red;'>❌ Erro: " . $error['message'] . "</p>";
} else {
    echo "<p style='color: green;'>✅ Página carregada com sucesso</p>";
    echo "<p>Tamanho da resposta: " . strlen($response) . " caracteres</p>";
}

// Teste 3: Verificar se o Apache está rodando
echo "<h3>3️⃣ Verificando se o Apache está rodando:</h3>";
$url = 'http://localhost:8080/';
$response = file_get_contents($url);
if ($response === false) {
    echo "<p style='color: red;'>❌ Apache não está rodando na porta 8080</p>";
} else {
    echo "<p style='color: green;'>✅ Apache está rodando na porta 8080</p>";
}

echo "<br><h3>🔗 Links para testar:</h3>";
echo "<p><a href='alunos.php'>👨‍🎓 Página de Alunos</a></p>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard</a></p>";
?>











