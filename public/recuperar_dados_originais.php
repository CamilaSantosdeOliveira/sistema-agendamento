<?php
echo "<h1>🔍 TENTANDO RECUPERAR DADOS ORIGINAIS</h1>";

include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Verificando se as tabelas ainda existem...</h3>";

// Verificar tabelas existentes
$tabelas = ['usuarios', 'cursos', 'agendamentos', 'avaliacoes', 'certificados', 'inscricoes', 'notificacoes', 'pagamentos'];

foreach ($tabelas as $tabela) {
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabela '$tabela' existe</p>";
        
        // Tentar contar registros
        $count_result = $conn->query("SELECT COUNT(*) as total FROM $tabela");
        if ($count_result) {
            $total = $count_result->fetch_assoc()['total'];
            echo "<p style='color: blue;'>📊 Registros em '$tabela': $total</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao contar '$tabela': " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tabela '$tabela' não existe</p>";
    }
}

echo "<h3>2️⃣ Tentando reparar tabelas...</h3>";

// Tentar reparar tabelas
foreach ($tabelas as $tabela) {
    $repair_sql = "REPAIR TABLE $tabela";
    $result = $conn->query($repair_sql);
    if ($result) {
        echo "<p style='color: green;'>✅ Tabela '$tabela' reparada</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Erro ao reparar '$tabela': " . $conn->error . "</p>";
    }
}

echo "<h3>3️⃣ Verificando dados após reparo...</h3>";

// Verificar dados novamente
foreach ($tabelas as $tabela) {
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result && $result->num_rows > 0) {
        $count_result = $conn->query("SELECT COUNT(*) as total FROM $tabela");
        if ($count_result) {
            $total = $count_result->fetch_assoc()['total'];
            if ($total > 0) {
                echo "<p style='color: green;'>🎉 '$tabela' tem $total registros!</p>";
                
                // Mostrar alguns dados de exemplo
                $sample_result = $conn->query("SELECT * FROM $tabela LIMIT 3");
                if ($sample_result) {
                    echo "<p style='color: blue;'>📋 Amostra de dados:</p>";
                    while ($row = $sample_result->fetch_assoc()) {
                        echo "<p style='margin-left: 20px;'>• " . json_encode($row) . "</p>";
                    }
                }
            } else {
                echo "<p style='color: orange;'>⚠️ '$tabela' existe mas está vazia</p>";
            }
        }
    }
}

echo "<h3>4️⃣ Tentando otimizar tabelas...</h3>";

// Tentar otimizar tabelas
foreach ($tabelas as $tabela) {
    $optimize_sql = "OPTIMIZE TABLE $tabela";
    $result = $conn->query($optimize_sql);
    if ($result) {
        echo "<p style='color: green;'>✅ Tabela '$tabela' otimizada</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Erro ao otimizar '$tabela': " . $conn->error . "</p>";
    }
}

echo "<h3>🎯 RESULTADO FINAL</h3>";
echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🚀 Testar Dashboard</a></p>";
echo "<p><a href='verificar_dados_completos.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>📊 Verificar Dados</a></p>";

$conn->close();
?>









