<?php
// Teste Simples das Configurações e Logs
echo "<h1>🧪 Teste das Configurações e Logs</h1>";

echo "<h2>1. Teste de Conexão com Banco</h2>";
try {
    include 'db.php';
    echo "✅ Conexão com banco: OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>2. Teste das Configurações do Sistema</h2>";
try {
    // Testar se a página carrega
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/configuracoes_sistema.php';
    $context = stream_context_create(['http' => ['timeout' => 10]]);
    $content = @file_get_contents($url, false, $context);
    
    if ($content !== false) {
        echo "✅ Configurações do Sistema: CARREGANDO<br>";
        if (strpos($content, 'Configurações do Sistema') !== false) {
            echo "✅ Configurações do Sistema: CONTEÚDO OK<br>";
        } else {
            echo "⚠️ Configurações do Sistema: Conteúdo inesperado<br>";
        }
    } else {
        echo "❌ Configurações do Sistema: ERRO AO CARREGAR<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "<br>";
}

echo "<h2>3. Teste dos Logs do Sistema</h2>";
try {
    // Testar se a página carrega
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/logs_sistema.php';
    $context = stream_context_create(['http' => ['timeout' => 10]]);
    $content = @file_get_contents($url, false, $context);
    
    if ($content !== false) {
        echo "✅ Logs do Sistema: CARREGANDO<br>";
        if (strpos($content, 'Logs do Sistema') !== false) {
            echo "✅ Logs do Sistema: CONTEÚDO OK<br>";
        } else {
            echo "⚠️ Logs do Sistema: Conteúdo inesperado<br>";
        }
    } else {
        echo "❌ Logs do Sistema: ERRO AO CARREGAR<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "<br>";
}

echo "<h2>4. Teste das Tabelas</h2>";

// Testar tabela configuracoes_sistema
try {
    $result = $conn->query("SHOW TABLES LIKE 'configuracoes_sistema'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Tabela configuracoes_sistema: EXISTE<br>";
        
        $count = $conn->query("SELECT COUNT(*) as total FROM configuracoes_sistema")->fetch_assoc()['total'];
        echo "✅ Configurações cadastradas: $count<br>";
    } else {
        echo "❌ Tabela configuracoes_sistema: NÃO EXISTE<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro na tabela configuracoes_sistema: " . $e->getMessage() . "<br>";
}

// Testar tabela logs_sistema
try {
    $result = $conn->query("SHOW TABLES LIKE 'logs_sistema'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Tabela logs_sistema: EXISTE<br>";
        
        $count = $conn->query("SELECT COUNT(*) as total FROM logs_sistema")->fetch_assoc()['total'];
        echo "✅ Logs cadastrados: $count<br>";
    } else {
        echo "❌ Tabela logs_sistema: NÃO EXISTE<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro na tabela logs_sistema: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Links Diretos</h2>";
echo "<p><a href='configuracoes_sistema.php' target='_blank'>🔗 Abrir Configurações do Sistema</a></p>";
echo "<p><a href='logs_sistema.php' target='_blank'>🔗 Abrir Logs do Sistema</a></p>";

echo "<h2>6. Teste de Inserção</h2>";

// Testar inserção de configuração
try {
    $sql = "INSERT IGNORE INTO configuracoes_sistema (chave, valor, descricao, categoria) VALUES ('teste_config', 'valor_teste', 'Teste de configuração', 'teste')";
    $conn->query($sql);
    echo "✅ Inserção de configuração: OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na inserção de configuração: " . $e->getMessage() . "<br>";
}

// Testar inserção de log
try {
    $sql = "INSERT INTO logs_sistema (acao, tabela_afetada, ip_address, user_agent) VALUES ('TESTE_INSERCAO', 'teste', '127.0.0.1', 'Teste')";
    $conn->query($sql);
    echo "✅ Inserção de log: OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na inserção de log: " . $e->getMessage() . "<br>";
}

echo "<h2>✅ Teste Concluído!</h2>";
echo "<p><a href='configuracoes.php'>🔗 Voltar às Configurações</a></p>";
?>







