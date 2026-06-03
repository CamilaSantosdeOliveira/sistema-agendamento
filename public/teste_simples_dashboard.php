<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste EduConnect Tech</h1>";
echo "<p>PHP está funcionando!</p>";

// Testar conexão com banco
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema_agendamento';

try {
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Erro de conexão com banco: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Conexão com banco OK!</p>";
        
        // Testar se as tabelas existem
        $tables = ['usuarios', 'cursos', 'agendamentos'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<p style='color: green;'>✅ Tabela '$table' existe</p>";
            } else {
                echo "<p style='color: red;'>❌ Tabela '$table' não existe</p>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard_final.php'>Tentar abrir Dashboard Final</a></p>";
echo "<p><a href='index.php'>Tentar abrir Index</a></p>";
?>











