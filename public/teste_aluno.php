<?php
session_start();
require_once 'db.php';

echo "<h1>🧪 Teste - Verificar Aluno</h1>";

try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=sistema_agendamento", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se o aluno existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND tipo_usuario = 'aluno'");
    $stmt->execute(['joao.silva@email.com']);
    $aluno = $stmt->fetch();
    
    if ($aluno) {
        echo "<p style='color: green;'>✅ Aluno encontrado!</p>";
        echo "<p><strong>ID:</strong> {$aluno['id']}</p>";
        echo "<p><strong>Nome:</strong> {$aluno['nome']}</p>";
        echo "<p><strong>Email:</strong> {$aluno['email']}</p>";
        echo "<p><strong>Tipo:</strong> {$aluno['tipo']}</p>";
        
        // Testar login
        $senha = '123456';
        if (password_verify($senha, $aluno['senha'])) {
            echo "<p style='color: green;'>✅ Senha correta!</p>";
            
            // Fazer login
            $_SESSION['user_id'] = $aluno['id'];
            $_SESSION['nome'] = $aluno['nome'];
            $_SESSION['email'] = $aluno['email'];
            $_SESSION['tipo'] = $aluno['tipo'];
            
            echo "<p style='color: green;'>✅ Login realizado com sucesso!</p>";
            echo "<p><a href='dashboard_aluno.php'>🎯 Ir para Dashboard do Aluno</a></p>";
            
        } else {
            echo "<p style='color: red;'>❌ Senha incorreta!</p>";
            echo "<p>Senha atual: $senha</p>";
            echo "<p>Hash no banco: {$aluno['senha']}</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Aluno não encontrado!</p>";
        
        // Listar todos os alunos
        $stmt = $pdo->query("SELECT id, nome, email, tipo FROM usuarios WHERE tipo = 'aluno'");
        $alunos = $stmt->fetchAll();
        
        echo "<h3>👨‍🎓 Alunos cadastrados:</h3>";
        if (count($alunos) > 0) {
            echo "<ul>";
            foreach ($alunos as $a) {
                echo "<li><strong>{$a['nome']}</strong> - {$a['email']} (ID: {$a['id']})</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum aluno cadastrado!</p>";
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


