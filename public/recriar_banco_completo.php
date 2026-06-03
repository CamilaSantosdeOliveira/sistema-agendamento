<?php
// Script para recriar completamente o banco de dados
echo "<h1>🔧 RECRIANDO BANCO DE DADOS COMPLETO</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .warning { color: #f59e0b; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    .btn:hover { background: #2563eb; }
    .btn-danger { background: #ef4444; }
    .btn-success { background: #10b981; }
</style>";

try {
    // Conectar sem especificar banco
    $conn = new mysqli('localhost', 'root', '');
    
    if ($conn->connect_error) {
        throw new Exception("❌ Erro ao conectar ao MySQL: " . $conn->connect_error);
    }
    
    echo "<div class='section'>";
    echo "<h2>✅ Conexão com MySQL estabelecida</h2>";
    echo "<p class='success'>Conectado ao MySQL com sucesso!</p>";
    echo "</div>";
    
    // 1. DELETAR BANCO SE EXISTIR
    echo "<div class='section'>";
    echo "<h2>🗑️ Removendo banco corrompido</h2>";
    
    $sql = "DROP DATABASE IF EXISTS sistema_agendamento";
    if ($conn->query($sql)) {
        echo "<p class='success'>✅ Banco 'sistema_agendamento' removido</p>";
    } else {
        echo "<p class='warning'>⚠️ Erro ao remover banco: " . $conn->error . "</p>";
    }
    echo "</div>";
    
    // 2. CRIAR NOVO BANCO
    echo "<div class='section'>";
    echo "<h2>🏗️ Criando novo banco de dados</h2>";
    
    $sql = "CREATE DATABASE sistema_agendamento CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "<p class='success'>✅ Banco 'sistema_agendamento' criado</p>";
    } else {
        throw new Exception("❌ Erro ao criar banco: " . $conn->error);
    }
    echo "</div>";
    
    // 3. SELECIONAR O BANCO
    $conn->select_db('sistema_agendamento');
    echo "<div class='section'>";
    echo "<h2>📁 Banco selecionado</h2>";
    echo "<p class='success'>✅ Usando banco 'sistema_agendamento'</p>";
    echo "</div>";
    
    // 4. CRIAR TABELA USUARIOS
    echo "<div class='section'>";
    echo "<h2>👥 Criando tabela de usuários</h2>";
    
    $sql = "CREATE TABLE usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        tipo_usuario ENUM('admin', 'professor', 'aluno') NOT NULL,
        formacao VARCHAR(255),
        valor_hora DECIMAL(10,2),
        ativo BOOLEAN DEFAULT TRUE,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "<p class='success'>✅ Tabela 'usuarios' criada</p>";
    } else {
        throw new Exception("❌ Erro ao criar tabela usuarios: " . $conn->error);
    }
    echo "</div>";
    
    // 5. CRIAR TABELA CURSOS
    echo "<div class='section'>";
    echo "<h2>📚 Criando tabela de cursos</h2>";
    
    $sql = "CREATE TABLE cursos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        categoria VARCHAR(100),
        nivel ENUM('Básico', 'Intermediário', 'Avançado'),
        duracao_horas INT,
        preco DECIMAL(10,2),
        descricao TEXT,
        status ENUM('ativo', 'inativo') DEFAULT 'ativo',
        alunos_inscritos INT DEFAULT 0,
        avaliacao DECIMAL(3,2) DEFAULT 0.00,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "<p class='success'>✅ Tabela 'cursos' criada</p>";
    } else {
        throw new Exception("❌ Erro ao criar tabela cursos: " . $conn->error);
    }
    echo "</div>";
    
    // 6. CRIAR TABELA AGENDAMENTOS
    echo "<div class='section'>";
    echo "<h2>📅 Criando tabela de agendamentos</h2>";
    
    $sql = "CREATE TABLE agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        aluno_id INT,
        professor_id INT,
        curso_id INT,
        data_agendamento DATE NOT NULL,
        hora_inicio TIME NOT NULL,
        hora_fim TIME NOT NULL,
        status ENUM('agendado', 'confirmado', 'cancelado', 'concluido') DEFAULT 'agendado',
        observacoes TEXT,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE SET NULL,
        FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
        FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "<p class='success'>✅ Tabela 'agendamentos' criada</p>";
    } else {
        throw new Exception("❌ Erro ao criar tabela agendamentos: " . $conn->error);
    }
    echo "</div>";
    
    // 7. INSERIR ADMIN PADRÃO
    echo "<div class='section'>";
    echo "<h2>👤 Criando usuário administrador</h2>";
    
    $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo) 
            VALUES ('Administrador', 'admin@educonnect.com', ?, 'admin', 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $senha_hash);
    
    if ($stmt->execute()) {
        echo "<p class='success'>✅ Usuário admin criado</p>";
        echo "<p><strong>Email:</strong> admin@educonnect.com</p>";
        echo "<p><strong>Senha:</strong> admin123</p>";
    } else {
        echo "<p class='warning'>⚠️ Erro ao criar admin: " . $stmt->error . "</p>";
    }
    echo "</div>";
    
    // 8. VERIFICAÇÃO FINAL
    echo "<div class='section'>";
    echo "<h2>🔍 Verificação Final</h2>";
    
    $result = $conn->query("SHOW TABLES");
    $tabelas = [];
    while ($row = $result->fetch_array()) {
        $tabelas[] = $row[0];
    }
    
    echo "<p><strong>Tabelas criadas:</strong></p>";
    echo "<ul>";
    foreach ($tabelas as $tabela) {
        echo "<li>✅ $tabela</li>";
    }
    echo "</ul>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p><strong>Usuários:</strong> $total_usuarios</p>";
    
    echo "<p class='success'>🎉 Banco de dados recriado com sucesso!</p>";
    echo "</div>";
    
    // 9. LINKS PARA PRÓXIMOS PASSOS
    echo "<div class='section'>";
    echo "<h2>🚀 Próximos Passos</h2>";
    echo "<p>O banco foi recriado. Agora vamos carregar os dados:</p>";
    echo "<a href='carregar_dados_permanentes.php' class='btn btn-success'>🔄 Carregar Dados Permanentes</a>";
    echo "<a href='verificar_dados_agora.php' class='btn'>🔍 Verificar Dados</a>";
    echo "<a href='dashboard_final.php' class='btn'>📊 Acessar Dashboard</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2>❌ Erro Crítico</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP Control Panel.</p>";
    echo "<p>Se o erro persistir, reinicie o XAMPP completamente.</p>";
    echo "</div>";
}
?>










