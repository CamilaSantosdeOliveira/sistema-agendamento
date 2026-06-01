<?php
echo "=== TESTE DE CONEXÃO COM BANCO DE DADOS ===\n\n";

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Conectar sem especificar banco
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die('❌ Erro de conexão: ' . $conn->connect_error . "\n");
    }
    
    echo "✅ Conectado ao MySQL com sucesso!\n\n";
    
    // Listar todos os bancos
    echo "=== BANCOS DE DADOS DISPONÍVEIS ===\n";
    $result = $conn->query("SHOW DATABASES");
    
    if ($result) {
        while ($row = $result->fetch_array()) {
            $database = $row[0];
            echo "📁 $database\n";
            
            // Verificar se tem tabelas do sistema
            $conn->select_db($database);
            $tables = $conn->query("SHOW TABLES");
            
            $system_tables = [];
            if ($tables) {
                while ($table = $tables->fetch_array()) {
                    $table_name = $table[0];
                    if (in_array($table_name, ['usuarios', 'professores', 'alunos', 'cursos', 'agendamentos'])) {
                        $system_tables[] = $table_name;
                    }
                }
            }
            
            if (!empty($system_tables)) {
                echo "   ✅ Tem tabelas do sistema: " . implode(', ', $system_tables) . "\n";
                
                // Contar registros
                foreach ($system_tables as $table) {
                    $count = $conn->query("SELECT COUNT(*) as total FROM $table");
                    if ($count) {
                        $total = $count->fetch_assoc()['total'];
                        echo "      📊 $table: $total registros\n";
                    }
                }
            }
            echo "\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>


