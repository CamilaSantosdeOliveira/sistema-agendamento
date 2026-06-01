<?php
echo "<h1>🔍 Teste Detalhado de Conexão MySQL</h1>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema_agendamento';

echo "<h2>📋 Configurações:</h2>";
echo "<p><strong>Host:</strong> $host</p>";
echo "<p><strong>Usuário:</strong> $user</p>";
echo "<p><strong>Banco:</strong> $db</p>";

echo "<h2>🔧 Testando Conexões:</h2>";

// Teste 1: Conectar sem especificar banco
echo "<h3>1. Teste sem especificar banco:</h3>";
try {
    $conn = new mysqli($host, $user, $pass, '', 3306);
    if ($conn->connect_error) {
        echo "<p>❌ <strong>Erro:</strong> " . $conn->connect_error . "</p>";
    } else {
        echo "<p>✅ <strong>Sucesso!</strong> Conectou ao MySQL</p>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p>❌ <strong>Exceção:</strong> " . $e->getMessage() . "</p>";
}

// Teste 2: Conectar especificando banco
echo "<h3>2. Teste especificando banco:</h3>";
try {
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    if ($conn->connect_error) {
        echo "<p>❌ <strong>Erro:</strong> " . $conn->connect_error . "</p>";
    } else {
        echo "<p>✅ <strong>Sucesso!</strong> Conectou ao banco $db</p>";
        
        // Verificar tabelas
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            $tables = $result->num_rows;
            echo "<p>📋 <strong>Tabelas encontradas:</strong> $tables</p>";
            
            if ($tables > 0) {
                echo "<ul>";
                while ($row = $result->fetch_array()) {
                    echo "<li>{$row[0]}</li>";
                }
                echo "</ul>";
            }
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p>❌ <strong>Exceção:</strong> " . $e->getMessage() . "</p>";
}

// Teste 3: Tentar porta 3307 (alternativa)
echo "<h3>3. Teste na porta 3307:</h3>";
try {
    $conn = new mysqli($host, $user, $pass, $db, 3307);
    if ($conn->connect_error) {
        echo "<p>❌ <strong>Erro:</strong> " . $conn->connect_error . "</p>";
    } else {
        echo "<p>✅ <strong>Sucesso!</strong> Conectou na porta 3307</p>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p>❌ <strong>Exceção:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Soluções:</h2>";
echo "<p><strong>1.</strong> Se o teste 1 falhar: MySQL não está rodando</p>";
echo "<p><strong>2.</strong> Se o teste 2 falhar: Banco não existe</p>";
echo "<p><strong>3.</strong> Se o teste 3 funcionar: MySQL está na porta 3307</p>";

echo "<h2>🔧 Links Úteis:</h2>";
echo "<p><a href='criar_tabelas_essenciais.php' target='_blank'>💾 Criar Tabelas</a></p>";
echo "<p><a href='http://localhost:8080/phpmyadmin' target='_blank'>🗄️ phpMyAdmin</a></p>";
echo "<p><a href='login.php' target='_blank'>🔐 Testar Login</a></p>";
?>






