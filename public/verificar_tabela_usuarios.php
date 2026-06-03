<?php
include 'db.php';

echo "=== VERIFICAÇÃO DA TABELA DE USUÁRIOS ===\n\n";

// Verificar se existe tabela de usuários
echo "=== TABELAS DE USUÁRIOS ===\n";
$tabelas_usuarios = ['usuarios', 'users', 'user', 'admin', 'administradores'];

foreach ($tabelas_usuarios as $tabela) {
    $check = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($check && $check->num_rows > 0) {
        echo "✅ Tabela '$tabela' existe\n";
        
        // Verificar estrutura
        $structure = $conn->query("DESCRIBE $tabela");
        if ($structure) {
            echo "  Estrutura da tabela $tabela:\n";
            while ($row = $structure->fetch_assoc()) {
                echo "    Campo: " . $row['Field'] . " | Tipo: " . $row['Type'] . " | Null: " . $row['Null'] . " | Key: " . $row['Key'] . "\n";
            }
        }
        
        // Verificar dados
        $count = $conn->query("SELECT COUNT(*) as total FROM $tabela");
        if ($count) {
            $total = $count->fetch_assoc()['total'];
            echo "  Total de registros: " . $total . "\n";
            
            if ($total > 0) {
                $data = $conn->query("SELECT * FROM $tabela LIMIT 3");
                while ($row = $data->fetch_assoc()) {
                    echo "    ID: " . $row['id'] . " | Nome: " . ($row['nome'] ?? $row['username'] ?? $row['user'] ?? 'N/A') . " | Email: " . ($row['email'] ?? 'N/A') . "\n";
                }
            }
        }
        echo "\n";
    } else {
        echo "❌ Tabela '$tabela' NÃO existe\n";
    }
}

// Verificar todas as tabelas do banco
echo "=== TODAS AS TABELAS DO BANCO ===\n";
$tables_query = "SHOW TABLES";
$result = $conn->query($tables_query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        echo "✅ " . $row[0] . "\n";
    }
} else {
    echo "❌ Nenhuma tabela encontrada\n";
}

// Verificar se existe arquivo de login
echo "\n=== ARQUIVOS DE LOGIN ===\n";
$arquivos_login = ['login.php', 'index.php', 'auth.php', 'autenticacao.php'];

foreach ($arquivos_login as $arquivo) {
    if (file_exists($arquivo)) {
        echo "✅ Arquivo '$arquivo' existe\n";
    } else {
        echo "❌ Arquivo '$arquivo' NÃO existe\n";
    }
}
?>




