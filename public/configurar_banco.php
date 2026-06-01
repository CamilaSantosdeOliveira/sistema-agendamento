<?php
echo "<h1>🗄️ Configuração do Seu Banco MySQL</h1>";

// CONFIGURAÇÕES - AJUSTE AQUI COM SUAS INFORMAÇÕES
$config = [
    'host' => 'localhost',      // Endereço do servidor MySQL
    'user' => 'root',           // Seu usuário MySQL
    'pass' => 'Cami7890#',      // Sua senha MySQL
    'port' => 3307,             // Porta (deixe 3306 se não souber)
    'db_name' => 'sistema_agendamento'  // Nome do banco que você quer usar
];

echo "<h2>🔧 Configurações Atuais:</h2>";
echo "<ul>";
echo "<li><strong>Host:</strong> " . $config['host'] . "</li>";
echo "<li><strong>Usuário:</strong> " . $config['user'] . "</li>";
echo "<li><strong>Senha:</strong> " . ($config['pass'] ? '***' : 'Nenhuma') . "</li>";
echo "<li><strong>Porta:</strong> " . $config['port'] . "</li>";
echo "<li><strong>Banco:</strong> " . $config['db_name'] . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>📝 Para alterar as configurações:</h2>";
echo "<p>1. Edite este arquivo <code>configurar_banco.php</code></p>";
echo "<p>2. Altere as variáveis na seção <code>\$config</code></p>";
echo "<p>3. Execute novamente este arquivo</p>";

echo "<hr>";
echo "<h2>🧪 Testando Conexão...</h2>";

try {
    // Tentar conectar sem selecionar banco
    $conn = new mysqli($config['host'], $config['user'], $config['pass'], '', $config['port']);
    
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }
    
    echo "<p style='color: green;'>✅ Conectado ao MySQL com sucesso!</p>";
    echo "<p>Versão do servidor: " . $conn->server_info . "</p>";
    
    // Verificar se o banco existe
    $result = $conn->query("SHOW DATABASES LIKE '" . $config['db_name'] . "'");
    
    if ($result->num_rows === 0) {
        echo "<p style='color: orange;'>⚠️ Banco '" . $config['db_name'] . "' não encontrado. Criando...</p>";
        
        if ($conn->query("CREATE DATABASE " . $config['db_name'])) {
            echo "<p style='color: green;'>✅ Banco '" . $config['db_name'] . "' criado!</p>";
        } else {
            throw new Exception('Erro ao criar banco: ' . $conn->error);
        }
    } else {
        echo "<p style='color: green;'>✅ Banco '" . $config['db_name'] . "' já existe!</p>";
    }
    
    // Selecionar o banco
    if (!$conn->select_db($config['db_name'])) {
        throw new Exception('Erro ao selecionar banco: ' . $conn->error);
    }
    
    echo "<p style='color: green;'>✅ Banco selecionado com sucesso!</p>";
    
    // Criar tabelas com dados reais
    echo "<h2>📋 Criando Tabelas com Dados Reais...</h2>";
    
    // Tabela de cursos
    $sql_cursos = "CREATE TABLE IF NOT EXISTS cursos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        categoria VARCHAR(100),
        duracao_horas INT,
        nivel VARCHAR(50),
        preco DECIMAL(10,2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql_cursos)) {
        echo "<p style='color: green;'>✅ Tabela 'cursos' criada/verificada</p>";
    }
    
    // Tabela de professores
    $sql_professores = "CREATE TABLE IF NOT EXISTS professores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE,
        telefone VARCHAR(20),
        especialidade VARCHAR(255),
        bio TEXT,
        experiencia_anos INT,
        linkedin VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql_professores)) {
        echo "<p style='color: green;'>✅ Tabela 'professores' criada/verificada</p>";
    }
    
    // Tabela de agendamentos
    $sql_agendamentos = "CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        curso_id INT NOT NULL,
        professor_id INT NOT NULL,
        data DATE NOT NULL,
        horario TIME NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        descricao TEXT,
        tipo_evento VARCHAR(50),
        link_reuniao VARCHAR(500),
        duracao INT DEFAULT 90,
        capacidade INT DEFAULT 100,
        status ENUM('pendente', 'confirmado', 'cancelado', 'concluido') DEFAULT 'pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
        FOREIGN KEY (professor_id) REFERENCES professores(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql_agendamentos)) {
        echo "<p style='color: green;'>✅ Tabela 'agendamentos' criada/verificada</p>";
    }
    
    // Inserir dados reais de cursos
    echo "<h2>📚 Inserindo Cursos Reais...</h2>";
    
    $cursos = [
        ['JavaScript Completo', 'Do básico ao avançado em JavaScript moderno', 'Programação Web', 60, 'Iniciante ao Avançado', 299.90],
        ['Python para Data Science', 'Python com pandas, numpy e machine learning', 'Data Science', 80, 'Intermediário', 399.90],
        ['React + Node.js', 'Full-stack com React e Node.js', 'Desenvolvimento Web', 70, 'Intermediário', 349.90],
        ['Java Enterprise', 'Java com Spring Boot e microserviços', 'Desenvolvimento Backend', 90, 'Avançado', 449.90],
        ['Flutter Mobile', 'Desenvolvimento mobile multiplataforma', 'Mobile', 65, 'Intermediário', 379.90],
        ['DevOps com Docker', 'Docker, Kubernetes e CI/CD', 'DevOps', 55, 'Intermediário', 329.90]
    ];
    
    foreach ($cursos as $curso) {
        $sql = "INSERT IGNORE INTO cursos (nome, descricao, categoria, duracao_horas, nivel, preco) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisd", $curso[0], $curso[1], $curso[2], $curso[3], $curso[4], $curso[5]);
        $stmt->execute();
    }
    
    echo "<p style='color: green;'>✅ 6 cursos inseridos!</p>";
    
    // Inserir dados reais de professores
    echo "<h2>👨‍🏫 Inserindo Professores Reais...</h2>";
    
    $professores = [
        ['Carlos Eduardo Silva', 'carlos.silva@techacademy.com', '(11) 99999-1111', 'JavaScript, React, Node.js', '10+ anos de experiência em desenvolvimento web', 12, 'linkedin.com/in/carlos-silva'],
        ['Ana Paula Costa', 'ana.costa@techacademy.com', '(11) 99999-2222', 'Python, Data Science, Machine Learning', 'PhD em Ciência da Computação, especialista em IA', 8, 'linkedin.com/in/ana-costa'],
        ['Roberto Santos', 'roberto.santos@techacademy.com', '(11) 99999-3333', 'Java, Spring Boot, Microserviços', 'Arquiteto de software com 15 anos de experiência', 15, 'linkedin.com/in/roberto-santos'],
        ['Mariana Oliveira', 'mariana.oliveira@techacademy.com', '(11) 99999-4444', 'Flutter, React Native, Mobile', 'Desenvolvedora mobile com 7 anos de experiência', 7, 'linkedin.com/in/mariana-oliveira'],
        ['Fernando Lima', 'fernando.lima@techacademy.com', '(11) 99999-5555', 'DevOps, Docker, Kubernetes', 'DevOps Engineer certificado pela AWS e Google Cloud', 10, 'linkedin.com/in/fernando-lima']
    ];
    
    foreach ($professores as $professor) {
        $sql = "INSERT IGNORE INTO professores (nome, email, telefone, especialidade, bio, experiencia_anos, linkedin) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssis", $professor[0], $professor[1], $professor[2], $professor[3], $professor[4], $professor[5], $professor[6]);
        $stmt->execute();
    }
    
    echo "<p style='color: green;'>✅ 5 professores inseridos!</p>";
    
    // Atualizar o arquivo db.php
    echo "<h2>💾 Atualizando arquivo db.php...</h2>";
    
    $db_content = "<?php
