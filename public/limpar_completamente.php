<?php
echo "<h1>🧹 LIMPEZA COMPLETA E RECRIAÇÃO</h1>";

include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Parando MySQL para limpar arquivos...</h3>";

// Tentar parar MySQL
$stop_command = 'C:\xampp\xampp_stop.exe';
if (file_exists($stop_command)) {
    echo "<p style='color: blue;'>🔄 Parando MySQL...</p>";
    exec($stop_command . ' mysql', $output, $return_var);
    sleep(3);
}

echo "<h3>2️⃣ Removendo arquivos .ibd antigos...</h3>";

$data_dir = 'C:\xampp\mysql\data\sistema_agendamento';
$ibd_files = [
    'usuarios.ibd',
    'cursos.ibd',
    'agendamentos.ibd',
    'avaliacoes.ibd',
    'certificados.ibd',
    'inscricoes.ibd',
    'notificacoes.ibd',
    'pagamentos.ibd'
];

foreach ($ibd_files as $file) {
    $full_path = $data_dir . '\\' . $file;
    if (file_exists($full_path)) {
        if (unlink($full_path)) {
            echo "<p style='color: green;'>✅ $file removido</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao remover $file</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ $file não encontrado</p>";
    }
}

echo "<h3>3️⃣ Iniciando MySQL...</h3>";

// Tentar iniciar MySQL
$start_command = 'C:\xampp\xampp_start.exe';
if (file_exists($start_command)) {
    echo "<p style='color: blue;'>🔄 Iniciando MySQL...</p>";
    exec($start_command . ' mysql', $output, $return_var);
    sleep(5);
}

echo "<h3>4️⃣ Recriando tabelas...</h3>";

// Reconectar
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento', 3306);

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Erro de conexão após reinicialização</p>";
    exit;
}

echo "<p style='color: green;'>✅ Conexão restaurada!</p>";

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

// Recriar outras tabelas
$outras_tabelas = [
    'avaliacoes' => "CREATE TABLE avaliacoes (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT, curso_id INT, nota INT NOT NULL, comentario TEXT, data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (curso_id) REFERENCES cursos(id))",
    'certificados' => "CREATE TABLE certificados (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT, curso_id INT, codigo VARCHAR(50) UNIQUE NOT NULL, data_emissao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, status ENUM('valido', 'invalido') DEFAULT 'valido', FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (curso_id) REFERENCES cursos(id))",
    'inscricoes' => "CREATE TABLE inscricoes (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT, curso_id INT, data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, status ENUM('ativa', 'cancelada', 'concluida') DEFAULT 'ativa', FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (curso_id) REFERENCES cursos(id))",
    'notificacoes' => "CREATE TABLE notificacoes (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT, titulo VARCHAR(100) NOT NULL, mensagem TEXT NOT NULL, lida BOOLEAN DEFAULT FALSE, data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (usuario_id) REFERENCES usuarios(id))",
    'pagamentos' => "CREATE TABLE pagamentos (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT, curso_id INT, valor DECIMAL(10,2) NOT NULL, metodo_pagamento VARCHAR(50), status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente', data_pagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (curso_id) REFERENCES cursos(id))"
];

foreach ($outras_tabelas as $tabela => $sql) {
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Tabela $tabela criada</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar $tabela: " . $conn->error . "</p>";
    }
}

echo "<h3>🎉 LIMPEZA E RECRIAÇÃO CONCLUÍDA!</h3>";
echo "<p><a href='inserir_dados_simples.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>📝 Inserir Dados Demo</a></p>";

$conn->close();
?>









