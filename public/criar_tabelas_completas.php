<?php
include 'db.php';

echo "<h2>🔧 Criando Tabelas do Sistema EduConnect</h2>";

// Array com todas as tabelas necessárias
$tabelas = [
    'usuarios' => "
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL,
            telefone VARCHAR(20),
            data_nascimento DATE,
            endereco TEXT,
            tipo_usuario ENUM('admin', 'professor', 'aluno') NOT NULL DEFAULT 'aluno',
            formacao VARCHAR(255),
            valor_hora DECIMAL(10,2),
            ativo TINYINT(1) DEFAULT 1,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'cursos' => "
        CREATE TABLE IF NOT EXISTS cursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            descricao TEXT,
            categoria VARCHAR(100),
            nivel ENUM('iniciante', 'intermediario', 'avancado') DEFAULT 'iniciante',
            duracao_horas INT DEFAULT 0,
            preco DECIMAL(10,2) DEFAULT 0.00,
            alunos_inscritos INT DEFAULT 0,
            avaliacao DECIMAL(3,2) DEFAULT 0.00,
            status ENUM('ativo', 'inativo') DEFAULT 'ativo',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'agendamentos' => "
        CREATE TABLE IF NOT EXISTS agendamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            professor_id INT NOT NULL,
            curso_id INT NOT NULL,
            data_aula DATE NOT NULL,
            hora_inicio TIME NOT NULL,
            duracao INT DEFAULT 60,
            status ENUM('agendado', 'confirmado', 'concluido', 'cancelado') DEFAULT 'agendado',
            observacoes TEXT,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'inscricoes' => "
        CREATE TABLE IF NOT EXISTS inscricoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            curso_id INT NOT NULL,
            data_inicio DATE NOT NULL,
            data_conclusao DATE,
            observacoes TEXT,
            status ENUM('ativa', 'concluida', 'cancelada') DEFAULT 'ativa',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'certificados' => "
        CREATE TABLE IF NOT EXISTS certificados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            curso_id INT NOT NULL,
            codigo_validacao VARCHAR(50) UNIQUE NOT NULL,
            data_emissao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_validacao TIMESTAMP NULL,
            status ENUM('emitido', 'validado', 'cancelado') DEFAULT 'emitido',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'avaliacoes' => "
        CREATE TABLE IF NOT EXISTS avaliacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            curso_id INT NOT NULL,
            nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
            comentario TEXT,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'mensagens' => "
        CREATE TABLE IF NOT EXISTS mensagens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            remetente_id INT NOT NULL,
            destinatario_id INT NOT NULL,
            assunto VARCHAR(255),
            mensagem TEXT NOT NULL,
            lida TINYINT(1) DEFAULT 0,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'notificacoes' => "
        CREATE TABLE IF NOT EXISTS notificacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            mensagem TEXT NOT NULL,
            tipo ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
            lida TINYINT(1) DEFAULT 0,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ",
    
    'configuracoes' => "
        CREATE TABLE IF NOT EXISTS configuracoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            chave VARCHAR(100) UNIQUE NOT NULL,
            valor TEXT,
            descricao TEXT,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    "
];

// Criar tabelas
$sucessos = 0;
$erros = 0;

foreach ($tabelas as $nome_tabela => $sql) {
    try {
        if ($conn->query($sql)) {
            echo "✅ Tabela <strong>{$nome_tabela}</strong> criada/verificada com sucesso!<br>";
            $sucessos++;
        } else {
            echo "❌ Erro ao criar tabela <strong>{$nome_tabela}</strong>: " . $conn->error . "<br>";
            $erros++;
        }
    } catch (Exception $e) {
        echo "❌ Erro ao criar tabela <strong>{$nome_tabela}</strong>: " . $e->getMessage() . "<br>";
        $erros++;
    }
}

// Inserir dados de exemplo
echo "<h3>📝 Inserindo Dados de Exemplo</h3>";

// Inserir usuário admin padrão
$admin_senha = password_hash('admin123', PASSWORD_DEFAULT);
$sql_admin = "INSERT IGNORE INTO usuarios (nome, email, senha, tipo_usuario, ativo) VALUES ('Administrador', 'admin@educonnect.com', '{$admin_senha}', 'admin', 1)";
if ($conn->query($sql_admin)) {
    echo "✅ Usuário admin criado com sucesso!<br>";
}

// Inserir alguns professores de exemplo
$professores = [
    ['João Silva', 'joao@educonnect.com', 'Professor de Programação', 80.00],
    ['Maria Santos', 'maria@educonnect.com', 'Professora de Design', 75.00],
    ['Pedro Costa', 'pedro@educonnect.com', 'Professor de Marketing', 70.00]
];

