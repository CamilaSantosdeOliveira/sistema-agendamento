<?php
echo "<h2>🔍 Testando Conexão com Banco de Dados</h2>";

// Testar diferentes configurações
$configuracoes = [
    ['localhost', 'root', 'Cami7890#', 'sistema_agendamento', 3306],
    ['localhost', 'root', 'Cami7890#', 'sistema_agendamento', 3307],
    ['127.0.0.1', 'root', 'Cami7890#', 'sistema_agendamento', 3306],
    ['127.0.0.1', 'root', 'Cami7890#', 'sistema_agendamento', 3307]
];

foreach ($configuracoes as $i => $config) {
    list($host, $user, $pass, $db, $port) = $config;
    
    echo "<h3>Teste " . ($i + 1) . ": $host:$port</h3>";
    
    try {
        $conn = new mysqli($host, $user, $pass, $db, $port);
        
        if ($conn->connect_error) {
            echo "❌ <strong>Falhou:</strong> " . $conn->connect_error . "<br>";
        } else {
            echo "✅ <strong>Sucesso!</strong> Conectado ao banco '$db'<br>";
            
            // Testar se as tabelas existem
            $result = $conn->query("SHOW TABLES");
            if ($result) {
                echo "📋 <strong>Tabelas encontradas:</strong><br>";
                while ($row = $result->fetch_array()) {
                    echo "&nbsp;&nbsp;• " . $row[0] . "<br>";
                }
            }
            
            $conn->close();
            break; // Parar no primeiro sucesso
        }
    } catch (Exception $e) {
        echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
}

echo "<h3>🔧 Próximos Passos:</h3>";
echo "<ol>";
echo "<li>Verifique se o XAMPP está rodando</li>";
echo "<li>Verifique se o MySQL está 'Started' no XAMPP Control Panel</li>";
echo "<li>Teste a conexão no phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
echo "</ol>";
?>
