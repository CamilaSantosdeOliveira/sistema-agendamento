<?php
session_start();
include 'db.php';

echo "<h1>👨‍🏫 Painel Administrativo - Professores</h1>";

// Buscar todos os professores
$query_professores = "SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
$result_professores = $conn->query($query_professores);

echo "<h2>📊 Resumo Geral dos Professores</h2>";

while ($professor = $result_professores->fetch_assoc()) {
    $professor_id = $professor['id'];
    
    // Contar aulas do professor
    $query_aulas = "SELECT COUNT(*) as total FROM agendamentos WHERE professor_id = ?";
    $stmt = $conn->prepare($query_aulas);
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $total_aulas = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar alunos do professor
    $query_alunos = "SELECT COUNT(DISTINCT aluno_id) as total FROM agendamentos WHERE professor_id = ?";
    $stmt = $conn->prepare($query_alunos);
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $total_alunos = $stmt->get_result()->fetch_assoc()['total'];
    
    // Contar cursos do professor
    $query_cursos = "SELECT COUNT(DISTINCT curso_id) as total FROM agendamentos WHERE professor_id = ?";
    $stmt = $conn->prepare($query_cursos);
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $total_cursos = $stmt->get_result()->fetch_assoc()['total'];
    
    echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 15px; border-radius: 8px;'>";
    echo "<h3>👨‍🏫 " . $professor['nome'] . " (ID: $professor_id)</h3>";
    echo "<p><strong>Email:</strong> " . $professor['email'] . "</p>";
    echo "<p><strong>📚 Cursos:</strong> $total_cursos</p>";
    echo "<p><strong>👥 Alunos:</strong> $total_alunos</p>";
    echo "<p><strong>📅 Aulas:</strong> $total_aulas</p>";
    
    // Mostrar aulas do professor
    if ($total_aulas > 0) {
        echo "<details style='margin-top: 10px;'>";
        echo "<summary style='cursor: pointer; color: #3b82f6;'><strong>📋 Ver Aulas</strong></summary>";
        
        $query_aulas_detalhes = "SELECT a.*, c.nome as curso_nome, u.nome as aluno_nome 
                                 FROM agendamentos a 
                                 JOIN cursos c ON a.curso_id = c.id 
                                 JOIN usuarios u ON a.aluno_id = u.id 
                                 WHERE a.professor_id = ? 
                                 ORDER BY a.data_agendamento, a.hora_inicio";
        $stmt = $conn->prepare($query_aulas_detalhes);
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();
        $aulas = $stmt->get_result();
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 10px; font-size: 12px;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>Curso</th><th>Aluno</th><th>Data</th><th>Hora</th><th>Status</th>";
        echo "</tr>";
        
        while ($aula = $aulas->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $aula['curso_nome'] . "</td>";
            echo "<td>" . $aula['aluno_nome'] . "</td>";
            echo "<td>" . $aula['data_agendamento'] . "</td>";
            echo "<td>" . $aula['hora_inicio'] . "</td>";
            echo "<td>" . $aula['status'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</details>";
    }
    
    echo "</div>";
}

// Resumo geral
echo "<h2>📈 Resumo Geral do Sistema</h2>";

$query_total = "SELECT 
                COUNT(DISTINCT a.professor_id) as total_professores,
                COUNT(DISTINCT a.aluno_id) as total_alunos,
                COUNT(DISTINCT a.curso_id) as total_cursos,
                COUNT(*) as total_aulas
                FROM agendamentos a";
$result_total = $conn->query($query_total);
$totais = $result_total->fetch_assoc();

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px;'>";
echo "<p><strong>👨‍🏫 Total de Professores:</strong> " . $totais['total_professores'] . "</p>";
echo "<p><strong>👥 Total de Alunos:</strong> " . $totais['total_alunos'] . "</p>";
echo "<p><strong>📚 Total de Cursos:</strong> " . $totais['total_cursos'] . "</p>";
echo "<p><strong>📅 Total de Aulas:</strong> " . $totais['total_aulas'] . "</p>";
echo "</div>";

echo "<h3>🔗 Links:</h3>";
echo "<p><a href='dashboard_final.php' target='_blank'>Dashboard Admin</a></p>";
echo "<p><a href='dashboard_professor.php' target='_blank'>Dashboard Professor</a></p>";
echo "<p><a href='dashboard_aluno.php' target='_blank'>Dashboard Aluno</a></p>";
?>






