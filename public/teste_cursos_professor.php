<?php
session_start();
include 'db.php';

echo "<h1>🔍 Teste - Cursos do Professor</h1>";

// Verificar se está logado como professor
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    echo "<p style='color: red;'>❌ Não está logado como professor</p>";
    exit();
}

$professor_id = $_SESSION['usuario_id'];

echo "<h2>📊 Dados do Professor</h2>";
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$professor = $stmt->get_result()->fetch_assoc();

if ($professor) {
    echo "<p><strong>ID:</strong> {$professor['id']}</p>";
    echo "<p><strong>Nome:</strong> {$professor['nome']}</p>";
    echo "<p><strong>Email:</strong> {$professor['email']}</p>";
} else {
    echo "<p style='color: red;'>❌ Professor não encontrado</p>";
    exit();
}

echo "<h2>🎓 TODOS os Cursos no Sistema</h2>";
$stmt = $conn->prepare("SELECT * FROM cursos ORDER BY nome");
$stmt->execute();
$todos_cursos = $stmt->get_result();

echo "<p><strong>Total de cursos no sistema:</strong> {$todos_cursos->num_rows}</p>";

if ($todos_cursos->num_rows > 0) {
    echo "<h3>📚 Cursos disponíveis no sistema:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
    echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Nome</th><th>Categoria</th><th>Nível</th><th>Duração</th><th>Preço</th><th>Descrição</th></tr>";
    
    while ($curso = $todos_cursos->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$curso['id']}</td>";
        echo "<td><strong>{$curso['nome']}</strong></td>";
        echo "<td>{$curso['categoria']}</td>";
        echo "<td>{$curso['nivel']}</td>";
        echo "<td>{$curso['duracao_horas']}h</td>";
        echo "<td>R$ " . number_format($curso['preco'], 2, ',', '.') . "</td>";
        echo "<td>" . substr($curso['descricao'], 0, 50) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Nenhum curso encontrado no sistema</p>";
}

echo "<h2>📚 Verificando Agendamentos</h2>";
$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE professor_id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$agendamentos = $stmt->get_result();

echo "<p><strong>Total de agendamentos do professor:</strong> {$agendamentos->num_rows}</p>";

if ($agendamentos->num_rows > 0) {
    echo "<h3>Agendamentos encontrados:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Curso ID</th><th>Aluno ID</th><th>Data</th><th>Hora</th><th>Status</th></tr>";
    
    while ($agendamento = $agendamentos->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$agendamento['id']}</td>";
        echo "<td>{$agendamento['curso_id']}</td>";
        echo "<td>{$agendamento['aluno_id']}</td>";
        echo "<td>{$agendamento['data_agendamento']}</td>";
        echo "<td>{$agendamento['hora_inicio']}</td>";
        echo "<td>{$agendamento['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Nenhum agendamento encontrado para este professor</p>";
}

echo "<h2>🔗 Testando Query Atual (Só cursos com agendamentos)</h2>";
$cursos_professor_query = "SELECT DISTINCT c.* FROM cursos c 
                          JOIN agendamentos a ON c.id = a.curso_id 
                          WHERE a.professor_id = ? 
                          ORDER BY c.nome";
$stmt = $conn->prepare($cursos_professor_query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$cursos_professor = $stmt->get_result();

echo "<p><strong>Query atual:</strong> $cursos_professor_query</p>";
echo "<p><strong>Parâmetro professor_id:</strong> $professor_id</p>";
echo "<p><strong>Resultados encontrados:</strong> {$cursos_professor->num_rows}</p>";

echo "<h2>💡 SOLUÇÃO</h2>";
echo "<p style='color: green; font-weight: bold;'>✅ Existem {$todos_cursos->num_rows} cursos no sistema!</p>";
echo "<p>O problema é que a página 'Meus Cursos' só mostra cursos que têm agendamentos.</p>";
echo "<p><strong>Opções para corrigir:</strong></p>";
echo "<ol>";
echo "<li><strong>Mostrar TODOS os cursos disponíveis</strong> (recomendado para demonstração)</li>";
echo "<li>Criar agendamentos de teste para o professor</li>";
echo "<li>Modificar a lógica para mostrar cursos disponíveis para lecionar</li>";
echo "</ol>";

echo "<h2>🚀 Ações Rápidas</h2>";
echo "<ul>";
echo "<li><a href='criar_agendamentos_teste.php' style='color: blue; font-weight: bold;'>➕ Criar Agendamentos de Teste</a></li>";
echo "<li><a href='cursos_professor_todos.php' style='color: blue; font-weight: bold;'>📚 Ver TODOS os Cursos Disponíveis</a></li>";
echo "<li><a href='cursos_professor.php' style='color: blue;'>🔙 Voltar para Meus Cursos</a></li>";
echo "</ul>";
?>


