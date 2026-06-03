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
    <title>Verificar e Corrigir Certificados</title>
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
            <h1>🔧 Verificar e Corrigir Certificados</h1>
            <p>Diagnóstico e correção específica para certificados</p>
        </div>";

try {
    // 1. VERIFICAR TABELA CURSOS
    echo "<div class='section'>
        <h2>📋 Verificar Tabela Cursos</h2>";
    
    $tabela_cursos = $conn->query("SHOW TABLES LIKE 'cursos'");
    if ($tabela_cursos && $tabela_cursos->num_rows > 0) {
        echo "<p class='success'>✅ Tabela 'cursos' existe</p>";
    } else {
        echo "<p class='error'>❌ Tabela 'cursos' não existe!</p>";
        echo "<p class='warning'>Criando tabela cursos...</p>";
        
        $sql_criar_tabela = "CREATE TABLE IF NOT EXISTS cursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            descricao TEXT,
            carga_horaria VARCHAR(50),
            preco DECIMAL(10,2),
            status ENUM('ativo', 'inativo') DEFAULT 'ativo',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql_criar_tabela)) {
            echo "<p class='success'>✅ Tabela 'cursos' criada com sucesso!</p>";
        } else {
            echo "<p class='error'>❌ Erro ao criar tabela cursos</p>";
        }
    }
    echo "</div>";

    // 2. VERIFICAR ALUNOS
    echo "<div class='section'>
        <h2>👨‍🎓 Verificar Alunos</h2>";
    
    $alunos = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $total_alunos = $alunos ? $alunos->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Total de alunos: $total_alunos</p>";
    
    if ($total_alunos >= 3) {
        echo "<p class='success'>✅ Alunos suficientes encontrados</p>";
    } else {
        echo "<p class='error'>❌ Poucos alunos encontrados</p>";
    }
    echo "</div>";

    // 3. VERIFICAR CURSOS
    echo "<div class='section'>
        <h2>📚 Verificar Cursos</h2>";
    
    $cursos = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $total_cursos = $cursos ? $cursos->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Total de cursos ativos: $total_cursos</p>";
    
    if ($total_cursos >= 3) {
        echo "<p class='success'>✅ Cursos suficientes encontrados</p>";
    } else {
        echo "<p class='warning'>⚠️ Poucos cursos encontrados. Criando cursos...</p>";
        
        // Criar cursos se necessário
        $cursos_para_criar = [
            ['nome' => 'Programação Web', 'carga_horaria' => '40 horas', 'preco' => 299.99],
            ['nome' => 'Design Gráfico', 'carga_horaria' => '30 horas', 'preco' => 249.99],
            ['nome' => 'Marketing Digital', 'carga_horaria' => '35 horas', 'preco' => 279.99],
            ['nome' => 'Gestão de Projetos', 'carga_horaria' => '45 horas', 'preco' => 349.99],
            ['nome' => 'Inglês Técnico', 'carga_horaria' => '25 horas', 'preco' => 199.99]
        ];
        
        $criados = 0;
        foreach ($cursos_para_criar as $curso) {
            $check = $conn->prepare("SELECT id FROM cursos WHERE nome = ?");
            $check->bind_param("s", $curso['nome']);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO cursos (nome, descricao, carga_horaria, preco, status) VALUES (?, ?, ?, ?, 'ativo')";
                $stmt = $conn->prepare($sql);
                $descricao = "Curso de " . $curso['nome'];
                $stmt->bind_param("sssd", $curso['nome'], $descricao, $curso['carga_horaria'], $curso['preco']);
                
                if ($stmt->execute()) {
                    $criados++;
                    echo "<p class='success'>✅ Curso criado: {$curso['nome']}</p>";
                }
            }
        }
        echo "<p class='success'>🎉 $criados cursos criados!</p>";
    }
    echo "</div>";

    // 4. VERIFICAÇÃO FINAL
    echo "<div class='section'>
        <h2>🎯 Verificação Final</h2>";
    
    $alunos_final = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'")->fetch_assoc()['total'];
    $cursos_final = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'")->fetch_assoc()['total'];
    
    echo "<div style='background: #dcfce7; padding: 15px; border-radius: 8px; margin: 10px 0;'>
        <h3>📊 Status Final:</h3>
        <p><strong>Alunos:</strong> $alunos_final</p>
        <p><strong>Cursos Ativos:</strong> $cursos_final</p>
    </div>";
    
    if ($alunos_final >= 3 && $cursos_final >= 3) {
        echo "<p class='success'>🎉 Sistema PRONTO para certificados!</p>";
        
        // Mostrar dados disponíveis
        echo "<h3>👨‍🎓 Alunos Disponíveis:</h3>";
        $alunos_lista = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id LIMIT 5");
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $alunos_lista->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
        
        echo "<h3>📚 Cursos Disponíveis:</h3>";
        $cursos_lista = $conn->query("SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' ORDER BY id LIMIT 5");
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Carga Horária</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $cursos_lista->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['carga_horaria']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
        
    } else {
        echo "<p class='error'>❌ Sistema ainda não está pronto para certificados</p>";
        echo "<p class='warning'>Alunos necessários: 3, Encontrados: $alunos_final</p>";
        echo "<p class='warning'>Cursos necessários: 3, Encontrados: $cursos_final</p>";
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









