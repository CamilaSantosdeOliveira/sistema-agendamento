<?php
echo "<h2>👥 Criando Usuários de Teste</h2>";

try {
    // Conectar ao banco
    include 'db.php';
    
    // Converter mysqli para PDO
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco com sucesso!<br><br>";
    
    // Verificar se usuários já existem
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE email IN (?, ?)");
    $stmt->execute(['maria.santos@educonnect.com', 'joao.silva@email.com']);
    $existentes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($existentes > 0) {
        echo "⚠️ Usuários de teste já existem!<br>";
        echo "<a href='login.html'>🔑 Ir para Login</a><br>";
        exit();
    }
    
    // Criar usuário Professor
    echo "👨‍🏫 Criando Professor...<br>";
    $senhaHash = password_hash('123456', PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario, formacao, experiencia, valor_hora, descricao, ativo, criado_em) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'Maria Santos',
        'maria.santos@educonnect.com',
        $senhaHash,
        '(11) 99999-9999',
        'professor',
        'Mestre em Tecnologia',
        '5 anos',
        '80.00',
        'Professora especializada em desenvolvimento web e mobile'
    ]);
    
    $professor_id = $pdo->lastInsertId();
    echo "✅ Professor criado com ID: $professor_id<br>";
    
    // Inserir matérias do professor
    $materias = ['javascript', 'python', 'react', 'nodejs'];
    foreach ($materias as $materia) {
        $stmt = $pdo->prepare("INSERT INTO professor_materias (professor_id, materia) VALUES (?, ?)");
        $stmt->execute([$professor_id, $materia]);
    }
    echo "✅ Matérias do professor inseridas<br><br>";
    
    // Criar usuário Aluno
    echo "👨‍🎓 Criando Aluno...<br>";
    $senhaHash = password_hash('123456', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'João Silva',
        'joao.silva@email.com',
        $senhaHash,
        '(11) 88888-8888',
        'aluno',
        'Estudante de Tecnologia',
        '1 ano',
        '0.00',
        'Aluno interessado em desenvolvimento web'
    ]);
    
    $aluno_id = $pdo->lastInsertId();
    echo "✅ Aluno criado com ID: $aluno_id<br><br>";
    
    // Verificar se foram criados
    $stmt = $pdo->prepare("SELECT nome, email, tipo_usuario FROM usuarios WHERE email IN (?, ?)");
    $stmt->execute(['maria.santos@educonnect.com', 'joao.silva@email.com']);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📋 Usuários Criados:</h3>";
    foreach ($usuarios as $usuario) {
        echo "✅ <strong>{$usuario['nome']}</strong> - {$usuario['email']} ({$usuario['tipo_usuario']})<br>";
    }
    
    echo "<br><hr>";
    echo "<h3>🎯 Agora você pode fazer login com:</h3>";
    echo "<strong>👨‍🏫 Professor:</strong> maria.santos@educonnect.com / 123456<br>";
    echo "<strong>👨‍🎓 Aluno:</strong> joao.silva@email.com / 123456<br><br>";
    
    echo "<a href='login.html' style='background: #1e40af; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔑 Testar Login</a>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    echo "<br><a href='teste_conexao_login.php'>🔍 Verificar Conexão</a>";
}
?>







