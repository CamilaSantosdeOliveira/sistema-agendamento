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
    <title>Diagnóstico Detalhado do Sistema</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
            margin: 0;
        }
        .container { 
            max-width: 1200px; 
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
        .debug {
            background: #fef3c7;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔍 Diagnóstico Detalhado do Sistema</h1>
            <p>Verificando exatamente o que está acontecendo</p>
        </div>";

try {
    // 1. VERIFICAR CONEXÃO
    echo "<div class='section'>
        <h2>🔌 Verificação de Conexão</h2>";
    
    if ($conn) {
        echo "<p class='success'>✅ Conexão com banco estabelecida</p>";
        echo "<p class='info'>Banco: " . $conn->database . "</p>";
    } else {
        echo "<p class='error'>❌ Erro na conexão com banco</p>";
    }
    echo "</div>";

    // 2. VERIFICAR TODOS OS USUÁRIOS
    echo "<div class='section'>
        <h2>👥 Todos os Usuários no Sistema</h2>";
    
    $todos_usuarios = $conn->query("SELECT id, nome, email, tipo_usuario, data_cadastro FROM usuarios ORDER BY id");
    
    if ($todos_usuarios && $todos_usuarios->num_rows > 0) {
        echo "<p class='info'>📊 Total de usuários: {$todos_usuarios->num_rows}</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Data Cadastro</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $todos_usuarios->fetch_assoc()) {
            $tipo_class = ($row['tipo_usuario'] == 'aluno') ? 'success' : 'info';
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td class='$tipo_class'>{$row['tipo_usuario']}</td>
                    <td>{$row['data_cadastro']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='error'>❌ Nenhum usuário encontrado</p>";
    }
    echo "</div>";

    // 3. VERIFICAR ALUNOS ESPECIFICAMENTE
    echo "<div class='section'>
        <h2>👨‍🎓 Alunos Especificamente</h2>";
    
    $alunos = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id");
    
    if ($alunos && $alunos->num_rows > 0) {
        echo "<p class='success'>✅ Alunos encontrados: {$alunos->num_rows}</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $alunos->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='error'>❌ Nenhum aluno encontrado</p>";
    }
    echo "</div>";

    // 4. VERIFICAR TODOS OS CURSOS
    echo "<div class='section'>
        <h2>📚 Todos os Cursos no Sistema</h2>";
    
    $todos_cursos = $conn->query("SELECT id, nome, descricao, carga_horaria, preco, status, data_criacao FROM cursos ORDER BY id");
    
    if ($todos_cursos && $todos_cursos->num_rows > 0) {
        echo "<p class='info'>📊 Total de cursos: {$todos_cursos->num_rows}</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Carga Horária</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Data Criação</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $todos_cursos->fetch_assoc()) {
            $status_class = ($row['status'] == 'ativo') ? 'success' : 'warning';
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['descricao']}</td>
                    <td>{$row['carga_horaria']}</td>
                    <td>R$ {$row['preco']}</td>
                    <td class='$status_class'>{$row['status']}</td>
                    <td>{$row['data_criacao']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='error'>❌ Nenhum curso encontrado</p>";
    }
    echo "</div>";

    // 5. VERIFICAR CURSOS ATIVOS
    echo "<div class='section'>
        <h2>✅ Cursos Ativos Especificamente</h2>";
    
    $cursos_ativos = $conn->query("SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' ORDER BY id");
    
    if ($cursos_ativos && $cursos_ativos->num_rows > 0) {
        echo "<p class='success'>✅ Cursos ativos encontrados: {$cursos_ativos->num_rows}</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Carga Horária</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $cursos_ativos->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['carga_horaria']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='error'>❌ Nenhum curso ativo encontrado</p>";
    }
    echo "</div>";

    // 6. TESTAR A QUERY EXATA DO SCRIPT DE CERTIFICADOS
    echo "<div class='section'>
        <h2>🧪 Teste da Query do Script de Certificados</h2>";
    
    // Simular exatamente o que o script de certificados faz
    $alunos_test = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 3");
    $cursos_test = $conn->query("SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' LIMIT 3");
    
    echo "<h3>Alunos encontrados:</h3>";
    if ($alunos_test && $alunos_test->num_rows > 0) {
        echo "<p class='success'>✅ {$alunos_test->num_rows} alunos disponíveis</p>";
        while ($row = $alunos_test->fetch_assoc()) {
            echo "<div class='debug'>ID: {$row['id']} - Nome: {$row['nome']}</div>";
        }
    } else {
        echo "<p class='error'>❌ Nenhum aluno encontrado para certificados</p>";
    }
    
    echo "<h3>Cursos encontrados:</h3>";
    if ($cursos_test && $cursos_test->num_rows > 0) {
        echo "<p class='success'>✅ {$cursos_test->num_rows} cursos disponíveis</p>";
        while ($row = $cursos_test->fetch_assoc()) {
            echo "<div class='debug'>ID: {$row['id']} - Nome: {$row['nome']} - Carga: {$row['carga_horaria']}</div>";
        }
    } else {
        echo "<p class='error'>❌ Nenhum curso encontrado para certificados</p>";
    }
    
    // Verificar se há dados suficientes
    $alunos_count = $alunos_test ? $alunos_test->num_rows : 0;
    $cursos_count = $cursos_test ? $cursos_test->num_rows : 0;
    
    echo "<h3>Resultado da Verificação:</h3>";
    if ($alunos_count >= 3 && $cursos_count >= 3) {
        echo "<p class='success'>🎉 Sistema PRONTO para certificados!</p>";
    } else {
        echo "<p class='error'>❌ Sistema NÃO está pronto para certificados</p>";
        echo "<p class='warning'>Alunos necessários: 3, Encontrados: $alunos_count</p>";
        echo "<p class='warning'>Cursos necessários: 3, Encontrados: $cursos_count</p>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Diagnóstico</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='adicionar_certificados_teste.php' class='btn' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 5px;'>🎓 Testar Certificados</a>
        <a href='certificados.php' class='btn' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 5px;'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 5px;'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>







