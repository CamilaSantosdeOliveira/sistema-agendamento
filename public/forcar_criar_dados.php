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
    <title>Forçar Criação de Dados</title>
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
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🚨 Forçar Criação de Dados</h1>
            <p>Criando dados essenciais para o sistema funcionar</p>
        </div>";

try {
    $dados_criados = 0;
    
    // 1. LIMPAR DADOS EXISTENTES (se necessário)
    echo "<div class='section'>
        <h2>🧹 Limpeza Inicial</h2>";
    
    // Verificar se existem dados
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_existentes = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $cursos_existentes = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Alunos existentes: $alunos_existentes</p>";
    echo "<p class='info'>📊 Cursos existentes: $cursos_existentes</p>";
    
    if ($alunos_existentes == 0 || $cursos_existentes == 0) {
        echo "<p class='warning'>⚠️ Dados insuficientes detectados. Criando dados mínimos...</p>";
    } else {
        echo "<p class='success'>✅ Dados suficientes encontrados!</p>";
    }
    echo "</div>";

    // 2. CRIAR ALUNOS (forçar criação)
    echo "<div class='section'>
        <h2>👨‍🎓 Criando Alunos</h2>";
    
    // Alunos de exemplo
    $alunos = [
        ['nome' => 'João Silva', 'email' => 'joao.silva@email.com', 'senha' => '123456'],
        ['nome' => 'Maria Santos', 'email' => 'maria.santos@email.com', 'senha' => '123456'],
        ['nome' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@email.com', 'senha' => '123456'],
        ['nome' => 'Ana Costa', 'email' => 'ana.costa@email.com', 'senha' => '123456'],
        ['nome' => 'Carlos Mendes', 'email' => 'carlos.mendes@email.com', 'senha' => '123456']
    ];
    
    foreach ($alunos as $aluno) {
        // Verificar se já existe
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->bind_param("s", $aluno['email']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows == 0) {
            $senha_hash = password_hash($aluno['senha'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, data_cadastro) VALUES (?, ?, ?, 'aluno', NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $aluno['nome'], $aluno['email'], $senha_hash);
            
            if ($stmt->execute()) {
                $dados_criados++;
                echo "<p class='success'>✅ Aluno criado: {$aluno['nome']}</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar aluno: {$aluno['nome']} - {$stmt->error}</p>";
            }
        } else {
            echo "<p class='info'>ℹ️ Aluno já existe: {$aluno['nome']}</p>";
        }
    }
    echo "</div>";

    // 3. CRIAR CURSOS (forçar criação)
    echo "<div class='section'>
        <h2>📚 Criando Cursos</h2>";
    
    // Cursos de exemplo
    $cursos = [
        ['nome' => 'Programação Web', 'descricao' => 'Curso completo de desenvolvimento web', 'carga_horaria' => 80, 'preco' => 299.99],
        ['nome' => 'Design Gráfico', 'descricao' => 'Fundamentos do design gráfico', 'carga_horaria' => 60, 'preco' => 199.99],
        ['nome' => 'Marketing Digital', 'descricao' => 'Estratégias de marketing online', 'carga_horaria' => 40, 'preco' => 149.99],
        ['nome' => 'Excel Avançado', 'descricao' => 'Domine o Excel para análise de dados', 'carga_horaria' => 30, 'preco' => 99.99],
        ['nome' => 'Inglês Básico', 'descricao' => 'Aprenda inglês do zero', 'carga_horaria' => 50, 'preco' => 179.99]
    ];
    
    foreach ($cursos as $curso) {
        // Verificar se já existe
        $check = $conn->prepare("SELECT id FROM cursos WHERE nome = ?");
        $check->bind_param("s", $curso['nome']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows == 0) {
            $sql = "INSERT INTO cursos (nome, descricao, carga_horaria, preco, status, data_criacao) VALUES (?, ?, ?, ?, 'ativo', NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssds", $curso['nome'], $curso['descricao'], $curso['carga_horaria'], $curso['preco']);
            
            if ($stmt->execute()) {
                $dados_criados++;
                echo "<p class='success'>✅ Curso criado: {$curso['nome']}</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar curso: {$curso['nome']} - {$stmt->error}</p>";
            }
        } else {
            echo "<p class='info'>ℹ️ Curso já existe: {$curso['nome']}</p>";
        }
    }
    echo "</div>";

    // 4. VERIFICAÇÃO FINAL
    echo "<div class='section'>
        <h2>🎯 Verificação Final</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_final = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $cursos_final = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>👨‍🎓 Alunos disponíveis: $alunos_final</p>";
    echo "<p class='info'>📚 Cursos ativos: $cursos_final</p>";
    
    if ($alunos_final > 0 && $cursos_final > 0) {
        echo "<p class='success'>🎉 SUCESSO! Sistema pronto para certificados!</p>";
        echo "<p class='success'>✅ Dados suficientes criados</p>";
        echo "<p class='success'>🚀 Pode prosseguir com a criação de certificados</p>";
    } else {
        echo "<p class='error'>❌ Ainda faltam dados essenciais</p>";
        if ($alunos_final == 0) {
            echo "<p class='error'>❌ Nenhum aluno criado</p>";
        }
        if ($cursos_final == 0) {
            echo "<p class='error'>❌ Nenhum curso criado</p>";
        }
    }
    
    echo "<p class='info'>📊 Total de registros criados nesta execução: $dados_criados</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='adicionar_certificados_teste.php' class='btn btn-success'>🎓 Criar Certificados AGORA</a>
        <a href='verificar_dados_sistema.php' class='btn'>🔍 Verificar Dados</a>
        <a href='certificados.php' class='btn'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>









