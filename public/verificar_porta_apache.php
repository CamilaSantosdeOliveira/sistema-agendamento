<?php
echo "<h1>🔍 VERIFICANDO PORTA DO APACHE</h1>";

// Teste 1: Verificar porta 80 (padrão)
echo "<h3>1️⃣ Testando porta 80 (padrão):</h3>";
$url = 'http://localhost/';
$response = @file_get_contents($url);
if ($response === false) {
    echo "<p style='color: red;'>❌ Apache não está na porta 80</p>";
} else {
    echo "<p style='color: green;'>✅ Apache está na porta 80</p>";
}

// Teste 2: Verificar porta 8080
echo "<h3>2️⃣ Testando porta 8080:</h3>";
$url = 'http://localhost:8080/';
$response = @file_get_contents($url);
if ($response === false) {
    echo "<p style='color: red;'>❌ Apache não está na porta 8080</p>";
} else {
    echo "<p style='color: green;'>✅ Apache está na porta 8080</p>";
}

// Teste 3: Verificar porta 8081
echo "<h3>3️⃣ Testando porta 8081:</h3>";
$url = 'http://localhost:8081/';
$response = @file_get_contents($url);
if ($response === false) {
    echo "<p style='color: red;'>❌ Apache não está na porta 8081</p>";
} else {
    echo "<p style='color: green;'>✅ Apache está na porta 8081</p>";
}

// Teste 4: Verificar se conseguimos acessar o sistema na porta correta
echo "<h3>4️⃣ Testando sistema na porta 80:</h3>";
$url = 'http://localhost/Sistema De Agendamento/public/dashboard_final.php';
$response = @file_get_contents($url);
if ($response === false) {
    echo "<p style='color: red;'>❌ Sistema não acessível na porta 80</p>";
} else {
    echo "<p style='color: green;'>✅ Sistema acessível na porta 80</p>";
}

echo "<br><h3>🔧 SOLUÇÃO:</h3>";
echo "<p>Se o Apache estiver na porta 80, use estes links:</p>";
echo "<p><a href='http://localhost/Sistema De Agendamento/public/dashboard_final.php'>📊 Dashboard (porta 80)</a></p>";
echo "<p><a href='http://localhost/Sistema De Agendamento/public/alunos.php'>👨‍🎓 Alunos (porta 80)</a></p>";

echo "<br><h3>🔗 Links atuais (porta 8080):</h3>";
echo "<p><a href='dashboard_final.php'>📊 Dashboard</a></p>";
echo "<p><a href='alunos.php'>👨‍🎓 Alunos</a></p>";
?>











