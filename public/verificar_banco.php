<?php
echo "<h1>🔍 Verificação do Banco de Dados</h1>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema_agendamento';

try {
    // Primeiro, conectar sem especificar banco
    echo "<p>🔄 Conectando ao MySQL...</p>";
    $conn = new mysqli($host, $user, $pass, '', 3306);
    
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }
    
    echo "<p>✅ <strong>MySQL conectado com sucesso!</strong></p>";
    
    // Verificar se o banco existe
    echo "<p>🔍 Verificando se o banco '$db' existe...</p>";
    $result = $conn->query("SHOW DATABASES LIKE '$db'");
    
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ <strong>Banco '$db' existe!</strong></p>";
        
        // Conectar ao banco específico
        $conn->select_db($db);
        
        // Verificar tabelas
        echo "<p>📋 Verificando tabelas...</p>";
        $tables = $conn->query("SHOW TABLES");
        
        if ($tables && $tables->num_rows > 0) {
            echo "<p>✅ <strong>Tabelas encontradas:</strong></p>";
            echo "<ul>";
            while ($table = $tables->fetch_array()) {
                $table_name = $table[0];
                echo "<li>$table_name</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>⚠️ <strong>Banco existe mas não tem tabelas!</strong></p>";
            echo "<p>Você precisa importar o backup ou criar as tabelas.</p>";
        }
        
    } else {
        echo "<p>❌ <strong>Banco '$db' não existe!</strong></p>";
        echo "<p>🔄 Criando banco de dados...</p>";
        
        if ($conn->query("CREATE DATABASE $db")) {
            echo "<p>✅ <strong>Banco '$db' criado com sucesso!</strong></p>";
            echo "<p>⚠️ <strong>Agora você precisa importar o backup ou criar as tabelas.</strong></p>";
        } else {
            echo "<p>❌ <strong>Erro ao criar banco:</strong> " . $conn->error . "</p>";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Próximos Passos:</h2>";
echo "<p><strong>1.</strong> Se o banco não existe: Importe o backup</p>";
echo "<p><strong>2.</strong> Se o banco existe mas não tem tabelas: Importe o backup</p>";
echo "<p><strong>3.</strong> Se tudo está OK: O sistema deve funcionar</p>";

echo "<h2>📋 Links Úteis:</h2>";
echo "<p><a href='backups/backup_completo_corrigido_2025-08-29_07-35-39.sql' target='_blank'>💾 Backup Completo</a></p>";
echo "<p><a href='http://localhost:8080/phpmyadmin' target='_blank'>🗄️ phpMyAdmin</a></p>";
?>
