<?php
echo "<h2>🔧 Configuração do Sistema</h2>";

try {
    // Conecta e cria banco
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sistema_agendamento");
    echo "✅ Banco criado<br>";
    
    // Conecta ao banco e cria tabela
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_agendamento', 'root', '');
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS agendamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            data DATE NOT NULL,
            hora TIME NOT NULL,
            servico VARCHAR(50) NOT NULL,
            status VARCHAR(20) DEFAULT 'agendado',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Tabela criada<br>";
    
    echo "<br><strong>🎉 Tudo pronto!</strong><br>";
    echo "<a href='teste.html'>📋 Ir para Teste</a><br>";
    echo "<a href='index.html'>🏠 Ir para Sistema</a>";
    
} catch(Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>


