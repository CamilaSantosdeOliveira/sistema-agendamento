<?php
echo "<h2>🔧 CORREÇÃO ESPECÍFICA - ERROS DE REPLICAÇÃO MARIADB</h2>";
echo "<p><strong>Problema identificado:</strong> MariaDB está falhando devido a erros de replicação</p>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

echo "<h3>📋 PASSO 1: Verificando arquivos de replicação</h3>";

// Verificar arquivos de replicação problemáticos
$arquivos_replicacao = [
    'C:\xampp\mysql\data\master.info',
    'C:\xampp\mysql\data\relay-log.info',
    'C:\xampp\mysql\data\mysql-relay-bin.*',
    'C:\xampp\mysql\data\mysql-bin.*'
];

foreach ($arquivos_replicacao as $arquivo) {
    if (file_exists($arquivo)) {
        echo "⚠️ <strong>Encontrado:</strong> $arquivo<br>";
    }
}

echo "<h3>🔧 PASSO 2: Parando MariaDB</h3>";
$resultado = executarComando('taskkill /f /im mysqld.exe');
if ($resultado['return'] == 0) {
    echo "✅ MariaDB parado com sucesso<br>";
} else {
    echo "ℹ️ MariaDB já estava parado ou não foi encontrado<br>";
}

echo "<h3>🗑️ PASSO 3: Removendo arquivos de replicação</h3>";

// Comandos para remover arquivos de replicação
$comandos_limpeza = [
    'del "C:\xampp\mysql\data\master.info"',
    'del "C:\xampp\mysql\data\relay-log.info"',
    'del "C:\xampp\mysql\data\mysql-relay-bin.*"',
    'del "C:\xampp\mysql\data\mysql-bin.*"'
];

foreach ($comandos_limpeza as $comando) {
    $resultado = executarComando($comando);
    if ($resultado['return'] == 0) {
        echo "✅ Arquivo removido com sucesso<br>";
    } else {
        echo "ℹ️ Arquivo não encontrado ou já removido<br>";
    }
}

echo "<h3>⚙️ PASSO 4: Verificando configuração my.ini</h3>";

$my_ini_path = 'C:\xampp\mysql\bin\my.ini';
if (file_exists($my_ini_path)) {
    $conteudo = file_get_contents($my_ini_path);
    
    // Verificar se há configurações de replicação
    $linhas_replicacao = [
        'log-bin',
        'relay-log',
        'server-id',
        'master-host',
        'master-user',
        'master-password'
    ];
    
    $encontrou_replicacao = false;
    foreach ($linhas_replicacao as $config) {
        if (strpos($conteudo, $config) !== false) {
            echo "⚠️ <strong>Configuração de replicação encontrada:</strong> $config<br>";
            $encontrou_replicacao = true;
        }
    }
    
    if (!$encontrou_replicacao) {
        echo "✅ Nenhuma configuração de replicação encontrada no my.ini<br>";
    }
} else {
    echo "❌ Arquivo my.ini não encontrado<br>";
}

echo "<h3>🚀 PASSO 5: Iniciando MariaDB</h3>";

// Aguardar um pouco antes de iniciar
sleep(3);

$resultado = executarComando('"C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone');
if ($resultado['return'] == 0) {
    echo "✅ MariaDB iniciado com sucesso!<br>";
} else {
    echo "❌ Erro ao iniciar MariaDB<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
}

echo "<h3>🔍 PASSO 6: Testando conexão</h3>";

// Aguardar um pouco para o servidor inicializar
sleep(5);

try {
    $conn = new mysqli('localhost', 'root', '', '', 3306);
    if ($conn->connect_error) {
        echo "❌ <strong>Falha na conexão:</strong> " . $conn->connect_error . "<br>";
    } else {
        echo "✅ <strong>SUCESSO!</strong> MariaDB está funcionando perfeitamente!<br>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
}

echo "<h3>📝 SOLUÇÃO MANUAL (se automática falhar)</h3>";
echo "<ol>";
echo "<li><strong>Abra o XAMPP Control Panel</strong></li>";
echo "<li><strong>Clique em 'Config' do MySQL</strong></li>";
echo "<li><strong>Selecione 'my.ini'</strong></li>";
echo "<li><strong>Procure e COMENTE estas linhas (adicione # no início):</strong><br>";
echo "   - log-bin<br>";
echo "   - relay-log<br>";
echo "   - server-id<br>";
echo "   - master-host<br>";
echo "   - master-user<br>";
echo "   - master-password</li>";
echo "<li><strong>Salve o arquivo</strong></li>";
echo "<li><strong>Vá para C:\\xampp\\mysql\\data\\</strong></li>";
echo "<li><strong>Delete estes arquivos:</strong><br>";
echo "   - master.info<br>";
echo "   - relay-log.info<br>";
echo "   - mysql-relay-bin.*<br>";
echo "   - mysql-bin.*</li>";
echo "<li><strong>Inicie o MySQL no XAMPP</strong></li>";
echo "</ol>";

echo "<h3>💡 COMANDOS ÚTEIS PARA CMD</h3>";
echo "<code>taskkill /f /im mysqld.exe</code> - Parar MariaDB<br>";
echo "<code>del \"C:\\xampp\\mysql\\data\\master.info\"</code> - Remover arquivo master.info<br>";
echo "<code>del \"C:\\xampp\\mysql\\data\\relay-log.info\"</code> - Remover arquivo relay-log.info<br>";
echo "<code>\"C:\\xampp\\mysql\\bin\\mysqld.exe\" --defaults-file=\"C:\\xampp\\mysql\\bin\\my.ini\" --standalone</code> - Iniciar MariaDB<br>";

echo "<hr>";
echo "<p><strong>🎯 Resumo:</strong> O problema era causado por arquivos de replicação corrompidos ou mal configurados. Esta correção remove esses arquivos e permite que o MariaDB inicie normalmente.</p>";
?>


