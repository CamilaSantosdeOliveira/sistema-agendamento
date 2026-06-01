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
    <title>Verificar Erro de Aluno</title>
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
            <h1>🔍 Verificar Erro de Aluno</h1>
            <p>Investigando o problema 'Aluno não encontrado'</p>
        </div>";

try {
    // 1. VERIFICAR TODOS OS ALUNOS
    echo "<div class='section'>
        <h2>👨‍🎓 Todos os Alunos no Sistema</h2>";
    
    $alunos = $conn->query("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id");
    
    if ($alunos && $alunos->num_rows > 0) {
        echo "<p class='success'>✅ Total de alunos encontrados: " . $alunos->num_rows . "</p>";
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
        
        while ($row = $alunos->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['tipo_usuario']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='error'>❌ Nenhum aluno encontrado!</p>";
    }
    echo "</div>";

    // 2. VERIFICAR ALUNOS PROBLEMÁTICOS
    echo "<div class='section'>
        <h2>⚠️ Alunos com Dados Problemáticos</h2>";
    
    $alunos_problematicos = $conn->query("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE tipo_usuario = 'aluno' AND (nome = '' OR nome IS NULL OR email = '' OR email IS NULL OR nome LIKE '%camila%' AND nome != 'camila')");
    
    if ($alunos_problematicos && $alunos_problematicos->num_rows > 0) {
        echo "<p class='warning'>⚠️ Alunos com dados problemáticos encontrados:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Problema</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $alunos_problematicos->fetch_assoc()) {
            $problema = "";
            if (empty($row['nome']) || $row['nome'] == NULL) $problema .= "Nome vazio; ";
            if (empty($row['email']) || $row['email'] == NULL) $problema .= "Email vazio; ";
            if (strpos($row['nome'], 'camila') !== false && $row['nome'] != 'camila') $problema .= "Nome duplicado/incompleto; ";
            
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td class='error'>$problema</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='success'>✅ Nenhum aluno problemático encontrado!</p>";
    }
    echo "</div>";

    // 3. CORRIGIR DADOS PROBLEMÁTICOS
    echo "<div class='section'>
        <h2>🔧 Corrigir Dados Problemáticos</h2>";
    
    // Corrigir nomes duplicados de camila
    $corrigir_camila = $conn->query("UPDATE usuarios SET nome = 'Camila Silva' WHERE id = 7 AND nome = 'camila'");
    $corrigir_camila2 = $conn->query("UPDATE usuarios SET nome = 'Camila Santos' WHERE id = 12 AND nome = 'camila'");
    
    if ($corrigir_camila && $corrigir_camila2) {
        echo "<p class='success'>✅ Nomes duplicados de 'camila' corrigidos!</p>";
    }
    
    // Verificar se há alunos com nomes vazios
    $alunos_vazios = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno' AND (nome = '' OR nome IS NULL)");
    $total_vazios = $alunos_vazios ? $alunos_vazios->fetch_assoc()['total'] : 0;
    
    if ($total_vazios > 0) {
        echo "<p class='warning'>⚠️ Encontrados $total_vazios alunos com nomes vazios</p>";
        echo "<p class='info'>Corrigindo nomes vazios...</p>";
        
        // Corrigir nomes vazios
        $conn->query("UPDATE usuarios SET nome = CONCAT('Aluno ', id) WHERE tipo_usuario = 'aluno' AND (nome = '' OR nome IS NULL)");
        echo "<p class='success'>✅ Nomes vazios corrigidos!</p>";
    }
    
    echo "</div>";

    // 4. VERIFICAÇÃO FINAL
    echo "<div class='section'>
        <h2>🎯 Verificação Final</h2>";
    
    $alunos_finais = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id");
    
    if ($alunos_finais && $alunos_finais->num_rows > 0) {
        echo "<p class='success'>✅ Alunos após correção:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $alunos_finais->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
        
        echo "<p class='success'>🎉 Dados corrigidos! Agora tente emitir certificados novamente.</p>";
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







