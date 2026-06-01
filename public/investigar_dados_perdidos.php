<?php
// Script para investigar por que os dados sumiram
include 'db.php';

echo "<h1>🔍 INVESTIGAÇÃO: Por que os dados sumiram?</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .section { background: white; margin: 20px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .warning { color: #f59e0b; font-weight: bold; }
    .info { color: #3b82f6; font-weight: bold; }
    .btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
    .btn:hover { background: #2563eb; }
    .btn-danger { background: #ef4444; }
    .btn-success { background: #10b981; }
</style>";

try {
    if (!$conn) {
        throw new Exception("❌ Banco de dados não está disponível");
    }
    
    echo "<div class='section'>";
    echo "<h2>🔍 DIAGNÓSTICO DO PROBLEMA</h2>";
    
    // 1. VERIFICAR CONFIGURAÇÃO DO BANCO
    echo "<h3>1️⃣ Configuração do Banco</h3>";
    echo "<p><strong>Host:</strong> " . $conn->host_info . "</p>";
    echo "<p><strong>Versão MySQL:</strong> " . $conn->server_info . "</p>";
    echo "<p><strong>Banco Atual:</strong> " . $conn->database . "</p>";
    
    // 2. VERIFICAR SE AS TABELAS EXISTEM
    echo "<h3>2️⃣ Verificando Tabelas</h3>";
    $tabelas_necessarias = ['usuarios', 'cursos', 'agendamentos'];
    $tabelas_faltando = [];
    
    foreach ($tabelas_necessarias as $tabela) {
        $result = $conn->query("SHOW TABLES LIKE '$tabela'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✅ Tabela '$tabela' existe</p>";
        } else {
            echo "<p class='error'>❌ Tabela '$tabela' NÃO existe!</p>";
            $tabelas_faltando[] = $tabela;
        }
    }
    
    // 3. VERIFICAR DADOS ATUAIS
    echo "<h3>3️⃣ Dados Atuais no Banco</h3>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>👥 Usuários: <strong>$total_usuarios</strong></p>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'");
    $total_professores = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>👨‍🏫 Professores: <strong>$total_professores</strong></p>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'aluno'");
    $total_alunos = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>👨‍🎓 Alunos: <strong>$total_alunos</strong></p>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $result ? $result->fetch_assoc()['total'] : 0;
    echo "<p>📚 Cursos: <strong>$total_cursos</strong></p>";
    
    // 4. POSSÍVEIS CAUSAS
    echo "<h3>4️⃣ Possíveis Causas dos Dados Terem Sumido</h3>";
    echo "<ul>";
    echo "<li><strong>MySQL reiniciou:</strong> Se o XAMPP foi reiniciado, os dados podem ter sido perdidos</li>";
    echo "<li><strong>Transação não commitada:</strong> Os dados foram inseridos mas não salvos permanentemente</li>";
    echo "<li><strong>Banco em modo de teste:</strong> Pode estar usando um banco temporário</li>";
    echo "<li><strong>Erro na inserção:</strong> Script parou no meio do processo</li>";
    echo "<li><strong>Permissões:</strong> Problemas de permissão no banco</li>";
    echo "</ul>";
    
    // 5. SOLUÇÕES
    echo "<h3>5️⃣ SOLUÇÕES</h3>";
    
    if (empty($tabelas_faltando)) {
        echo "<p class='success'>✅ Todas as tabelas existem</p>";
        
        if ($total_usuarios == 0) {
            echo "<p class='warning'>⚠️ Banco está vazio - vamos recarregar os dados</p>";
            echo "<a href='carregar_dados_permanentes.php' class='btn btn-success'>🔄 Carregar Dados Permanentes</a>";
        } else {
            echo "<p class='success'>✅ Dados existem no banco</p>";
            echo "<a href='dashboard_final.php' class='btn'>📊 Acessar Dashboard</a>";
        }
    } else {
        echo "<p class='error'>❌ Faltam tabelas - vamos recriar o banco</p>";
        echo "<a href='recriar_banco_completo.php' class='btn btn-danger'>🔧 Recriar Banco Completo</a>";
    }
    
    echo "</div>";
    
    // 6. VERIFICAÇÃO DE CONFIGURAÇÃO
    echo "<div class='section'>";
    echo "<h2>⚙️ Verificação de Configuração</h2>";
    
    // Verificar se o banco está em modo transacional
    $result = $conn->query("SELECT @@autocommit as autocommit");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p><strong>Auto-commit:</strong> " . ($row['autocommit'] ? 'ON' : 'OFF') . "</p>";
        if (!$row['autocommit']) {
            echo "<p class='warning'>⚠️ Auto-commit está OFF - isso pode causar perda de dados!</p>";
        }
    }
    
    // Verificar permissões
    $result = $conn->query("SHOW GRANTS");
    if ($result) {
        echo "<p><strong>Permissões do usuário:</strong></p>";
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";
    
    // 7. AÇÕES IMEDIATAS
    echo "<div class='section'>";
    echo "<h2>🚀 Ações Imediatas</h2>";
    echo "<p>Escolha uma das opções abaixo:</p>";
    
    if ($total_usuarios == 0) {
        echo "<a href='carregar_dados_permanentes.php' class='btn btn-success'>🔄 1. Carregar Dados Permanentes</a>";
        echo "<a href='verificar_dados_agora.php' class='btn'>🔍 2. Verificar Dados</a>";
        echo "<a href='dashboard_final.php' class='btn'>📊 3. Acessar Dashboard</a>";
    } else {
        echo "<a href='dashboard_final.php' class='btn'>📊 Acessar Dashboard</a>";
        echo "<a href='verificar_dados_agora.php' class='btn'>🔍 Verificar Dados</a>";
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2>❌ Erro na Investigação</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP Control Panel.</p>";
    echo "</div>";
}
?>








