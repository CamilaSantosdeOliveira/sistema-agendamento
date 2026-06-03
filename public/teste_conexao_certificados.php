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
    <title>Teste de Conexão - Certificados</title>
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
            <h1>🔍 Teste de Conexão - Certificados</h1>
            <p>Verificando conexão com banco e API de certificados</p>
        </div>";

try {
    // 1. VERIFICAR CONEXÃO COM BANCO
    echo "<div class='section'>
        <h2>🔌 Teste de Conexão com Banco</h2>";
    
    if ($conn) {
        echo "<p class='success'>✅ Conexão com banco de dados estabelecida!</p>";
        echo "<p class='info'>📊 Banco: sistema_agendamento</p>";
        echo "<p class='info'>🖥️ Servidor: MySQL/MariaDB</p>";
    } else {
        echo "<p class='error'>❌ Erro na conexão com banco de dados!</p>";
    }
    echo "</div>";

    // 2. VERIFICAR TABELA CERTIFICADOS
    echo "<div class='section'>
        <h2>📋 Verificar Tabela Certificados</h2>";
    
    $check_table = $conn->query("SHOW TABLES LIKE 'certificados'");
    if ($check_table && $check_table->num_rows > 0) {
        echo "<p class='success'>✅ Tabela 'certificados' existe!</p>";
        
        // Contar certificados
        $result = $conn->query("SELECT COUNT(*) as total FROM certificados");
        if ($result) {
            $count = $result->fetch_assoc()['total'];
            echo "<p class='info'>📊 Total de certificados: $count</p>";
        }
        
        // Mostrar alguns certificados
        $result = $conn->query("SELECT * FROM certificados LIMIT 5");
        if ($result && $result->num_rows > 0) {
            echo "<table class='data-table'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Status</th>
                        <th>Data Emissão</th>
                    </tr>
                </thead>
                <tbody>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['codigo_verificacao']}</td>
                        <td>{$row['status']}</td>
                        <td>{$row['data_emissao']}</td>
                      </tr>";
            }
            
            echo "</tbody></table>";
        }
    } else {
        echo "<p class='error'>❌ Tabela 'certificados' não existe!</p>";
        echo "<p class='info'>💡 Criando tabela certificados...</p>";
        
        // Criar tabela certificados
        $create_table = "
        CREATE TABLE IF NOT EXISTS certificados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            aluno_id INT NOT NULL,
            curso_id INT NOT NULL,
            codigo_verificacao VARCHAR(50) UNIQUE NOT NULL,
            data_emissao DATE NOT NULL,
            data_conclusao DATE NOT NULL,
            status ENUM('pendente', 'emitido', 'validado', 'revogado') DEFAULT 'pendente',
            carga_horaria INT DEFAULT 0,
            observacoes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (aluno_id) REFERENCES usuarios(id),
            FOREIGN KEY (curso_id) REFERENCES cursos(id)
        )";
        
        if ($conn->query($create_table)) {
            echo "<p class='success'>✅ Tabela 'certificados' criada com sucesso!</p>";
        } else {
            echo "<p class='error'>❌ Erro ao criar tabela: " . $conn->error . "</p>";
        }
    }
    echo "</div>";

    // 3. TESTAR API DE CERTIFICADOS
    echo "<div class='section'>
        <h2>🔧 Teste da API de Certificados</h2>";
    
    // Testar se a API responde
    $api_url = 'api/certificados.php';
    if (file_exists($api_url)) {
        echo "<p class='success'>✅ Arquivo da API encontrado: $api_url</p>";
        
        // Fazer teste de requisição
        $test_data = ['action' => 'test_connection'];
        $post_data = http_build_query($test_data);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $post_data
            ]
        ]);
        
        $response = @file_get_contents($api_url, false, $context);
        
        if ($response !== false) {
            echo "<p class='success'>✅ API respondeu com sucesso!</p>";
            echo "<p class='info'>📄 Resposta: " . substr($response, 0, 100) . "...</p>";
        } else {
            echo "<p class='warning'>⚠️ API não respondeu (pode ser normal para teste)</p>";
        }
    } else {
        echo "<p class='error'>❌ Arquivo da API não encontrado: $api_url</p>";
    }
    echo "</div>";

    // 4. VERIFICAR DADOS NECESSÁRIOS
    echo "<div class='section'>
        <h2>📊 Verificar Dados Necessários</h2>";
    
    // Verificar alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    if ($result) {
        $alunos_count = $result->fetch_assoc()['total'];
        echo "<p class='info'>👨‍🎓 Alunos disponíveis: $alunos_count</p>";
    }
    
    // Verificar cursos
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    if ($result) {
        $cursos_count = $result->fetch_assoc()['total'];
        echo "<p class='info'>📚 Cursos ativos: $cursos_count</p>";
    }
    
    // Verificar inscrições
    $result = $conn->query("SELECT COUNT(*) as total FROM inscricoes");
    if ($result) {
        $inscricoes_count = $result->fetch_assoc()['total'];
        echo "<p class='info'>🎓 Inscrições: $inscricoes_count</p>";
    }
    echo "</div>";

    // 5. TESTE DE FUNCIONALIDADE
    echo "<div class='section'>
        <h2>🧪 Teste de Funcionalidade</h2>";
    
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
        <h2>❌ Erro no Teste</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='certificados.php' class='btn'>📜 Voltar aos Certificados</a>
        <a href='dashboard_final.php' class='btn btn-success'>🏠 Dashboard</a>
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









