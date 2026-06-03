<?php
echo "<h1>🔍 Verificando Conta de Administrador</h1>";

try {
    include 'db.php';
    
    if ($conn && !$conn->connect_error) {
        echo "<p>✅ <strong>Conexão com banco:</strong> OK</p>";
        
        // Verificar se a conta admin existe
        $stmt = $conn->prepare("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE email = ?");
        $email = 'admin@educonnect.com';
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            echo "<p>✅ <strong>Conta de administrador encontrada!</strong></p>";
            echo "<p>👤 <strong>Nome:</strong> {$usuario['nome']}</p>";
            echo "<p>📧 <strong>Email:</strong> {$usuario['email']}</p>";
            echo "<p>🎓 <strong>Tipo:</strong> {$usuario['tipo_usuario']}</p>";
            echo "<p>🆔 <strong>ID:</strong> {$usuario['id']}</p>";
            
            echo "<h2>✅ <strong>Login deve funcionar!</strong></h2>";
            echo "<p>🎯 <strong>Use:</strong> admin@educonnect.com / 123456</p>";
            
        } else {
            echo "<p>❌ <strong>Conta de administrador não encontrada!</strong></p>";
            echo "<p>🔄 Criando conta de administrador...</p>";
            
            // Criar conta admin
            $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $nome = 'Administrador';
            $email = 'admin@educonnect.com';
            $tipo = 'admin';
            $stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo);
            
            if ($stmt->execute()) {
                echo "<p>✅ <strong>Conta de administrador criada!</strong></p>";
                echo "<p>🎯 <strong>Use:</strong> admin@educonnect.com / 123456</p>";
            } else {
                echo "<p>❌ <strong>Erro ao criar conta:</strong> " . $stmt->error . "</p>";
            }
        }
        
        // Listar todas as contas
        echo "<h2>📋 Todas as contas no sistema:</h2>";
        $result = $conn->query("SELECT id, nome, email, tipo_usuario FROM usuarios ORDER BY tipo_usuario, nome");
        
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['nome']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['tipo_usuario']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p>❌ <strong>Erro na conexão com banco</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Teste o Login:</h2>";
echo "<p><a href='login.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Testar Login</a></p>";
?>








