<?php
echo "<h1>🔍 Teste Dashboard Professor</h1>";
echo "<p><strong>Data:</strong> " . date('d/m/Y H:i:s') . "</p>";

// Verificar sessão
session_start();
echo "<h2>📋 Status da Sessão:</h2>";
if (isset($_SESSION['usuario_id'])) {
    echo "<p>✅ Usuário logado: {$_SESSION['usuario_nome']} ({$_SESSION['usuario_tipo']})</p>";
} else {
    echo "<p>❌ Nenhum usuário logado</p>";
}

// Testar conexão com banco
echo "<h2>🗄️ Teste de Conexão:</h2>";
try {
    include 'db.php';
    if ($conn && !$conn->connect_error) {
        echo "<p>✅ Conexão com banco OK</p>";
    } else {
        echo "<p>❌ Erro na conexão com banco</p>";
        exit();
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
    exit();
}

// Verificar estrutura da tabela cursos
echo "<h2>📚 Verificar Tabela Cursos:</h2>";
try {
    $result = $conn->query("DESCRIBE cursos");
    if ($result) {
        echo "<p>✅ Tabela cursos existe</p>";
        echo "<p><strong>Colunas:</strong></p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['Field']} - {$row['Type']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Erro ao verificar tabela cursos</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

// Verificar estrutura da tabela agendamentos
echo "<h2>📅 Verificar Tabela Agendamentos:</h2>";
try {
    $result = $conn->query("DESCRIBE agendamentos");
    if ($result) {
        echo "<p>✅ Tabela agendamentos existe</p>";
        echo "<p><strong>Colunas:</strong></p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['Field']} - {$row['Type']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Erro ao verificar tabela agendamentos</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

// Verificar estrutura da tabela inscricoes
echo "<h2>👥 Verificar Tabela Inscrições:</h2>";
try {
    $result = $conn->query("DESCRIBE inscricoes");
    if ($result) {
        echo "<p>✅ Tabela inscricoes existe</p>";
        echo "<p><strong>Colunas:</strong></p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['Field']} - {$row['Type']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Erro ao verificar tabela inscricoes</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

// Testar consultas específicas do professor
if (isset($_SESSION['usuario_id'])) {
    echo "<h2>👨‍🏫 Teste de Consultas do Professor:</h2>";
    $professor_id = $_SESSION['usuario_id'];
    
    // Teste 1: Buscar professor
    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_usuario = 'professor'");
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $professor = $result->fetch_assoc();
            echo "<p>✅ Professor encontrado: {$professor['nome']}</p>";
        } else {
            echo "<p>❌ Professor não encontrado ou não é professor</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Erro na consulta do professor: " . $e->getMessage() . "</p>";
    }
    
    // Teste 2: Contar cursos
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cursos WHERE professor_id = ?");
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        echo "<p>✅ Cursos do professor: $count</p>";
    } catch (Exception $e) {
        echo "<p>❌ Erro na contagem de cursos: " . $e->getMessage() . "</p>";
    }
    
    // Teste 3: Contar alunos
    try {
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT i.aluno_id) as count FROM inscricoes i 
                               JOIN cursos c ON i.curso_id = c.id 
                               WHERE c.professor_id = ?");
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        echo "<p>✅ Alunos do professor: $count</p>";
    } catch (Exception $e) {
        echo "<p>❌ Erro na contagem de alunos: " . $e->getMessage() . "</p>";
    }
    
    // Teste 4: Contar aulas
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM agendamentos WHERE professor_id = ? AND data >= CURDATE()");
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        echo "<p>✅ Aulas agendadas: $count</p>";
    } catch (Exception $e) {
        echo "<p>❌ Erro na contagem de aulas: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>🔗 Links para Teste:</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;'>";

echo "<div style='border: 2px solid #3b82f6; border-radius: 8px; padding: 20px; background: #f0f9ff;'>";
echo "<h3 style='color: #3b82f6; margin-bottom: 15px;'>👨‍🏫 Testar Professor</h3>";
echo "<p><strong>Email:</strong> ricardo.silva@educonnect.com</p>";
echo "<p><strong>Senha:</strong> 123456</p>";
echo "<a href='login.php' target='_blank' style='display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Fazer Login Professor</a>";
echo "</div>";

echo "<div style='border: 2px solid #ef4444; border-radius: 8px; padding: 20px; background: #fef2f2;'>";
echo "<h3 style='color: #ef4444; margin-bottom: 15px;'>🔍 Testar Dashboard</h3>";
echo "<p><strong>Link:</strong> dashboard_professor.php</p>";
echo "<a href='dashboard_professor.php' target='_blank' style='display: inline-block; background: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Testar Dashboard Professor</a>";
echo "</div>";

echo "</div>";

echo "<h2>🎯 Possíveis Problemas:</h2>";
echo "<ul>";
echo "<li>❌ Coluna 'professor_id' não existe na tabela cursos</li>";
echo "<li>❌ Coluna 'professor_id' não existe na tabela agendamentos</li>";
echo "<li>❌ Usuário logado não é do tipo 'professor'</li>";
echo "<li>❌ Erro de sintaxe SQL</li>";
echo "</ul>";

echo "<p style='margin-top: 30px; padding: 20px; background: #fef3c7; border-radius: 8px; color: #92400e;'>";
echo "<strong>💡 Dica:</strong> Execute este teste para identificar exatamente onde está o erro!";
echo "</p>";
?>








