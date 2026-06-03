<?php
// Script para verificar se os professores estão no banco de dados
include 'db.php';

echo "<h1>🔍 Verificando Professores no Banco de Dados</h1>";

try {
    // Verificar se o banco está conectado
    if (!$conn) {
        throw new Exception("Banco de dados não está disponível");
    }
    
    // Contar total de professores
    $count_sql = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'professor'";
    $count_result = $conn->query($count_sql);
    $total_professores = $count_result->fetch_assoc()['total'];
    
    echo "<h2>📊 Estatísticas:</h2>";
    echo "<p>Total de professores no banco: <strong>$total_professores</strong></p>";
    
    // Listar todos os professores
    $sql = "SELECT id, nome, email, formacao, valor_hora, ativo, criado_em FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<h2>👨‍🏫 Lista Completa de Professores:</h2>";
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>ID</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Email</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Formação</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Valor/Hora</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Status</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 10px; text-align: left;'>Criado em</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            $status = $row['ativo'] ? '✅ Ativo' : '❌ Inativo';
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>" . $row['id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'><strong>" . htmlspecialchars($row['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>" . htmlspecialchars($row['formacao']) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>R$ " . number_format($row['valor_hora'], 2, ',', '.') . "/h</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>$status</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 10px;'>" . $row['criado_em'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar se os novos professores estão lá
        echo "<h2>🔍 Verificando Novos Professores:</h2>";
        $novos_emails = [
            'ricardo.silva@educonnect.com',
            'fernanda.costa@educonnect.com',
            'diego.santos@educonnect.com',
            'juliana.lima@educonnect.com',
            'andre.oliveira@educonnect.com',
            'camila.rodrigues@educonnect.com',
            'marcelo.ferreira@educonnect.com',
            'patricia.alves@educonnect.com'
        ];
        
        $encontrados = 0;
        foreach ($novos_emails as $email) {
            $check_sql = "SELECT nome FROM usuarios WHERE email = ? AND tipo_usuario = 'professor'";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $nome = $result->fetch_assoc()['nome'];
                echo "<p>✅ <strong>$nome</strong> ($email) - ENCONTRADO</p>";
                $encontrados++;
            } else {
                echo "<p>❌ <strong>$email</strong> - NÃO ENCONTRADO</p>";
            }
        }
        
        echo "<hr>";
        echo "<h3>📈 Resumo:</h3>";
        echo "<p>Novos professores encontrados: <strong>$encontrados/8</strong></p>";
        
        if ($encontrados == 8) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🎉 SUCESSO!</h3>";
            echo "<p>Todos os 8 novos professores foram adicionados ao banco de dados!</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>⚠️ ATENÇÃO</h3>";
            echo "<p>Apenas $encontrados dos 8 novos professores foram encontrados no banco.</p>";
            echo "</div>";
        }
        
    } else {
        echo "<p>❌ Nenhum professor encontrado no banco de dados.</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Erro na Verificação</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
    echo "</div>";
}

$conn->close();
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
hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0;
}
</style>

















