<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h2>🗄️ Criando Tabela de Certificados</h2>";

// SQL para criar a tabela certificados
$sql_certificados = "
CREATE TABLE IF NOT EXISTS certificados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    curso_id INT NOT NULL,
    codigo_verificacao VARCHAR(50) UNIQUE NOT NULL,
    data_emissao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_conclusao DATE,
    carga_horaria INT DEFAULT 40,
    status ENUM('pendente', 'emitido', 'validado', 'revogado') DEFAULT 'pendente',
    observacoes TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
)";

// SQL para criar a tabela cursos se não existir
$sql_cursos = "
CREATE TABLE IF NOT EXISTS cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    carga_horaria INT DEFAULT 40,
    ativo BOOLEAN DEFAULT TRUE,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
)";

try {
    // Criar tabela cursos
    if ($conn->query($sql_cursos) === TRUE) {
        echo "<p>✅ Tabela 'cursos' criada com sucesso!</p>";
    } else {
        echo "<p>ℹ️ Tabela 'cursos' já existe ou erro: " . $conn->error . "</p>";
    }

    // Criar tabela certificados
    if ($conn->query($sql_certificados) === TRUE) {
        echo "<p>✅ Tabela 'certificados' criada com sucesso!</p>";
    } else {
        echo "<p>ℹ️ Tabela 'certificados' já existe ou erro: " . $conn->error . "</p>";
    }

    // Inserir cursos padrão se não existirem
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

    // Gerar certificados para alunos existentes
    $alunos_sql = "SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' AND ativo = 1";
    $alunos_result = $conn->query($alunos_sql);
    
    if ($alunos_result && $alunos_result->num_rows > 0) {
        echo "<h3>🎓 Gerando Certificados para Alunos Existentes</h3>";
        
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
    }

    // Mostrar estatísticas finais
    echo "<h3>📊 Estatísticas Finais</h3>";
    
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

    echo "<h3>🎉 Configuração Concluída!</h3>";
    echo "<p>Agora você pode acessar <a href='certificados.php'>certificados.php</a> para ver os dados reais do banco de dados.</p>";

} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

$conn->close();
?>


















