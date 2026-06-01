<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h1>📅 INSERINDO AGENDAMENTOS DE EXEMPLO</h1>";
echo "<h2>✅ Criando sistema de aulas funcionais</h2>";

try {
    // Verificar se a tabela agendamentos existe
    $result = $conn->query("SHOW TABLES LIKE 'agendamentos'");
    if ($result->num_rows == 0) {
        echo "<h3>🔧 Criando tabela agendamentos...</h3>";
        
        $sql_agendamentos = "CREATE TABLE IF NOT EXISTS agendamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            professor_id INT NOT NULL,
            curso_id INT NOT NULL,
            data_aula DATE NOT NULL,
            hora_inicio TIME NOT NULL,
            hora_fim TIME NOT NULL,
            status ENUM('agendado', 'confirmado', 'cancelado', 'concluido') DEFAULT 'agendado',
            observacoes TEXT,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
        )";
        
        if ($conn->query($sql_agendamentos)) {
            echo "✅ Tabela agendamentos criada com sucesso!<br>";
        } else {
            echo "❌ Erro ao criar tabela: " . $conn->error . "<br>";
            return;
        }
    } else {
        echo "✅ Tabela agendamentos já existe<br>";
    }
    
    // Limpar agendamentos existentes
    $conn->query("DELETE FROM agendamentos");
    echo "🧹 Tabela agendamentos limpa<br><br>";
    
    // Buscar IDs de usuários e cursos
    $alunos = [];
    $professores = [];
    $cursos = [];
    
    // Buscar alunos
    $result = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $alunos[] = $row;
        }
    }
    
    // Buscar professores
    $result = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'professor' AND ativo = 1");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $professores[] = $row;
        }
    }
    
    // Buscar cursos
    $result = $conn->query("SELECT id, nome FROM cursos WHERE status = 'ativo'");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cursos[] = $row;
        }
    }
    
    echo "<h3>📊 Dados encontrados:</h3>";
    echo "👨‍🎓 Alunos: " . count($alunos) . "<br>";
    echo "👨‍🏫 Professores: " . count($professores) . "<br>";
    echo "📚 Cursos: " . count($cursos) . "<br><br>";
    
    if (empty($alunos) || empty($professores) || empty($cursos)) {
        echo "❌ É necessário ter alunos, professores e cursos para criar agendamentos<br>";
        echo "<p><a href='inserir_usuarios_teste.php'>Inserir usuários primeiro</a></p>";
        return;
    }
    
    // Criar agendamentos de exemplo
    $agendamentos = [];
    
    // Agendamentos para esta semana
    $hoje = date('Y-m-d');
    for ($i = 1; $i <= 7; $i++) {
        $data = date('Y-m-d', strtotime("+$i days"));
        $dia_semana = date('w', strtotime($data));
        
        // Não agendar aos domingos
        if ($dia_semana == 0) continue;
        
        // Criar 2-3 agendamentos por dia
        $num_agendamentos = rand(2, 3);
        
        for ($j = 0; $j < $num_agendamentos; $j++) {
            $aluno = $alunos[array_rand($alunos)];
            $professor = $professores[array_rand($professores)];
            $curso = $cursos[array_rand($cursos)];
            
            // Horários: 9h, 14h, 19h
            $horarios = ['09:00:00', '14:00:00', '19:00:00'];
            $hora_inicio = $horarios[array_rand($horarios)];
            $hora_fim = date('H:i:s', strtotime($hora_inicio) + 3600); // +1 hora
            
            $statuses = ['agendado', 'confirmado'];
            $status = $statuses[array_rand($statuses)];
            
            $agendamentos[] = [
                'aluno_id' => $aluno['id'],
                'professor_id' => $professor['id'],
                'curso_id' => $curso['id'],
                'data_aula' => $data,
                'hora_inicio' => $hora_inicio,
                'hora_fim' => $hora_fim,
                'status' => $status,
                'observacoes' => "Aula de " . $curso['nome'] . " com " . $professor['nome']
            ];
        }
    }
    
    echo "<h3>🎯 Inserindo agendamentos...</h3>";
    
    $agendamentos_inseridos = 0;
    foreach ($agendamentos as $agendamento) {
        $sql = "INSERT INTO agendamentos (aluno_id, professor_id, curso_id, data_aula, hora_inicio, hora_fim, status, observacoes) 
                VALUES (
                    {$agendamento['aluno_id']}, 
                    {$agendamento['professor_id']}, 
                    {$agendamento['curso_id']}, 
                    '{$agendamento['data_aula']}', 
                    '{$agendamento['hora_inicio']}', 
                    '{$agendamento['hora_fim']}', 
                    '{$agendamento['status']}', 
                    '{$agendamento['observacoes']}'
                )";
        
        if ($conn->query($sql)) {
            echo "✅ Agendamento para {$agendamento['data_aula']} às {$agendamento['hora_inicio']} inserido!<br>";
            $agendamentos_inseridos++;
        } else {
            echo "❌ Erro ao inserir agendamento: " . $conn->error . "<br>";
        }
    }
    
    echo "<br><h2>🎉 AGENDAMENTOS INSERIDOS COM SUCESSO!</h2>";
    echo "<p>✅ Total de agendamentos inseridos: <strong>$agendamentos_inseridos</strong></p>";
    
    // Verificar total final
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "<p>✅ Total de agendamentos na tabela: <strong>$count</strong></p>";
    }
    
    // Mostrar próximos agendamentos
    if ($count > 0) {
        echo "<h3>📋 Próximos agendamentos:</h3>";
        $proximos = $conn->query("
            SELECT 
                a.id,
                a.data_aula,
                a.hora_inicio,
                a.status,
                u1.nome as aluno,
                u2.nome as professor,
                c.nome as curso
            FROM agendamentos a
            JOIN usuarios u1 ON a.aluno_id = u1.id
            JOIN usuarios u2 ON a.professor_id = u2.id
            JOIN cursos c ON a.curso_id = c.id
            WHERE a.data_aula >= CURDATE()
            ORDER BY a.data_aula, a.hora_inicio
            LIMIT 10
        ");
        
        if ($proximos) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
            echo "<tr style='background: #f3f4f6;'>";
            echo "<th>Data</th><th>Hora</th><th>Aluno</th><th>Professor</th><th>Curso</th><th>Status</th>";
            echo "</tr>";
            
            while ($row = $proximos->fetch_assoc()) {
                $status_color = $row['status'] === 'confirmado' ? '#10b981' : '#f59e0b';
                echo "<tr>";
                echo "<td>" . date('d/m/Y', strtotime($row['data_aula'])) . "</td>";
                echo "<td>" . substr($row['hora_inicio'], 0, 5) . "</td>";
                echo "<td>{$row['aluno']}</td>";
                echo "<td>{$row['professor']}</td>";
                echo "<td>{$row['curso']}</td>";
                echo "<td style='color: $status_color; font-weight: bold;'>{$row['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<br><h3>🚀 PRÓXIMOS PASSOS:</h3>";
    echo "<ol>";
    echo "<li><a href='dashboard_completo.php' style='color: #3b82f6; text-decoration: none; font-weight: bold;'>🎯 Dashboard Completo com Agendamentos</a></li>";
    echo "<li><a href='cursos_completo.php' style='color: #10b981; text-decoration: none; font-weight: bold;'>📚 Página de Cursos Funcionando</a></li>";
    echo "<li><a href='sistema_usuarios.php' style='color: #8b5cf6; text-decoration: none; font-weight: bold;'>👥 Sistema de Usuários</a></li>";
    echo "</ol>";
    
    echo "<p style='background: #dbeafe; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6;'>";
    echo "<strong>🎯 SISTEMA DE AGENDAMENTOS FUNCIONANDO!</strong><br>";
    echo "Agora você tem agendamentos reais no banco de dados!<br>";
    echo "O dashboard mostrará estatísticas reais de aulas agendadas.";
    echo "</p>";

} catch (Exception $e) {
    echo "❌ Erro durante a inserção: " . $e->getMessage();
}
?>



































