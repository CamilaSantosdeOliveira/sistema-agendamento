<?php
echo "<h2>🔍 VERIFICANDO STATUS DO XAMPP</h2>";

// Verificar se conseguimos acessar localhost
$urls_testadas = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:80',
    'http://127.0.0.1:80'
];

$apache_rodando = false;

foreach ($urls_testadas as $url) {
    echo "<h3>🔍 Testando: $url</h3>";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET'
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    
    if ($result !== false) {
        echo "✅ <strong>SUCESSO!</strong> Apache está rodando!<br>";
        $apache_rodando = true;
        break;
    } else {
        echo "❌ <strong>Falhou:</strong> Não foi possível conectar<br>";
    }
    
    echo "<br>";
}

if (!$apache_rodando) {
    echo "<h2>❌ APACHE NÃO ESTÁ RODANDO</h2>";
    
    echo "<h3>🔧 SOLUÇÕES:</h3>";
    
    echo "<h4>1️⃣ Ativar XAMPP Control Panel:</h4>";
    echo "<ol>";
    echo "<li>Abra o <strong>XAMPP Control Panel</strong></li>";
    echo "<li>Procure por <strong>Apache</strong> na lista</li>";
    echo "<li>Clique em <strong>Start</strong> ao lado do Apache</li>";
    echo "<li>Se der erro, clique em <strong>Config</strong> → <strong>httpd.conf</strong></li>";
    echo "<li>Verifique se a porta 80 está livre</li>";
    echo "</ol>";
    
    echo "<h4>2️⃣ Verificar portas em uso:</h4>";
    echo "<p>Execute no CMD como administrador:</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo "netstat -an | findstr :80\n";
    echo "netstat -an | findstr :443\n";
    echo "tasklist | findstr apache\n";
    echo "tasklist | findstr httpd\n";
    echo "</pre>";
    
    echo "<h4>3️⃣ Verificar se XAMPP está instalado:</h4>";
    echo "<ol>";
    echo "<li>Procure por <strong>XAMPP</strong> no menu Iniciar</li>";
    echo "<li>Ou vá em <strong>C:\\xampp\\xampp-control.exe</strong></li>";
    echo "<li>Se não encontrar, baixe em: <a href='https://www.apachefriends.org/' target='_blank'>apachefriends.org</a></li>";
    echo "</ol>";
    
    echo "<h4>4️⃣ Solucionar conflitos de porta:</h4>";
    echo "<ol>";
    echo "<li>Feche outros servidores web (IIS, etc.)</li>";
    echo "<li>Verifique se Skype não está usando porta 80</li>";
    echo "<li>Use porta alternativa: <strong>8080</strong></li>";
    echo "</ol>";
    
    echo "<h3>🔄 DEPOIS DE ATIVAR:</h3>";
    echo "<p>Recarregue esta página para testar novamente!</p>";
    
    echo "<h3>📞 LINKS ÚTEIS:</h3>";
    echo "<ul>";
    echo "<li><strong>XAMPP:</strong> http://localhost</li>";
    echo "<li><strong>phpMyAdmin:</strong> http://localhost/phpmyadmin</li>";
    echo "<li><strong>Seu Sistema:</strong> http://localhost/Sistema%20De%20Agendamento/public/</li>";
    echo "</ul>";
    
} else {
    echo "<h2>✅ APACHE ESTÁ FUNCIONANDO!</h2>";
    
    echo "<h3>🎯 Próximos Passos:</h3>";
    echo "<ol>";
    echo "<li><a href='http://localhost/Sistema%20De%20Agendamento/public/verificar_mysql.php'>Verificar MySQL</a></li>";
    echo "<li><a href='http://localhost/Sistema%20De%20Agendamento/public/dashboard_corrigido.php'>Acessar Dashboard</a></li>";
    echo "<li><a href='http://localhost/Sistema%20De%20Agendamento/public/'>Página Principal</a></li>";
    echo "</ol>";
}

echo "<h3>📋 COMANDOS ÚTEIS:</h3>";
echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
echo "# Verificar se Apache está rodando\n";
echo "netstat -an | findstr :80\n\n";
echo "# Verificar processos Apache\n";
echo "tasklist | findstr apache\n";
echo "tasklist | findstr httpd\n\n";
echo "# Verificar serviços\n";
echo "sc query apache\n";
echo "</pre>";

echo "<h3>🔧 CONFIGURAÇÃO ALTERNATIVA:</h3>";
echo "<p>Se a porta 80 estiver ocupada, configure o Apache para usar porta 8080:</p>";
echo "<ol>";
echo "<li>XAMPP Control Panel → Apache → Config → httpd.conf</li>";
echo "<li>Procure por <strong>Listen 80</strong></li>";
echo "<li>Mude para <strong>Listen 8080</strong></li>";
echo "<li>Reinicie o Apache</li>";
echo "<li>Acesse: <strong>http://localhost:8080</strong></li>";
echo "</ol>";
?>
