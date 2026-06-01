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
    <title>Criar Cursos Necessários</title>
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
            <h1>📚 Criar Cursos Necessários</h1>
            <p>Adicionando cursos para o sistema de certificados</p>
        </div>";

try {
    // 1. VERIFICAR CURSOS EXISTENTES
    echo "<div class='section'>
        <h2>📊 Verificação de Cursos Existentes</h2>";
    
    $cursos_existentes = $conn->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $cursos_existentes ? $cursos_existentes->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Cursos existentes: $total_cursos</p>";
    
    if ($total_cursos > 0) {
        $cursos = $conn->query("SELECT id, nome, status FROM cursos ORDER BY id");
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $cursos->fetch_assoc()) {
            $status_class = ($row['status'] == 'ativo') ? 'success' : 'error';
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td class='$status_class'>{$row['status']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    }
    echo "</div>";

    // 2. CRIAR CURSOS NECESSÁRIOS
    echo "<div class='section'>
        <h2>✨ Criando Cursos</h2>";
    
    $cursos_para_criar = [
        [
            'nome' => 'Programação Web',
            'descricao' => 'Curso completo de desenvolvimento web com HTML, CSS, JavaScript e PHP',
            'carga_horaria' => '40 horas',
            'preco' => 299.99
        ],
        [
            'nome' => 'Design Gráfico',
            'descricao' => 'Aprenda design gráfico com ferramentas profissionais',
            'carga_horaria' => '30 horas',
            'preco' => 249.99
        ],
        [
            'nome' => 'Marketing Digital',
            'descricao' => 'Estratégias de marketing digital e redes sociais',
            'carga_horaria' => '35 horas',
            'preco' => 279.99
        ],
        [
            'nome' => 'Gestão de Projetos',
            'descricao' => 'Metodologias ágeis e gestão eficiente de projetos',
            'carga_horaria' => '45 horas',
            'preco' => 349.99
        ],
        [
            'nome' => 'Inglês Técnico',
            'descricao' => 'Inglês focado em tecnologia e negócios',
            'carga_horaria' => '25 horas',
            'preco' => 199.99
        ]
    ];
    
    $criados = 0;
    foreach ($cursos_para_criar as $curso) {
        // Verificar se já existe
        $check = $conn->prepare("SELECT id FROM cursos WHERE nome = ?");
        $check->bind_param("s", $curso['nome']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows == 0) {
            $sql = "INSERT INTO cursos (nome, descricao, carga_horaria, preco, status, data_criacao) VALUES (?, ?, ?, ?, 'ativo', NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssd", $curso['nome'], $curso['descricao'], $curso['carga_horaria'], $curso['preco']);
            
            if ($stmt->execute()) {
                $criados++;
                echo "<p class='success'>✅ Curso criado: {$curso['nome']} - {$curso['carga_horaria']}</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar: {$curso['nome']}</p>";
            }
        } else {
            echo "<p class='info'>ℹ️ Curso já existe: {$curso['nome']}</p>";
        }
    }
    
    echo "<p class='success'>🎉 Total de cursos criados: $criados</p>";
    echo "</div>";

    // 3. VERIFICAÇÃO FINAL
    echo "<div class='section'>
        <h2>🎯 Verificação Final</h2>";
    
    $cursos_finais = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $total_final = $cursos_finais ? $cursos_finais->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>📊 Cursos ativos após criação: $total_final</p>";
    
    if ($total_final >= 3) {
        echo "<p class='success'>🎉 Sistema PRONTO para certificados!</p>";
        
        // Mostrar cursos criados
        $cursos_lista = $conn->query("SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' ORDER BY id");
        echo "<h3>📚 Cursos Disponíveis:</h3>";
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
        echo "<p class='error'>❌ Ainda faltam cursos para certificados</p>";
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







