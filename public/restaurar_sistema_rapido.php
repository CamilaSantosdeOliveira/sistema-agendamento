<?php
// Script de Restauração Rápida do Sistema
echo "<h1>🔄 Restaurando Sistema de Agendamento</h1>";

// Configurações
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sistema_agendamento';

echo "<p>📋 Iniciando restauração do sistema...</p>";

try {
    // Conectar ao MySQL
    $mysqli = new mysqli($host, $username, $password);
    
    if ($mysqli->connect_error) {
        throw new Exception("Erro de conexão: " . $mysqli->connect_error);
    }
    
    echo "<p>✅ Conectado ao MySQL</p>";
    
    // Criar banco se não existir
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `$database`");
    $mysqli->select_db($database);
    
    echo "<p>✅ Banco de dados criado/selecionado</p>";
    
    // Criar tabelas
    $tables = [
        "usuarios" => "CREATE TABLE `usuarios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL UNIQUE,
            `senha` varchar(255) NOT NULL,
            `tipo_usuario` enum('admin','professor','aluno') NOT NULL,
            `data_criacao` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )",
        
        "cursos" => "CREATE TABLE `cursos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` varchar(255) NOT NULL,
            `descricao` text,
            `categoria` varchar(100),
            `nivel` varchar(50),
            `carga_horaria` int(11),
            `preco` decimal(10,2),
            `status` enum('ativo','inativo') DEFAULT 'ativo',
            `data_criacao` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )",
        
        "agendamentos" => "CREATE TABLE `agendamentos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `professor_id` int(11),
            `aluno_id` int(11),
            `curso_id` int(11),
            `data_aula` datetime,
            `duracao` int(11) DEFAULT 60,
            `status` enum('agendado','confirmado','cancelado','concluido') DEFAULT 'agendado',
            `observacoes` text,
            `data_criacao` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )",
        
        "certificados" => "CREATE TABLE `certificados` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `aluno_id` int(11),
            `curso_id` int(11),
            `codigo` varchar(50) UNIQUE,
            `data_conclusao` date,
            `status` enum('validado','pendente','revogado') DEFAULT 'pendente',
            `data_criacao` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )"
    ];
    
    foreach ($tables as $tableName => $sql) {
        $mysqli->query("DROP TABLE IF EXISTS `$tableName`");
        if ($mysqli->query($sql)) {
            echo "<p>✅ Tabela '$tableName' criada</p>";
        } else {
            echo "<p>⚠️ Erro ao criar tabela '$tableName': " . $mysqli->error . "</p>";
        }
    }
    
    // Inserir dados básicos
    echo "<h3>👥 Inserindo usuários básicos...</h3>";
    
    // Admin
    $adminPassword = password_hash('123456', PASSWORD_DEFAULT);
    $mysqli->query("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES 
        ('Administrador', 'admin@educonnect.com', '$adminPassword', 'admin')");
    
    // Professores
    $profPassword = password_hash('123456', PASSWORD_DEFAULT);
    $mysqli->query("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES 
        ('Prof. Ricardo Silva', 'ricardo.silva@educonnect.com', '$profPassword', 'professor'),
        ('Prof. André Oliveira', 'andre.oliveira@educonnect.com', '$profPassword', 'professor'),
        ('Profa. Camila Rodrigues', 'camila.rodrigues@educonnect.com', '$profPassword', 'professor')");
    
    // Alunos
    $alunoPassword = password_hash('123456', PASSWORD_DEFAULT);
    $mysqli->query("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES 
        ('Camila Santos', 'camilacah7890@gmail.com', '$alunoPassword', 'aluno'),
        ('João Silva', 'joao.silva@email.com', '$alunoPassword', 'aluno')");
    
    echo "<p>✅ Usuários básicos inseridos</p>";
    
    // Inserir cursos básicos
    echo "<h3>📚 Inserindo cursos básicos...</h3>";
    
    $mysqli->query("INSERT INTO cursos (nome, descricao, categoria, nivel, carga_horaria, preco) VALUES 
        ('Desenvolvimento Web Full Stack', 'Curso completo de desenvolvimento web', 'Programação', 'Intermediário', 80, 299.90),
        ('DevOps e Docker', 'Aprenda DevOps e containerização', 'DevOps', 'Avançado', 60, 399.90),
        ('Python para Iniciantes', 'Introdução à programação Python', 'Programação', 'Iniciante', 40, 199.90)");
    
    echo "<p>✅ Cursos básicos inseridos</p>";
    
    // Inserir agendamentos de exemplo
    echo "<h3>📅 Inserindo agendamentos de exemplo...</h3>";
    
    $mysqli->query("INSERT INTO agendamentos (professor_id, aluno_id, curso_id, data_aula, status) VALUES 
        (2, 5, 1, '2025-09-05 14:00:00', 'agendado'),
        (3, 5, 2, '2025-09-06 15:00:00', 'agendado')");
    
    echo "<p>✅ Agendamentos de exemplo inseridos</p>";
    
    // Inserir certificados de exemplo
    echo "<h3>🏆 Inserindo certificados de exemplo...</h3>";
    
    $mysqli->query("INSERT INTO certificados (aluno_id, curso_id, codigo, data_conclusao, status) VALUES 
        (6, 1, 'CERT-445CEB95', '2025-08-29', 'validado'),
        (6, 1, 'CERT-1953043C', '2025-08-29', 'revogado')");
    
    echo "<p>✅ Certificados de exemplo inseridos</p>";
    
    $mysqli->close();
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2 style='color: #155724; margin-top: 0;'>✅ Sistema Restaurado com Sucesso!</h2>";
    echo "<p><strong>🗄️ Banco de dados:</strong> Criado e populado</p>";
    echo "<p><strong>👥 Usuários:</strong> Admin, Professores e Alunos</p>";
    echo "<p><strong>📚 Cursos:</strong> 3 cursos básicos</p>";
    echo "<p><strong>📅 Agendamentos:</strong> 2 agendamentos de exemplo</p>";
    echo "<p><strong>🏆 Certificados:</strong> 2 certificados de exemplo</p>";
    echo "</div>";
    
    echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>🔐 Credenciais de Login:</h3>";
    echo "<p><strong>👨‍💼 Admin:</strong> admin@educonnect.com / 123456</p>";
    echo "<p><strong>👨‍🏫 Professor:</strong> ricardo.silva@educonnect.com / 123456</p>";
    echo "<p><strong>👨‍🎓 Aluno:</strong> camilacah7890@gmail.com / 123456</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na restauração: " . $e->getMessage() . "</p>";
}

echo "<div style='margin-top: 20px;'>";
echo "<a href='./' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Voltar ao Sistema</a>";
echo "<a href='login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Ir para Login</a>";
echo "</div>";
?>








