<?php
echo "<h2>🔍 Verificação da Estrutura da Tabela Usuários</h2>";

// Conectar ao banco
include 'db.php';
if ($conn) {
    echo "✅ Conexão com banco OK<br>";
} else {
    echo "❌ Erro na conexão com banco<br>";
    exit;
}

// Verificar estrutura da tabela usuarios
echo "<h3>1. Estrutura da tabela 'usuarios':</h3>";
try {
    $structure = $conn->query("DESCRIBE usuarios");
    echo "📋 Colunas da tabela usuarios:<br>";
    while ($row = $structure->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']}<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar estrutura: " . $e->getMessage() . "<br>";
}

// Verificar se as colunas necessárias existem
echo "<h3>2. Verificando colunas necessárias:</h3>";
$colunas_necessarias = ['id', 'nome', 'email', 'senha', 'tipo_usuario', 'telefone', 'ativo', 'criado_em', 'formacao', 'valor_hora'];

try {
    $structure = $conn->query("DESCRIBE usuarios");
    $colunas_existentes = [];
    while ($row = $structure->fetch_assoc()) {
        $colunas_existentes[] = $row['Field'];
    }
    
    foreach ($colunas_necessarias as $coluna) {
        if (in_array($coluna, $colunas_existentes)) {
            echo "✅ Coluna '$coluna' existe<br>";
        } else {
            echo "❌ Coluna '$coluna' NÃO existe<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

// Adicionar colunas que faltam
echo "<h3>3. Adicionando colunas que faltam:</h3>";
try {
    // Verificar se formacao existe
    $result = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'formacao'");
    if ($result->num_rows === 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN formacao VARCHAR(255) NULL");
        echo "✅ Coluna 'formacao' adicionada<br>";
    } else {
        echo "✅ Coluna 'formacao' já existe<br>";
    }
    
    // Verificar se valor_hora existe
    $result = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'valor_hora'");
    if ($result->num_rows === 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN valor_hora DECIMAL(10,2) DEFAULT 0.00");
        echo "✅ Coluna 'valor_hora' adicionada<br>";
    } else {
        echo "✅ Coluna 'valor_hora' já existe<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao adicionar colunas: " . $e->getMessage() . "<br>";
}

// Verificar dados de professores
echo "<h3>4. Dados de professores:</h3>";
try {
    $result = $conn->query("SELECT id, nome, email, ativo, formacao, valor_hora FROM usuarios WHERE tipo_usuario = 'professor'");
    if ($result) {
        echo "📊 Professores encontrados:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "- ID: {$row['id']} | Nome: {$row['nome']} | Email: {$row['email']} | Ativo: " . ($row['ativo'] ? 'Sim' : 'Não') . " | Formação: " . ($row['formacao'] ?: 'Não informado') . " | Valor/hora: R$ " . ($row['valor_hora'] ?: '0.00') . "<br>";
        }
    } else {
        echo "❌ Erro na consulta: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><a href='professores.php'>Voltar para Professores</a>";
?>
