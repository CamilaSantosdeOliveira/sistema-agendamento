<?php
// Script para verificar MySQL e forçar atualização
echo "<h1>🔍 Verificando MySQL e Forçando Atualização</h1>";

// Primeiro, verificar se conseguimos conectar
try {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'sistema_agendamento';
    
    echo "<p>🔄 Tentando conectar ao MySQL...</p>";
    
    $conn = new mysqli($host, $user, $pass, $db, 3306);
    
    if ($conn->connect_error) {
        throw new Exception('Erro de conexão: ' . $conn->connect_error);
    }
    
    echo "<p style='color: green;'>✅ MySQL conectado com sucesso!</p>";
    
    // Verificar dados atuais
    echo "<h2>📊 Dados Atuais dos Professores:</h2>";
    
    $sql = "SELECT id, nome, email, formacao, valor_hora FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<table style='width: 100%; border-collapse: collapse; background: white; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa; font-weight: bold;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>ID</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Formação Atual</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Valor Atual</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $row['id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'><strong>" . htmlspecialchars($row['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($row['formacao'] ?: 'Não informado') . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>R$ " . number_format($row['valor_hora'], 2, ',', '.') . "/h</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Forçar atualização com queries diretas
    echo "<h2>🚀 Forçando Atualização...</h2>";
    
    $atualizacoes = [
        [
            'id' => 7, // Prof. Ana Costa
            'formacao' => 'Bacharelado em Ciência da Computação - USP',
            'valor' => 80.00
        ],
        [
            'id' => 8, // Prof. Carlos Lima
            'formacao' => 'Engenharia de Software - UNICAMP',
            'valor' => 85.00
        ],
        [
            'id' => 9, // Prof. Maria Santos
            'formacao' => 'Sistemas de Informação - PUC',
            'valor' => 75.00
        ]
    ];
    
    $sucessos = 0;
    
    foreach ($atualizacoes as $prof) {
        echo "<p>🔄 Atualizando professor ID {$prof['id']}...</p>";
        
        // Query direta sem prepared statement
        $sql_update = "UPDATE usuarios SET formacao = '{$prof['formacao']}', valor_hora = {$prof['valor']} WHERE id = {$prof['id']}";
        
        if ($conn->query($sql_update)) {
            if ($conn->affected_rows > 0) {
                echo "<p style='color: green;'>✅ Professor ID {$prof['id']} atualizado!</p>";
                $sucessos++;
            } else {
                echo "<p style='color: orange;'>⚠️ Professor ID {$prof['id']} não foi alterado</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Erro: " . $conn->error . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h2>📊 Verificando Mudanças...</h2>";
    
    // Verificar novamente
    $sql_verificar = "SELECT id, nome, email, formacao, valor_hora FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result_verificar = $conn->query($sql_verificar);
    
    if ($result_verificar && $result_verificar->num_rows > 0) {
        echo "<table style='width: 100%; border-collapse: collapse; background: white; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa; font-weight: bold;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>ID</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Formação</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Valor/Hora</th>";
        echo "</tr>";
        
        while ($row = $result_verificar->fetch_assoc()) {
            $formacao = $row['formacao'] ?: 'Não informado';
            $is_tech = strpos(strtolower($formacao), 'computação') !== false || 
                      strpos(strtolower($formacao), 'software') !== false || 
                      strpos(strtolower($formacao), 'sistemas') !== false;
            
            $row_color = $is_tech ? '#d4edda' : '#f8d7da';
            
            echo "<tr style='background: $row_color;'>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $row['id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'><strong>" . htmlspecialchars($row['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($formacao) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>R$ " . number_format($row['valor_hora'], 2, ',', '.') . "/h</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🎯 Próximos Passos:</h3>";
    echo "<p><a href='sistema_usuarios.php' style='color: #155724; font-weight: bold;'>👥 Ver Lista de Usuários</a></p>";
    echo "<p><a href='verificar_dados_professores.php' style='color: #155724; font-weight: bold;'>🔍 Verificar Dados</a></p>";
    echo "<p><strong>💡 Dica:</strong> Se ainda não mudou, limpe o cache do navegador (Ctrl+F5)</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Erro de Conexão:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>🔧 Solução:</strong> Inicie o MySQL no XAMPP Control Panel</p>";
    echo "</div>";
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
hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 20px 0;
}
</style>
















