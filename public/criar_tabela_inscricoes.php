<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

echo "<h1>📚 Criando Tabela de Inscrições</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;}</style>";

// Verificar se a tabela já existe
echo "<h2>📋 Verificando tabela existente...</h2>";
$result = $conn->query("SHOW TABLES LIKE 'inscricoes'");

if ($result && $result->num_rows > 0) {
    echo "<div class='info'>ℹ️ Tabela 'inscricoes' já existe!</div>";
} else {
    echo "<div class='info'>ℹ️ Tabela 'inscricoes' não encontrada. Criando...</div>";
    
    // Criar tabela de inscrições
    $sql = "
        CREATE TABLE inscricoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            curso_id INT NOT NULL,
            aluno_id INT NOT NULL,
            data_inicio DATE,
            observacoes TEXT,
            status ENUM('ativa', 'cancelada', 'concluida') DEFAULT 'ativa',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            UNIQUE KEY unique_inscricao (curso_id, aluno_id)
        )
    ";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabela 'inscricoes' criada com sucesso!</div>";
    } else {
        echo "<div class='error'>❌ Erro ao criar tabela: " . $conn->error . "</div>";
    }
}

// Verificar estrutura da tabela
echo "<h2>🔍 Estrutura da tabela:</h2>";
$result = $conn->query("DESCRIBE inscricoes");

if ($result) {
    echo "<table style='border-collapse:collapse;width:100%;margin-top:20px;'>";
    echo "<tr style='background:#f2f2f2;'>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Campo</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Tipo</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Null</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Key</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Default</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['Field']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['Type']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['Null']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['Key']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Inserir algumas inscrições de exemplo
echo "<h2>📝 Inserindo inscrições de exemplo...</h2>";

$inscricoes_exemplo = [
    [1, 10, '2025-09-01', 'Aluno interessado em desenvolvimento web'],
    [2, 11, '2025-09-02', 'Foco em data science'],
    [3, 13, '2025-09-03', 'Interesse em React e Node.js'],
    [4, 15, '2025-09-04', 'Design de interfaces'],
    [5, 17, '2025-09-05', 'DevOps e containerização']
];

$inseridos = 0;
$erros = 0;

foreach ($inscricoes_exemplo as $inscricao) {
    $curso_id = $inscricao[0];
    $aluno_id = $inscricao[1];
    $data_inicio = $inscricao[2];
    $observacoes = $inscricao[3];
    
    $sql = "INSERT IGNORE INTO inscricoes (curso_id, aluno_id, data_inicio, observacoes) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiss', $curso_id, $aluno_id, $data_inicio, $observacoes);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<div class='success'>✅ Inscrição criada: Curso $curso_id - Aluno $aluno_id</div>";
            $inseridos++;
        } else {
            echo "<div class='info'>ℹ️ Inscrição já existe: Curso $curso_id - Aluno $aluno_id</div>";
        }
    } else {
        echo "<div class='error'>❌ Erro ao criar inscrição: " . $stmt->error . "</div>";
        $erros++;
    }
}

// Verificar inscrições criadas
echo "<h2>📊 Inscrições no sistema:</h2>";
$result = $conn->query("
    SELECT 
        i.id,
        c.nome as curso_nome,
        u.nome as aluno_nome,
        i.data_inicio,
        i.status,
        i.criado_em
    FROM inscricoes i
    JOIN cursos c ON i.curso_id = c.id
    JOIN usuarios u ON i.aluno_id = u.id
    ORDER BY i.criado_em DESC
");

if ($result && $result->num_rows > 0) {
    echo "<table style='border-collapse:collapse;width:100%;margin-top:20px;'>";
    echo "<tr style='background:#f2f2f2;'>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>ID</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Curso</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Aluno</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Data Início</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Status</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Criado em</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['id']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['curso_nome']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['aluno_nome']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>" . date('d/m/Y', strtotime($row['data_inicio'])) . "</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$row['status']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>" . date('d/m/Y H:i', strtotime($row['criado_em'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='info'>ℹ️ Nenhuma inscrição encontrada</div>";
}

echo "<h2>📊 Resumo:</h2>";
echo "<div class='success'>✅ Inscrições inseridas: $inseridos</div>";
if ($erros > 0) {
    echo "<div class='error'>❌ Erros: $erros</div>";
}

echo "<h2>🎯 PRÓXIMOS PASSOS:</h2>";
echo "<div style='margin:20px 0;'>";
echo "<a href='alunos.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>👨‍🎓 Testar Inscrições</a>";
echo "<a href='dashboard_final.php' style='background:blue;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>📊 Dashboard</a>";
echo "</div>";

$conn->close();
?>