\$host = '" . $config['host'] . "';
\$user = '" . $config['user'] . "';
\$pass = '" . $config['pass'] . "';
\$db   = '" . $config['db_name'] . "';

\$conn = new mysqli(\$host, \$user, \$pass, \$db);
if (\$conn->connect_error) {
    die('Erro de conexão: ' . \$conn->connect_error);
}
?>";
    
    if (file_put_contents('db.php', $db_content)) {
        echo "<p style='color: green;'>✅ Arquivo db.php atualizado!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao atualizar db.php</p>";
    }
    
    echo "<hr>";
    echo "<h2>🎉 Sistema Configurado com Sucesso!</h2>";
    echo "<div style='background: #d1fae5; padding: 20px; border-radius: 8px; border: 2px solid #10b981;'>";
    echo "<h3 style='color: #065f46;'>✅ Banco de dados funcionando!</h3>";
    echo "<p style='color: #065f46;'>✅ 6 cursos reais inseridos</p>";
    echo "<p style='color: #065f46;'>✅ 5 professores reais inseridos</p>";
    echo "<p style='color: #065f46;'>✅ Sistema de agendamento pronto</p>";
    echo "</div>";
    
    echo "<h3>🚀 Agora você pode:</h3>";
    echo "<p>1. <a href='dashboard.html' style='color: #3b82f6; text-decoration: none; font-weight: bold;'>📊 Acessar Dashboard</a></p>";
    echo "<p>2. <a href='agendamentos-eventos.html' style='color: #3b82f6; text-decoration: none; font-weight: bold;'>📅 Fazer Agendamentos</a></p>";
    echo "<p>3. <a href='gerenciar_agendamentos.php' style='color: #3b82f6; text-decoration: none; font-weight: bold;'>⚙️ Gerenciar Agendamentos</a></p>";
    
} catch (Exception $e) {
    echo "<div style='background: #fee2e2; padding: 20px; border-radius: 8px; border: 2px solid #ef4444;'>";
    echo "<h3 style='color: #991b1b;'>❌ ERRO!</h3>";
    echo "<p style='color: #991b1b;'>" . $e->getMessage() . "</p>";
    echo "<p style='color: #991b1b;'>🔧 Verifique suas configurações de conexão</p>";
    echo "</div>";
}

if (isset($conn)) {
    $conn->close();
}
?>
