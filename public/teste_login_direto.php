<?php
echo "<h1>🧪 Teste Direto do Login</h1>";

// Simular o processo de login
$email = 'admin@educonnect.com';
$senha = '123456';

echo "<h2>📋 Testando Login:</h2>";
echo "<p><strong>Email:</strong> $email</p>";
echo "<p><strong>Senha:</strong> $senha</p>";

try {
    // Teste 1: Conectar ao banco
    echo "<h3>1. Testando conexão com banco...</h3>";
    
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'sistema_agendamento';
    
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        echo "<p>❌ <strong>Erro de conexão:</strong> " . $conn->connect_error . "</p>";
        exit;
    } else {
        echo "<p>✅ <strong>Conexão OK!</strong></p>";
    }
    
    // Teste 2: Buscar usuário
    echo "<h3>2. Buscando usuário...</h3>";
    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        echo "<p>✅ <strong>Usuário encontrado!</strong></p>";
        echo "<p>👤 Nome: {$usuario['nome']}</p>";
        echo "<p>🎓 Tipo: {$usuario['tipo_usuario']}</p>";
        
        // Teste 3: Verificar senha
        echo "<h3>3. Verificando senha...</h3>";
        
        if (password_verify($senha, $usuario['senha'])) {
            echo "<p>✅ <strong>Senha correta!</strong></p>";
            
            // Teste 4: Verificar se é admin
            echo "<h3>4. Verificando tipo de usuário...</h3>";
            
            if ($usuario['tipo_usuario'] === 'admin') {
                echo "<p>✅ <strong>É administrador!</strong></p>";
                
                // Simular criação de sessão
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                echo "<h2>🎉 <strong>LOGIN SIMULADO COM SUCESSO!</strong></h2>";
                echo "<p>✅ <strong>Sessão criada:</strong></p>";
                echo "<p>🆔 ID: {$_SESSION['usuario_id']}</p>";
                echo "<p>👤 Nome: {$_SESSION['usuario_nome']}</p>";
                echo "<p>📧 Email: {$_SESSION['usuario_email']}</p>";
                echo "<p>🎓 Tipo: {$_SESSION['usuario_tipo']}</p>";
                
                echo "<h2>🎯 Próximos Passos:</h2>";
                echo "<p><a href='dashboard_corrigido.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Ir para Dashboard</a></p>";
                
            } else {
                echo "<p>❌ <strong>Não é administrador!</strong> Tipo: {$usuario['tipo_usuario']}</p>";
            }
            
        } else {
            echo "<p>❌ <strong>Senha incorreta!</strong></p>";
        }
        
    } else {
        echo "<p>❌ <strong>Usuário não encontrado!</strong></p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>🔧 Links Úteis:</h2>";
echo "<p><a href='login.php' target='_blank'>🔐 Tela de Login</a></p>";
echo "<p><a href='teste_conexao_mysql.php' target='_blank'>🗄️ Teste MySQL</a></p>";
echo "<p><a href='verificar_conta_admin.php' target='_blank'>👤 Verificar Conta</a></p>";
?>





