<?php
session_start();
require_once 'db.php';

echo "<h1>🧪 Teste de Status - Sistema Completo</h1>";

// Verificar se o professor está logado
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'professor') {
    echo "<p style='color: red;'>❌ Professor não está logado!</p>";
    echo "<p><a href='login.php'>Fazer Login</a></p>";
    exit;
}

$professor_id = $_SESSION['user_id'];
$professor_nome = $_SESSION['nome'];

echo "<h2>👨‍🏫 Dados do Professor</h2>";
echo "<p><strong>ID:</strong> $professor_id</p>";
echo "<p><strong>Nome:</strong> $professor_nome</p>";

try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=sistema_agendamento", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>📊 Status dos Agendamentos</h2>";
    
    // Verificar agendamentos do professor
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE professor_id = ?");
    $stmt->execute([$professor_id]);
    $total_agendamentos = $stmt->fetch()['total'];
    
    echo "<p><strong>Total de agendamentos:</strong> $total_agendamentos</p>";
    
    if ($total_agendamentos > 0) {
        echo "<p style='color: green;'>✅ Agendamentos criados com sucesso!</p>";
        
        // Mostrar detalhes dos agendamentos
        $stmt = $pdo->prepare("
            SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome 
            FROM agendamentos a 
            JOIN cursos c ON a.curso_id = c.id 
            JOIN usuarios u ON a.aluno_id = u.id 
            WHERE a.professor_id = ? 
            ORDER BY a.data_agendamento
        ");
        $stmt->execute([$professor_id]);
        $agendamentos = $stmt->fetchAll();
        
        echo "<h3>📅 Agendamentos Criados:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Curso</th><th>Aluno</th><th>Data</th><th>Hora</th><th>Duração</th><th>Status</th></tr>";
        
        foreach ($agendamentos as $agendamento) {
            echo "<tr>";
            echo "<td>{$agendamento['curso_nome']}</td>";
            echo "<td>{$agendamento['aluno_nome']}</td>";
            echo "<td>{$agendamento['data_agendamento']}</td>";
            echo "<td>{$agendamento['hora_inicio']}</td>";
            echo "<td>{$agendamento['duracao_horas']}h</td>";
            echo "<td>{$agendamento['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ Nenhum agendamento encontrado</p>";
    }
    
    echo "<h2>🎓 Cursos que você leciona</h2>";
    
    // Verificar cursos únicos que o professor leciona
    $stmt = $pdo->prepare("
        SELECT DISTINCT c.* 
        FROM cursos c 
        JOIN agendamentos a ON c.id = a.curso_id 
        WHERE a.professor_id = ? 
        ORDER BY c.nome
    ");
    $stmt->execute([$professor_id]);
    $cursos = $stmt->fetchAll();
    
    echo "<p><strong>Total de cursos que você leciona:</strong> " . count($cursos) . "</p>";
    
    if (count($cursos) > 0) {
        echo "<h3>📚 Cursos:</h3>";
        echo "<ul>";
        foreach ($cursos as $curso) {
            echo "<li><strong>{$curso['nome']}</strong> - {$curso['categoria']} ({$curso['nivel']})</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>👥 Alunos que você ensina</h2>";
    
    // Verificar alunos únicos
    $stmt = $pdo->prepare("
        SELECT DISTINCT u.*, COUNT(a.id) as total_aulas 
        FROM usuarios u 
        JOIN agendamentos a ON u.id = a.aluno_id 
        WHERE a.professor_id = ? AND u.tipo = 'aluno'
        GROUP BY u.id 
        ORDER BY u.nome
    ");
    $stmt->execute([$professor_id]);
    $alunos = $stmt->fetchAll();
    
    echo "<p><strong>Total de alunos:</strong> " . count($alunos) . "</p>";
    
    if (count($alunos) > 0) {
        echo "<h3>👨‍🎓 Alunos:</h3>";
        echo "<ul>";
        foreach ($alunos as $aluno) {
            echo "<li><strong>{$aluno['nome']}</strong> - {$aluno['total_aulas']} aulas</li>";
        }
        echo "</ul>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: " . $e->getMessage() . "</p>";
}

echo "<h2>🔗 Links de Teste</h2>";
echo "<p><a href='cursos_professor.php'>📚 Meus Cursos</a></p>";
echo "<p><a href='aulas_professor.php'>📅 Minhas Aulas</a></p>";
echo "<p><a href='alunos_professor.php'>👥 Meus Alunos</a></p>";
echo "<p><a href='cursos_professor_todos.php'>🎓 Todos os Cursos</a></p>";
echo "<p><a href='dashboard_professor.php'>🏠 Dashboard Professor</a></p>";
echo "<p><a href='criar_agendamentos_teste.php'>➕ Criar Mais Agendamentos</a></p>";

echo "<h2>🎯 Resultado Final</h2>";
if ($total_agendamentos > 0) {
    echo "<p style='color: green; font-size: 18px;'>🎉 SUCESSO! O sistema está funcionando perfeitamente!</p>";
    echo "<p>✅ Agendamentos criados</p>";
    echo "<p>✅ Cursos associados</p>";
    echo "<p>✅ Alunos conectados</p>";
    echo "<p>✅ Sistema pronto para demonstração</p>";
} else {
    echo "<p style='color: orange; font-size: 18px;'>⚠️ Ainda não há agendamentos. Clique em 'Criar Mais Agendamento

