<?php
// Script para forçar atualização das formações
include 'db.php';

echo "<h1>🚀 Forçando Atualização das Formações</h1>";

try {
    if (!$conn) {
        throw new Exception("Banco de dados não está disponível");
    }
    
    echo "<p>✅ Conexão estabelecida!</p>";
    
    // Atualizações diretas e específicas
    $atualizacoes = [
        [
            'nome' => 'Prof. Ana Costa',
            'formacao' => 'Bacharelado em Ciência da Computação - USP',
            'valor' => 80.00
        ],
        [
            'nome' => 'Prof. Carlos Lima',
            'formacao' => 'Engenharia de Software - UNICAMP',
            'valor' => 85.00
        ],
        [
            'nome' => 'Prof. Maria Santos',
            'formacao' => 'Sistemas de Informação - PUC',
            'valor' => 75.00
        ]
    ];
    
    echo "<h2>🔄 Executando Atualizações Diretas...</h2>";
    
    $sucessos = 0;
    $erros = 0;
    
    foreach ($atualizacoes as $prof) {
        echo "<p>🔄 Atualizando <strong>{$prof['nome']}</strong>...</p>";
        
        // Query direta
        $sql = "UPDATE usuarios SET formacao = '{$prof['formacao']}', valor_hora = {$prof['valor']} WHERE nome = '{$prof['nome']}' AND tipo_usuario = 'professor'";
        
        if ($conn->query($sql)) {
            if ($conn->affected_rows > 0) {
                echo "<p style='color: green;'>✅ <strong>{$prof['nome']}</strong> atualizado!</p>";
                echo "<ul>";
                echo "<li>Formação: <strong>{$prof['formacao']}</strong></li>";
                echo "<li>Valor: <strong>R$ " . number_format($prof['valor'], 2, ',', '.') . "/h</strong></li>";
                echo "</ul>";
                $sucessos++;
            } else {
                echo "<p style='color: orange;'>⚠️ <strong>{$prof['nome']}</strong> não encontrado ou já atualizado</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Erro ao atualizar <strong>{$prof['nome']}</strong>: " . $conn->error . "</p>";
            $erros++;
        }
    }
    
    echo "<hr>";
    echo "<h2>📊 Resultado:</h2>";
    echo "<p>✅ Sucessos: <strong>$sucessos</strong></p>";
    echo "<p>❌ Erros: <strong>$erros</strong></p>";
    
    // Verificar se as mudanças foram aplicadas
    echo "<hr>";
    echo "<h2>🔍 Verificando Mudanças...</h2>";
    
    $sql_verificar = "SELECT nome, formacao, valor_hora FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result = $conn->query($sql_verificar);
    
    if ($result && $result->num_rows > 0) {
        echo "<table style='width: 100%; border-collapse: collapse; background: white;'>";
        echo "<tr style='background: #f8f9fa; font-weight: bold;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Formação</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px;'>Valor/Hora</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            $formacao = $row['formacao'] ?: 'Não informado';
            $is_tech = strpos(strtolower($formacao), 'computação') !== false || 
                      strpos(strtolower($formacao), 'software') !== false || 
                      strpos(strtolower($formacao), 'sistemas') !== false;
            
            $row_color = $is_tech ? '#d4edda' : '#f8d7da';
            
            echo "<tr style='background: $row_color;'>";
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
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Erro:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
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


















