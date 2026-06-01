<?php
session_start();
require_once 'db.php';

echo "<h1>🧪 Teste das Páginas do Aluno</h1>";

try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=sistema_agendamento", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar aluno
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND tipo_usuario = 'aluno'");
    $stmt->execute(['joao.silva@email.com']);
    $aluno = $stmt->fetch();
    
    if ($aluno) {
        // Fazer login automático
        $_SESSION['user_id'] = $aluno['id'];
        $_SESSION['nome'] = $aluno['nome'];
        $_SESSION['email'] = $aluno['email'];
        $_SESSION['tipo_usuario'] = $aluno['tipo_usuario'];
        
        echo "✅ Login realizado como: " . $aluno['nome'] . "<br>";
        echo "📧 Email: " . $aluno['email'] . "<br>";
        echo "🎯 Tipo: " . $aluno['tipo_usuario'] . "<br><br>";
        
        echo "<h2>🔗 Links para Testar:</h2>";
        echo "<a href='dashboard_aluno.php' target='_blank' style='display: inline-block; margin: 10px; padding: 15px; background: #10b981; color: white; text-decoration: none; border-radius: 8px;'>🏠 Dashboard Aluno</a><br>";
        echo "<a href='meus_cursos_aluno.php' target='_blank' style='display: inline-block; margin: 10px; padding: 15px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px;'>📚 Meus Cursos</a><br>";
        echo "<a href='minhas_aulas_aluno.php' target='_blank' style='display: inline-block; margin: 10px; padding: 15px; background: #f59e0b; color: white; text-decoration: none; border-radius: 8px;'>📅 Minhas Aulas</a><br>";
        
        echo "<h2>📊 Status das Páginas:</h2>";
        
        // Verificar se as páginas existem
        $paginas = [
            'dashboard_aluno.php' => 'Dashboard Aluno',
            'meus_cursos_aluno.php' => 'Meus Cursos',
            'minhas_aulas_aluno.php' => 'Minhas Aulas'
        ];
        
        foreach ($paginas as $arquivo => $nome) {
            if (file_exists($arquivo)) {
                echo "✅ $nome ($arquivo) - <span style='color: green;'>Arquivo existe</span><br>";
            } else {
                echo "❌ $nome ($arquivo) - <span style='color: red;'>Arquivo não encontrado</span><br>";
            }
        }
        
        echo "<h2>🔍 Verificar Dados:</h2>";
        
        // Verificar agendamentos do aluno
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE aluno_id = ?");
        $stmt->execute([$aluno['id']]);
        $total_agendamentos = $stmt->fetchColumn();
        
        echo "📅 Total de agendamentos: $total_agendamentos<br>";
        
        if ($total_agendamentos > 0) {
            echo "✅ Aluno tem dados para mostrar<br>";
        } else {
            echo "⚠️ Aluno não tem agendamentos - páginas podem aparecer vazias<br>";
            echo "<a href='criar_agendamentos_teste.php' style='color: blue;'>➕ Criar dados de teste</a><br>";
        }
        
    } else {
        echo "❌ Aluno não encontrado!<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f1f5f9;
}

h1, h2 {
    color: #1e293b;
}

a {
    text-decoration: none;
}

a:hover {
    opacity: 0.8;
}
</style>






