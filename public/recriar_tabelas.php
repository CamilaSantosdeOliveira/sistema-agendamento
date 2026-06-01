<?php
echo "<h1>🔧 RECRIANDO TABELAS DO SISTEMA</h1>";

include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Removendo tabelas corrompidas...</h3>";

// Lista de tabelas para recriar
$tabelas = ['usuarios', 'cursos', 'agendamentos', 'avaliacoes', 'certificados', 'inscricoes', 'notificacoes', 'pagamentos'];

foreach ($tabelas as $tabela) {
    $drop_sql = "DROP TABLE IF EXISTS $tabela";
    if ($conn->query($drop_sql)) {
        echo "<p style='color: green;'>✅ Tabela $tabela removida</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao remover $tabela: " . $conn->error . "</p>";
    }
}

echo "<h3>2️⃣ Recriando tabelas...</h3>";

// Recriar tabela usuarios
$usuarios_sql = "CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('admin', 'professor', 'aluno') NOT NULL,
    telefone VARCHAR(20),
    endereco TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
)";

if ($conn->query($usuarios_sql)) {
    echo "<p style='color: green;'>✅ Tabela usuarios criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar usuarios: " . $conn->error . "</p>";
}

// Recriar tabela cursos
$cursos_sql = "CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    duracao VARCHAR(50),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($cursos_sql)) {
    echo "<p style='color: green;'>✅ Tabela cursos criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar cursos: " . $conn->error . "</p>";
}

// Recriar tabela agendamentos
$agendamentos_sql = "CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    curso_id INT,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    status ENUM('agendado', 'confirmado', 'cancelado', 'concluido') DEFAULT 'agendado',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
)";

if ($conn->query($agendamentos_sql)) {
    echo "<p style='color: green;'>✅ Tabela agendamentos criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar agendamentos: " . $conn->error . "</p>";
}

// Recriar tabela avaliacoes
$avaliacoes_sql = "CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    curso_id INT,
    nota INT NOT NULL,
    comentario TEXT,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
)";

if ($conn->query($avaliacoes_sql)) {
    echo "<p style='color: green;'>✅ Tabela avaliacoes criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar avaliacoes: " . $conn->error . "</p>";
}

// Recriar tabela certificados
$certificados_sql = "CREATE TABLE certificados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    curso_id INT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    data_emissao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('valido', 'invalido') DEFAULT 'valido',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
)";

if ($conn->query($certificados_sql)) {
    echo "<p style='color: green;'>✅ Tabela certificados criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar certificados: " . $conn->error . "</p>";
}

// Recriar tabela inscricoes
$inscricoes_sql = "CREATE TABLE inscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    curso_id INT,
    data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('ativa', 'cancelada', 'concluida') DEFAULT 'ativa',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
)";

if ($conn->query($inscricoes_sql)) {
    echo "<p style='color: green;'>✅ Tabela inscricoes criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar inscricoes: " . $conn->error . "</p>";
}

// Recriar tabela notificacoes
$notificacoes_sql = "CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    titulo VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
)";

if ($conn->query($notificacoes_sql)) {
    echo "<p style='color: green;'>✅ Tabela notificacoes criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar notificacoes: " . $conn->error . "</p>";
}

// Recriar tabela pagamentos
$pagamentos_sql = "CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    curso_id INT,
    valor DECIMAL(10,2) NOT NULL,
    metodo_pagamento VARCHAR(50),
    status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente',
    data_pagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
)";

if ($conn->query($pagamentos_sql)) {
    echo "<p style='color: green;'>✅ Tabela pagamentos criada</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar pagamentos: " . $conn->error . "</p>";
}

echo "<h3>🎉 TABELAS RECRIADAS COM SUCESSO!</h3>";
echo "<p><a href='inserir_dados_simples.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>📝 Inserir Dados</a></p>";

$conn->close();
?>









