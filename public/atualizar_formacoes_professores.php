<?php
// Script para atualizar formações dos professores para tecnologia
include 'db.php';

echo "<h1>🔄 Atualizando Formações dos Professores</h1>";
echo "<p>Alterando formações para serem mais apropriadas para cursos de tecnologia...</p>";

try {
    // Verificar se o banco está conectado
    if (!$conn) {
        throw new Exception("Banco de dados não está disponível");
    }
    
    // Lista de atualizações para professores de tecnologia
    $atualizacoes = [
        [
            'nome' => 'Prof. Ana Costa',
            'formacao' => 'Bacharelado em Ciência da Computação - USP',
            'valor_hora' => 80.00
        ],
        [
            'nome' => 'Prof. Carlos Lima', 
            'formacao' => 'Engenharia de Software - UNICAMP',
            'valor_hora' => 85.00
        ],
        [
            'nome' => 'Prof. Maria Santos',
            'formacao' => 'Sistemas de Informação - PUC',
            'valor_hora' => 75.00
        ]
    ];
    
    $sucessos = 0;
    $erros = 0;
    
    foreach ($atualizacoes as $professor) {
        $sql = "UPDATE usuarios SET formacao = ?, valor_hora = ? WHERE nome = ? AND tipo_usuario = 'professor'";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sds", $professor['formacao'], $professor['valor_hora'], $professor['nome']);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "<p>✅ <strong>{$professor['nome']}</strong> atualizado:</p>";
                    echo "<ul>";
                    echo "<li>Formação: <strong>{$professor['formacao']}</strong></li>";
                    echo "<li>Valor/Hora: <strong>R$ " . number_format($professor['valor_hora'], 2, ',', '.') . "/h</strong></li>";
                    echo "</ul>";
                    $sucessos++;
                } else {
                    echo "<p>⚠️ Professor <strong>{$professor['nome']}</strong> não encontrado</p>";
                    $erros++;
                }
            } else {
                echo "<p>❌ Erro ao atualizar <strong>{$professor['nome']}</strong>: " . $stmt->error . "</p>";
                $erros++;
            }
            $stmt->close();
        } else {
            echo "<p>❌ Erro na preparação da query para <strong>{$professor['nome']}</strong></p>";
            $erros++;
        }
    }
    
    echo "<hr>";
    echo "<h2>📊 Resumo da Atualização:</h2>";
    echo "<p>✅ Professores atualizados com sucesso: <strong>$sucessos</strong></p>";
    echo "<p>❌ Erros encontrados: <strong>$erros</strong></p>";
    
    if ($sucessos > 0) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>🎉 Atualização Concluída!</h3>";
        echo "<p>As formações dos professores foram atualizadas para serem mais apropriadas para cursos de tecnologia.</p>";
        echo "<p><a href='sistema_usuarios.php' style='color: #155724; font-weight: bold;'>👥 Ver Lista de Usuários Atualizada</a></p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Erro na Atualização</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP Control Panel.</p>";
    echo "</div>";
}

// Mostrar professores atuais
echo "<hr>";
echo "<h2>👨‍🏫 Professores Atuais no Sistema:</h2>";

try {
    $sql = "SELECT nome, formacao, valor_hora FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Formação</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Valor/Hora</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'><strong>" . htmlspecialchars($row['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>" . htmlspecialchars($row['formacao']) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>R$ " . number_format($row['valor_hora'], 2, ',', '.') . "/h</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhum professor encontrado no sistema.</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao buscar professores: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
h1, h2, h3 {
    color: #333;
}
p {
    margin: 10px 0;
}
ul {
    margin: 5px 0;
    padding-left: 20px;
}
hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0;
}
</style>


















