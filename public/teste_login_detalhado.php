<?php
echo "<h1>🔍 Teste Detalhado do Login</h1>";

// Teste 1: Verificar se a API de login existe
echo "<h2>1. Verificando usuarios_api.php</h2>";
if (file_exists('usuarios_api.php')) {
    echo "<p>✅ usuarios_api.php existe</p>";
    
    // Verificar se a função fazerLogin existe
    $content = file_get_contents('usuarios_api.php');
    if (strpos($content, 'fazerLogin') !== false) {
        echo "<p>✅ Função fazerLogin encontrada</p>";
    } else {
        echo "<p>❌ Função fazerLogin não encontrada</p>";
    }
} else {
    echo "<p>❌ usuarios_api.php não existe</p>";
}

// Teste 2: Verificar conexão com banco
echo "<h2>2. Testando conexão com banco</h2>";
try {
    include 'db.php';
    if ($conn && !$conn->connect_error) {
        echo "<p>✅ Conexão com banco OK</p>";
        
        // Verificar se a tabela usuarios existe
        $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
        if ($result && $result->num_rows > 0) {
            echo "<p>✅ Tabela usuarios existe</p>";
        } else {
            echo "<p>❌ Tabela usuarios não existe</p>";
        }
    } else {
        echo "<p>❌ Erro na conexão: " . ($conn ? $conn->connect_error : 'Conexão nula') . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

// Teste 3: Verificar se o usuário admin existe
echo "<h2>3. Verificando usuário admin</h2>";
try {
    if ($conn) {
        $stmt = $conn->prepare("SELECT id, nome, email, tipo_usuario, ativo FROM usuarios WHERE email = ?");
        $email = 'admin@educonnect.com';
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            echo "<p>✅ Usuário admin encontrado:</p>";
            echo "<ul>";
            echo "<li>ID: " . $usuario['id'] . "</li>";
            echo "<li>Nome: " . $usuario['nome'] . "</li>";
            echo "<li>Email: " . $usuario['email'] . "</li>";
            echo "<li>Tipo: " . $usuario['tipo_usuario'] . "</li>";
            echo "<li>Ativo: " . ($usuario['ativo'] ? 'Sim' : 'Não') . "</li>";
            echo "</ul>";
        } else {
            echo "<p>❌ Usuário admin não encontrado</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao verificar usuário: " . $e->getMessage() . "</p>";
}

// Teste 4: Simular processo de login
echo "<h2>4. Simulando processo de login</h2>";
try {
    if ($conn) {
        $email = 'admin@educonnect.com';
        $senha = '123456';
        
        // Buscar usuário
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            echo "<p>✅ Usuário encontrado no banco</p>";
            
            // Verificar senha
            if (password_verify($senha, $usuario['senha'])) {
                echo "<p>✅ Senha correta!</p>";
                
                // Verificar se é admin
                if ($usuario['tipo_usuario'] === 'admin') {
                    echo "<p>✅ Usuário é administrador</p>";
                    echo "<p>🎉 Login deve funcionar perfeitamente!</p>";
                } else {
                    echo "<p>❌ Usuário não é administrador (tipo: " . $usuario['tipo_usuario'] . ")</p>";
                }
            } else {
                echo "<p>❌ Senha incorreta!</p>";
                echo "<p>Hash no banco: " . substr($usuario['senha'], 0, 20) . "...</p>";
            }
        } else {
            echo "<p>❌ Usuário não encontrado</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Erro no teste de login: " . $e->getMessage() . "</p>";
}

// Teste 5: Verificar se a API está acessível
echo "<h2>5. Testando acesso à API</h2>";
echo "<p><a href='usuarios_api.php?acao=teste' target='_blank'>🔗 Testar API diretamente</a></p>";

echo "<h2>🎯 Próximos Passos:</h2>";
echo "<ol>";
echo "<li>Se todos os testes passaram, o problema pode ser no JavaScript</li>";
echo "<li>Abra o console do navegador (F12) e tente fazer login</li>";
echo "<li>Verifique se há erros JavaScript</li>";
echo "<li>Teste em uma aba anônima</li>";
echo "</ol>";
?>






