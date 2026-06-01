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
    <title>Corrigir Dados - Sem Apagar Estrutura</title>
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
            <h1>🔧 Corrigir Dados - Sem Apagar Estrutura</h1>
            <p>Apenas corrigindo dados problemáticos</p>
        </div>";

try {
    // 1. CORRIGIR DADOS PROBLEMÁTICOS DOS ALUNOS
    echo "<div class='section'>
        <h2>👨‍🎓 Corrigindo Dados dos Alunos</h2>";
    
    // Corrigir nomes e emails inválidos
    $correcoes = [
        ['id' => 7, 'nome' => 'Camila Silva', 'email' => 'camila.silva@email.com'],
        ['id' => 12, 'nome' => 'Carlos Mendes', 'email' => 'carlos.mendes@email.com'],
        ['id' => 4, 'nome' => 'João Silva', 'email' => 'joao.silva@email.com'],
        ['id' => 8, 'nome' => 'Maria Santos', 'email' => 'maria.santos@email.com']
    ];
    
    $corrigidos = 0;
    foreach ($correcoes as $correcao) {
        $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ? AND tipo_usuario = 'aluno'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $correcao['nome'], $correcao['email'], $correcao['id']);
        
        if ($stmt->execute()) {
            $corrigidos++;
            echo "<p class='success'>✅ Corrigido: ID {$correcao['id']} -> {$correcao['nome']}</p>";
        }
    }
    
    // Remover duplicatas (manter apenas o primeiro)
    $duplicatas = $conn->query("
        DELETE u1 FROM usuarios u1
        INNER JOIN usuarios u2 
        WHERE u1.id > u2.id 
        AND u1.nome = u2.nome 
        AND u1.tipo_usuario = 'aluno' 
        AND u2.tipo_usuario = 'aluno'
    ");
    
    if ($duplicatas) {
        echo "<p class='success'>✅ Duplicatas removidas</p>";
    }
    
    echo "<p class='success'>🎉 $corrigidos alunos corrigidos!</p>";
    echo "</div>";

    // 2. VERIFICAR E CORRIGIR CURSOS
    echo "<div class='section'>
        <h2>📚 Verificando Cursos</h2>";
    
    $cursos = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $total_cursos = $cursos ? $cursos->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Cursos ativos: $total_cursos</p>";
    
    if ($total_cursos < 3) {
        echo "<p class='warning'>⚠️ Poucos cursos. Adicionando cursos válidos...</p>";
        
        $cursos_novos = [
            ['nome' => 'Programação Web', 'carga_horaria' => '40 horas'],
            ['nome' => 'Design Gráfico', 'carga_horaria' => '30 horas'],
            ['nome' => 'Marketing Digital', 'carga_horaria' => '35 horas']
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
        echo "<p class='success'>🎉 $criados cursos adicionados!</p>";
    }
    echo "</div>";

    // 3. VERIFICAÇÃO FINAL
    echo "<div class='section'>
        <h2>🎯 Verificação Final</h2>";
    
    $alunos_final = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'")->fetch_assoc()['total'];
    $cursos_final = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'")->fetch_assoc()['total'];
    
    echo "<div style='background: #dcfce7; padding: 15px; border-radius: 8px; margin: 10px 0;'>
        <h3>📊 Dados Finais:</h3>
        <p><strong>Alunos:</strong> $alunos_final</p>
        <p><strong>Cursos Ativos:</strong> $cursos_final</p>
    </div>";
    
    if ($alunos_final >= 3 && $cursos_final >= 3) {
        echo "<p class='success'>🎉 Sistema pronto para certificados!</p>";
        
        // Mostrar alunos disponíveis
        $alunos_disponiveis = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY id");
        echo "<h4>👨‍🎓 Alunos Disponíveis:</h4>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $alunos_disponiveis->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
        
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







