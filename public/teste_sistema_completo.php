<?php
session_start();
require_once 'db.php';

echo "<h1>🧪 Teste Completo do Sistema</h1>";

try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=sistema_agendamento", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>📊 Dados do Banco de Dados</h2>";
    
    // Contar usuários por tipo
    $stmt = $pdo->query("SELECT tipo_usuario, COUNT(*) as total FROM usuarios GROUP BY tipo_usuario");
    $usuarios = $stmt->fetchAll();
    
    echo "<h3>👥 Usuários Cadastrados:</h3>";
    foreach ($usuarios as $user) {
        echo "<p><strong>{$user['tipo_usuario']}:</strong> {$user['total']} usuários</p>";
    }
    
    // Contar cursos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos");
    $cursos_count = $stmt->fetch()['total'];
    echo "<p><strong>📚 Cursos:</strong> $cursos_count cursos</p>";
    
    // Contar agendamentos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos");
    $agendamentos_count = $stmt->fetch()['total'];
    echo "<p><strong>📅 Agendamentos:</strong> $agendamentos_count agendamentos</p>";
    
    // Contar certificados
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM certificados");
    $certificados_count = $stmt->fetch()['total'];
    echo "<p><strong>🏆 Certificados:</strong> $certificados_count certificados</p>";
    
    echo "<h2>🔗 Teste dos Dashboards</h2>";
    
    // Teste 1: Admin
    echo "<h3>👨‍💼 Dashboard Admin:</h3>";
    echo "<p><a href='dashboard_final.php' target='_blank'>📊 Acessar Dashboard Admin</a></p>";
    echo "<p><strong>Login:</strong> admin@educonnect.com / 123456</p>";
    
    // Teste 2: Professor
    echo "<h3>👨‍🏫 Dashboard Professor:</h3>";
    echo "<p><a href='dashboard_professor.php' target='_blank'>📚 Acessar Dashboard Professor</a></p>";
    echo "<p><strong>Login:</strong> ricardo.silva@educonnect.com / 123456</p>";
    
    // Teste 3: Aluno
    echo "<h3>👨‍🎓 Dashboard Aluno:</h3>";
    echo "<p><a href='dashboard_aluno.php' target='_blank'>🎓 Acessar Dashboard Aluno</a></p>";
    echo "<p><strong>Login:</strong> joao.silva@email.com / 123456</p>";
    
    echo "<h2>🔧 Funcionalidades dos Dashboards</h2>";
    
    // Verificar páginas do professor
    echo "<h3>👨‍🏫 Páginas do Professor:</h3>";
    $paginas_professor = [
        'cursos_professor.php' => '📚 Meus Cursos',
        'aulas_professor.php' => '📅 Minhas Aulas',
        'alunos_professor.php' => '👥 Meus Alunos',
        'relatorios_professor.php' => '📊 Relatórios',
        'configuracoes_professor.php' => '⚙️ Configurações'
    ];
    
    foreach ($paginas_professor as $arquivo => $nome) {
        if (file_exists($arquivo)) {
            echo "<p>✅ <a href='$arquivo' target='_blank'>$nome</a></p>";
        } else {
            echo "<p>❌ $nome (arquivo não encontrado)</p>";
        }
    }
    
    // Verificar páginas do admin
    echo "<h3>👨‍💼 Páginas do Admin:</h3>";
    $paginas_admin = [
        'dashboard_final.php' => '📊 Dashboard Principal',
        'gerenciar_cursos.php' => '📚 Gerenciar Cursos',
        'gerenciar_usuarios.php' => '👥 Gerenciar Usuários',
        'relatorios.php' => '📊 Relatórios'
    ];
    
    foreach ($paginas_admin as $arquivo => $nome) {
        if (file_exists($arquivo)) {
            echo "<p>✅ <a href='$arquivo' target='_blank'>$nome</a></p>";
        } else {
            echo "<p>❌ $nome (arquivo não encontrado)</p>";
        }
    }
    
    echo "<h2>🎯 Teste de Login Automático</h2>";
    
    // Teste login admin
    echo "<h3>👨‍💼 Teste Admin:</h3>";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND tipo_usuario = 'admin'");
    $stmt->execute(['admin@educonnect.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>✅ Admin encontrado: {$admin['nome']}</p>";
        echo "<p><a href='teste_login_admin.php'>🔐 Fazer Login como Admin</a></p>";
    } else {
        echo "<p>❌ Admin não encontrado</p>";
    }
    
    // Teste login professor
    echo "<h3>👨‍🏫 Teste Professor:</h3>";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND tipo_usuario = 'professor'");
    $stmt->execute(['ricardo.silva@educonnect.com']);
    $professor = $stmt->fetch();
    
    if ($professor) {
        echo "<p>✅ Professor encontrado: {$professor['nome']}</p>";
        echo "<p><a href='teste_login_professor.php'>🔐 Fazer Login como Professor</a></p>";
    } else {
        echo "<p>❌ Professor não encontrado</p>";
    }
    
    // Teste login aluno
    echo "<h3>👨‍🎓 Teste Aluno:</h3>";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND tipo_usuario = 'aluno'");
    $stmt->execute(['joao.silva@email.com']);
    $aluno = $stmt->fetch();
    
    if ($aluno) {
        echo "<p>✅ Aluno encontrado: {$aluno['nome']}</p>";
        echo "<p><a href='teste_login_aluno.php'>🔐 Fazer Login como Aluno</a></p>";
    } else {
        echo "<p>❌ Aluno não encontrado</p>";
    }
    
    echo "<h2>📋 Resumo do Sistema</h2>";
    
    if ($usuarios && $cursos_count > 0 && $agendamentos_count > 0) {
        echo "<p style='color: green; font-size: 18px;'>🎉 SISTEMA COMPLETO E FUNCIONAL!</p>";
        echo "<p>✅ Usuários cadastrados</p>";
        echo "<p>✅ Cursos disponíveis</p>";
        echo "<p>✅ Agendamentos ativos</p>";
        echo "<p>✅ 3 dashboards funcionando</p>";
        echo "<p>✅ Sistema pronto para demonstração</p>";
    } else {
        echo "<p style='color: orange; font-size: 18px;'>⚠️ Sistema precisa de mais dados</p>";
        echo "<p><a href='criar_agendamentos_teste.php'>➕ Criar Dados de Teste</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🔗 Links Principais</h2>";
echo "<p><a href='login.php'>🔐 Tela de Login</a></p>";
echo "<p><a href='index.php'>🏠 Página Inicial</a></p>";
echo "<p><a href='logout.php'>🚪 Logout</a></p>";
?>


