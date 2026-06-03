<?php
// Configuração final dos certificados
include 'db.php';

echo "<h1>🎉 CONFIGURAÇÃO FINAL DOS CERTIFICADOS</h1>";

// 1. Verificar se as tabelas existem
echo "<h2>🔍 Verificando tabelas:</h2>";
$tables = ['certificados', 'cursos'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ Tabela '$table' existe</p>";
    } else {
        echo "<p>❌ Tabela '$table' NÃO existe</p>";
    }
}

// 2. Inserir cursos se não existirem
echo "<h2>📚 Configurando cursos:</h2>";
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
            echo "<p>✅ Curso '" . $curso['nome'] . "' inserido</p>";
        } else {
            echo "<p>❌ Erro ao inserir curso: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>ℹ️ Curso '" . $curso['nome'] . "' já existe</p>";
    }
}

// 3. Gerar certificados para alunos
echo "<h2>🎓 Gerando certificados para alunos:</h2>";
$alunos_sql = "SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1";
$alunos_result = $conn->query($alunos_sql);

if ($alunos_result && $alunos_result->num_rows > 0) {
    while ($aluno = $alunos_result->fetch_assoc()) {
        // Verificar se já existe certificado
        $check_cert_sql = "SELECT id FROM certificados WHERE aluno_id = " . $aluno['id'];
        $cert_result = $conn->query($check_cert_sql);
        
        if ($cert_result->num_rows == 0) {
            // Buscar curso aleatório
            $curso_sql = "SELECT id, nome FROM cursos WHERE ativo = 1 ORDER BY RAND() LIMIT 1";
            $curso_result = $conn->query($curso_sql);
            
            if ($curso_result && $curso_result->num_rows > 0) {
                $curso = $curso_result->fetch_assoc();
                
                // Gerar código único
                $codigo = 'CERT-' . strtoupper(substr(md5($aluno['id'] . time()), 0, 8));
                
                $insert_cert_sql = "INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, status, data_conclusao) VALUES (
                    " . $aluno['id'] . ",
                    " . $curso['id'] . ",
                    '" . $codigo . "',
                    'pendente',
                    DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND() * 30) DAY)
                )";
                
                if ($conn->query($insert_cert_sql) === TRUE) {
                    echo "<p>✅ Certificado para " . $aluno['nome'] . " - " . $curso['nome'] . "</p>";
                } else {
                    echo "<p>❌ Erro: " . $conn->error . "</p>";
                }
            }
        } else {
            echo "<p>ℹ️ " . $aluno['nome'] . " já tem certificado</p>";
        }
    }
} else {
    echo "<p>⚠️ Nenhum aluno encontrado</p>";
}

// 4. Mostrar estatísticas finais
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

// 5. Testar consulta dos certificados
echo "<h2>🧪 Testando consulta de certificados:</h2>";
$test_sql = "
SELECT 
    c.id,
    c.codigo_verificacao,
    c.status,
    c.data_emissao,
    c.data_conclusao,
    c.carga_horaria,
    u.nome as aluno_nome,
    u.email as aluno_email,
    u.criado_em as aluno_cadastro,
    cur.nome as curso_nome,
    cur.descricao as curso_descricao
FROM certificados c
INNER JOIN usuarios u ON c.aluno_id = u.id
INNER JOIN cursos cur ON c.curso_id = cur.id
WHERE u.tipo_usuario = 'aluno' AND u.ativo = 1
ORDER BY c.data_emissao DESC
LIMIT 5
";

$test_result = $conn->query($test_sql);
if ($test_result && $test_result->num_rows > 0) {
    echo "<p>✅ Consulta funcionando! Encontrados " . $test_result->num_rows . " certificados</p>";
    while ($cert = $test_result->fetch_assoc()) {
        echo "<p>🎓 " . $cert['aluno_nome'] . " - " . $cert['curso_nome'] . " (" . $cert['status'] . ")</p>";
    }
} else {
    echo "<p>❌ Consulta não retornou resultados</p>";
}

echo "<h2>🎉 CONFIGURAÇÃO CONCLUÍDA!</h2>";
echo "<p><strong>Agora você pode acessar:</strong></p>";
echo "<p><a href='certificados.php' target='_blank'>🎓 certificados.php - Ver certificados com dados reais</a></p>";
echo "<p><a href='http://localhost:8080/certificados.php' target='_blank'>🌐 http://localhost:8080/certificados.php</a></p>";

$conn->close();
?>


















