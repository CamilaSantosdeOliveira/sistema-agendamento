<?php
echo "<h1>🔧 Configuração Final do Sistema</h1>";

try {
    // Conecta ao MySQL
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Cria banco
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sistema_agendamento");
    echo "<p>✅ Banco de dados criado/verificado</p>";
    
    // Conecta ao banco
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_agendamento', 'root', '');
    
    // Cria tabela
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
    echo "<p>✅ Tabela criada/verificada</p>";
    
    // Testa API
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos");
    $result = $stmt->fetch();
    echo "<p>✅ API testada - {$result['total']} agendamentos no banco</p>";
    
    echo "<br><h2>🎉 SISTEMA PRONTO!</h2>";
    echo "<p><a href='sistema_final.html' style='background:#28a745;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;font-size:18px;'>🚀 USAR SISTEMA AGORA</a></p>";
    
} catch(Exception $e) {
    echo "<p style='color:red'>❌ Erro: " . $e->getMessage() . "</p>";
    echo "<p>Certifique-se que o MySQL está rodando no XAMPP!</p>";
}
?>


