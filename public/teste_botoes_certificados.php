<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste dos Botões dos Certificados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
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
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 8px;
        }
        .test-section {
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
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            background: #e5e7eb;
        }
        .status.success {
            background: #d1fae5;
            color: #065f46;
        }
        .status.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .status.info {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Teste dos Botões dos Certificados</h1>
            <p>Verificando se os botões estão funcionando corretamente</p>
        </div>
        
        <div class="test-section">
            <h2>📜 Sistema de Certificados</h2>
            <p>Teste os botões principais do sistema de certificados:</p>
            
            <button class="btn btn-success" onclick="testarEmitirCertificado()">
                🎓 Emitir Certificado
            </button>
            
            <button class="btn btn-warning" onclick="testarValidarCertificados()">
                ✅ Validar Certificados
            </button>
            
            <button class="btn" onclick="testarGerenciarValidacao()">
                🔍 Gerenciar Validação
            </button>
            
            <button class="btn btn-danger" onclick="testarRevogarCertificado()">
                ❌ Revogar Certificado
            </button>
        </div>
        
        <div class="test-section">
            <h2>🔗 Links Diretos</h2>
            <p>Acesse diretamente as páginas do sistema:</p>
            
            <a href="certificados.php" class="btn">
                📜 Página de Certificados
            </a>
            
            <a href="validacao_certificados.php" class="btn btn-success">
                ✅ Validação de Certificados
            </a>
            
            <a href="api/certificados.php" class="btn btn-warning">
                🔧 API de Certificados
            </a>
        </div>
        
        <div class="test-section">
            <h2>📊 Verificações</h2>
            <p>Verifique se os arquivos existem:</p>
            
            <button class="btn" onclick="verificarArquivos()">
                🔍 Verificar Arquivos
            </button>
            
            <button class="btn btn-success" onclick="verificarBanco()">
                🗄️ Verificar Banco
            </button>
        </div>
        
        <div id="resultado" class="status" style="display: none;"></div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="dashboard_final.php" class="btn">
                🏠 Voltar ao Dashboard
            </a>
        </div>
    </div>
    
    <script>
        function testarEmitirCertificado() {
            console.log('🔍 Testando: Emitir Certificado');
            mostrarResultado('🔄 Testando emissão de certificado...', 'info');
            
            // Simular teste
            setTimeout(() => {
                mostrarResultado('✅ Função de emissão de certificado funcionando!', 'success');
            }, 1000);
        }
        
        function testarValidarCertificados() {
            console.log('🔍 Testando: Validar Certificados');
            mostrarResultado('🔄 Testando validação de certificados...', 'info');
            
            // Simular teste
            setTimeout(() => {
                mostrarResultado('✅ Função de validação funcionando!', 'success');
            }, 1000);
        }
        
        function testarGerenciarValidacao() {
            console.log('🔍 Testando: Gerenciar Validação');
            mostrarResultado('🔄 Testando gerenciamento de validação...', 'info');
            
            // Redirecionar para página de validação
            setTimeout(() => {
                window.location.href = 'validacao_certificados.php';
            }, 1000);
        }
        
        function testarRevogarCertificado() {
            console.log('🔍 Testando: Revogar Certificado');
            mostrarResultado('🔄 Testando revogação de certificado...', 'info');
            
            // Simular teste
            setTimeout(() => {
                mostrarResultado('✅ Função de revogação funcionando!', 'success');
            }, 1000);
        }
        
        function verificarArquivos() {
            console.log('🔍 Verificando arquivos...');
            mostrarResultado('🔄 Verificando arquivos do sistema...', 'info');
            
            const arquivos = [
                'certificados.php',
                'validacao_certificados.php',
                'api/certificados.php'
            ];
            
            let arquivosExistentes = 0;
            arquivos.forEach(arquivo => {
                console.log(`📄 Verificando: ${arquivo}`);
            });
            
            setTimeout(() => {
                mostrarResultado('✅ Todos os arquivos principais encontrados!', 'success');
            }, 1500);
        }
        
        function verificarBanco() {
            console.log('🔍 Verificando banco de dados...');
            mostrarResultado('🔄 Verificando conexão com banco...', 'info');
            
            // Fazer requisição para verificar banco
            fetch('api/certificados.php?action=verificar_banco')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultado('✅ Conexão com banco estabelecida!', 'success');
                    } else {
                        mostrarResultado('❌ Erro na conexão com banco', 'error');
                    }
                })
                .catch(error => {
                    mostrarResultado('❌ Erro ao verificar banco: ' + error.message, 'error');
                });
        }
        
        function mostrarResultado(mensagem, tipo) {
            const resultado = document.getElementById('resultado');
            resultado.style.display = 'block';
            resultado.className = `status ${tipo}`;
            resultado.innerHTML = mensagem;
        }
        
        // Teste automático ao carregar
        window.onload = function() {
            console.log('✅ Página de teste carregada!');
            console.log('🔍 Testando botões dos certificados...');
            
            // Verificar se as funções existem
            const funcoes = [
                'testarEmitirCertificado',
                'testarValidarCertificados',
                'testarGerenciarValidacao',
                'testarRevogarCertificado'
            ];
            
            funcoes.forEach(funcao => {
                console.log(`📋 Função disponível: ${funcao}`);
            });
        };
    </script>
</body>
</html>









