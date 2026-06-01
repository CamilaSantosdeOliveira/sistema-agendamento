<?php
echo "<h2>🧪 Teste do Banco de Dados</h2>";

// Teste 1: Verificar se o MySQL está rodando
echo "<h3>1. Verificando MySQL:</h3>";
$output = shell_exec('netstat -an | findstr :3306 2>&1');
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Teste 2: Tentar conexão com banco
echo "<h3>2. Testando conexão com banco:</h3>";
try {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'sistema_agendamento';
    
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        echo "❌ Erro de conexão: " . $conn->connect_error . "<br>";
    } else {
        echo "✅ Conexão estabelecida com sucesso!<br>";
        
        // Teste 3: Verificar se a tabela usuarios existe
        echo "<h3>3. Verificando tabela usuarios:</h3>";
        $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
        if ($result->num_rows > 0) {
            echo "✅ Tabela 'usuarios' existe<br>";
            
            // Teste 4: Contar usuários
            $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
            $row = $result->fetch_assoc();
            echo "📊 Total de usuários: " . $row['total'] . "<br>";
        } else {
            echo "❌ Tabela 'usuarios' não existe<br>";
        }
        
        $conn->close();
    }
    
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}
?>
















