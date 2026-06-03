<?php
// Script para verificar dados dos professores no banco
include 'db.php';

echo "<h1>🔍 Verificando Dados dos Professores no Banco</h1>";

try {
    // Verificar se o banco está conectado
    if (!$conn) {
        throw new Exception("Banco de dados não está disponível");
    }
    
    echo "<p>✅ Conexão com banco estabelecida!</p>";
    
    // Buscar todos os professores
    $sql = "SELECT id, nome, email, formacao, valor_hora, ativo, criado_em FROM usuarios WHERE tipo_usuario = 'professor' ORDER BY nome";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<h2>👨‍🏫 Professores Encontrados: " . $result->num_rows . "</h2>";
        
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px; background: white;'>";
        echo "<tr style='background: #f8f9fa; font-weight: bold;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>ID</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Nome</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Email</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Formação</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Valor/Hora</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Status</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Data Criação</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            $status = $row['ativo'] ? 'Ativo' : 'Inativo';
            $status_color = $row['ativo'] ? '#28a745' : '#dc3545';
            
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $row['id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'><strong>" . htmlspecialchars($row['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($row['formacao'] ?: 'Não informado') . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>R$ " . number_format($row['valor_hora'], 2, ',', '.') . "/h</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px; color: $status_color; font-weight: bold;'>$status</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . date('d/m/Y', strtotime($row['criado_em'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Análise das formações
        echo "<hr>";
        echo "<h2>📊 Análise das Formações:</h2>";
        
        $result->data_seek(0); // Voltar ao início do resultado
        $formacoes_tecnologia = 0;
        $formacoes_outras = 0;
        $sem_formacao = 0;
        
        while ($row = $result->fetch_assoc()) {
            $formacao = strtolower($row['formacao'] ?? '');
            
            if (empty($formacao) || $formacao === 'não informado') {
                $sem_formacao++;
            } elseif (strpos($formacao, 'computação') !== false || 
                     strpos($formacao, 'software') !== false || 
                     strpos($formacao, 'sistemas') !== false ||
                     strpos($formacao, 'tecnologia') !== false ||
                     strpos($formacao, 'engenharia') !== false) {
                $formacoes_tecnologia++;
            } else {
                $formacoes_outras++;
            }
        }
        
        echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<p><strong>Formações de Tecnologia:</strong> $formacoes_tecnologia</p>";
        echo "<p><strong>Outras Formações:</strong> $formacoes_outras</p>";
        echo "<p><strong>Sem Formação:</strong> $sem_formacao</p>";
        echo "</div>";
        
        if ($formacoes_outras > 0) {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107;'>";
            echo "<h3>⚠️ Atenção!</h3>";
            echo "<p>Existem $formacoes_outras professores com formações que podem não ser apropriadas para cursos de tecnologia.</p>";
            echo "<p><a href='atualizar_formacoes_professores.php' style='color: #856404; font-weight: bold;'>🔄 Atualizar Formações para Tecnologia</a></p>";
            echo "</div>";
        }
        
    } else {
        echo "<p>Nenhum professor encontrado no sistema.</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Erro ao Verificar Dados</h3>";
    echo "<p>Erro: " . $e->getMessage() . "</p>";
    echo "<p>Verifique se o MySQL está rodando no XAMPP Control Panel.</p>";
    echo "</div>";
}

// Verificar também os cursos disponíveis
echo "<hr>";
echo "<h2>📚 Cursos Disponíveis no Sistema:</h2>";

try {
    $sql_cursos = "SELECT id, nome, descricao, carga_horaria, valor FROM cursos ORDER BY nome";
    $result_cursos = $conn->query($sql_cursos);
    
    if ($result_cursos && $result_cursos->num_rows > 0) {
        echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px; background: white;'>";
        echo "<tr style='background: #f8f9fa; font-weight: bold;'>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>ID</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Nome do Curso</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Descrição</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Carga Horária</th>";
        echo "<th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Valor</th>";
        echo "</tr>";
        
        while ($curso = $result_cursos->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $curso['id'] . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'><strong>" . htmlspecialchars($curso['nome']) . "</strong></td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . htmlspecialchars($curso['descricao'] ?: 'Sem descrição') . "</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>" . $curso['carga_horaria'] . " horas</td>";
            echo "<td style='border: 1px solid #dee2e6; padding: 12px;'>R$ " . number_format($curso['valor'], 2, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhum curso encontrado no sistema.</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao buscar cursos: " . $e->getMessage() . "</p>";
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


















