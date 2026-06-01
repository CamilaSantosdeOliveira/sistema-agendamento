<?php
include 'db.php';

echo "=== VERIFICAÇÃO COMPLETA DO BANCO DE DADOS ===\n\n";

// Verificar se o banco existe
echo "=== CONEXÃO ===\n";
if ($conn->ping()) {
    echo "✅ Conexão com banco OK\n";
} else {
    echo "❌ Erro na conexão\n";
}

echo "Banco atual: " . $conn->database . "\n\n";

// Verificar todas as tabelas
echo "=== TABELAS EXISTENTES ===\n";
$tables_query = "SHOW TABLES";
$result = $conn->query($tables_query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        echo "✅ " . $row[0] . "\n";
    }
} else {
    echo "❌ Nenhuma tabela encontrada\n";
}

echo "\n=== ESTRUTURA DAS TABELAS ===\n";

// Verificar estrutura da tabela professores
echo "\n--- TABELA PROFESSORES ---\n";
$check = $conn->query("SHOW TABLES LIKE 'professores'");
if ($check && $check->num_rows > 0) {
    echo "✅ Tabela existe\n";
    
    // Verificar estrutura
    $structure = $conn->query("DESCRIBE professores");
    if ($structure) {
        while ($row = $structure->fetch_assoc()) {
            echo "  Campo: " . $row['Field'] . " | Tipo: " . $row['Type'] . " | Null: " . $row['Null'] . " | Key: " . $row['Key'] . " | Default: " . $row['Default'] . "\n";
        }
    }
    
    // Verificar dados
    $count = $conn->query("SELECT COUNT(*) as total FROM professores");
    if ($count) {
        $total = $count->fetch_assoc()['total'];
        echo "  Total de registros: " . $total . "\n";
        
        if ($total > 0) {
            $data = $conn->query("SELECT * FROM professores LIMIT 3");
            while ($row = $data->fetch_assoc()) {
                echo "  ID: " . $row['id'] . " | Nome: " . $row['nome'] . " | Status: " . $row['status'] . "\n";
            }
        }
    }
} else {
    echo "❌ Tabela não existe\n";
}

// Verificar estrutura da tabela alunos
echo "\n--- TABELA ALUNOS ---\n";
$check = $conn->query("SHOW TABLES LIKE 'alunos'");
if ($check && $check->num_rows > 0) {
    echo "✅ Tabela existe\n";
    
    // Verificar estrutura
    $structure = $conn->query("DESCRIBE alunos");
    if ($structure) {
        while ($row = $structure->fetch_assoc()) {
            echo "  Campo: " . $row['Field'] . " | Tipo: " . $row['Type'] . " | Null: " . $row['Null'] . " | Key: " . $row['Key'] . " | Default: " . $row['Default'] . "\n";
        }
    }
    
    // Verificar dados
    $count = $conn->query("SELECT COUNT(*) as total FROM alunos");
    if ($count) {
        $total = $count->fetch_assoc()['total'];
        echo "  Total de registros: " . $total . "\n";
        
        if ($total > 0) {
            $data = $conn->query("SELECT * FROM alunos LIMIT 3");
            while ($row = $data->fetch_assoc()) {
                echo "  ID: " . $row['id'] . " | Nome: " . $row['nome'] . " | Status: " . $row['status'] . "\n";
            }
        }
    }
} else {
    echo "❌ Tabela não existe\n";
}

// Verificar estrutura da tabela cursos
echo "\n--- TABELA CURSOS ---\n";
$check = $conn->query("SHOW TABLES LIKE 'cursos'");
if ($check && $check->num_rows > 0) {
    echo "✅ Tabela existe\n";
    
    // Verificar dados
    $count = $conn->query("SELECT COUNT(*) as total FROM cursos");
    if ($count) {
        $total = $count->fetch_assoc()['total'];
        echo "  Total de registros: " . $total . "\n";
        
        if ($total > 0) {
            $data = $conn->query("SELECT * FROM cursos LIMIT 3");
            while ($row = $data->fetch_assoc()) {
                echo "  ID: " . $row['id'] . " | Nome: " . $row['nome'] . " | Status: " . $row['status'] . "\n";
            }
        }
    }
} else {
    echo "❌ Tabela não existe\n";
}

// Verificar estrutura da tabela agendamentos
echo "\n--- TABELA AGENDAMENTOS ---\n";
$check = $conn->query("SHOW TABLES LIKE 'agendamentos'");
if ($check && $check->num_rows > 0) {
    echo "✅ Tabela existe\n";
    
    // Verificar dados
    $count = $conn->query("SELECT COUNT(*) as total FROM agendamentos");
    if ($count) {
        $total = $count->fetch_assoc()['total'];
        echo "  Total de registros: " . $total . "\n";
    }
} else {
    echo "❌ Tabela não existe\n";
}

echo "\n=== TESTE DAS CONSULTAS DO DASHBOARD ===\n";

// Testar as consultas exatas do dashboard
echo "\n--- CONSULTA CURSOS ---\n";
$cursos_query = "SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'";
$cursos_result = $conn->query($cursos_query);
if ($cursos_result) {
    $cursos_count = $cursos_result->fetch_assoc()['count'];
    echo "Cursos ativos: " . $cursos_count . "\n";
} else {
    echo "❌ Erro na consulta: " . $conn->error . "\n";
}

echo "\n--- CONSULTA PROFESSORES ---\n";
$professores_query = "SELECT COUNT(*) as count FROM professores WHERE status = 'ativo'";
$professores_result = $conn->query($professores_query);
if ($professores_result) {
    $professores_count = $professores_result->fetch_assoc()['count'];
    echo "Professores ativos: " . $professores_count . "\n";
} else {
    echo "❌ Erro na consulta: " . $conn->error . "\n";
}

echo "\n--- CONSULTA ALUNOS ---\n";
$alunos_query = "SELECT COUNT(*) as count FROM alunos WHERE status = 'ativo'";
$alunos_result = $conn->query($alunos_query);
if ($alunos_result) {
    $alunos_count = $alunos_result->fetch_assoc()['count'];
    echo "Alunos ativos: " . $alunos_count . "\n";
} else {
    echo "❌ Erro na consulta: " . $conn->error . "\n";
}

echo "\n--- CONSULTA AGENDAMENTOS ---\n";
$agendamentos_query = "SELECT COUNT(*) as count FROM agendamentos WHERE status != 'cancelado'";
$agendamentos_result = $conn->query($agendamentos_query);
if ($agendamentos_result) {
    $agendamentos_count = $agendamentos_result->fetch_assoc()['count'];
    echo "Agendamentos ativos: " . $agendamentos_count . "\n";
} else {
    echo "❌ Erro na consulta: " . $conn->error . "\n";
}
?>


