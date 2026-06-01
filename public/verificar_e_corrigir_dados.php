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
    <title>Verificar e Corrigir Dados do Sistema</title>
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
        .status-ok { background: #dcfce7; }
        .status-error { background: #fef2f2; }
        .status-warning { background: #fef3c7; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔍 Verificar e Corrigir Dados do Sistema</h1>
            <p>Diagnóstico completo e correção automática de dados</p>
        </div>";

try {
    // 1. VERIFICAR TABELAS
    echo "<div class='section'>
        <h2>📋 Verificação de Tabelas</h2>";
    
    $tabelas_necessarias = ['usuarios', 'cursos', 'professores', 'certificados'];
    $tabelas_ok = [];
    
    foreach ($tabelas_necessarias as $tabela) {
        $result = $conn->query("SHOW TABLES LIKE '$tabela'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✅ Tabela '$tabela' existe</p>";
            $tabelas_ok[] = $tabela;
        } else {
            echo "<p class='error'>❌ Tabela '$tabela' não existe</p>";
        }
    }
    echo "</div>";

    // 2. VERIFICAR ALUNOS
    echo "<div class='section'>
        <h2>👨‍🎓 Verificação de Alunos</h2>";
    
    $alunos = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $total_alunos = $alunos ? $alunos->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Total de alunos: $total_alunos</p>";
    
    if ($total_alunos > 0) {
        $alunos_detalhes = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id LIMIT 5");
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $alunos_detalhes->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    }
    
    if ($total_alunos < 3) {
        echo "<p class='warning'>⚠️ Poucos alunos encontrados. Criando mais alunos...</p>";
        
        // Criar alunos
        $alunos_novos = [
            ['nome' => 'João Silva', 'email' => 'joao.silva@email.com'],
            ['nome' => 'Maria Santos', 'email' => 'maria.santos@email.com'],
            ['nome' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@email.com'],
            ['nome' => 'Ana Costa', 'email' => 'ana.costa@email.com'],
            ['nome' => 'Carlos Mendes', 'email' => 'carlos.mendes@email.com']
        ];
        
        $criados = 0;
        foreach ($alunos_novos as $aluno) {
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
            }
        }
        echo "<p class='success'>🎉 $criados alunos criados!</p>";
    }
    echo "</div>";

    // 3. VERIFICAR CURSOS
    echo "<div class='section'>
        <h2>📚 Verificação de Cursos</h2>";
    
    $cursos = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $total_cursos = $cursos ? $cursos->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Total de cursos ativos: $total_cursos</p>";
    
    if ($total_cursos > 0) {
        $cursos_detalhes = $conn->query("SELECT id, nome, carga_horaria, status FROM cursos WHERE status = 'ativo' ORDER BY id LIMIT 5");
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
        
        while ($row = $cursos_detalhes->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['carga_horaria']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    }
    
    if ($total_cursos < 3) {
        echo "<p class='warning'>⚠️ Poucos cursos encontrados. Criando mais cursos...</p>";
        
        // Criar cursos
        $cursos_novos = [
            ['nome' => 'Programação Web', 'carga_horaria' => '40 horas'],
            ['nome' => 'Design Gráfico', 'carga_horaria' => '30 horas'],
            ['nome' => 'Marketing Digital', 'carga_horaria' => '35 horas'],
            ['nome' => 'Gestão de Projetos', 'carga_horaria' => '45 horas'],
            ['nome' => 'Inglês Técnico', 'carga_horaria' => '25 horas']
        ];
        
        $criados = 0;
        foreach ($cursos_novos as $curso) {
            $check = $conn->prepare("SELECT id FROM cursos WHERE nome = ?");
            $check->bind_param("s", $curso['nome']);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO cursos (nome, descricao, carga_horaria, preco, status, data_criacao) VALUES (?, ?, ?, ?, 'ativo', NOW())";
                $stmt = $conn->prepare($sql);
                $descricao = "Curso de " . $curso['nome'];
                $preco = 299.99;
                $stmt->bind_param("sssd", $curso['nome'], $descricao, $curso['carga_horaria'], $preco);
                
                if ($stmt->execute()) {
                    $criados++;
                    echo "<p class='success'>✅ Curso criado: {$curso['nome']}</p>";
                }
            }
        }
        echo "<p class='success'>🎉 $criados cursos criados!</p>";
    }
    echo "</div>";

    // 4. VERIFICAR PROFESSORES
    echo "<div class='section'>
        <h2>👨‍🏫 Verificação de Professores</h2>";
    
    $professores = $conn->query("SELECT COUNT(*) as total FROM professores");
    $total_professores = $professores ? $professores->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Total de professores: $total_professores</p>";
    
    if ($total_professores < 2) {
        echo "<p class='warning'>⚠️ Poucos professores encontrados. Criando professores...</p>";
        
        $professores_novos = [
            ['nome' => 'Dr. Carlos Silva', 'email' => 'carlos.silva@instituto.com', 'especialidade' => 'Programação'],
            ['nome' => 'Prof. Ana Santos', 'email' => 'ana.santos@instituto.com', 'especialidade' => 'Design'],
            ['nome' => 'Prof. Roberto Lima', 'email' => 'roberto.lima@instituto.com', 'especialidade' => 'Marketing']
        ];
        
        $criados = 0;
        foreach ($professores_novos as $prof) {
            $check = $conn->prepare("SELECT id FROM professores WHERE email = ?");
            $check->bind_param("s", $prof['email']);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO professores (nome, email, especialidade, data_cadastro) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $prof['nome'], $prof['email'], $prof['especialidade']);
                
                if ($stmt->execute()) {
                    $criados++;
                    echo "<p class='success'>✅ Professor criado: {$prof['nome']}</p>";
                }
            }
        }
        echo "<p class='success'>🎉 $criados professores criados!</p>";
    }
    echo "</div>";

    // 5. VERIFICAÇÃO FINAL
    echo "<div class='section'>
        <h2>🎯 Verificação Final</h2>";
    
    $alunos_final = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'")->fetch_assoc()['total'];
    $cursos_final = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'")->fetch_assoc()['total'];
    $professores_final = $conn->query("SELECT COUNT(*) as total FROM professores")->fetch_assoc()['total'];
    
    echo "<div class='status-ok' style='padding: 15px; border-radius: 8px; margin: 10px 0;'>
        <h3>📊 Resumo Final:</h3>
        <p><strong>Alunos:</strong> $alunos_final</p>
        <p><strong>Cursos Ativos:</strong> $cursos_final</p>
        <p><strong>Professores:</strong> $professores_final</p>
    </div>";
    
    if ($alunos_final >= 3 && $cursos_final >= 3) {
        echo "<p class='success'>🎉 Sistema pronto para certificados!</p>";
    } else {
        echo "<p class='error'>❌ Ainda faltam dados para certificados</p>";
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







