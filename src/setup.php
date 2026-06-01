<?php
/**
 * Script para criar o banco de dados
 * Execute este arquivo no navegador para criar automaticamente
 */

$host = 'localhost';
$username = 'root'; 
$password = '';
$dbname = 'sistema_agendamento';

try {
    // Conecta ao MySQL sem especificar banco
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Cria o banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco de dados '$dbname' criado com sucesso!<br>";
    
    // Conecta ao banco criado
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Cria a tabela
    $sql = "CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        data DATE NOT NULL,
        hora TIME NOT NULL,
        servico ENUM('consulta', 'exame', 'procedimento') NOT NULL,
        status ENUM('agendado', 'confirmado', 'realizado', 'cancelado') DEFAULT 'agendado',
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        INDEX idx_data_hora (data, hora),
        INDEX idx_email (email),
        INDEX idx_status (status)
    )";
    
    $pdo->exec($sql);
    echo "✅ Tabela 'agendamentos' criada com sucesso!<br>";
    
    // Insere dados de exemplo com seus dados
    $stmt = $pdo->prepare("INSERT IGNORE INTO agendamentos (id, nome, email, data, hora, servico, status) VALUES 
        (1, 'Seu Nome Aqui', 'seu.email@exemplo.com', '2025-08-02', '09:00:00', 'consulta', 'agendado'),
        (2, 'Teste Usuario', 'teste@email.com', '2025-08-03', '14:30:00', 'exame', 'confirmado'),
        (3, 'Exemplo Cliente', 'cliente@teste.com', '2025-08-05', '16:00:00', 'procedimento', 'agendado')");
    
    $stmt->execute();
    echo "✅ Dados de teste inseridos!<br>";
    
    echo "<br><strong>🎉 Configuração concluída!</strong><br>";
    echo "Agora você pode usar o sistema: <a href='../public/'>Ir para o Sistema</a>";
    
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
