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
    <title>Debug Emissão Certificado</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
            margin: 0;
        }
        .container { 
            max-width: 1000px; 
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
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
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
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .data-table th, .data-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table th {
            background: #f9fafb;
            font-weight: bold;
        }
        .debug-box {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            margin: 10px 0;
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔍 Debug Emissão Certificado</h1>
            <p>Investigando o erro 'Aluno não encontrado' na emissão</p>
        </div>";

try {
    // 1. VERIFICAR PARÂMETROS RECEBIDOS
    echo "<div class='section'>
        <h2>📋 Parâmetros Recebidos</h2>";
    
    echo "<div class='debug-box'>";
    echo "<strong>GET Parameters:</strong><br>";
    foreach ($_GET as $key => $value) {
        echo "$key = $value<br>";
    }
    echo "<br><strong>POST Parameters:</strong><br>";
    foreach ($_POST as $key => $value) {
        echo "$key = $value<br>";
    }
    echo "</div>";
    echo "</div>";

    // 2. TESTAR BUSCA POR ID ESPECÍFICO
    echo "<div class='section'>
        <h2>🔍 Teste de Busca por ID</h2>";
    
    // Testar IDs que apareceram na lista
    $ids_para_testar = [7, 12, 4, 8, 5, 10, 11, 13, 15, 17, 19, 21, 23, 25, 27];
    
    foreach ($ids_para_testar as $id) {
        $aluno = $conn->query("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE id = $id");
        
        if ($aluno && $aluno->num_rows > 0) {
            $row = $aluno->fetch_assoc();
            $status = ($row['tipo_usuario'] == 'aluno') ? 'success' : 'warning';
            $status_text = ($row['tipo_usuario'] == 'aluno') ? '✅ Aluno' : '⚠️ ' . $row['tipo_usuario'];
            
            echo "<div class='debug-box'>
                <strong>ID $id:</strong> {$row['nome']} - {$row['email']} - <span class='$status'>$status_text</span>
            </div>";
        } else {
            echo "<div class='debug-box'>
                <strong>ID $id:</strong> <span class='error'>❌ NÃO ENCONTRADO</span>
            </div>";
        }
    }
    echo "</div>";

    // 3. VERIFICAR TODOS OS USUÁRIOS
    echo "<div class='section'>
        <h2>👥 Todos os Usuários no Sistema</h2>";
    
    $todos_usuarios = $conn->query("SELECT id, nome, email, tipo_usuario FROM usuarios ORDER BY id");
    
    if ($todos_usuarios && $todos_usuarios->num_rows > 0) {
        echo "<p class='info'>📊 Total de usuários: " . $todos_usuarios->num_rows . "</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $todos_usuarios->fetch_assoc()) {
            $tipo_class = $row['tipo_usuario'] == 'aluno' ? 'success' : 'info';
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td class='$tipo_class'>{$row['tipo_usuario']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    }
    echo "</div>";

    // 4. SIMULAR A QUERY DA API
    echo "<div class='section'>
        <h2>🧪 Simular Query da API</h2>";
    
    // Simular a query que a API de certificados usa
    $query_simulada = "SELECT id, nome, email FROM usuarios WHERE id = ? AND tipo_usuario = 'aluno'";
    echo "<div class='debug-box'>Query simulada: $query_simulada</div>";
    
    // Testar com alguns IDs
    $ids_teste = [7, 12, 4, 8, 5];
    
    foreach ($ids_teste as $id) {
        $stmt = $conn->prepare($query_simulada);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<div class='debug-box'>
                <strong>ID $id:</strong> ✅ ENCONTRADO - {$row['nome']} ({$row['email']})
            </div>";
        } else {
            echo "<div class='debug-box'>
                <strong>ID $id:</strong> ❌ NÃO ENCONTRADO como aluno
            </div>";
        }
    }
    echo "</div>";

    // 5. VERIFICAR API DE CERTIFICADOS
    echo "<div class='section'>
        <h2>🔧 Verificar API de Certificados</h2>";
    
    echo "<p class='info'>Vamos verificar se a API está funcionando corretamente...</p>";
    
    // Simular uma requisição para a API
    $url = 'http://localhost:8080/Sistema%20De%20Agendamento/public/api/certificados.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "<div class='debug-box'>
            <strong>API Response:</strong><br>
            $response
        </div>";
    } else {
        echo "<div class='debug-box'>
            <strong>API Error:</strong> Não foi possível acessar a API
        </div>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='certificados.php' class='btn'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>









