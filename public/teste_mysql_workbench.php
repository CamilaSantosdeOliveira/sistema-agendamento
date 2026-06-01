<?php
echo "<h2>🔍 Testando Conexão MySQL Workbench</h2>";

echo "<h3>📋 Configurações comuns do MySQL Workbench:</h3>";

// Configurações típicas do MySQL Workbench
$configuracoes = [
    // Configuração padrão MySQL 8.0
    ['localhost', 'root', '', 'sistema_agendamento', 3306],
    ['127.0.0.1', 'root', '', 'sistema_agendamento', 3306],
    
    // Com senha comum
    ['localhost', 'root', 'root', 'sistema_agendamento', 3306],
    ['127.0.0.1', 'root', 'root', 'sistema_agendamento', 3306],
    
    // Com senha admin
    ['localhost', 'root', 'admin', 'sistema_agendamento', 3306],
    ['127.0.0.1', 'root', 'admin', 'sistema_agendamento', 3306],
    
    // Com senha password
    ['localhost', 'root', 'password', 'sistema_agendamento', 3306],
    ['127.0.0.1', 'root', 'password', 'sistema_agendamento', 3306],
    
    // Com senha 123456
    ['localhost', 'root', '123456', 'sistema_agendamento', 3306],
    ['127.0.0.1', 'root', '123456', 'sistema_agendamento', 3306],
    
    // Com senha original
    ['localhost', 'root', 'Cami7890#', 'sistema_agendamento', 3306],
    ['127.0.0.1', 'root', 'Cami7890#', 'sistema_agendamento', 3306],
];

$sucesso = false;

foreach ($configuracoes as $i => $config) {
    list($host, $user, $pass, $db, $port) = $config;
    
    $senha_display = $pass ? $pass : '(sem senha)';
    echo "<h4>Teste " . ($i + 1) . ": $host:$port - $user - $senha_display</h4>";
    
    try {
        $conn = new mysqli($host, $user, $pass, $db, $port);
        
        if ($conn->connect_error) {
            echo "❌ <strong>Falhou:</strong> " . $conn->connect_error . "<br>";
        } else {
            echo "✅ <strong>SUCESSO!</strong> Conectado ao MySQL Workbench!<br>";
            
            // Verificar se o banco existe
            $result = $conn->query("SHOW DATABASES LIKE '$db'");
            if ($result && $result->num_rows > 0) {
                echo "📋 Banco '$db' encontrado!<br>";
                
                // Mostrar tabelas
                $tabelas = $conn->query("SHOW TABLES");
                if ($tabelas) {
                    echo "📊 Tabelas no banco:<br>";
                    while ($row = $tabelas->fetch_array()) {
                        echo "&nbsp;&nbsp;• " . $row[0] . "<br>";
                    }
                }
            } else {
                echo "⚠️ Banco '$db' não encontrado<br>";
                echo "📋 Bancos disponíveis:<br>";
                $bancos = $conn->query("SHOW DATABASES");
                while ($row = $bancos->fetch_array()) {
                    echo "&nbsp;&nbsp;• " . $row[0] . "<br>";
                }
            }
            
            $conn->close();
            $sucesso = true;
            break; // Parar no primeiro sucesso
        }
    } catch (Exception $e) {
        echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
}

if (!$sucesso) {
    echo "<h3>🔧 Como verificar no MySQL Workbench:</h3>";
    echo "<ol>";
    echo "<li>Abra o MySQL Workbench</li>";
    echo "<li>Clique na sua conexão (ex: Local instance MySQL80)</li>";
    echo "<li>Anote as configurações:</li>";
    echo "<ul>";
    echo "<li><strong>Hostname:</strong> (ex: localhost)</li>";
    echo "<li><strong>Port:</strong> (ex: 3306)</li>";
    echo "<li><strong>Username:</strong> (ex: root)</li>";
    echo "<li><strong>Password:</strong> (a senha que você usa)</li>";
    echo "</ul>";
    echo "<li>Me informe essas configurações para eu atualizar o db.php</li>";
    echo "</ol>";
    
    echo "<h3>📞 Alternativa:</h3>";
    echo "<p>Se você conseguir conectar no MySQL Workbench, me diga:</p>";
    echo "<ul>";
    echo "<li>Qual é a senha que você usa?</li>";
    echo "<li>Qual é o nome do banco de dados?</li>";
    echo "<li>Quais tabelas você já tem?</li>";
    echo "</ul>";
}
?>

