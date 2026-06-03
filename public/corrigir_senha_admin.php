<?php
echo "<h1>🔧 Corrigindo Senha do Administrador</h1>";

try {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'sistema_agendamento';
    
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        echo "<p>❌ <strong>Erro de conexão:</strong> " . $conn->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ <strong>Conexão OK!</strong></p>";
    
    // Buscar o administrador
    $email = 'admin@educonnect.com';
    $stmt = $conn->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        echo "<p>✅ <strong>Administrador encontrado:</strong> {$usuario['nome']}</p>";
        
        // Gerar nova senha hash
        $nova_senha = '123456';
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        echo "<p>🔄 <strong>Atualizando senha...</strong></p>";
        
        // Atualizar senha
        $update_stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $nova_senha_hash, $email);
        
        if ($update_stmt->execute()) {
            echo "<p>✅ <strong>Senha atualizada com sucesso!</strong></p>";
            echo "<p>🎯 <strong>Nova senha:</strong> $nova_senha</p>";
            
            // Testar a nova senha
            echo "<p>🧪 <strong>Testando nova senha...</strong></p>";
            
            if (password_verify($nova_senha, $nova_senha_hash)) {
                echo "<p>✅ <strong>Senha testada e funcionando!</strong></p>";
                
                echo "<h2>🎉 <strong>SENHA CORRIGIDA!</strong></h2>";
                echo "<p><strong>Email:</strong> $email</p>";
                echo "<p><strong>Senha:</strong> $nova_senha</p>";
                
                echo "<h2>🎯 Teste o Login:</h2>";
                echo "<p><a href='login.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Fazer Login</a></p>";
                
            } else {
                echo "<p>❌ <strong>Erro ao testar senha!</strong></p>";
            }
            
        } else {
            echo "<p>❌ <strong>Erro ao atualizar senha:</strong> " . $update_stmt->error . "</p>";
        }
        
    } else {
        echo "<p>❌ <strong>Administrador não encontrado!</strong></p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}
?>








