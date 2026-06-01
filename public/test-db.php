<?php
// Teste simples de conexão com banco de dados
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🧪 Teste de Conexão - EduConnect</h1>";

try {
    // Testar conexão PDO direta
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    echo "<p style='color: green;'>✅ Conexão PDO básica OK</p>";
    
    // Verificar se o banco existe
    $stmt = $pdo->query("SHOW DATABASES LIKE 'educonnect'");
    $dbExists = $stmt->fetch();
    
    if ($dbExists) {
        echo "<p style='color: green;'>✅ Banco 'educonnect' existe</p>";
        
        // Conectar ao banco
        $pdo = new PDO('mysql:host=localhost;dbname=educonnect;charset=utf8mb4', 'root', '');
        echo "<p style='color: green;'>✅ Conectado ao banco 'educonnect'</p>";
        
        // Verificar tabelas
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>📊 Tabelas encontradas:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>✅ $table</li>";
        }
        echo "</ul>";
        
        // Testar API de cursos
        echo "<h3>🔌 Testando API de Cursos:</h3>";
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos");
        $result = $stmt->fetch();
        echo "<p>Total de cursos: <strong>{$result['total']}</strong></p>";
        
        // Testar API de professores
        echo "<h3>👨‍🏫 Testando API de Professores:</h3>";
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'professor'");
        $result = $stmt->fetch();
        echo "<p>Total de professores: <strong>{$result['total']}</strong></p>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ Banco 'educonnect' não existe</p>";
        echo "<p><a href='install.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Instalar Banco de Dados</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro de conexão: " . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.html'>← Voltar ao Dashboard</a></p>";
?>





































