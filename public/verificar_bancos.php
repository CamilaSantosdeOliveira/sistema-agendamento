<?php
$host = 'localhost';
$user = 'root';
$pass = '';

// Conectar sem especificar banco
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

echo "=== BANCOS DE DADOS DISPONÍVEIS ===\n\n";

// Listar todos os bancos
$result = $conn->query("SHOW DATABASES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $database = $row[0];
        echo "📁 Banco: $database\n";
        
        // Verificar se tem tabelas relacionadas ao sistema
        $conn->select_db($database);
        $tables = $conn->query("SHOW TABLES");
        
        $has_system_tables = false;
        $system_tables = [];
        
        if ($tables) {
            while ($table = $tables->fetch_array()) {
                $table_name = $table[0];
                if (in_array($table_name, ['usuarios', 'professores', 'alunos', 'cursos', 'agendamentos', 'users', 'user'])) {
                    $has_system_tables = true;
                    $system_tables[] = $table_name;
                }
            }
        }
        
        if ($has_system_tables) {
            echo "   ✅ Tem tabelas do sistema: " . implode(', ', $system_tables) . "\n";
            
            // Verificar dados nas tabelas principais
            foreach ($system_tables as $table) {
                $count = $conn->query("SELECT COUNT(*) as total FROM $table");
                if ($count) {
                    $total = $count->fetch_assoc()['total'];
                    echo "      📊 $table: $total registros\n";
                }
            }
        } else {
            echo "   ❌ Sem tabelas do sistema\n";
        }
        echo "\n";
    }
} else {
    echo "❌ Erro ao listar bancos de dados\n";
}

$conn->close();
?>



