<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h2>🔑 Criando Usuários Demo...</h2>";

try {
    // Verificar se os usuários já existem
    $check_query = "SELECT id FROM usuarios WHERE email IN ('maria.santos@educonnect.com', 'joao.silva@email.com')";
    $result = $conn->query($check_query);
    
    if ($result->num_rows > 0) {
        echo "<p>⚠️ Usuários demo já existem!</p>";
        echo "<p>📧 Emails disponíveis:</p>";
        
        $existing_users = $conn->query("SELECT nome, email, tipo_usuario FROM usuarios WHERE email IN ('maria.santos@educonnect.com', 'joao.silva@email.com')");
        while ($user = $existing_users->fetch_assoc()) {
            echo "<p>• <strong>{$user['nome']}</strong> ({$user['email']}) - {$user['tipo_usuario']}</p>";
        }
    } else {
        // Criar usuários demo
        $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
        
        // Professor
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo, data_criacao) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('ssssi', $nome, $email, $senha, $tipo, $ativo);
        
        // Maria Santos (Professora)
        $nome = 'Maria Santos';
        $email = 'maria.santos@educonnect.com';
        $senha = $senha_hash;
        $tipo = 'professor';
        $ativo = 1;
        $stmt->execute();
        
        echo "<p>✅ <strong>Maria Santos</strong> criada como professora</p>";
        
        // João Silva (Aluno)
        $nome = 'João Silva';
        $email = 'joao.silva@email.com';
        $senha = $senha_hash;
        $tipo = 'aluno';
        $ativo = 1;
        $stmt->execute();
        
        echo "<p>✅ <strong>João Silva</strong> criado como aluno</p>";
        
        echo "<p>🎯 <strong>Senha para ambos: 123456</strong></p>";
    }
    
    echo "<br><hr>";
    echo "<h3>🔑 Testar Login:</h3>";
    echo "<a href='login.php' style='background: #1e40af; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🚀 Ir para Login</a>";
    
    echo "<br><br>";
    echo "<h3>📊 Ver Dashboard:</h3>";
    echo "<a href='dashboard_corrigido.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>📊 Ver Dashboard</a>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>





