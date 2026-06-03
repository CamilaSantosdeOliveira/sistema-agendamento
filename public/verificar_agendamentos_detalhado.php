<?php
include 'db.php';

echo "=== ANÁLISE DETALHADA DOS AGENDAMENTOS ===\n\n";

// Verificar todos os agendamentos
$agendamentos_query = "SELECT * FROM agendamentos ORDER BY data DESC";
$result = $conn->query($agendamentos_query);

if ($result && $result->num_rows > 0) {
    echo "Total de agendamentos: " . $result->num_rows . "\n\n";
    
    echo "=== LISTA COMPLETA DOS AGENDAMENTOS ===\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "  Data: " . $row['data'] . "\n";
        echo "  Hora: " . $row['hora'] . "\n";
        echo "  Professor: " . $row['professor'] . "\n";
        echo "  Serviço: " . $row['servico'] . "\n";
        echo "  Aluno: " . $row['nome'] . "\n";
        echo "  Status: " . $row['status'] . "\n";
        echo "  Observações: " . $row['observacoes'] . "\n";
        echo "  ---\n";
    }
    
    // Agora vou verificar quais professores únicos estão sendo usados
    echo "\n=== PROFESSORES ÚNICOS NOS AGENDAMENTOS ===\n";
    $professores_unicos = "SELECT DISTINCT professor FROM agendamentos WHERE professor IS NOT NULL AND professor != '' ORDER BY professor";
    $result = $conn->query($professores_unicos);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['professor'] . "\n";
        }
    } else {
        echo "Nenhum professor encontrado nos agendamentos.\n";
    }
    
    // Verificar quais alunos únicos estão sendo usados
    echo "\n=== ALUNOS ÚNICOS NOS AGENDAMENTOS ===\n";
    $alunos_unicos = "SELECT DISTINCT nome FROM agendamentos WHERE nome IS NOT NULL AND nome != '' ORDER BY nome";
    $result = $conn->query($alunos_unicos);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['nome'] . "\n";
        }
    } else {
        echo "Nenhum aluno encontrado nos agendamentos.\n";
    }
    
    // Verificar quais serviços únicos estão sendo usados
    echo "\n=== SERVIÇOS ÚNICOS NOS AGENDAMENTOS ===\n";
    $servicos_unicos = "SELECT DISTINCT servico FROM agendamentos WHERE servico IS NOT NULL AND servico != '' ORDER BY servico";
    $result = $conn->query($servicos_unicos);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['servico'] . "\n";
        }
    } else {
        echo "Nenhum serviço encontrado nos agendamentos.\n";
    }
    
} else {
    echo "Nenhum agendamento encontrado.\n";
}
?>




