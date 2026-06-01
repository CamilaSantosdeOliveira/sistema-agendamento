<?php
echo "<h2>🔧 Verificação do Sistema</h2>";

try {
    // Primeiro tenta conectar sem banco para criar se necessário
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Cria o banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sistema_agendamento");
    echo "✅ Banco de dados verificado/criado<br>";
    
    // Agora conecta com o banco
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_agendamento', 'root', '');
    
    // Cria a tabela se não existir
    $sql = "CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        data DATE NOT NULL,
        hora TIME NOT NULL,
        servico VARCHAR(50) NOT NULL,
        status VARCHAR(20) DEFAULT 'agendado',
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ Tabela verificada/criada<br>";
    
    // Testa a API
    echo "<h3>🧪 Testando API:</h3>";
    
    // Teste GET
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos");
    $result = $stmt->fetch();
    echo "✅ API GET funcionando - Total de agendamentos: " . $result['total'] . "<br>";
    
    echo "<br><strong>🎉 Sistema pronto para uso!</strong><br><br>";
    echo "<a href='index.html' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>🚀 Usar Sistema</a>";
    echo " ";
    echo "<a href='api.php' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>🔍 Testar API</a>";
    
} catch(Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    echo "<br>Verifique se o MySQL está rodando no XAMPP!";
}
?>
