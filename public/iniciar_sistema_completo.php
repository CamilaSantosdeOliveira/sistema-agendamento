<?php
echo "<h2>🚀 INICIALIZAÇÃO COMPLETA DO SISTEMA</h2>";
echo "<p><strong>Objetivo:</strong> Corrigir e iniciar Apache + MySQL automaticamente</p>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

echo "<h3>📋 PASSO 1: Verificando status atual</h3>";

// Verificar se Apache está rodando
$resultado = executarComando('netstat -an | find ":80"');
$apache_rodando = (strpos(implode(' ', $resultado['output']), 'LISTENING') !== false);

// Verificar se MySQL está rodando
$resultado = executarComando('netstat -an | find ":3306"');
$mysql_rodando = (strpos(implode(' ', $resultado['output']), 'LISTENING') !== false);

echo "Apache: " . ($apache_rodando ? "✅ Rodando" : "❌ Parado") . "<br>";
echo "MySQL: " . ($mysql_rodando ? "✅ Rodando" : "❌ Parado") . "<br>";

echo "<h3>🔧 PASSO 2: Corrigindo arquivo my.ini</h3>";

$my_ini_path = 'C:\xampp\mysql\bin\my.ini';

if (file_exists($my_ini_path)) {
    // Fazer backup
    $backup_path = $my_ini_path . '.backup.' . date('Y-m-d-H-i-s');
    copy($my_ini_path, $backup_path);
    echo "✅ Backup criado: $backup_path<br>";
    
    // Ler e corrigir arquivo
    $conteudo = file_get_contents($my_ini_path);
    $linhas = explode("\n", $conteudo);
    $linhas_alteradas = 0;
    
    $configuracoes_problematicas = [
        'server-id',
        'log-bin',
        'master-host',
        'master-user', 
        'master-password',
        'master-port'
    ];
    
    $novas_linhas = [];
    foreach ($linhas as $linha) {
        $linha_trim = trim($linha);
        
        foreach ($configuracoes_problematicas as $config) {
            if (strpos($linha_trim, $config) === 0 && !strpos($linha_trim, '#') === 0) {
                $linha = '# ' . $linha;
                $linhas_alteradas++;
                break;
            }
        }
        
        $novas_linhas[] = $linha;
    }
    
    // Salvar arquivo corrigido
    $novo_conteudo = implode("\n", $novas_linhas);
    file_put_contents($my_ini_path, $novo_conteudo);
    echo "✅ my.ini corrigido ($linhas_alteradas linhas alteradas)<br>";
} else {
    echo "❌ Arquivo my.ini não encontrado<br>";
}

echo "<h3>🛑 PASSO 3: Parando serviços</h3>";

// Parar todos os serviços
exec('taskkill /f /im mysqld.exe 2>&1');
exec('taskkill /f /im httpd.exe 2>&1');
sleep(3);
echo "✅ Serviços parados<br>";

echo "<h3>🗑️ PASSO 4: Limpando arquivos de replicação</h3>";

// Remover arquivos de replicação
$arquivos_para_remover = [
    'C:\xampp\mysql\data\master.info',
    'C:\xampp\mysql\data\relay-log.info'
];

foreach ($arquivos_para_remover as $arquivo) {
    if (file_exists($arquivo)) {
        unlink($arquivo);
        echo "✅ Removido: $arquivo<br>";
    }
}

exec('del "C:\xampp\mysql\data\mysql-relay-bin.*" 2>&1');
exec('del "C:\xampp\mysql\data\mysql-bin.*" 2>&1');
echo "✅ Arquivos de log removidos<br>";

echo "<h3>🚀 PASSO 5: Iniciando Apache</h3>";

// Iniciar Apache
$resultado = executarComando('"C:\xampp\apache\bin\httpd.exe" -k start');
if ($resultado['return'] == 0) {
    echo "✅ Apache iniciado com sucesso!<br>";
} else {
    echo "❌ Erro ao iniciar Apache<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
}

sleep(3);

echo "<h3>🚀 PASSO 6: Iniciando MySQL</h3>";

// Iniciar MySQL
$resultado = executarComando('"C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone');
if ($resultado['return'] == 0) {
    echo "✅ MySQL iniciado com sucesso!<br>";
} else {
    echo "❌ Erro ao iniciar MySQL<br>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
}

echo "<h3>⏳ PASSO 7: Aguardando inicialização</h3>";
sleep(8);

echo "<h3>🔍 PASSO 8: Testando serviços</h3>";

// Testar Apache
$context = stream_context_create(['http' => ['timeout' => 5]]);
$resultado_apache = @file_get_contents('http://localhost', false, $context);
if ($resultado_apache !== false) {
    echo "✅ <strong>Apache:</strong> Funcionando! Página carregada<br>";
} else {
    echo "❌ <strong>Apache:</strong> Não conseguiu carregar página<br>";
}

// Testar MySQL
try {
    $conn = new mysqli('localhost', 'root', '', '', 3306);
    if ($conn->connect_error) {
        echo "❌ <strong>MySQL:</strong> " . $conn->connect_error . "<br>";
    } else {
        echo "✅ <strong>MySQL:</strong> Conexão bem-sucedida!<br>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ <strong>MySQL:</strong> " . $e->getMessage() . "<br>";
}

echo "<h3>🎯 RESULTADO FINAL</h3>";

// Verificar status final
$resultado = executarComando('netstat -an | find ":80"');
$apache_final = (strpos(implode(' ', $resultado['output']), 'LISTENING') !== false);

$resultado = executarComando('netstat -an | find ":3306"');
$mysql_final = (strpos(implode(' ', $resultado['output']), 'LISTENING') !== false);

echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>Status dos Serviços:</h4>";
echo "🌐 <strong>Apache (Porta 80):</strong> " . ($apache_final ? "✅ FUNCIONANDO" : "❌ PARADO") . "<br>";
echo "🗄️ <strong>MySQL (Porta 3306):</strong> " . ($mysql_final ? "✅ FUNCIONANDO" : "❌ PARADO") . "<br>";
echo "</div>";

if ($apache_final && $mysql_final) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>🎉 SUCESSO!</h4>";
    echo "<p>Ambos os serviços estão funcionando perfeitamente!</p>";
    echo "<p><strong>Teste agora:</strong></p>";
    echo "<ul>";
    echo "<li><a href='http://localhost' target='_blank'>http://localhost</a> - Página principal</li>";
    echo "<li><a href='http://localhost/Sistema%20De%20Agendamento/public/' target='_blank'>Sistema de Agendamento</a></li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>⚠️ ATENÇÃO</h4>";
    echo "<p>Alguns serviços não estão funcionando. Execute:</p>";
    echo "<ul>";
    echo "<li><a href='verificar_servicos.php' target='_blank'>Verificação de Serviços</a></li>";
    echo "<li><a href='corrigir_replicacao_rapido.php' target='_blank'>Correção Rápida MySQL</a></li>";
    echo "</ul>";
    echo "</div>";
}

echo "<h3>💡 COMANDOS ÚTEIS</h3>";
echo "<code>netstat -an | find \":80\"</code> - Verificar Apache<br>";
echo "<code>netstat -an | find \":3306\"</code> - Verificar MySQL<br>";
echo "<code>taskkill /f /im mysqld.exe</code> - Parar MySQL<br>";
echo "<code>taskkill /f /im httpd.exe</code> - Parar Apache<br>";

echo "<hr>";
echo "<p><strong>🎯 Inicialização concluída!</strong> Verifique os resultados acima.</p>";
?>
