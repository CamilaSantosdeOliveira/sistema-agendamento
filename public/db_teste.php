<?php
// Teste de conexão com banco de dados
header('Content-Type: application/json');

try {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'sistema_agendamento';
    
    echo "Tentando conectar ao banco...\n";
    
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }
    
    echo "✅ Conexão bem-sucedida!\n";
    echo "Servidor: " . $conn->server_info . "\n";
    echo "Versão: " . $conn->server_version . "\n";
    
    // Testar se a tabela usuarios existe
    $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
    if ($result->num_rows > 0) {
        echo "✅ Tabela 'usuarios' encontrada!\n";
        
        // Contar usuários
        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
        $row = $result->fetch_assoc();
        echo "👥 Total de alunos: " . $row['total'] . "\n";
    } else {
        echo "❌ Tabela 'usuarios' não encontrada!\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
?>



















