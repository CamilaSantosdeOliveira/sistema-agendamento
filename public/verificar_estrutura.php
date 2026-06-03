<?php
session_start();
require_once 'db.php';

echo "<h1>🔍 Verificar Estrutura da Tabela Usuarios</h1>";

try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=sistema_agendamento", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar estrutura da tabela usuarios
    $stmt = $pdo->query("DESCRIBE usuarios");
    $colunas = $stmt->fetchAll();
    
    echo "<h2>📋 Estrutura da Tabela 'usuarios':</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($colunas as $coluna) {
        echo "<tr>";
        echo "<td>{$coluna['Field']}</td>";
        echo "<td>{$coluna['Type']}</td>";
        echo "<td>{$coluna['Null']}</td>";
        echo "<td>{$coluna['Key']}</td>";
        echo "<td>{$coluna['Default']}</td>";
        echo "<td>{$coluna['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar se existe coluna de tipo de usuário
    $tem_tipo = false;
    $coluna_tipo = '';
    
    foreach ($colunas as $coluna) {
        if (in_array($coluna['Field'], ['tipo', 'tipo_usuario', 'role', 'user_type', 'categoria'])) {
            $tem_tipo = true;
            $coluna_tipo = $coluna['Field'];
            break;
        }
    }
    
    if ($tem_tipo) {
        echo "<p style='color: green;'>✅ Coluna de tipo encontrada: <strong>$coluna_tipo</strong></p>";
        
        // Listar todos os tipos de usuário
        $stmt = $pdo->query("SELECT DISTINCT $coluna_tipo FROM usuarios");
        $tipos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>👥 Tipos de usuário encontrados:</h3>";
        echo "<ul>";
        foreach ($tipos as $tipo) {
            echo "<li><strong>$tipo</strong></li>";
        }
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>❌ Nenhuma coluna de tipo encontrada!</p>";
        echo "<p>Colunas disponíveis:</p>";
        echo "<ul>";
        foreach ($colunas as $coluna) {
            echo "<li>{$coluna['Field']}</li>";
        }
        echo "</ul>";
    }
    
    // Verificar se o aluno existe (sem filtro de tipo)
    echo "<h2>👨‍🎓 Verificar Aluno:</h2>";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute(['joao.silva@email.com']);
    $aluno = $stmt->fetch();
    
    if ($aluno) {
        echo "<p style='color: green;'>✅ Usuário encontrado!</p>";
        echo "<p><strong>ID:</strong> {$aluno['id']}</p>";
        echo "<p><strong>Nome:</strong> {$aluno['nome']}</p>";
        echo "<p><strong>Email:</strong> {$aluno['email']}</p>";
        
        if ($tem_tipo) {
            echo "<p><strong>Tipo:</strong> {$aluno[$coluna_tipo]}</p>";
        }
        
        // Testar login
        $senha = '123456';
        if (password_verify($senha, $aluno['senha'])) {
            echo "<p style='color: green;'>✅ Senha correta!</p>";
            
            // Fazer login
            $_SESSION['user_id'] = $aluno['id'];
            $_SESSION['nome'] = $aluno['nome'];
            $_SESSION['email'] = $aluno['email'];
            if ($tem_tipo) {
                $_SESSION['tipo_usuario'] = $aluno[$coluna_tipo];
            }
            
            echo "<p style='color: green;'>✅ Login realizado com sucesso!</p>";
            echo "<p><a href='dashboard_aluno.php'>🎯 Ir para Dashboard do Aluno</a></p>";
            
        } else {
            echo "<p style='color: red;'>❌ Senha incorreta!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Usuário não encontrado!</p>";
        
        // Listar todos os usuários
        $stmt = $pdo->query("SELECT id, nome, email FROM usuarios LIMIT 10");
        $usuarios = $stmt->fetchAll();
        
        echo "<h3>👥 Usuários cadastrados (primeiros 10):</h3>";
        if (count($usuarios) > 0) {
            echo "<ul>";
            foreach ($usuarios as $u) {
                echo "<li><strong>{$u['nome']}</strong> - {$u['email']} (ID: {$u['id']})</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum usuário cadastrado!</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🔗 Links de Teste</h2>";
echo "<p><a href='login.php'>🔐 Tela de Login</a></p>";
echo "<p><a href='dashboard_aluno.php'>👨‍🎓 Dashboard Aluno</a></p>";
echo "<p><a href='logout.php'>🚪 Logout</a></p>";
?>




