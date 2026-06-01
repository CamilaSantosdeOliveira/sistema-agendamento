<?php
echo "<h1>🧪 Teste Rápido do Login</h1>";

// Testar conexão com banco
try {
    include 'db.php';
    
    if ($conn && !$conn->connect_error) {
        echo "<p>✅ <strong>Conexão com banco:</strong> OK</p>";
        
        // Verificar se a tabela usuarios existe
        $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
        if ($result && $result->num_rows > 0) {
            echo "<p>✅ <strong>Tabela usuarios:</strong> Existe</p>";
            
            // Verificar se há usuários
            $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
            if ($result) {
                $total = $result->fetch_assoc()['total'];
                echo "<p>📊 <strong>Usuários cadastrados:</strong> $total</p>";
                
                if ($total > 0) {
                    echo "<p>✅ <strong>Login deve funcionar!</strong></p>";
                    
                    // Testar uma conta específica
                    $stmt = $conn->prepare("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE email = ?");
                    $email = 'maria.santos@educonnect.com';
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result && $result->num_rows > 0) {
                        $usuario = $result->fetch_assoc();
                        echo "<p>✅ <strong>Conta de teste encontrada:</strong></p>";
                        echo "<p>👤 Nome: {$usuario['nome']}</p>";
                        echo "<p>📧 Email: {$usuario['email']}</p>";
                        echo "<p>🎓 Tipo: {$usuario['tipo_usuario']}</p>";
                    } else {
                        echo "<p>⚠️ <strong>Conta de teste não encontrada!</strong></p>";
                    }
                } else {
                    echo "<p>❌ <strong>Nenhum usuário cadastrado!</strong></p>";
                    echo "<p>Você precisa importar o backup.</p>";
                }
            }
        } else {
            echo "<p>❌ <strong>Tabela usuarios:</strong> NÃO existe</p>";
            echo "<p>Você precisa importar o backup.</p>";
        }
        
    } else {
        echo "<p>❌ <strong>Conexão com banco:</strong> ERRO</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Links para Teste:</h2>";
echo "<p><a href='login.php' target='_blank'>🔐 Testar Login</a></p>";
echo "<p><a href='importar_backup_automatico.php' target='_blank'>💾 Importar Backup</a></p>";
echo "<p><a href='http://localhost:8080/phpmyadmin' target='_blank'>🗄️ phpMyAdmin</a></p>";

echo "<h2>📋 Contas de Teste:</h2>";
echo "<p><strong>Professor:</strong> maria.santos@educonnect.com / 123456</p>";
echo "<p><strong>Aluno:</strong> joao.silva@email.com / 123456</p>";
?>






