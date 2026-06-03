<?php
echo "<h1>🔧 Criando Tabelas Essenciais</h1>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema_agendamento';

try {
    // Conectar ao MySQL
    echo "<p>🔄 Conectando ao MySQL...</p>";
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }
    
    echo "<p>✅ <strong>MySQL conectado!</strong></p>";
    
    // Criar tabela usuarios
    echo "<p>🔄 Criando tabela usuarios...</p>";
    $sql_usuarios = "
    CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nome` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `senha` varchar(255) NOT NULL,
        `tipo_usuario` enum('admin','professor','aluno') NOT NULL,
        `formacao` varchar(255) DEFAULT NULL,
        `valor_hora` decimal(10,2) DEFAULT NULL,
        `ativo` tinyint(1) DEFAULT 1,
        `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
        `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `telefone` varchar(20) DEFAULT NULL,
        `data_nascimento` date DEFAULT NULL,
        `endereco` text DEFAULT NULL,
        `cidade` varchar(100) DEFAULT NULL,
        `estado` varchar(2) DEFAULT NULL,
        `cep` varchar(10) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    if ($conn->query($sql_usuarios)) {
        echo "<p>✅ <strong>Tabela usuarios criada!</strong></p>";
    } else {
        echo "<p>❌ <strong>Erro ao criar tabela usuarios:</strong> " . $conn->error . "</p>";
    }
    
    // Inserir usuários de teste
    echo "<p>🔄 Inserindo usuários de teste...</p>";
    
    // Hash da senha 123456
    $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
    
    // Inserir admin
    $sql_admin = "INSERT IGNORE INTO usuarios (id, nome, email, senha, tipo_usuario) VALUES (1, 'Administrador', 'admin@educonnect.com', '$senha_hash', 'admin')";
    if ($conn->query($sql_admin)) {
        echo "<p>✅ <strong>Admin criado!</strong></p>";
    }
    
    // Inserir professor
    $sql_professor = "INSERT IGNORE INTO usuarios (id, nome, email, senha, tipo_usuario) VALUES (2, 'Prof. Maria Santos', 'maria.santos@educonnect.com', '$senha_hash', 'professor')";
    if ($conn->query($sql_professor)) {
        echo "<p>✅ <strong>Professor criado!</strong></p>";
    }
    
    // Inserir aluno
    $sql_aluno = "INSERT IGNORE INTO usuarios (id, nome, email, senha, tipo_usuario) VALUES (3, 'João Silva', 'joao.silva@email.com', '$senha_hash', 'aluno')";
    if ($conn->query($sql_aluno)) {
        echo "<p>✅ <strong>Aluno criado!</strong></p>";
    }
    
    // Verificar se funcionou
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    if ($result) {
        $total = $result->fetch_assoc()['total'];
        echo "<p>📊 <strong>Total de usuários:</strong> $total</p>";
    }
    
    $conn->close();
    
    echo "<h2>✅ <strong>Tabelas criadas com sucesso!</strong></h2>";
    echo "<p>🎯 <strong>Agora o login deve funcionar!</strong></p>";
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Teste o Login:</h2>";
echo "<p><a href='login.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Testar Login</a></p>";

echo "<h2>📋 Contas Criadas:</h2>";
echo "<p><strong>Admin:</strong> admin@educonnect.com / 123456</p>";
echo "<p><strong>Professor:</strong> maria.santos@educonnect.com / 123456</p>";
echo "<p><strong>Aluno:</strong> joao.silva@email.com / 123456</p>";
?>








