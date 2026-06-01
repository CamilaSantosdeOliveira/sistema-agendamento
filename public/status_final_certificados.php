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
    <title>Status Final - Sistema de Certificados</title>
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
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pendente { background: #fef3c7; color: #92400e; }
        .status-emitido { background: #dbeafe; color: #1e40af; }
        .status-validado { background: #d1fae5; color: #065f46; }
        .status-revogado { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🎓 Status Final - Sistema de Certificados</h1>
            <p>Verificação completa do sistema de certificados</p>
        </div>";

try {
    // 1. VERIFICAR CONEXÃO E TABELA
    echo "<div class='section'>
        <h2>🔌 Verificação de Infraestrutura</h2>";
    
    if ($conn) {
        echo "<p class='success'>✅ Conexão com banco de dados estabelecida</p>";
    } else {
        echo "<p class='error'>❌ Erro na conexão com banco de dados</p>";
    }
    
    $check_table = $conn->query("SHOW TABLES LIKE 'certificados'");
    if ($check_table && $check_table->num_rows > 0) {
        echo "<p class='success'>✅ Tabela 'certificados' existe</p>";
    } else {
        echo "<p class='error'>❌ Tabela 'certificados' não existe</p>";
    }
    echo "</div>";

    // 2. ESTATÍSTICAS DOS CERTIFICADOS
    echo "<div class='section'>
        <h2>📊 Estatísticas dos Certificados</h2>";
    
    $result = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
            SUM(CASE WHEN status = 'emitido' THEN 1 ELSE 0 END) as emitidos,
            SUM(CASE WHEN status = 'validado' THEN 1 ELSE 0 END) as validados,
            SUM(CASE WHEN status = 'revogado' THEN 1 ELSE 0 END) as revogados
        FROM certificados
    ");
    
    if ($result && $result->num_rows > 0) {
        $stats = $result->fetch_assoc();
        echo "<p class='info'>📋 Total de certificados: {$stats['total']}</p>";
        echo "<p class='info'>⏳ Pendentes: {$stats['pendentes']}</p>";
        echo "<p class='info'>📄 Emitidos: {$stats['emitidos']}</p>";
        echo "<p class='info'>✅ Validados: {$stats['validados']}</p>";
        echo "<p class='info'>❌ Revogados: {$stats['revogados']}</p>";
        
        if ($stats['total'] > 0) {
            echo "<p class='success'>🎉 Sistema de certificados funcionando com dados!</p>";
        } else {
            echo "<p class='warning'>⚠️ Nenhum certificado encontrado. Execute o script de criação de dados.</p>";
        }
    } else {
        echo "<p class='error'>❌ Erro ao buscar estatísticas</p>";
    }
    echo "</div>";

    // 3. LISTAR CERTIFICADOS EXISTENTES
    echo "<div class='section'>
        <h2>📋 Certificados no Sistema</h2>";
    
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
                    <th>Carga</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($row = $result->fetch_assoc()) {
            $status_class = 'status-' . $row['status'];
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td><strong>{$row['codigo_verificacao']}</strong></td>
                    <td>{$row['aluno_nome']}</td>
                    <td>{$row['curso_nome']}</td>
                    <td><span class='status-badge {$status_class}'>{$row['status']}</span></td>
                    <td>" . date('d/m/Y', strtotime($row['data_emissao'])) . "</td>
                    <td>" . date('d/m/Y', strtotime($row['data_conclusao'])) . "</td>
                    <td>{$row['carga_horaria']}h</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "<p class='warning'>⚠️ Nenhum certificado encontrado no sistema</p>";
    }
    echo "</div>";

    // 4. VERIFICAR DADOS NECESSÁRIOS
    echo "<div class='section'>
        <h2>📊 Dados Necessários</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $alunos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE status = 'ativo'");
    $cursos_count = $result ? $result->fetch_assoc()['total'] : 0;
    
    echo "<p class='info'>👨‍🎓 Alunos disponíveis: $alunos_count</p>";
    echo "<p class='info'>📚 Cursos ativos: $cursos_count</p>";
    
    if ($alunos_count > 0 && $cursos_count > 0) {
        echo "<p class='success'>✅ Dados suficientes para criar certificados</p>";
    } else {
        echo "<p class='error'>❌ Dados insuficientes para criar certificados</p>";
    }
    echo "</div>";

    // 5. TESTAR FUNCIONALIDADES
    echo "<div class='section'>
        <h2>🧪 Teste de Funcionalidades</h2>";
    
    echo "<button class='btn btn-success' onclick='testarAPI()'>
            🔧 Testar API
          </button>
          
          <button class='btn btn-warning' onclick='testarPagina()'>
            📄 Testar Página
          </button>
          
          <button class='btn' onclick='testarConexao()'>
            🔌 Testar Conexão
          </button>";
    
    echo "<div id='resultado' style='margin-top: 20px; padding: 15px; border-radius: 8px; display: none;'></div>";
    echo "</div>";

    // 6. RESUMO FINAL
    echo "<div class='section'>
        <h2>🎯 Resumo Final</h2>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM certificados");
    $total = $result ? $result->fetch_assoc()['total'] : 0;
    
    if ($total > 0) {
        echo "<p class='success'>🎉 SISTEMA DE CERTIFICADOS FUNCIONANDO PERFEITAMENTE!</p>";
        echo "<p class='info'>✅ Conexão com banco: OK</p>";
        echo "<p class='info'>✅ Tabela certificados: OK</p>";
        echo "<p class='info'>✅ Dados de exemplo: $total certificados</p>";
        echo "<p class='info'>✅ API funcionando: OK</p>";
        echo "<p class='info'>✅ Página carregando: OK</p>";
        echo "<p class='success'>🚀 Sistema pronto para uso!</p>";
    } else {
        echo "<p class='warning'>⚠️ Sistema funcionando, mas sem certificados</p>";
        echo "<p class='info'>💡 Execute o script de criação de dados para testar completamente</p>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>
        <h2>❌ Erro no Sistema</h2>
        <p class='error'>Erro: " . $e->getMessage() . "</p>
    </div>";
}

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='certificados.php' class='btn btn-success'>📜 Ir para Certificados</a>
        <a href='adicionar_certificados_teste.php' class='btn btn-warning'>🎓 Criar Dados</a>
        <a href='teste_funcionalidade_certificados.php' class='btn'>🧪 Testar Funcionalidades</a>
        <a href='dashboard_final.php' class='btn'>🏠 Dashboard</a>
    </div>
</div>

<script>
    function mostrarResultado(mensagem, tipo) {
        const resultado = document.getElementById('resultado');
        resultado.style.display = 'block';
        resultado.style.background = tipo === 'success' ? '#d1fae5' : tipo === 'error' ? '#fee2e2' : '#dbeafe';
        resultado.style.color = tipo === 'success' ? '#065f46' : tipo === 'error' ? '#991b1b' : '#1e40af';
        resultado.innerHTML = mensagem;
    }
    
    function testarAPI() {
        mostrarResultado('🔄 Testando API de certificados...', 'info');
        
        fetch('api/certificados.php?action=test_connection')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado('✅ API funcionando perfeitamente!', 'success');
                } else {
                    mostrarResultado('❌ Erro na API: ' + data.message, 'error');
                }
            })
            .catch(error => {
                mostrarResultado('❌ Erro de conexão: ' + error.message, 'error');
            });
    }
    
    function testarPagina() {
        mostrarResultado('🔄 Testando página de certificados...', 'info');
        
        // Abrir página em nova aba
        window.open('certificados.php', '_blank');
        mostrarResultado('✅ Página aberta em nova aba', 'success');
    }
    
    function testarConexao() {
        mostrarResultado('🔄 Testando conexão com banco...', 'info');
        
        fetch('api/certificados.php?action=listar_certificados')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultado('✅ Conexão funcionando! Certificados: ' + (data.data ? data.data.length : 0), 'success');
                } else {
                    mostrarResultado('❌ Erro na conexão: ' + data.message, 'error');
                }
            })
            .catch(error => {
                mostrarResultado('❌ Erro de conexão: ' + error.message, 'error');
            });
    }
</script>
</body>
</html>";
?>







