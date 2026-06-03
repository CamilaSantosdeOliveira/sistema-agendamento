<?php
echo "<h2>⚡ CORREÇÃO RÁPIDA - ERROS DE REPLICAÇÃO MARIADB</h2>";
echo "<p><strong>Problema:</strong> MariaDB falha devido a arquivos de replicação corrompidos</p>";

// Parar MariaDB
echo "<h3>🛑 Parando MariaDB...</h3>";
exec('taskkill /f /im mysqld.exe 2>&1');
sleep(2);

// Remover arquivos de replicação problemáticos
echo "<h3>🗑️ Removendo arquivos de replicação...</h3>";
$arquivos_para_remover = [
    'C:\xampp\mysql\data\master.info',
    'C:\xampp\mysql\data\relay-log.info'
];

foreach ($arquivos_para_remover as $arquivo) {
    if (file_exists($arquivo)) {
        unlink($arquivo);
        echo "✅ Removido: $arquivo<br>";
    } else {
        echo "ℹ️ Não encontrado: $arquivo<br>";
    }
}

// Remover arquivos de log de replicação
exec('del "C:\xampp\mysql\data\mysql-relay-bin.*" 2>&1');
exec('del "C:\xampp\mysql\data\mysql-bin.*" 2>&1');
echo "✅ Arquivos de log de replicação removidos<br>";

// Aguardar um pouco
sleep(3);

// Iniciar MariaDB
echo "<h3>🚀 Iniciando MariaDB...</h3>";
exec('"C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone >nul 2>&1 &');
sleep(5);

// Testar conexão
echo "<h3>🔍 Testando conexão...</h3>";
try {
    $conn = new mysqli('localhost', 'root', '', '', 3306);
    if ($conn->connect_error) {
        echo "❌ <strong>Falha:</strong> " . $conn->connect_error . "<br>";
        echo "<p><strong>💡 Solução manual:</strong></p>";
        echo "<ol>";
        echo "<li>Abra o XAMPP Control Panel</li>";
        echo "<li>Clique em 'Config' do MySQL → 'my.ini'</li>";
        echo "<li>Comente estas linhas (adicione #): log-bin, relay-log, server-id</li>";
        echo "<li>Salve e inicie o MySQL</li>";
        echo "</ol>";
    } else {
        echo "✅ <strong>SUCESSO!</strong> MariaDB está funcionando!<br>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>🎯 Pronto!</strong> Se ainda não funcionar, use a solução manual acima.</p>";
?>


