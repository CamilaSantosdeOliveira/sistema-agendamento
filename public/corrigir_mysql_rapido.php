<?php
echo "<h1>⚡ CORREÇÃO RÁPIDA - MYSQL XAMPP</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .erro { color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .sucesso { color: green; background: #e6ffe6; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: blue; background: #e6f3ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .executando { color: purple; background: #f0e6ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn-success { background: #28a745; }
    .btn-danger { background: #dc3545; }
</style>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

// Função para verificar se o MySQL está funcionando
function testarMySQL() {
    try {
        $conn = new mysqli('localhost', 'root', '', '', 3306);
        if (!$conn->connect_error) {
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

echo "<h2>🚀 CORREÇÃO RÁPIDA EM ANDAMENTO...</h2>";

// PASSO 1: Encerrar todos os processos MySQL
echo "<h3>1️⃣ Encerrando processos MySQL...</h3>";
echo "<div class='executando'>🔄 Encerrando todos os processos MySQL...</div>";

$comandos_kill = [
    "taskkill /F /IM mysqld.exe",
    "taskkill /F /IM mysql.exe",
    "taskkill /F /IM mysqld-nt.exe"
];

foreach ($comandos_kill as $comando) {
    $resultado = executarComando($comando);
    if ($resultado['return'] == 0) {
        echo "<div class='sucesso'>✅ Processo encerrado: $comando</div>";
    }
}

// PASSO 2: Aguardar um pouco
echo "<h3>2️⃣ Aguardando liberação de recursos...</h3>";
echo "<div class='info'>⏳ Aguardando 3 segundos...</div>";
sleep(3);

// PASSO 3: Verificar se a porta está livre
echo "<h3>3️⃣ Verificando porta 3306...</h3>";
$resultado = executarComando("netstat -an | findstr :3306");
if (empty($resultado['output'])) {
    echo "<div class='sucesso'>✅ Porta 3306 está livre!</div>";
} else {
    echo "<div class='erro'>❌ Porta 3306 ainda está em uso!</div>";
    echo "<pre>" . implode("\n", $resultado['output']) . "</pre>";
}

// PASSO 4: Tentar iniciar MySQL via XAMPP
echo "<h3>4️⃣ Tentando iniciar MySQL...</h3>";
echo "<div class='executando'>🔄 Tentando iniciar MySQL via XAMPP...</div>";

// Tentar iniciar MySQL diretamente
$mysql_bin = 'C:\\xampp\\mysql\\bin\\mysqld.exe';
if (file_exists($mysql_bin)) {
    // Tentar iniciar em background
    $resultado_start = executarComando("start /B \"\" \"$mysql_bin\" --defaults-file=\"C:\\xampp\\mysql\\bin\\my.ini\"");
    
    // Aguardar um pouco
    sleep(5);
    
    // Verificar se iniciou
    if (testarMySQL()) {
        echo "<div class='sucesso'>";
        echo "<h3>🎉 SUCESSO! MYSQL ESTÁ FUNCIONANDO!</h3>";
        echo "<p>O MySQL foi corrigido e está funcionando corretamente.</p>";
        echo "</div>";
        
        echo "<h3>✅ PRÓXIMOS PASSOS:</h3>";
        echo "<div class='info'>";
        echo "<ol>";
        echo "<li>Abra o XAMPP Control Panel</li>";
        echo "<li>Verifique se o MySQL está rodando (luz verde)</li>";
        echo "<li>Teste o phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Volte para o sistema principal: <a href='http://localhost:8080/Sistema%20De%20Agendamento/public/'>Sistema Principal</a></li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<a href='http://localhost/phpmyadmin' target='_blank' class='btn btn-success'>🌐 Abrir phpMyAdmin</a>";
        echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
        echo "</div>";
        
    } else {
        echo "<div class='erro'>❌ Falha ao iniciar MySQL automaticamente</div>";
        echo "<div class='info'>";
        echo "<h3>🔧 SOLUÇÃO MANUAL NECESSÁRIA:</h3>";
        echo "<ol>";
        echo "<li>Abra o XAMPP Control Panel como administrador</li>";
        echo "<li>Clique em <strong>Start</strong> ao lado do MySQL</li>";
        echo "<li>Se der erro, clique em <strong>Config</strong> → <strong>my.ini</strong></li>";
        echo "<li>Verifique se a porta 3306 está configurada</li>";
        echo "</ol>";
        echo "</div>";
    }
} else {
    echo "<div class='erro'>❌ Executável MySQL não encontrado: $mysql_bin</div>";
    echo "<div class='info'>";
    echo "<h3>🔧 SOLUÇÃO:</h3>";
    echo "<ol>";
    echo "<li>Verifique se o XAMPP está instalado corretamente</li>";
    echo "<li>Reinstale o XAMPP se necessário</li>";
    echo "<li>Ou execute o XAMPP Control Panel manualmente</li>";
    echo "</ol>";
    echo "</div>";
}

// Se ainda não funcionar, mostrar soluções manuais
if (!testarMySQL()) {
    echo "<h2>📋 SOLUÇÕES MANUAIS</h2>";
    
    echo "<div class='info'>";
    echo "<h3>🔄 SOLUÇÃO 1: XAMPP Control Panel</h3>";
    echo "<ol>";
    echo "<li>Abra o XAMPP Control Panel como administrador</li>";
    echo "<li>Clique em <strong>Start</strong> ao lado do MySQL</li>";
    echo "<li>Se der erro, clique em <strong>Logs</strong> para ver detalhes</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>🔧 SOLUÇÃO 2: Verificar Configuração</h3>";
    echo "<ol>";
    echo "<li>No XAMPP Control Panel, clique em <strong>Config</strong> ao lado do MySQL</li>";
    echo "<li>Selecione <strong>my.ini</strong></li>";
    echo "<li>Verifique se a porta está como 3306</li>";
    echo "<li>Verifique se o datadir está correto</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>🚫 SOLUÇÃO 3: Mudar Porta</h3>";
    echo "<ol>";
    echo "<li>Edite <strong>C:\\xampp\\mysql\\bin\\my.ini</strong></li>";
    echo "<li>Mude <strong>port=3306</strong> para <strong>port=3307</strong></li>";
    echo "<li>Reinicie o XAMPP</li>";
    echo "</ol>";
    echo "</div>";
}

// COMANDOS ÚTEIS
echo "<h2>💻 COMANDOS ÚTEIS</h2>";
echo "<div class='info'>";
echo "<strong>Execute no CMD como administrador:</strong><br>";
echo "<pre>";
echo "# Encerrar processos MySQL\n";
echo "taskkill /F /IM mysqld.exe\n";
echo "taskkill /F /IM mysql.exe\n\n";
echo "# Verificar porta\n";
echo "netstat -an | findstr :3306\n\n";
echo "# Verificar processos\n";
echo "tasklist | findstr mysql\n";
echo "</pre>";
echo "</div>";

// LINKS ÚTEIS
echo "<h2>🔗 FERRAMENTAS ÚTEIS</h2>";
echo "<div class='info'>";
echo "<a href='diagnostico_mysql.php' class='btn'>🔍 Diagnóstico Completo</a>";
echo "<a href='corrigir_mysql_automatico.php' class='btn'>🔧 Correção Avançada</a>";
echo "<a href='verificar_mysql.php' class='btn'>✅ Verificar MySQL</a>";
echo "<a href='SOLUCAO-MYSQL-XAMPP.html' class='btn'>📖 Guia Completo</a>";
echo "</div>";

echo "<h2>📞 SE NADA FUNCIONAR</h2>";
echo "<div class='info'>";
echo "<ol>";
echo "<li>Reinicie o computador</li>";
echo "<li>Abra o XAMPP como administrador</li>";
echo "<li>Se ainda não funcionar, reinstale o XAMPP</li>";
echo "<li>Ou use a correção avançada: <a href='corrigir_mysql_automatico.php'>Correção Avançada</a></li>";
echo "</ol>";
echo "</div>";
?>


