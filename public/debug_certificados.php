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
    <title>Debug Certificados</title>
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
        .query-box {
            background: #f1f5f9;
            padding: 10px;
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
            <h1>🔍 Debug Certificados</h1>
            <p>Investigando o problema com criação de certificados</p>
        </div>";

try {
    // 1. TESTAR A QUERY EXATA DE ALUNOS
    echo "<div class='section'>
        <h2>👨‍🎓 Teste Query Alunos</h2>";
    
    $query_alunos = "SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 3";
    echo "<div class='query-box'>Query: $query_alunos</div>";
    
    $alunos = $conn->query($query_alunos);
    
    if ($alunos) {
        echo "<p class='success'>✅ Query executada com sucesso</p>";
        echo "<p class='info'>📊 Número de linhas retornadas: " . $alunos->num_rows . "</p>";
        
        if ($alunos->num_rows > 0) {
            echo "<h3>Alunos encontrados:</h3>";
            echo "<table class='data-table'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                    </tr>
                </thead>
                <tbody>";
            
            while ($row = $alunos->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nome']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='error'>❌ Nenhum aluno encontrado!</p>";
        }
    } else {
        echo "<p class='error'>❌ Erro na query: " . $conn->error . "</p>";
    }
    echo "</div>";

    // 2. TESTAR A QUERY EXATA DE CURSOS
    echo "<div class='section'>
        <h2>📚 Teste Query Cursos</h2>";
    
    $query_cursos = "SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' LIMIT 3";
    echo "<div class='query-box'>Query: $query_cursos</div>";
    
    $cursos = $conn->query($query_cursos);
    
    if ($cursos) {
        echo "<p class='success'>✅ Query executada com sucesso</p>";
        echo "<p class='info'>📊 Número de linhas retornadas: " . $cursos->num_rows . "</p>";
        
        if ($cursos->num_rows > 0) {
            echo "<h3>Cursos encontrados:</h3>";
            echo "<table class='data-table'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Carga Horária</th>
                    </tr>
                </thead>
                <tbody>";
            
            while ($row = $cursos->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$row['carga_horaria']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='error'>❌ Nenhum curso encontrado!</p>";
        }
    } else {
        echo "<p class='error'>❌ Erro na query: " . $conn->error . "</p>";
    }
    echo "</div>";

    // 3. VERIFICAR TODOS OS USUÁRIOS
    echo "<div class='section'>
        <h2>👥 Todos os Usuários</h2>";
    
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
    } else {
        echo "<p class='error'>❌ Nenhum usuário encontrado!</p>";
    }
    echo "</div>";

    // 4. VERIFICAR TODOS OS CURSOS
    echo "<div class='section'>
        <h2>📚 Todos os Cursos</h2>";
    
    $todos_cursos = $conn->query("SELECT id, nome, status, carga_horaria FROM cursos ORDER BY id");
    
    if ($todos_cursos && $todos_cursos->num_rows > 0) {
        echo "<p class='info'>📊 Total de cursos: " . $todos_cursos->num_rows . "</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Carga Horária</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $todos_cursos->fetch_assoc()) {
            $status_class = $row['status'] == 'ativo' ? 'success' : 'warning';
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td class='$status_class'>{$row['status']}</td>
                    <td>{$row['carga_horaria']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='error'>❌ Nenhum curso encontrado!</p>";
    }
    echo "</div>";

    // 5. ANÁLISE DO PROBLEMA
    echo "<div class='section'>
        <h2>🔍 Análise do Problema</h2>";
    
    $alunos_count = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'")->fetch_assoc()['total'];
    $cursos_count = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'")->fetch_assoc()['total'];
    
    echo "<div style='background: #fef3c7; padding: 15px; border-radius: 8px; margin: 10px 0;'>
        <h3>📊 Contadores:</h3>
        <p><strong>Alunos (tipo_usuario = 'aluno'):</strong> $alunos_count</p>
        <p><strong>Cursos (status = 'ativo'):</strong> $cursos_count</p>
    </div>";
    
    if ($alunos_count == 0) {
        echo "<p class='error'>❌ PROBLEMA: Nenhum aluno com tipo_usuario = 'aluno' encontrado!</p>";
    }
    
    if ($cursos_count == 0) {
        echo "<p class='error'>❌ PROBLEMA: Nenhum curso com status = 'ativo' encontrado!</p>";
    }
    
    if ($alunos_count > 0 && $cursos_count > 0) {
        echo "<p class='success'>✅ Dados suficientes encontrados!</p>";
        echo "<p class='info'>O problema pode estar na lógica do script adicionar_certificados_teste.php</p>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='adicionar_certificados_teste.php' class='btn'>🎓 Tentar Criar Certificados</a>
        <a href='certificados.php' class='btn'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>









