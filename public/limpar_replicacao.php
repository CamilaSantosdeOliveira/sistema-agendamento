<?php
echo "<h1>🧹 LIMPEZA DE ARQUIVOS DE REPLICAÇÃO</h1>";

$data_dir = "C:/xampp/mysql/data";

echo "<h3>1️⃣ Removendo arquivos de replicação corrompidos...</h3>";

// Lista de arquivos de replicação para remover
$arquivos_replicacao = [
    'master-*.info',
    'relay-log-*.info',
    'mysql-relay-bin-*',
    'mysql-bin-*',
    'relay-log-*',
    '*.relay'
];

$arquivos_removidos = 0;

// Remover arquivos de replicação
foreach (glob($data_dir . '/master-*.info') as $arquivo) {
    if (unlink($arquivo)) {
        echo "<p style='color: green;'>✅ Removido: " . basename($arquivo) . "</p>";
        $arquivos_removidos++;
    }
}

foreach (glob($data_dir . '/relay-log-*.info') as $arquivo) {
    if (unlink($arquivo)) {
        echo "<p style='color: green;'>✅ Removido: " . basename($arquivo) . "</p>";
        $arquivos_removidos++;
    }
}

foreach (glob($data_dir . '/mysql-relay-bin-*') as $arquivo) {
    if (unlink($arquivo)) {
        echo "<p style='color: green;'>✅ Removido: " . basename($arquivo) . "</p>";
        $arquivos_removidos++;
    }
}

foreach (glob($data_dir . '/mysql-bin-*') as $arquivo) {
    if (unlink($arquivo)) {
        echo "<p style='color: green;'>✅ Removido: " . basename($arquivo) . "</p>";
        $arquivos_removidos++;
    }
}

echo "<h3>2️⃣ Verificando arquivos de usuário...</h3>";

// Verificar se os arquivos de usuário existem
$user_files = [
    'mysql/user.frm',
    'mysql/user.MYD',
    'mysql/user.MYI'
];

foreach ($user_files as $file) {
    $caminho = $data_dir . '/' . $file;
    if (file_exists($caminho)) {
        echo "<p style='color: green;'>✅ $file existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $file não existe</p>";
    }
}

echo "<h3>3️⃣ Tentando iniciar MySQL...</h3>";

// Parar qualquer processo MySQL
exec('taskkill /F /IM mysqld.exe 2>nul');
sleep(3);

// Tentar iniciar MySQL
$comando = '"C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone';
exec($comando . ' >nul 2>&1 &');

echo "<p>⏳ Aguardando 10 segundos...</p>";
sleep(10);

// Verificar se iniciou
$conexao = @new mysqli('localhost', 'root', '', '', 3306);
if ($conexao->connect_error) {
    echo "<p style='color: red;'>❌ MySQL ainda não está funcionando</p>";
    echo "<p>Erro: " . $conexao->connect_error . "</p>";
    
    echo "<h3>🔧 PRÓXIMA SOLUÇÃO:</h3>";
    echo "<p>Se ainda não funcionar, precisamos:</p>";
    echo "<ol>";
    echo "<li><strong>Reinstalar MySQL</strong> - os arquivos de sistema estão corrompidos</li>";
    echo "<li><strong>Ou usar porta 3307</strong> - pode haver conflito de porta</li>";
    echo "</ol>";
} else {
    echo "<p style='color: green;'>🎉 <strong>SUCESSO! MySQL está funcionando!</strong></p>";
    $conexao->close();
}

echo "<h3>📊 RESUMO:</h3>";
echo "<p>Arquivos de replicação removidos: <strong>$arquivos_removidos</strong></p>";

echo "<p><a href='teste_simples_dashboard.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🧪 Testar Conexão</a></p>";
?>









