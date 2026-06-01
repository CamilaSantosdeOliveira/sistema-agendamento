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
    <title>Criar Dados de Certificados</title>
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
            <h1>🎓 Criar Dados de Certificados</h1>
            <p>Adicionando certificados de exemplo ao sistema</p>
        </div>";

try {
    // 1. VERIFICAR DADOS EXISTENTES
    echo "<div class='section'>
        <h2>📊 Verificar Dados Existentes</h2>";
    
    // Verificar certificados existentes
    $result = $conn->query("SELECT COUNT(*) as total FROM certificados");
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "<p class='info'>📋 Certificados existentes: $count</p>";
    }
    
    // Verificar alunos
    $result = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<p class='success'>✅ Alunos disponíveis:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    }
    
    // Verificar cursos
    $result = $conn->query("SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<p class='success'>✅ Cursos disponíveis:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Carga Horária</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['carga_horaria']}h</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    }
    echo "</div>";

    // 2. CRIAR CERTIFICADOS DE EXEMPLO
    echo "<div class='section'>
        <h2>🎓 Criar Certificados de Exemplo</h2>";
    
    // Função para gerar código de verificação
    function gerarCodigoVerificacao() {
        return 'CERT-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . date('Y');
    }
    
    // Buscar alunos e cursos para criar certificados
    $alunos = $conn->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' LIMIT 5");
    $cursos = $conn->query("SELECT id, nome, carga_horaria FROM cursos WHERE status = 'ativo' LIMIT 5");
    
    if ($alunos && $cursos && $alunos->num_rows > 0 && $cursos->num_rows > 0) {
        $alunos_array = [];
        $cursos_array = [];
        
        while ($aluno = $alunos->fetch_assoc()) {
            $alunos_array[] = $aluno;
        }
        
        while ($curso = $cursos->fetch_assoc()) {
            $cursos_array[] = $curso;
        }
        
        // Criar certificados
        $certificados_criados = 0;
        $status_options = ['pendente', 'emitido', 'validado'];
        
        for ($i = 0; $i < min(5, count($alunos_array), count($cursos_array)); $i++) {
            $aluno = $alunos_array[$i];
            $curso = $cursos_array[$i];
            $status = $status_options[$i % count($status_options)];
            
            $codigo = gerarCodigoVerificacao();
            $data_emissao = date('Y-m-d', strtotime('-' . rand(1, 30) . ' days'));
            $data_conclusao = date('Y-m-d', strtotime('-' . rand(1, 15) . ' days'));
            
            $sql = "INSERT INTO certificados (aluno_id, curso_id, codigo_verificacao, data_emissao, data_conclusao, status, carga_horaria, observacoes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $observacoes = "Certificado gerado automaticamente para teste do sistema.";
            $stmt->bind_param("iisssss", 
                $aluno['id'], 
                $curso['id'], 
                $codigo, 
                $data_emissao, 
                $data_conclusao, 
                $status, 
                $curso['carga_horaria'],
                $observacoes
            );
            
            if ($stmt->execute()) {
                $certificados_criados++;
                echo "<p class='success'>✅ Certificado criado: {$aluno['nome']} - {$curso['nome']} ({$status})</p>";
            } else {
                echo "<p class='error'>❌ Erro ao criar certificado: " . $stmt->error . "</p>";
            }
        }
        
        echo "<p class='info'>📊 Total de certificados criados: $certificados_criados</p>";
    } else {
        echo "<p class='error'>❌ Não há alunos ou cursos suficientes para criar certificados</p>";
    }
    echo "</div>";

    // 3. VERIFICAR RESULTADO
    echo "<div class='section'>
        <h2>📋 Verificar Certificados Criados</h2>";
    
    $result = $conn->query("
        SELECT 
            c.id,
            c.codigo_verificacao,
            c.status,
            c.data_emissao,
            c.data_conclusao,
            c.carga_horaria,
            u.nome as aluno_nome,
            cur.nome as curso_nome
        FROM certificados c
        JOIN usuarios u ON c.aluno_id = u.id
        JOIN cursos cur ON c.curso_id = cur.id
        ORDER BY c.id DESC
        LIMIT 10
    ");
    
    if ($result && $result->num_rows > 0) {
        echo "<p class='success'>✅ Certificados no sistema:</p>";
        echo "<table class='data-table'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Aluno</th>
                    <th>Curso</th>
                    <th>Status</th>
                    <th>Emissão</th>
                    <th>Conclusão</th>
                    <th>Carga</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $result->fetch_assoc()) {
            $status_color = '';
            switch($row['status']) {
                case 'pendente': $status_color = 'color: #f59e0b;'; break;
                case 'emitido': $status_color = 'color: #3b82f6;'; break;
                case 'validado': $status_color = 'color: #10b981;'; break;
                case 'revogado': $status_color = 'color: #ef4444;'; break;
            }
            
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td><strong>{$row['codigo_verificacao']}</strong></td>
                    <td>{$row['aluno_nome']}</td>
                    <td>{$row['curso_nome']}</td>
                    <td style='$status_color'><strong>{$row['status']}</strong></td>
                    <td>" . date('d/m/Y', strtotime($row['data_emissao'])) . "</td>
                    <td>" . date('d/m/Y', strtotime($row['data_conclusao'])) . "</td>
                    <td>{$row['carga_horaria']}h</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "<p class='warning'>⚠️ Nenhum certificado encontrado</p>";
    }
    echo "</div>";

    // 4. TESTAR FUNCIONALIDADE
    echo "<div class='section'>
        <h2>🧪 Testar Funcionalidade</h2>";
    
    echo "<button class='btn btn-success' onclick='testarCarregarCertificados()'>
            🔄 Testar Carregar Certificados
          </button>
          
          <button class='btn btn-warning' onclick='testarEmitirCertificado()'>
            🎓 Testar Emitir Certificado
          </button>
          
          <button class='btn' onclick='testarValidarCertificado()'>
            ✅ Testar Validar Certificado
          </button>";
    
    echo "<div id='resultado' style='margin-top: 20px; padding: 15px; border-radius: 8px; display: none;'></div>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Processo</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='certificados.php' class='btn btn-success'>📜 Ir para Certificados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>

<script>
    function testarCarregarCertificados() {
        const resultado = document.getElementById('resultado');
        resultado.style.display = 'block';
        resultado.style.background = '#dbeafe';
        resultado.style.color = '#1e40af';
        resultado.innerHTML = '🔄 Testando carregamento de certificados...';
        
        fetch('api/certificados.php?action=listar')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultado.style.background = '#d1fae5';
                    resultado.style.color = '#065f46';
                    resultado.innerHTML = '✅ Certificados carregados com sucesso! Total: ' + (data.data ? data.data.length : 0);
                } else {
                    resultado.style.background = '#fee2e2';
                    resultado.style.color = '#991b1b';
                    resultado.innerHTML = '❌ Erro ao carregar certificados: ' + data.message;
                }
            })
            .catch(error => {
                resultado.style.background = '#fee2e2';
                resultado.style.color = '#991b1b';
                resultado.innerHTML = '❌ Erro de conexão: ' + error.message;
            });
    }
    
    function testarEmitirCertificado() {
        const resultado = document.getElementById('resultado');
        resultado.style.display = 'block';
        resultado.style.background = '#dbeafe';
        resultado.style.color = '#1e40af';
        resultado.innerHTML = '🔄 Testando emissão de certificado...';
        
        fetch('api/certificados.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'emitir',
                aluno_id: 1,
                curso_id: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultado.style.background = '#d1fae5';
                resultado.style.color = '#065f46';
                resultado.innerHTML = '✅ Certificado emitido com sucesso!';
            } else {
                resultado.style.background = '#fee2e2';
                resultado.style.color = '#991b1b';
                resultado.innerHTML = '❌ Erro ao emitir certificado: ' + data.message;
            }
        })
        .catch(error => {
            resultado.style.background = '#fee2e2';
            resultado.style.color = '#991b1b';
            resultado.innerHTML = '❌ Erro de conexão: ' + error.message;
        });
    }
    
    function testarValidarCertificado() {
        const resultado = document.getElementById('resultado');
        resultado.style.display = 'block';
        resultado.style.background = '#dbeafe';
        resultado.style.color = '#1e40af';
        resultado.innerHTML = '🔄 Testando validação de certificado...';
        
        fetch('api/certificados.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'validar',
                certificado_id: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultado.style.background = '#d1fae5';
                resultado.style.color = '#065f46';
                resultado.innerHTML = '✅ Certificado validado com sucesso!';
            } else {
                resultado.style.background = '#fee2e2';
                resultado.style.color = '#991b1b';
                resultado.innerHTML = '❌ Erro ao validar certificado: ' + data.message;
            }
        })
        .catch(error => {
            resultado.style.background = '#fee2e2';
            resultado.style.color = '#991b1b';
            resultado.innerHTML = '❌ Erro de conexão: ' + error.message;
        });
    }
</script>
</body>
</html>";
?>







