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
    <title>Criar Certificados - Versão Corrigida</title>
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
            <h1>🎓 Criar Certificados - Versão Corrigida</h1>
            <p>Criando certificados com a estrutura correta da tabela</p>
        </div>";

try {
    // Função para gerar código de verificação
    function gerarCodigoVerificacao() {
        return 'CERT-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . date('Y');
    }
    
    // Buscar alunos e cursos (CORRIGIDO - sem carga_horaria)
    $alunos = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 3");
    $cursos = $conn->query("SELECT id, nome FROM cursos WHERE status = 'ativo' LIMIT 3");
    
    if ($alunos && $cursos && $alunos->num_rows > 0 && $cursos->num_rows > 0) {
        $alunos_array = [];
        $cursos_array = [];
        
        while ($aluno = $alunos->fetch_assoc()) {
            $alunos_array[] = $aluno;
        }
        
        while ($curso = $cursos->fetch_assoc()) {
            $cursos_array[] = $curso;
        }
        
        echo "<div class='section'>
            <h2>📊 Dados Encontrados</h2>
            <p class='success'>✅ Alunos: " . count($alunos_array) . "</p>
            <p class='success'>✅ Cursos: " . count($cursos_array) . "</p>
        </div>";
        
        // Criar certificados
        $certificados_criados = 0;
        $status_options = ['pendente', 'emitido', 'validado'];
        
        for ($i = 0; $i < min(3, count($alunos_array), count($cursos_array)); $i++) {
            $aluno = $alunos_array[$i];
            $curso = $cursos_array[$i];
            $status = $status_options[$i % count($status_options)];
            
            $codigo = gerarCodigoVerificacao();
            $data_emissao = date('Y-m-d', strtotime('-' . rand(1, 30) . ' days'));
            $data_conclusao = date('Y-m-d', strtotime('-' . rand(1, 15) . ' days'));
            
            // CORRIGIDO - sem carga_horaria e com valores padrão
            $sql = "INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao, status, carga_horaria, observacoes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $observacoes = "Certificado de teste criado automaticamente.";
            $carga_horaria = 40; // Valor padrão
            $stmt->bind_param("iisssss", 
                $aluno['id'], 
                $curso['id'], 
                $codigo, 
                $data_emissao, 
                $data_conclusao, 
                $status, 
                $carga_horaria,
                $observacoes
            );
            
            if ($stmt->execute()) {
                $certificados_criados++;
                echo "<div class='section'>
                    <h3>✅ Certificado Criado #$certificados_criados</h3>
                    <p><strong>Aluno:</strong> {$aluno['nome']}</p>
                    <p><strong>Curso:</strong> {$curso['nome']}</p>
                    <p><strong>Código:</strong> {$codigo}</p>
                    <p><strong>Status:</strong> {$status}</p>
                    <p><strong>Data Emissão:</strong> " . date('d/m/Y', strtotime($data_emissao)) . "</p>
                    <p><strong>Data Conclusão:</strong> " . date('d/m/Y', strtotime($data_conclusao)) . "</p>
                    <p><strong>Carga Horária:</strong> {$carga_horaria} horas</p>
                </div>";
            } else {
                echo "<div class='section'>
                    <h3>❌ Erro ao Criar Certificado</h3>
                    <p class='error'>Erro: " . $stmt->error . "</p>
                </div>";
            }
        }
        
        echo "<div class='section'>
            <h2>🎉 Resumo</h2>
            <p class='success'>✅ Total de certificados criados: $certificados_criados</p>
            <p class='info'>📋 Agora você pode testar o sistema de certificados!</p>
        </div>";
        
    } else {
        echo "<div class='section'>
            <h2>❌ Erro</h2>";
        
        if (!$alunos || $alunos->num_rows == 0) {
            echo "<p class='error'>❌ Nenhum aluno encontrado!</p>";
        }
        
        if (!$cursos || $cursos->num_rows == 0) {
            echo "<p class='error'>❌ Nenhum curso encontrado!</p>";
        }
        
        echo "<p class='info'>Certifique-se de que existem alunos e cursos ativos no sistema.</p>
        </div>";
    }

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='certificados.php' class='btn btn-success'>📜 Ver Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>
</body>
</html>";
?>









