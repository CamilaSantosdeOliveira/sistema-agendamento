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
    <title>Criar Dados Mínimos</title>
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
            background: linear-gradient(135deg, #10b981, #059669);
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
            <h1>🔄 Criar Dados Mínimos</h1>
            <p>Criando dados essenciais para o sistema de certificados</p>
        </div>";

try {
    $dados_criados = 0;
    
    // 1. VERIFICAR SE JÁ EXISTEM ALUNOS
    echo "<div class='section'>
        <h2>👨‍🎓 Verificar Alunos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Alunos existentes: $alunos_count</p>";
    
    if ($alunos_count == 0) {
        // Criar alunos mínimos
        $alunos = [
            ['nome' => 'João Silva', 'email' => 'joao.silva@email.com', 'senha' => '123456'],
            ['nome' => 'Maria Santos', 'email' => 'maria.santos@email.com', 'senha' => '123456'],
            ['nome' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@email.com', 'senha' => '123456']
        ];
        
        foreach ($alunos as $aluno) {
            $senha_hash = password_hash($aluno['senha'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, data_cadastro) VALUES (?, ?, ?, 'aluno', NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $aluno['nome'], $aluno['email'], $senha_hash);
            
            if ($stmt->execute()) {
                $dados_criados++;
                echo "<p class='success'>✅ Aluno criado: {$aluno['nome']}</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar aluno: {$aluno['nome']}</p>";
            }
        }
    } else {
        echo "<p class='success'>✅ Alunos já existem no sistema</p>";
    }
    echo "</div>";

    // 2. VERIFICAR SE JÁ EXISTEM CURSOS
    echo "<div class='section'>
        <h2>📚 Verificar Cursos</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $cursos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Cursos ativos existentes: $cursos_count</p>";
    
    if ($cursos_count == 0) {
        // Criar cursos mínimos
        $cursos = [
            ['nome' => 'Programação Web', 'descricao' => 'Curso completo de desenvolvimento web', 'carga_horaria' => 80, 'preco' => 299.99],
            ['nome' => 'Design Gráfico', 'descricao' => 'Fundamentos do design gráfico', 'carga_horaria' => 60, 'preco' => 199.99],
            ['nome' => 'Marketing Digital', 'descricao' => 'Estratégias de marketing online', 'carga_horaria' => 40, 'preco' => 149.99]
        ];
        
        foreach ($cursos as $curso) {
            $sql = "INSERT INTO cursos (nome, descricao, carga_horaria, preco, status, data_criacao) VALUES (?, ?, ?, ?, 'ativo', NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssds", $curso['nome'], $curso['descricao'], $curso['carga_horaria'], $curso['preco']);
            
            if ($stmt->execute()) {
                $dados_criados++;
                echo "<p class='success'>✅ Curso criado: {$curso['nome']}</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar curso: {$curso['nome']}</p>";
            }
        }
    } else {
        echo "<p class='success'>✅ Cursos já existem no sistema</p>";
    }
    echo "</div>";

    // 3. VERIFICAR SE JÁ EXISTEM PROFESSORES
    echo "<div class='section'>
        <h2>👨‍🏫 Verificar Professores</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM professores");
    $professores_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Professores existentes: $professores_count</p>";
    
    if ($professores_count == 0) {
        // Criar professores mínimos
        $professores = [
            ['nome' => 'Dr. Carlos Mendes', 'email' => 'carlos.mendes@instituto.com', 'especialidade' => 'Desenvolvimento Web'],
            ['nome' => 'Profa. Ana Costa', 'email' => 'ana.costa@instituto.com', 'especialidade' => 'Design Gráfico'],
            ['nome' => 'Prof. Roberto Lima', 'email' => 'roberto.lima@instituto.com', 'especialidade' => 'Marketing Digital']
        ];
        
        foreach ($professores as $professor) {
            $sql = "INSERT INTO professores (nome, email, especialidade, data_cadastro) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $professor['nome'], $professor['email'], $professor['especialidade']);
            
            if ($stmt->execute()) {
                $dados_criados++;
                echo "<p class='success'>✅ Professor criado: {$professor['nome']}</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar professor: {$professor['nome']}</p>";
            }
        }
    } else {
        echo "<p class='success'>✅ Professores já existem no sistema</p>";
    }
    echo "</div>";

    // 4. RESUMO FINAL
    echo "<div class='section'>
        <h2>🎯 Resumo da Criação</h2>";
    
    if ($dados_criados > 0) {
        echo "<p class='success'>🎉 Dados criados com sucesso!</p>";
        echo "<p class='info'>📊 Total de registros criados: $dados_criados</p>";
    } else {
        echo "<p class='info'>ℹ️ Todos os dados já existem no sistema</p>";
    }
    
    // Verificar status final
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_final = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $cursos_final = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>👨‍🎓 Alunos disponíveis: $alunos_final</p>";
    echo "<p class='info'>📚 Cursos ativos: $cursos_final</p>";
    
    if ($alunos_final > 0 && $cursos_final > 0) {
        echo "<p class='success'>🚀 Sistema pronto para criar certificados!</p>";
    } else {
        echo "<p class='error'>❌ Ainda faltam dados essenciais</p>";
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
        <a href='verificar_dados_sistema.php' class='btn'>🔍 Verificar Dados</a>
        <a href='status_final_certificados.php' class='btn'>📊 Status Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>









