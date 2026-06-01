<?php
// Verificar e corrigir tabelas
include 'db.php';

echo "<h1>🔍 VERIFICAÇÃO E CORREÇÃO DAS TABELAS</h1>";

// Verificar estrutura da tabela cursos
echo "<h2>📚 Verificando tabela cursos:</h2>";
$result = $conn->query("DESCRIBE cursos");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar se a coluna carga_horaria existe
$check_column = $conn->query("SHOW COLUMNS FROM cursos LIKE 'carga_horaria'");
if ($check_column && $check_column->num_rows == 0) {
    echo "<p>❌ Coluna 'carga_horaria' não existe na tabela cursos</p>";
    echo "<p>🔧 Adicionando coluna 'carga_horaria'...</p>";
    
    $add_column = $conn->query("ALTER TABLE cursos ADD COLUMN carga_horaria INT DEFAULT 40 AFTER descricao");
    if ($add_column) {
        echo "<p>✅ Coluna 'carga_horaria' adicionada com sucesso!</p>";
    } else {
        echo "<p>❌ Erro ao adicionar coluna: " . $conn->error . "</p>";
    }
} else {
    echo "<p>✅ Coluna 'carga_horaria' já existe</p>";
}

// Inserir cursos se não existirem
echo "<h2>📚 Inserindo cursos padrão:</h2>";
$cursos_padrao = [
    ['nome' => 'Curso Básico de Programação', 'descricao' => 'Fundamentos de programação e lógica', 'carga_horaria' => 40],
    ['nome' => 'Desenvolvimento Web', 'descricao' => 'HTML, CSS e JavaScript', 'carga_horaria' => 60],
    ['nome' => 'Banco de Dados', 'descricao' => 'MySQL e SQL', 'carga_horaria' => 50],
    ['nome' => 'PHP Avançado', 'descricao' => 'Programação backend com PHP', 'carga_horaria' => 80]
];

foreach ($cursos_padrao as $curso) {
    $check_sql = "SELECT id FROM cursos WHERE nome = '" . $conn->real_escape_string($curso['nome']) . "'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows == 0) {
        $insert_sql = "INSERT INTO cursos (nome, descricao, carga_horaria) VALUES (
            '" . $conn->real_escape_string($curso['nome']) . "',
            '" . $conn->real_escape_string($curso['descricao']) . "',
            " . $curso['carga_horaria'] . "
        )";
        
        if ($conn->query($insert_sql) === TRUE) {
            echo "<p>✅ Curso '" . $curso['nome'] . "' inserido com sucesso!</p>";
        } else {
            echo "<p>❌ Erro ao inserir curso: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>ℹ️ Curso '" . $curso['nome'] . "' já existe</p>";
    }
}

// Verificar estrutura da tabela certificados
echo "<h2>🎓 Verificando tabela certificados:</h2>";
$result = $conn->query("DESCRIBE certificados");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Gerar certificados para alunos existentes
echo "<h2>🎓 Gerando certificados para alunos:</h2>";
$alunos_sql = "SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1";
$alunos_result = $conn->query($alunos_sql);

if ($alunos_result && $alunos_result->num_rows > 0) {
    while ($aluno = $alunos_result->fetch_assoc()) {
        // Verificar se já existe certificado para este aluno
        $check_cert_sql = "SELECT id FROM certificados WHERE aluno_id = " . $aluno['id'];
        $cert_result = $conn->query($check_cert_sql);
        
        if ($cert_result->num_rows == 0) {
            // Buscar um curso aleatório
            $curso_sql = "SELECT id, nome FROM cursos WHERE ativo = 1 ORDER BY RAND() LIMIT 1";
            $curso_result = $conn->query($curso_sql);
            
            if ($curso_result && $curso_result->num_rows > 0) {
                $curso = $curso_result->fetch_assoc();
                
                // Gerar código de verificação único
                $codigo = 'CERT-' . strtoupper(substr(md5($aluno['id'] . time()), 0, 8));
                
                $insert_cert_sql = "INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, status, data_conclusao) VALUES (
                    " . $aluno['id'] . ",
                    " . $curso['id'] . ",
                    '" . $codigo . "',
                    'pendente',
                    DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND() * 30) DAY)
                )";
                
                if ($conn->query($insert_cert_sql) === TRUE) {
                    echo "<p>✅ Certificado gerado para " . $aluno['nome'] . " - Curso: " . $curso['nome'] . "</p>";
                } else {
                    echo "<p>❌ Erro ao gerar certificado para " . $aluno['nome'] . ": " . $conn->error . "</p>";
                }
            }
        } else {
            echo "<p>ℹ️ " . $aluno['nome'] . " já possui certificado</p>";
        }
    }
} else {
    echo "<p>⚠️ Nenhum aluno encontrado para gerar certificados</p>";
}

// Mostrar estatísticas finais
echo "<h2>📊 Estatísticas Finais:</h2>";
$stats_sql = "
SELECT 
    COUNT(*) as total_certificados,
    SUM(CASE WHEN status = 'emitido' THEN 1 ELSE 0 END) as emitidos,
    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN status = 'validado' THEN 1 ELSE 0 END) as validados
FROM certificados
";

$stats_result = $conn->query($stats_sql);
if ($stats_result && $stats_result->num_rows > 0) {
    $stats = $stats_result->fetch_assoc();
    echo "<p>📋 Total de Certificados: " . $stats['total_certificados'] . "</p>";
    echo "<p>✅ Emitidos: " . $stats['emitidos'] . "</p>";
    echo "<p>⏳ Pendentes: " . $stats['pendentes'] . "</p>";
    echo "<p>🎯 Validados: " . $stats['validados'] . "</p>";
}

echo "<h2>🎉 CONFIGURAÇÃO CONCLUÍDA!</h2>";
echo "<p><a href='certificados.php' target='_blank'>🎓 Clique aqui para ver os certificados com dados reais</a></p>";

$conn->close();
?>
















