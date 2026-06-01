<?php
// Script para corrigir automaticamente as formações dos professores
include 'db.php';

echo "<h1>🔄 Corrigindo Formações Automaticamente</h1>";

try {
    // Verificar se o banco está conectado
    if (!$conn) {
        throw new Exception("Banco de dados não está disponível");
    }
    
    echo "<p>✅ Conexão com banco estabelecida!</p>";
    
    // Buscar todos os professores
    $sql = "SELECT id, nome, email, formacao, valor_hora FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<h2>👨‍🏫 Professores Encontrados: " . $result->num_rows . "</h2>";
        
        $atualizacoes = [];
        
        while ($row = $result->fetch_assoc()) {
            $nome = $row['nome'];
            $formacao_atual = $row['formacao'];
            $valor_atual = $row['valor_hora'];
            
            // Definir nova formação baseada no nome
            $nova_formacao = '';
            $novo_valor = 0;
            
            if (strpos(strtolower($nome), 'ana') !== false) {
                $nova_formacao = 'Bacharelado em Ciência da Computação - USP';
                $novo_valor = 80.00;
            } elseif (strpos(strtolower($nome), 'carlos') !== false) {
                $nova_formacao = 'Engenharia de Software - UNICAMP';
                $novo_valor = 85.00;
            } elseif (strpos(strtolower($nome), 'maria') !== false) {
                $nova_formacao = 'Sistemas de Informação - PUC';
                $novo_valor = 75.00;
            } else {
                // Para outros professores, usar formações genéricas de tecnologia
                $formacoes_tech = [
                    'Bacharelado em Ciência da Computação - USP',
                    'Engenharia de Software - UNICAMP',
                    'Sistemas de Informação - PUC',
                    'Tecnologia em Análise e Desenvolvimento de Sistemas - FATEC',
                    'Bacharelado em Engenharia da Computação - ITA'
                ];
                $nova_formacao = $formacoes_tech[array_rand($formacoes_tech)];
                $novo_valor = rand(70, 90);
            }
            
            $atualizacoes[] = [
                'id' => $row['id'],
                'nome' => $nome,
                'formacao_atual' => $formacao_atual,
                'nova_formacao' => $nova_formacao,
                'valor_atual' => $valor_atual,
                'novo_valor' => $novo_valor
            ];
        }
        
        // Mostrar o que será atualizado
        echo "<h3>📋 Alterações que serão feitas:</h3>";
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px; background: white;'>";
        echo "<tr style='background: #f8f9fa; font-weight: bold;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Formação Atual</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Nova Formação</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Valor Atual</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Novo Valor</th>";
        echo "</tr>";
        
        foreach ($atualizacoes as $prof) {
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'><strong>" . htmlspecialchars($prof['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($prof['formacao_atual'] ?: 'Não informado') . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px; color: #28a745; font-weight: bold;'>" . htmlspecialchars($prof['nova_formacao']) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>R$ " . number_format($prof['valor_atual'], 2, ',', '.') . "/h</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px; color: #28a745; font-weight: bold;'>R$ " . number_format($prof['novo_valor'], 2, ',', '.') . "/h</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Executar as atualizações
        echo "<hr>";
        echo "<h3>🔄 Executando Atualizações...</h3>";
        
        $sucessos = 0;
        $erros = 0;
        
        foreach ($atualizacoes as $prof) {
            $sql_update = "UPDATE usuarios SET formacao = ?, valor_hora = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update);
            
            if ($stmt) {
                $stmt->bind_param("sdi", $prof['nova_formacao'], $prof['novo_valor'], $prof['id']);
                
                if ($stmt->execute()) {
                    echo "<p>✅ <strong>{$prof['nome']}</strong> atualizado com sucesso!</p>";
                    $sucessos++;
                } else {
                    echo "<p>❌ Erro ao atualizar <strong>{$prof['nome']}</strong>: " . $stmt->error . "</p>";
                    $erros++;
                }
                $stmt->close();
            } else {
                echo "<p>❌ Erro na preparação da query para <strong>{$prof['nome']}</strong></p>";
                $erros++;
            }
        }
        
        echo "<hr>";
        echo "<h2>📊 Resumo da Atualização:</h2>";
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<p>✅ Professores atualizados com sucesso: <strong>$sucessos</strong></p>";
        echo "<p>❌ Erros encontrados: <strong>$erros</strong></p>";
        echo "</div>";
        
        if ($sucessos > 0) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;'>";
            echo "<h3>🎉 Atualização Concluída!</h3>";
            echo "<p>As formações dos professores foram automaticamente corrigidas para serem apropriadas para cursos de tecnologia.</p>";
            echo "<p><a href='sistema_usuarios.php' style='color: #155724; font-weight: bold;'>👥 Ver Lista de Usuários Atualizada</a></p>";
            echo "<p><a href='verificar_dados_professores.php' style='color: #155724; font-weight: bold;'>🔍 Verificar Dados Atualizados</a></p>";
            echo "</div>";
        }
        
    } else {
        echo "<p>Nenhum professor encontrado no sistema.</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Erro na Atualização</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP Control Panel.</p>";
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
















