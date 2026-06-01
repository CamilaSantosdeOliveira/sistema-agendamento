<?php
// Verificar Professores e Atribuições - Detalhado
session_start();
include 'db.php';

echo "<h1>🔍 Verificação Detalhada: Professores e Atribuições</h1>";

// 1. Verificar se existe tabela 'professores'
echo "<h2>📋 1. Verificação da Tabela 'professores':</h2>";
$result = $conn->query("SHOW TABLES LIKE 'professores'");
if ($result && $result->num_rows > 0) {
    echo "✅ <strong>Tabela 'professores' EXISTE!</strong><br>";
    $count = $conn->query("SELECT COUNT(*) as total FROM professores")->fetch_assoc()['total'];
    echo "📊 Registros: $count<br>";
} else {
    echo "❌ <strong>Tabela 'professores' NÃO EXISTE</strong><br>";
}

// 2. Verificar professores na tabela usuarios
echo "<h2>👥 2. Professores na Tabela 'usuarios':</h2>";
$professores = $conn->query("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE tipo_usuario = 'professor'");
if ($professores && $professores->num_rows > 0) {
    echo "✅ <strong>Encontrados $professores->num_rows professores na tabela 'usuarios':</strong><br>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th></tr>";
    while ($prof = $professores->fetch_assoc()) {
        echo "<tr><td>{$prof['id']}</td><td>{$prof['nome']}</td><td>{$prof['email']}</td><td>{$prof['tipo_usuario']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "❌ <strong>Nenhum professor encontrado na tabela 'usuarios'</strong><br>";
}

// 3. Verificar tabela 'atribuicoes'
echo "<h2>🔗 3. Verificação da Tabela 'atribuicoes':</h2>";
$result = $conn->query("SHOW TABLES LIKE 'atribuicoes'");
if ($result && $result->num_rows > 0) {
    echo "✅ <strong>Tabela 'atribuicoes' EXISTE!</strong><br>";
    $count = $conn->query("SELECT COUNT(*) as total FROM atribuicoes")->fetch_assoc()['total'];
    echo "📊 Registros: $count<br>";
} else {
    echo "❌ <strong>Tabela 'atribuicoes' NÃO EXISTE</strong><br>";
}

// 4. Verificar tabela 'atribuicoes_cursos'
echo "<h2>📚 4. Verificação da Tabela 'atribuicoes_cursos':</h2>";
$result = $conn->query("SHOW TABLES LIKE 'atribuicoes_cursos'");
if ($result && $result->num_rows > 0) {
    echo "✅ <strong>Tabela 'atribuicoes_cursos' EXISTE!</strong><br>";
    $count = $conn->query("SELECT COUNT(*) as total FROM atribuicoes_cursos")->fetch_assoc()['total'];
    echo "📊 Registros: $count<br>";
    
    // Mostrar dados da tabela
    $dados = $conn->query("SELECT * FROM atribuicoes_cursos");
    if ($dados && $dados->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Professor ID</th><th>Curso ID</th><th>Data</th></tr>";
        while ($row = $dados->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['professor_id']}</td><td>{$row['curso_id']}</td><td>{$row['data_atribuicao']}</td></tr>";
        }
        echo "</table>";
    }
} else {
    echo "❌ <strong>Tabela 'atribuicoes_cursos' NÃO EXISTE</strong><br>";
}

// 5. Verificar todas as tabelas que começam com 'atribuicoes'
echo "<h2>🔍 5. Todas as Tabelas com 'atribuicoes':</h2>";
$result = $conn->query("SHOW TABLES LIKE 'atribuicoes%'");
if ($result && $result->num_rows > 0) {
    echo "✅ <strong>Encontradas tabelas com 'atribuicoes':</strong><br>";
    while ($row = $result->fetch_array()) {
        $tabela = $row[0];
        $count = $conn->query("SELECT COUNT(*) as total FROM $tabela")->fetch_assoc()['total'];
        echo "📋 <strong>$tabela</strong>: $count registros<br>";
    }
} else {
    echo "❌ <strong>Nenhuma tabela encontrada com 'atribuicoes'</strong><br>";
}

// 6. Resumo final
echo "<h2>📊 6. Resumo Final:</h2>";
echo "<ul>";
echo "<li><strong>Professores:</strong> ";
if ($conn->query("SHOW TABLES LIKE 'professores'")->num_rows > 0) {
    echo "✅ Tabela 'professores' existe";
} else {
    echo "❌ Tabela 'professores' não existe, mas estão em 'usuarios'";
}
echo "</li>";

echo "<li><strong>Atribuições:</strong> ";
if ($conn->query("SHOW TABLES LIKE 'atribuicoes'")->num_rows > 0) {
    echo "✅ Tabela 'atribuicoes' existe";
} elseif ($conn->query("SHOW TABLES LIKE 'atribuicoes_cursos'")->num_rows > 0) {
    echo "✅ Tabela 'atribuicoes_cursos' existe";
} else {
    echo "❌ Nenhuma tabela de atribuições encontrada";
}
echo "</li>";
echo "</ul>";

echo "<p><a href='configuracoes.php'>← Voltar às Configurações</a></p>";
?>







