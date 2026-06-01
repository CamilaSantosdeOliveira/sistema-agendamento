<?php
echo "<h1>🔍 VERIFICADOR DE PORTA MYSQL</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .erro { color: #d32f2f; background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #d32f2f; }
    .sucesso { color: #388e3c; background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #388e3c; }
    .info { color: #1976d2; background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #1976d2; }
    .aviso { color: #f57c00; background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #f57c00; }
    .btn { display: inline-block; padding: 10px 20px; background: #2196f3; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn-success { background: #4caf50; }
    .comando { background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
    .porta { background: #f0f8ff; padding: 15px; border-radius: 5px; border-left: 4px solid #2196f3; margin: 10px 0; }
</style>";

echo "<div class='container'>";

// Função para executar comandos
function executarComando($comando) {
    $output = [];
    $return_var = 0;
    exec($comando . " 2>&1", $output, $return_var);
    return ['output' => $output, 'return' => $return_var];
}

// Função para testar MySQL em uma porta específica
function testarMySQLPorta($porta) {
    try {
        $conn = new mysqli('localhost', 'root', '', '', $porta);
        if (!$conn->connect_error) {
            $conn->close();
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

echo "<h2>🔍 VERIFICANDO PORTAS DO MYSQL</h2>";

// PASSO 1: Verificar portas em uso
echo "<h3>1️⃣ Verificando portas em uso...</h3>";

$portas_para_verificar = [3306, 3307, 3308, 3309, 3310];

foreach ($portas_para_verificar as $porta) {
    $resultado = executarComando("netstat -ano | findstr :$porta");
    if (!empty($resultado['output'])) {
        echo "<div class='porta'>";
        echo "<strong>🔴 Porta $porta está em uso:</strong><br>";
        foreach ($resultado['output'] as $linha) {
            echo "<div class='comando'>$linha</div>";
        }
        echo "</div>";
    } else {
        echo "<div class='info'>✅ Porta $porta está livre</div>";
    }
}

// PASSO 2: Testar conexão MySQL
echo "<h3>2️⃣ Testando conexão MySQL...</h3>";

$mysql_funcionando = false;
foreach ($portas_para_verificar as $porta) {
    if (testarMySQLPorta($porta)) {
        echo "<div class='sucesso'>";
        echo "<h4>🎉 MYSQL FUNCIONANDO NA PORTA $porta!</h4>";
        echo "<p>O MySQL está rodando e aceitando conexões na porta $porta.</p>";
        echo "</div>";
        $mysql_funcionando = true;
        break;
    }
}

if (!$mysql_funcionando) {
    echo "<div class='erro'>❌ MySQL não está funcionando em nenhuma porta testada</div>";
}

// PASSO 3: Verificar configuração do my.ini
echo "<h3>3️⃣ Verificando configuração do my.ini...</h3>";

$my_ini_path = 'C:\\xampp\\mysql\\bin\\my.ini';
if (file_exists($my_ini_path)) {
    $conteudo = file_get_contents($my_ini_path);
    
    // Procurar por configuração de porta
    if (preg_match('/port\s*=\s*(\d+)/i', $conteudo, $matches)) {
        $porta_configurada = $matches[1];
        echo "<div class='info'>";
        echo "<strong>📋 Configuração encontrada:</strong> porta $porta_configurada<br>";
        echo "Arquivo: $my_ini_path";
        echo "</div>";
    } else {
        echo "<div class='aviso'>⚠️ Porta não especificada no my.ini</div>";
    }
} else {
    echo "<div class='erro'>❌ Arquivo my.ini não encontrado</div>";
}

// PASSO 4: Verificar processos MySQL
echo "<h3>4️⃣ Verificando processos MySQL...</h3>";

$resultado = executarComando("tasklist | findstr mysql");
if (!empty($resultado['output'])) {
    echo "<div class='info'>";
    echo "<strong>🔄 Processos MySQL encontrados:</strong><br>";
    foreach ($resultado['output'] as $linha) {
        echo "<div class='comando'>$linha</div>";
    }
    echo "</div>";
} else {
    echo "<div class='aviso'>⚠️ Nenhum processo MySQL encontrado</div>";
}

// PASSO 5: Resumo e recomendações
echo "<h3>📋 RESUMO E RECOMENDAÇÕES</h3>";

if ($mysql_funcionando) {
    echo "<div class='sucesso'>";
    echo "<h4>✅ MYSQL ESTÁ FUNCIONANDO!</h4>";
    echo "<p>O MySQL está rodando corretamente. Seu sistema deve funcionar normalmente.</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h4>🔗 LINKS ÚTEIS:</h4>";
    echo "<a href='http://localhost/phpmyadmin' target='_blank' class='btn btn-success'>🌐 Abrir phpMyAdmin</a>";
    echo "<a href='http://localhost:8080/Sistema%20De%20Agendamento/public/' class='btn btn-success'>🏠 Sistema Principal</a>";
    echo "</div>";
} else {
    echo "<div class='erro'>";
    echo "<h4>❌ MYSQL NÃO ESTÁ FUNCIONANDO</h4>";
    echo "<p>O MySQL não está rodando. Execute uma das correções abaixo:</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h4>🔧 SOLUÇÕES:</h4>";
    echo "<a href='corrigir_porta_mysql.php' class='btn'>🔧 Correção de Porta</a>";
    echo "<a href='corrigir_mysql_ultimo_recurso.php' class='btn'>⚡ Correção de Último Recurso</a>";
    echo "<a href='verificar_mysql.php' class='btn'>🔍 Verificar MySQL</a>";
    echo "</div>";
}

echo "<h3>💻 COMANDOS MANUAIS</h3>";
echo "<p>Execute no CMD como administrador:</p>";
echo "<div class='comando'># Verificar portas em uso
netstat -ano | findstr :3306
netstat -ano | findstr :3307

# Verificar processos MySQL
tasklist | findstr mysql

# Verificar serviços MySQL
sc query mysql</div>";

echo "</div>";
?>











