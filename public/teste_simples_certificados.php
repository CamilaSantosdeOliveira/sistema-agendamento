<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste Simples - Certificados</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
            margin: 0;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 8px;
        }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .info { color: #3b82f6; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .section { 
            margin: 20px 0; 
            padding: 20px; 
            border: 1px solid #e5e7eb; 
            border-radius: 8px;
            background: #f8fafc;
        }
        .btn {
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #2563eb;
        }
        .btn-success {
            background: #10b981;
        }
        .btn-success:hover {
            background: #059669;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔍 Teste Simples - Certificados</h1>
            <p>Verificando se a página de certificados funciona</p>
        </div>";

try {
    // 1. TESTAR CONEXÃO
    echo "<div class='section'>
        <h2>🔌 Teste de Conexão</h2>";
    
    if ($conn) {
        echo "<p class='success'>✅ Conexão com banco estabelecida!</p>";
    } else {
        echo "<p class='error'>❌ Erro na conexão com banco!</p>";
    }
    echo "</div>";

    // 2. TESTAR TABELA CERTIFICADOS
    echo "<div class='section'>
        <h2>📋 Teste da Tabela Certificados</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM certificados");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "<p class='success'>✅ Tabela certificados encontrada!</p>";
        echo "<p class='info'>📊 Total de certificados: $count</p>";
    } else {
        echo "<p class='error'>❌ Erro ao acessar tabela certificados!</p>";
    }
    echo "</div>";

    // 3. TESTAR PÁGINA DE CERTIFICADOS
    echo "<div class='section'>
        <h2>📄 Teste da Página de Certificados</h2>";
    
    if (file_exists('certificados.php')) {
        echo "<p class='success'>✅ Arquivo certificados.php encontrado!</p>";
        
        // Tentar incluir o arquivo para ver se há erros
        ob_start();
        try {
            include 'certificados.php';
            $output = ob_get_contents();
            ob_end_clean();
            
            if (strpos($output, 'error') !== false || strpos($output, 'Error') !== false) {
                echo "<p class='error'>❌ Erro encontrado na página de certificados!</p>";
                echo "<p class='info'>🔍 Verifique o console do navegador para mais detalhes.</p>";
            } else {
                echo "<p class='success'>✅ Página de certificados carregada sem erros!</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erro ao carregar página: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Arquivo certificados.php não encontrado!</p>";
    }
    echo "</div>";

    // 4. TESTAR API DE CERTIFICADOS
    echo "<div class='section'>
        <h2>🔧 Teste da API de Certificados</h2>";
    
    if (file_exists('api/certificados.php')) {
        echo "<p class='success'>✅ Arquivo api/certificados.php encontrado!</p>";
        
        // Testar se a API responde
        $test_url = 'api/certificados.php?action=listar_certificados';
        $response = @file_get_contents($test_url);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data && isset($data['success'])) {
                echo "<p class='success'>✅ API respondeu corretamente!</p>";
                if (isset($data['data'])) {
                    echo "<p class='info'>📊 Certificados retornados: " . count($data['data']) . "</p>";
                }
            } else {
                echo "<p class='warning'>⚠️ API respondeu mas com formato inesperado</p>";
            }
        } else {
            echo "<p class='error'>❌ API não respondeu</p>";
        }
    } else {
        echo "<p class='error'>❌ Arquivo api/certificados.php não encontrado!</p>";
    }
    echo "</div>";

    // 5. LINKS PARA TESTE
    echo "<div class='section'>
        <h2>🔗 Links para Teste</h2>";
    
    echo "<a href='certificados.php' class='btn btn-success' target='_blank'>
            📜 Abrir Página de Certificados
          </a>
          
          <a href='api/certificados.php?action=listar_certificados' class='btn' target='_blank'>
            🔧 Testar API Diretamente
          </a>
          
          <a href='dashboard_final.php' class='btn'>
            🏠 Voltar ao Dashboard
          </a>";
    
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Teste</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "</div>
</body>
</html>";
?>







