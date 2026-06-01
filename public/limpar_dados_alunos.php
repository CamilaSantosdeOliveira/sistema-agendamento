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
    <title>Limpar e Corrigir Dados dos Alunos</title>
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
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
        .btn-danger {
            background: #ef4444;
        }
        .btn-danger:hover {
            background: #dc2626;
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
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🧹 Limpar e Corrigir Dados dos Alunos</h1>
            <p>Removendo duplicatas e corrigindo dados inconsistentes</p>
        </div>";

try {
    // 1. VERIFICAR DADOS ATUAIS
    echo "<div class='section'>
        <h2>📊 Dados Atuais dos Alunos</h2>";
    
    $result = $conn->query("SELECT id, nome, email, tipo_usuario, data_cadastro FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id");
    
    if ($result && $result->num_rows > 0) {
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Data Cadastro</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['data_cadastro']}</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "<p class='warning'>⚠️ Nenhum aluno encontrado</p>";
    }
    echo "</div>";

    // 2. IDENTIFICAR DUPLICATAS
    echo "<div class='section'>
        <h2>🔍 Identificando Duplicatas</h2>";
    
    $duplicatas = $conn->query("
        SELECT nome, email, COUNT(*) as total
        FROM usuarios 
        WHERE tipo_usuario = 'aluno'
        GROUP BY nome, email
        HAVING COUNT(*) > 1
    ");
    
    if ($duplicatas && $duplicatas->num_rows > 0) {
        echo "<p class='warning'>⚠️ Duplicatas encontradas:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $duplicatas->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['total']}</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "<p class='success'>✅ Nenhuma duplicata encontrada</p>";
    }
    echo "</div>";

    // 3. LIMPAR DADOS
    echo "<div class='section'>
        <h2>🧹 Limpeza de Dados</h2>";
    
    // Remover duplicatas (manter apenas o primeiro registro)
    $limpeza = $conn->query("
        DELETE u1 FROM usuarios u1
        INNER JOIN usuarios u2 
        WHERE u1.id > u2.id 
        AND u1.nome = u2.nome 
        AND u1.email = u2.email 
        AND u1.tipo_usuario = 'aluno' 
        AND u2.tipo_usuario = 'aluno'
    ");
    
    if ($limpeza) {
        echo "<p class='success'>✅ Duplicatas removidas com sucesso!</p>";
    } else {
        echo "<p class='info'>ℹ️ Nenhuma duplicata para remover</p>";
    }
    
    // Remover registros com dados inválidos
    $invalidos = $conn->query("
        DELETE FROM usuarios 
        WHERE tipo_usuario = 'aluno' 
        AND (nome = '' OR nome IS NULL OR email = '' OR email IS NULL)
    ");
    
    if ($invalidos) {
        echo "<p class='success'>✅ Registros inválidos removidos!</p>";
    } else {
        echo "<p class='info'>ℹ️ Nenhum registro inválido encontrado</p>";
    }
    echo "</div>";

    // 4. CRIAR DADOS LIMPOS
    echo "<div class='section'>
        <h2>✨ Criando Dados Limpos</h2>";
    
    // Verificar quantos alunos restaram
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_restantes = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Alunos restantes após limpeza: $alunos_restantes</p>";
    
    if ($alunos_restantes < 3) {
        // Criar alunos limpos
        $alunos_limpos = [
            ['nome' => 'João Silva', 'email' => 'joao.silva@email.com'],
            ['nome' => 'Maria Santos', 'email' => 'maria.santos@email.com'],
            ['nome' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@email.com'],
            ['nome' => 'Ana Costa', 'email' => 'ana.costa@email.com'],
            ['nome' => 'Carlos Mendes', 'email' => 'carlos.mendes@email.com']
        ];
        
        $criados = 0;
        foreach ($alunos_limpos as $aluno) {
            // Verificar se já existe
            $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $check->bind_param("s", $aluno['email']);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows == 0) {
                $senha_hash = password_hash('123456', PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, data_cadastro) VALUES (?, ?, ?, 'aluno', NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $aluno['nome'], $aluno['email'], $senha_hash);
                
                if ($stmt->execute()) {
                    $criados++;
                    echo "<p class='success'>✅ Aluno criado: {$aluno['nome']}</p>";
                }
            } else {
                echo "<p class='info'>ℹ️ Aluno já existe: {$aluno['nome']}</p>";
            }
        }
        
        echo "<p class='success'>🎉 Total de alunos criados: $criados</p>";
    } else {
        echo "<p class='success'>✅ Dados suficientes já existem</p>";
    }
    echo "</div>";

    // 5. VERIFICAÇÃO FINAL
    echo "<div class='section'>
        <h2>🎯 Verificação Final</h2>";
    
    $result = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id");
    
    if ($result && $result->num_rows > 0) {
        echo "<p class='success'>✅ Dados finais dos alunos:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
        
        echo "<p class='success'>🎉 Dados limpos e organizados!</p>";
    } else {
        echo "<p class='error'>❌ Nenhum aluno encontrado após limpeza</p>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='adicionar_certificados_teste.php' class='btn btn-success'>🎓 Criar Certificados</a>
        <a href='certificados.php' class='btn'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>







