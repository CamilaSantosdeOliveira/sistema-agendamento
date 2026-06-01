<?php
echo "<h1>🔍 VERIFICAÇÃO DOS ARQUIVOS DE DADOS</h1>";

$data_dir = "C:/xampp/mysql/data";

echo "<h3>1️⃣ Verificando diretório de dados...</h3>";
if (is_dir($data_dir)) {
    echo "<p style='color: green;'>✅ Diretório existe: $data_dir</p>";
} else {
    echo "<p style='color: red;'>❌ Diretório não existe: $data_dir</p>";
    exit;
}

echo "<h3>2️⃣ Verificando arquivos essenciais...</h3>";
$arquivos_essenciais = [
    'ibdata1',
    'ib_logfile0',
    'ib_logfile1',
    'mysql/user.frm',
    'mysql/user.MYD',
    'mysql/user.MYI'
];

foreach ($arquivos_essenciais as $arquivo) {
    $caminho = $data_dir . '/' . $arquivo;
    if (file_exists($caminho)) {
        echo "<p style='color: green;'>✅ $arquivo existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $arquivo não existe</p>";
    }
}

echo "<h3>3️⃣ Verificando banco sistema_agendamento...</h3>";
$banco_dir = $data_dir . '/sistema_agendamento';
if (is_dir($banco_dir)) {
    echo "<p style='color: green;'>✅ Banco sistema_agendamento existe</p>";
    
    $tabelas = ['usuarios', 'cursos', 'agendamentos', 'avaliacoes', 'certificados'];
    foreach ($tabelas as $tabela) {
        $arquivo_frm = $banco_dir . '/' . $tabela . '.frm';
        $arquivo_ibd = $banco_dir . '/' . $tabela . '.ibd';
        
        if (file_exists($arquivo_frm) && file_exists($arquivo_ibd)) {
            echo "<p style='color: green;'>✅ Tabela $tabela existe</p>";
        } else {
            echo "<p style='color: red;'>❌ Tabela $tabela não existe ou está corrompida</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Banco sistema_agendamento não existe</p>";
}

echo "<h3>4️⃣ Verificando permissões...</h3>";
if (is_readable($data_dir) && is_writable($data_dir)) {
    echo "<p style='color: green;'>✅ Permissões OK</p>";
} else {
    echo "<p style='color: red;'>❌ Problema de permissões</p>";
}

echo "<h3>5️⃣ Tentando iniciar MySQL...</h3>";
echo "<p>🔄 Tentando iniciar MySQL com configuração corrigida...</p>";

// Tentar iniciar MySQL
$comando = '"C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone';
exec($comando . ' >nul 2>&1 &');

echo "<p>⏳ Aguardando 5 segundos...</p>";
sleep(5);

// Verificar se iniciou
$conexao = @new mysqli('localhost', 'root', '', '', 3306);
if ($conexao->connect_error) {
    echo "<p style='color: red;'>❌ MySQL ainda não está funcionando</p>";
    echo "<p>Erro: " . $conexao->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ MySQL está funcionando!</p>";
    $conexao->close();
}

echo "<h3>🔧 SOLUÇÕES POSSÍVEIS:</h3>";
echo "<ol>";
echo "<li><strong>Reiniciar computador</strong> - às vezes resolve problemas de porta</li>";
echo "<li><strong>Verificar se há outros MySQL rodando</strong> - pode haver conflito</li>";
echo "<li><strong>Reinstalar MySQL</strong> - se os arquivos estiverem corrompidos</li>";
echo "<li><strong>Usar porta diferente</strong> - mudar para porta 3307</li>";
echo "</ol>";

echo "<p><a href='teste_simples_dashboard.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🧪 Testar Novamente</a></p>";
?>









