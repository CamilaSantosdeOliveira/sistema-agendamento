<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h2>🔧 Criando Tabelas no Banco de Dados</h2>";

try {
    // Criar tabela usuarios
    echo "<h3>👥 Criando tabela usuarios...</h3>";
    $sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        telefone VARCHAR(20),
        tipo_usuario ENUM('aluno', 'professor') NOT NULL,
        formacao VARCHAR(200),
        experiencia VARCHAR(50),
        valor_hora DECIMAL(10,2) DEFAULT 0.00,
        descricao TEXT,
        ativo TINYINT(1) DEFAULT 1,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql_usuarios)) {
        echo "✅ Tabela 'usuarios' criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela 'usuarios': " . $conn->error . "<br>";
    }

    // Criar tabela pagamentos
    echo "<h3>💳 Criando tabela pagamentos...</h3>";
    $sql_pagamentos = "CREATE TABLE IF NOT EXISTS pagamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        valor DECIMAL(10,2) NOT NULL,
        status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente',
        data_pagamento DATE,
        metodo ENUM('cartao_credito', 'pix', 'boleto', 'dinheiro') NOT NULL,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql_pagamentos)) {
        echo "✅ Tabela 'pagamentos' criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela 'pagamentos': " . $conn->error . "<br>";
    }

    // Criar tabela avaliacoes
    echo "<h3>⭐ Criando tabela avaliacoes...</h3>";
    $sql_avaliacoes = "CREATE TABLE IF NOT EXISTS avaliacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        nota INT CHECK (nota >= 1 AND nota <= 5),
        comentario TEXT,
        data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql_avaliacoes)) {
        echo "✅ Tabela 'avaliacoes' criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela 'avaliacoes': " . $conn->error . "<br>";
    }

    // Criar tabela notificacoes
    echo "<h3>🔔 Criando tabela notificacoes...</h3>";
    $sql_notificacoes = "CREATE TABLE IF NOT EXISTS notificacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        titulo VARCHAR(100) NOT NULL,
        mensagem TEXT NOT NULL,
        lida TINYINT(1) DEFAULT 0,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql_notificacoes)) {
        echo "✅ Tabela 'notificacoes' criada com sucesso!<br>";
    } else {
        echo "❌ Erro ao criar tabela 'notificacoes': " . $conn->error . "<br>";
    }

    echo "<br><h2>🎉 Tabelas Criadas com Sucesso!</h2>";
    echo "<p>Agora você pode:</p>";
    echo "<ol>";
    echo "<li><a href='inserir_usuarios_teste.php'>Inserir usuários de teste</a></li>";
    echo "<li><a href='dashboard_corrigido.php'>Ver o dashboard funcionando</a></li>";
    echo "</ol>";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>





































