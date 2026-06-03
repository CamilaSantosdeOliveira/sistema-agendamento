<?php
echo "<h2>🔍 Verificação de Aulas Agendadas</h2>";

// Conectar ao banco
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
    exit;
}

// Verificar total de agendamentos
echo "<h3>1. Total de agendamentos no banco:</h3>";
try {
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "📊 Total de agendamentos: " . $row['total'] . "<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}

// Verificar agendamentos futuros
echo "<h3>2. Agendamentos futuros (data >= hoje):</h3>";
try {
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "📊 Agendamentos futuros: " . $row['total'] . "<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}

// Listar todos os agendamentos
echo "<h3>3. Todos os agendamentos:</h3>";
try {
    $result = $conn->query("
        SELECT 
            a.id,
            a.data,
            a.hora,
            a.status,
            u.nome as aluno_nome,
            p.nome as professor_nome,
            c.nome as curso_nome
        FROM agendamentos a
        LEFT JOIN usuarios u ON a.aluno_id = u.id
        LEFT JOIN usuarios p ON a.professor_id = p.id
        LEFT JOIN cursos c ON a.curso_id = c.id
        ORDER BY a.data, a.hora
    ");
    
    if ($result) {
        echo "📋 Lista de agendamentos:<br>";
        while ($row = $result->fetch_assoc()) {
            $data = date('d/m/Y', strtotime($row['data']));
            $hora = $row['hora'];
            $status = $row['status'];
            $aluno = $row['aluno_nome'] ?: 'Não informado';
            $professor = $row['professor_nome'] ?: 'Não informado';
            $curso = $row['curso_nome'] ?: 'Não informado';
            
            echo "- ID: {$row['id']} | Data: {$data} {$hora} | Status: {$status} | Aluno: {$aluno} | Professor: {$professor} | Curso: {$curso}<br>";
        }
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}

// Verificar o que o dashboard está contando
echo "<h3>4. O que o dashboard está contando:</h3>";
try {
    // Simular a query do dashboard
    $result = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data >= CURDATE()");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "📊 Dashboard mostra: " . $row['total'] . " aulas<br>";
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>";
}

echo "<br><a href='dashboard_corrigido.php'>Voltar ao Dashboard</a>";
?>


