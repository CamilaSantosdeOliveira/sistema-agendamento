<?php
require_once 'db.php';

echo "<h2>🔍 Verificação de Status dos Certificados</h2>";

try {
    // Verificar certificados existentes
    $sql = "SELECT id, codigo_verificacao, status, data_emissao FROM certificados ORDER BY id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<h3>📋 Certificados Encontrados:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Código</th><th>Status</th><th>Data Emissão</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['codigo_verificacao']}</td>";
            echo "<td style='color: " . ($row['status'] == 'validado' ? 'green' : ($row['status'] == 'revogado' ? 'red' : 'orange')) . "'>{$row['status']}</td>";
            echo "<td>{$row['data_emissao']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar por status
        $sql_count = "SELECT status, COUNT(*) as total FROM certificados GROUP BY status";
        $result_count = $conn->query($sql_count);
        
        echo "<h3>📊 Estatísticas:</h3>";
        while ($row = $result_count->fetch_assoc()) {
            echo "<p><strong>{$row['status']}:</strong> {$row['total']} certificados</p>";
        }
        
    } else {
        echo "<p>❌ Nenhum certificado encontrado no banco de dados.</p>";
    }
    
    // Verificar se há certificados com status NULL ou vazio
    $sql_null = "SELECT COUNT(*) as total FROM certificados WHERE status IS NULL OR status = ''";
    $result_null = $conn->query($sql_null);
    $null_count = $result_null->fetch_assoc()['total'];
    
    if ($null_count > 0) {
        echo "<h3>⚠️ Certificados com Status Inválido:</h3>";
        echo "<p>Encontrados {$null_count} certificados com status NULL ou vazio.</p>";
        
        // Corrigir status NULL para 'emitido'
        $sql_fix = "UPDATE certificados SET status = 'emitido' WHERE status IS NULL OR status = ''";
        $conn->query($sql_fix);
        
        echo "<p>✅ Status corrigido para 'emitido' automaticamente.</p>";
    }
    
    // Verificar se há certificados com status inválidos
    $sql_invalid = "SELECT COUNT(*) as total FROM certificados WHERE status NOT IN ('emitido', 'validado', 'revogado')";
    $result_invalid = $conn->query($sql_invalid);
    $invalid_count = $result_invalid->fetch_assoc()['total'];
    
    if ($invalid_count > 0) {
        echo "<h3>⚠️ Certificados com Status Inválido:</h3>";
        echo "<p>Encontrados {$invalid_count} certificados com status inválido.</p>";
        
        // Mostrar status inválidos
        $sql_show = "SELECT id, codigo_verificacao, status FROM certificados WHERE status NOT IN ('emitido', 'validado', 'revogado')";
        $result_show = $conn->query($sql_show);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Código</th><th>Status Inválido</th></tr>";
        
        while ($row = $result_show->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['codigo_verificacao']}</td>";
            echo "<td style='color: red;'>{$row['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Corrigir para 'emitido'
        $sql_fix_invalid = "UPDATE certificados SET status = 'emitido' WHERE status NOT IN ('emitido', 'validado', 'revogado')";
        $conn->query($sql_fix_invalid);
        
        echo "<p>✅ Status inválidos corrigidos para 'emitido'.</p>";
    }
    
    echo "<h3>✅ Verificação Concluída!</h3>";
    echo "<p><a href='validacao_certificados.php'>🔗 Ir para Validação de Certificados</a></p>";
    echo "<p><a href='certificados.php'>🔗 Voltar aos Certificados</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>


















