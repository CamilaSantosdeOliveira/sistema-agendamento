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
    <title>Verificar Dados do Sistema</title>
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
        .btn-warning {
            background: #f59e0b;
        }
        .btn-warning:hover {
            background: #d97706;
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
            <h1>🔍 Verificar Dados do Sistema</h1>
            <p>Verificando alunos e cursos disponíveis</p>
        </div>";

try {
    // 1. VERIFICAR ALUNOS
    echo "<div class='section'>
        <h2>👨‍🎓 Verificar Alunos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Total de alunos: $alunos_count</p>";
    
    if ($alunos_count > 0) {
        echo "<p class='success'>✅ Alunos encontrados!</p>";
        
        // Listar alguns alunos
        $result = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 5");
        if ($result && $result->num_rows > 0) {
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
        }
    } else {
        echo "<p class='error'>❌ Nenhum aluno encontrado!</p>";
    }
    echo "</div>";

    // 2. VERIFICAR CURSOS
    echo "<div class='section'>
        <h2>📚 Verificar Cursos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $cursos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Total de cursos ativos: $cursos_count</p>";
    
    if ($cursos_count > 0) {
        echo "<p class='success'>✅ Cursos encontrados!</p>";
        
        // Listar alguns cursos
        $result = $conn->query("SELECT id, nome, carga_horaria, status FROM cursos WHERE status = 'ativo' LIMIT 5");
        if ($result && $result->num_rows > 0) {
            echo "<table class='data-table'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Carga Horária</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$row['carga_horaria']}h</td>
                        <td>{$row['status']}</td>
                      </tr>";
            }
            
            echo "</tbody></table>";
        }
    } else {
        echo "<p class='error'>❌ Nenhum curso ativo encontrado!</p>";
    }
    echo "</div>";

    // 3. VERIFICAR TODAS AS TABELAS
    echo "<div class='section'>
        <h2>📋 Verificar Todas as Tabelas</h2>";
    
    $tables = ['usuarios', 'cursos', 'professores', 'certificados', 'agendamentos', 'inscricoes'];
    
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as total FROM $table");
        $count = $result ? $result->fetch_assoc()['total'] : 0;
        
        if ($count > 0) {
            echo "<p class='success'>✅ Tabela '$table': $count registros</p>";
        } else {
            echo "<p class='warning'>⚠️ Tabela '$table': $count registros</p>";
        }
    }
    echo "</div>";

    // 4. RESUMO
    echo "<div class='section'>
        <h2>🎯 Resumo</h2>";
    
    if ($alunos_count > 0 && $cursos_count > 0) {
        echo "<p class='success'>🎉 Sistema pronto para criar certificados!</p>";
        echo "<p class='info'>✅ Alunos: $alunos_count</p>";
        echo "<p class='info'>✅ Cursos ativos: $cursos_count</p>";
        echo "<p class='success'>🚀 Pode prosseguir com a criação de certificados</p>";
    } else {
        echo "<p class='error'>❌ Sistema não está pronto para certificados</p>";
        if ($alunos_count == 0) {
            echo "<p class='error'>❌ Nenhum aluno encontrado</p>";
        }
        if ($cursos_count == 0) {
            echo "<p class='error'>❌ Nenhum curso ativo encontrado</p>";
        }
        echo "<p class='warning'>⚠️ Execute o script de carregamento de dados primeiro</p>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Sistema</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='carregar_dados_final.php' class='btn btn-warning'>🔄 Carregar Dados</a>
        <a href='adicionar_certificados_teste.php' class='btn btn-success'>🎓 Criar Certificados</a>
        <a href='status_final_certificados.php' class='btn'>📊 Status Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>