foreach ($professores as $prof) {
    $senha = password_hash('123456', PASSWORD_DEFAULT);
    $sql = "INSERT IGNORE INTO usuarios (nome, email, senha, tipo_usuario, formacao, valor_hora, ativo) 
            VALUES (?, ?, ?, 'professor', ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssd', $prof[0], $prof[1], $senha, $prof[2], $prof[3]);
    if ($stmt->execute()) {
        echo "✅ Professor {$prof[0]} criado com sucesso!<br>";
    }
}

// Inserir alguns alunos de exemplo
$alunos = [
    ['Ana Oliveira', 'ana@email.com'],
    ['Carlos Ferreira', 'carlos@email.com'],
    ['Lucia Rodrigues', 'lucia@email.com'],
    ['Roberto Lima', 'roberto@email.com']
];

foreach ($alunos as $aluno) {
    $senha = password_hash('123456', PASSWORD_DEFAULT);
    $sql = "INSERT IGNORE INTO usuarios (nome, email, senha, tipo_usuario, ativo) 
            VALUES (?, ?, ?, 'aluno', 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $aluno[0], $aluno[1], $senha);
    if ($stmt->execute()) {
        echo "✅ Aluno {$aluno[0]} criado com sucesso!<br>";
    }
}

// Inserir alguns cursos de exemplo
$cursos = [
    ['JavaScript Básico', 'Aprenda os fundamentos do JavaScript', 'programacao', 'iniciante', 40, 299.99],
    ['React.js Avançado', 'Desenvolvimento de aplicações com React', 'programacao', 'avancado', 60, 499.99],
    ['UI/UX Design', 'Design de interfaces e experiência do usuário', 'design', 'intermediario', 50, 399.99],
    ['Marketing Digital', 'Estratégias de marketing online', 'marketing', 'iniciante', 45, 349.99],
    ['Python para Data Science', 'Análise de dados com Python', 'tecnologia', 'intermediario', 80, 599.99]
];

foreach ($cursos as $curso) {
    $sql = "INSERT IGNORE INTO cursos (nome, descricao, categoria, nivel, duracao_horas, preco, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'ativo')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssid', $curso[0], $curso[1], $curso[2], $curso[3], $curso[4], $curso[5]);
    if ($stmt->execute()) {
        echo "✅ Curso {$curso[0]} criado com sucesso!<br>";
    }
}

// Inserir algumas inscrições de exemplo
$inscricoes = [
    [1, 1, '2024-01-15'], // Aluno 1 no curso 1
    [2, 1, '2024-01-20'], // Aluno 2 no curso 1
    [3, 2, '2024-01-25'], // Aluno 3 no curso 2
    [4, 3, '2024-02-01']  // Aluno 4 no curso 3
];

foreach ($inscricoes as $inscricao) {
    $sql = "INSERT IGNORE INTO inscricoes (aluno_id, curso_id, data_inicio, status) 
            VALUES (?, ?, ?, 'ativa')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $inscricao[0], $inscricao[1], $inscricao[2]);
    if ($stmt->execute()) {
        echo "✅ Inscrição criada com sucesso!<br>";
    }
}

// Inserir alguns agendamentos de exemplo
$agendamentos = [
    [1, 1, 1, '2024-02-15', '14:00:00', 60], // Aluno 1, Professor 1, Curso 1
    [2, 1, 1, '2024-02-16', '15:00:00', 60], // Aluno 2, Professor 1, Curso 1
    [3, 2, 2, '2024-02-17', '16:00:00', 90]  // Aluno 3, Professor 2, Curso 2
];

foreach ($agendamentos as $agendamento) {
    $sql = "INSERT IGNORE INTO agendamentos (aluno_id, professor_id, curso_id, data_aula, hora_inicio, duracao, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'agendado')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiissi', $agendamento[0], $agendamento[1], $agendamento[2], $agendamento[3], $agendamento[4], $agendamento[5]);
    if ($stmt->execute()) {
        echo "✅ Agendamento criado com sucesso!<br>";
    }
}

// Inserir configurações padrão
$configuracoes = [
    ['site_nome', 'EduConnect', 'Nome do sistema'],
    ['site_descricao', 'Sistema Educacional Profissional', 'Descrição do sistema'],
    ['email_contato', 'contato@educonnect.com', 'Email de contato'],
    ['max_alunos_por_curso', '50', 'Máximo de alunos por curso'],
    ['duracao_aula_padrao', '60', 'Duração padrão das aulas em minutos']
];

foreach ($configuracoes as $config) {
    $sql = "INSERT IGNORE INTO configuracoes (chave, valor, descricao) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $config[0], $config[1], $config[2]);
    if ($stmt->execute()) {
        echo "✅ Configuração {$config[0]} criada com sucesso!<br>";
    }
}

echo "<h3>🎉 Resumo da Instalação</h3>";
echo "✅ Tabelas criadas com sucesso: {$sucessos}<br>";
echo "❌ Erros encontrados: {$erros}<br>";

if ($erros == 0) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🎊 Sistema Instalado com Sucesso!</h4>";
    echo "<p><strong>Credenciais de Acesso:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@educonnect.com / admin123</li>";
    echo "<li><strong>Professores:</strong> joao@educonnect.com / 123456</li>";
    echo "<li><strong>Alunos:</strong> ana@email.com / 123456</li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Acessar Sistema</a></p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>⚠️ Erros na Instalação</h4>";
    echo "<p>Alguns erros foram encontrados durante a instalação. Verifique os logs acima.</p>";
    echo "</div>";
}

$conn->close();
?>

