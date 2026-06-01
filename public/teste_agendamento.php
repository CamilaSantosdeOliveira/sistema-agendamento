<?php
include 'db.php';

// Verificar se a tabela agendamentos existe
$check_table = "SHOW TABLES LIKE 'agendamentos'";
$result = $conn->query($check_table);

if ($result->num_rows == 0) {
    echo "<h2>❌ Tabela 'agendamentos' não encontrada!</h2>";
    echo "<p>Vamos criar a tabela...</p>";
    
    // Criar tabela agendamentos
    $create_table = "CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        curso_id INT NOT NULL,
        data DATE NOT NULL,
        horario TIME NOT NULL,
        professor_id INT,
        titulo VARCHAR(255),
        descricao TEXT,
        tipo_evento VARCHAR(50),
        link_reuniao VARCHAR(500),
        duracao INT DEFAULT 90,
        capacidade INT DEFAULT 100,
        status ENUM('pendente', 'confirmado', 'cancelado') DEFAULT 'pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_table) === TRUE) {
        echo "<p>✅ Tabela 'agendamentos' criada com sucesso!</p>";
    } else {
        echo "<p>❌ Erro ao criar tabela: " . $conn->error . "</p>";
    }
} else {
    echo "<h2>✅ Tabela 'agendamentos' encontrada!</h2>";
}

// Verificar se a tabela cursos existe
$check_cursos = "SHOW TABLES LIKE 'cursos'";
$result_cursos = $conn->query($check_cursos);

if ($result_cursos->num_rows == 0) {
    echo "<p>❌ Tabela 'cursos' não encontrada!</p>";
    echo "<p>Vamos criar a tabela...</p>";
    
    // Criar tabela cursos
    $create_cursos = "CREATE TABLE IF NOT EXISTS cursos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        duracao_horas INT,
        status ENUM('ativo', 'inativo') DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_cursos) === TRUE) {
        echo "<p>✅ Tabela 'cursos' criada com sucesso!</p>";
        
        // Inserir alguns cursos de exemplo
        $insert_cursos = "INSERT INTO cursos (nome, descricao, duracao_horas) VALUES 
        ('Python para Data Science', 'Curso completo de Python focado em análise de dados', 120),
        ('React.js Avançado', 'Desenvolvimento de aplicações web modernas com React', 80),
        ('SQL e Banco de Dados', 'Administração e consultas avançadas em bancos de dados', 60)";
        
        if ($conn->query($insert_cursos) === TRUE) {
            echo "<p>✅ Cursos de exemplo inseridos com sucesso!</p>";
        } else {
            echo "<p>❌ Erro ao inserir cursos: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>❌ Erro ao criar tabela cursos: " . $conn->error . "</p>";
    }
} else {
    echo "<h2>✅ Tabela 'cursos' encontrada!</h2>";
}

// Verificar se a tabela professores existe
$check_professores = "SHOW TABLES LIKE 'professores'";
$result_professores = $conn->query($check_professores);

if ($result_professores->num_rows == 0) {
    echo "<p>❌ Tabela 'professores' não encontrada!</p>";
    echo "<p>Vamos criar a tabela...</p>";
    
    // Criar tabela professores
    $create_professores = "CREATE TABLE IF NOT EXISTS professores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        especialidade VARCHAR(255),
        status ENUM('ativo', 'inativo') DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_professores) === TRUE) {
        echo "<p>✅ Tabela 'professores' criada com sucesso!</p>";
        
        // Inserir alguns professores de exemplo
        $insert_professores = "INSERT INTO professores (nome, email, especialidade) VALUES 
        ('João Silva', 'joao.silva@educonnect.com', 'Data Science & Machine Learning'),
        ('Maria Santos', 'maria.santos@educonnect.com', 'Frontend Development'),
        ('Pedro Costa', 'pedro.costa@educonnect.com', 'Database & Backend')";
        
        if ($conn->query($insert_professores) === TRUE) {
            echo "<p>✅ Professores de exemplo inseridos com sucesso!</p>";
        } else {
            echo "<p>❌ Erro ao inserir professores: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>❌ Erro ao criar tabela professores: " . $conn->error . "</p>";
    }
} else {
    echo "<h2>✅ Tabela 'professores' encontrada!</h2>";
}

// Testar inserção de agendamento
echo "<h2>🧪 Testando inserção de agendamento...</h2>";

$test_curso_id = 1;
$test_data = date('Y-m-d', strtotime('+1 day'));
$test_horario = '14:00:00';

$test_sql = "INSERT INTO agendamentos (curso_id, data, horario) VALUES (?, ?, ?)";
$test_stmt = $conn->prepare($test_sql);
$test_stmt->bind_param("iss", $test_curso_id, $test_data, $test_horario);

if ($test_stmt->execute()) {
    echo "<p>✅ Teste de inserção de agendamento realizado com sucesso!</p>";
    echo "<p>Agendamento criado para amanhã às 14:00</p>";
} else {
    echo "<p>❌ Erro no teste de inserção: " . $test_stmt->error . "</p>";
}

$test_stmt->close();

// Mostrar agendamentos existentes
echo "<h2>📅 Agendamentos existentes:</h2>";
$select_agendamentos = "SELECT a.*, c.nome as curso_nome FROM agendamentos a 
                        JOIN cursos c ON a.curso_id = c.id 
                        ORDER BY a.data, a.horario";
$result_agendamentos = $conn->query($select_agendamentos);

if ($result_agendamentos->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Curso</th><th>Data</th><th>Horário</th><th>Status</th></tr>";
    
    while($row = $result_agendamentos->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['curso_nome'] . "</td>";
        echo "<td>" . $row['data'] . "</td>";
        echo "<td>" . $row['horario'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum agendamento encontrado.</p>";
}

echo "<hr>";
echo "<h2>🔗 Links úteis:</h2>";
echo "<p><a href='dashboard.html'>← Voltar ao Dashboard</a></p>";
echo "<p><a href='agendamentos-eventos.html'>📅 Ir para Agendamentos</a></p>";

$conn->close();
?>
