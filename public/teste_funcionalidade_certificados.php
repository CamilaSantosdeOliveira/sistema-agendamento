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
    <title>Teste de Funcionalidade - Certificados</title>
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
        .btn-warning {
            background: #f59e0b;
        }
        .btn-warning:hover {
            background: #d97706;
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
        .test-result {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .test-result.success {
            background: #d1fae5;
            color: #065f46;
        }
        .test-result.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .test-result.info {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🧪 Teste de Funcionalidade - Certificados</h1>
            <p>Testando todas as funcionalidades do sistema de certificados</p>
        </div>";

try {
    // 1. VERIFICAR DADOS ATUAIS
    echo "<div class='section'>
        <h2>📊 Status Atual do Sistema</h2>";
    
    // Contar certificados
    $result = $conn->query("SELECT COUNT(*) as total FROM certificados");
    $total_certificados = $result ? $result->fetch_assoc()['total'] : 0;
    
    // Contar por status
    $result = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
            SUM(CASE WHEN status = 'emitido' THEN 1 ELSE 0 END) as emitidos,
            SUM(CASE WHEN status = 'validado' THEN 1 ELSE 0 END) as validados,
            SUM(CASE WHEN status = 'revogado' THEN 1 ELSE 0 END) as revogados
        FROM certificados
    ");
    
    $stats = $result ? $result->fetch_assoc() : [];
    
    echo "<p class='info'>📋 Total de certificados: $total_certificados</p>";
    if ($stats) {
        echo "<p class='info'>⏳ Pendentes: {$stats['pendentes']}</p>";
        echo "<p class='info'>📄 Emitidos: {$stats['emitidos']}</p>";
        echo "<p class='info'>✅ Validados: {$stats['validados']}</p>";
        echo "<p class='info'>❌ Revogados: {$stats['revogados']}</p>";
    }
    echo "</div>";

    // 2. TESTAR FUNCIONALIDADES
    echo "<div class='section'>
        <h2>🔧 Testar Funcionalidades</h2>";
    
    echo "<button class='btn btn-success' onclick='testarListarCertificados()'>
            📋 Listar Certificados
          </button>
          
          <button class='btn btn-warning' onclick='testarEmitirCertificado()'>
            🎓 Emitir Certificado
          </button>
          
          <button class='btn' onclick='testarValidarCertificado()'>
            ✅ Validar Certificado
          </button>
          
          <button class='btn btn-danger' onclick='testarRevogarCertificado()'>
            ❌ Revogar Certificado
          </button>
          
          <button class='btn btn-success' onclick='testarVerCertificado()'>
            👁️ Ver Certificado
          </button>
          
          <button class='btn btn-warning' onclick='testarBaixarCertificado()'>
            📥 Baixar Certificado
          </button>";
    
    echo "<div id='resultado' class='test-result' style='display: none;'></div>";
    echo "</div>";

    // 3. MOSTRAR CERTIFICADOS EXISTENTES
    echo "<div class='section'>
        <h2>📋 Certificados Existentes</h2>";
    
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
                    <th>Ações</th>
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
                    <td>
                        <button class='btn' onclick='testarAcao({$row['id']}, \"ver\")'>👁️</button>
                        <button class='btn btn-warning' onclick='testarAcao({$row['id']}, \"validar\")'>✅</button>
                        <button class='btn btn-danger' onclick='testarAcao({$row['id']}, \"revogar\")'>❌</button>
                    </td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "<p class='warning'>⚠️ Nenhum certificado encontrado. Execute o script de criação de dados primeiro.</p>";
    }
    echo "</div>";

    // 4. TESTAR DADOS NECESSÁRIOS
    echo "<div class='section'>
        <h2>📊 Dados Necessários</h2>";
    
    // Verificar alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    // Verificar cursos
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $cursos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>👨‍🎓 Alunos disponíveis: $alunos_count</p>";
    echo "<p class='info'>📚 Cursos ativos: $cursos_count</p>";
    
    if ($alunos_count == 0 || $cursos_count == 0) {
        echo "<p class='error'>❌ É necessário ter alunos e cursos para criar certificados!</p>";
    } else {
        echo "<p class='success'>✅ Dados suficientes para criar certificados!</p>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Teste</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='certificados.php' class='btn btn-success'>📜 Ir para Certificados</a>
        <a href='criar_dados_certificados.php' class='btn btn-warning'>🎓 Criar Dados</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>

<script>
    function mostrarResultado(mensagem, tipo) {
        const resultado = document.getElementById('resultado');
        resultado.style.display = 'block';
        resultado.className = `test-result ${tipo}`;
        resultado.innerHTML = mensagem;
    }
    
    function testarListarCertificados() {
        mostrarResultado('🔄 Testando listagem de certificados...', 'info');
        
        fetch('api/certificados.php?action=listar_certificados')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado(`✅ Certificados listados com sucesso! Total: ${data.data ? data.data.length : 0}`, 'success');
                } else {
                    mostrarResultado(`❌ Erro ao listar certificados: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                mostrarResultado(`❌ Erro de conexão: ${error.message}`, 'error');
            });
    }
    
    function testarEmitirCertificado() {
        mostrarResultado('🔄 Testando emissão de certificado...', 'info');
        
        fetch('api/certificados.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'emitir_certificado'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarResultado(`✅ ${data.message}`, 'success');
            } else {
                mostrarResultado(`❌ Erro ao emitir certificado: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            mostrarResultado(`❌ Erro de conexão: ${error.message}`, 'error');
        });
    }
    
    function testarValidarCertificado() {
        mostrarResultado('🔄 Testando validação de certificado...', 'info');
        
        fetch('api/certificados.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'validar_certificado',
                id: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarResultado(`✅ ${data.message}`, 'success');
            } else {
                mostrarResultado(`❌ Erro ao validar certificado: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            mostrarResultado(`❌ Erro de conexão: ${error.message}`, 'error');
        });
    }
    
    function testarRevogarCertificado() {
        mostrarResultado('🔄 Testando revogação de certificado...', 'info');
        
        fetch('api/certificados.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'revogar_certificado',
                id: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarResultado(`✅ ${data.message}`, 'success');
            } else {
                mostrarResultado(`❌ Erro ao revogar certificado: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            mostrarResultado(`❌ Erro de conexão: ${error.message}`, 'error');
        });
    }
    
    function testarVerCertificado() {
        mostrarResultado('🔄 Testando visualização de certificado...', 'info');
        
        fetch('api/certificados.php?action=ver_certificado&id=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado(`✅ Certificado visualizado: ${data.data.aluno_nome} - ${data.data.curso_nome}`, 'success');
                } else {
                    mostrarResultado(`❌ Erro ao visualizar certificado: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                mostrarResultado(`❌ Erro de conexão: ${error.message}`, 'error');
            });
    }
    
    function testarBaixarCertificado() {
        mostrarResultado('🔄 Testando download de certificado...', 'info');
        
        // Abrir em nova aba para download
        window.open('api/certificados.php?action=baixar_certificado&id=1', '_blank');
        mostrarResultado('✅ Download iniciado em nova aba', 'success');
    }
    
    function testarAcao(id, acao) {
        mostrarResultado(`🔄 Testando ${acao} do certificado ${id}...`, 'info');
        
        let url = '';
        let method = 'GET';
        let body = null;
        
        switch(acao) {
            case 'ver':
                url = `api/certificados.php?action=ver_certificado&id=${id}`;
                break;
            case 'validar':
                url = 'api/certificados.php';
                method = 'POST';
                body = JSON.stringify({
                    action: 'validar_certificado',
                    id: id
                });
                break;
            case 'revogar':
                url = 'api/certificados.php';
                method = 'POST';
                body = JSON.stringify({
                    action: 'revogar_certificado',
                    id: id
                });
                break;
        }
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };
        
        if (body) {
            options.body = body;
        }
        
        fetch(url, options)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado(`✅ ${data.message || 'Ação executada com sucesso!'}`, 'success');
                    // Recarregar página após 2 segundos
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarResultado(`❌ Erro: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                mostrarResultado(`❌ Erro de conexão: ${error.message}`, 'error');
            });
    }
</script>
</body>
</html>";
?>







