<?php
echo "<h1>🧪 Teste Simples MySQL</h1>";

try {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'sistema_agendamento';
    
    echo "<p>🔄 Conectando...</p>";
    
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        throw new Exception('Erro: ' . $conn->connect_error);
    }
    
    echo "<p style='color: green;'>✅ Conectado!</p>";
    
    // Teste simples
    $sql = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>📊 Total de professores: <strong>{$row['total']}</strong></p>";
    } else {
        echo "<p>❌ Erro na query</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial; margin: 20px; }
</style>
















