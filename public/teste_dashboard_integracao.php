<?php
echo "=== TESTE DE INTEGRAÇÃO DO DASHBOARD ===\n\n";

// Testar conexão com o banco atual
include 'db.php';

echo "✅ Conectado ao banco: sistema_agendamento\n\n";

// Verificar se as tabelas existem
$tabelas = ['cursos', 'professores', 'alunos', 'agendamentos', 'usuarios'];

echo "=== VERIFICAÇÃO DAS TABELAS ===\n";
foreach ($tabelas as $tabela) {
    $check = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($check && $check->num_rows > 0) {
        $count = $conn->query("SELECT COUNT(*) as total FROM $tabela");
        $total = $count ? $count->fetch_assoc()['total'] : 0;
        echo "✅ Tabela '$tabela': $total registros\n";
    } else {
        echo "❌ Tabela '$tabela' NÃO existe\n";
    }
}

echo "\n=== TESTE DAS QUERIES DO DASHBOARD ===\n";

// Testar as mesmas queries que o dashboard usa
$queries = [
    'cursos_ativos' => "SELECT COUNT(*) as count FROM cursos WHERE status = 'ativo'",
    'professores_ativos' => "SELECT COUNT(*) as count FROM professores WHERE status = 'ativo'",
    'alunos_ativos' => "SELECT COUNT(*) as count FROM alunos WHERE status = 'ativo'",
    'agendamentos' => "SELECT COUNT(*) as count FROM agendamentos WHERE status != 'cancelado'"
];

foreach ($queries as $nome => $query) {
    $result = $conn->query($query);
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "✅ $nome: $count\n";
    } else {
        echo "❌ $nome: ERRO na query\n";
    }
}

echo "\n=== VERIFICAÇÃO DE DADOS REAIS ===\n";

// Verificar se há dados reais nas tabelas
$tabelas_com_dados = ['cursos', 'professores', 'alunos', 'agendamentos'];
$tem_dados_reais = false;

foreach ($tabelas_com_dados as $tabela) {
    $check = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($check && $check->num_rows > 0) {
        $count = $conn->query("SELECT COUNT(*) as total FROM $tabela");
        $total = $count ? $count->fetch_assoc()['total'] : 0;
        if ($total > 0) {
            $tem_dados_reais = true;
            echo "✅ $tabela tem $total registros reais\n";
            
            // Mostrar alguns exemplos
            $dados = $conn->query("SELECT * FROM $tabela LIMIT 2");
            if ($dados) {
                while ($row = $dados->fetch_assoc()) {
                    if (isset($row['nome'])) {
                        echo "   - " . $row['nome'] . "\n";
                    } elseif (isset($row['id'])) {
                        echo "   - ID: " . $row['id'] . "\n";
                    }
                }
            }
        } else {
            echo "❌ $tabela está vazia\n";
        }
    }
}

echo "\n=== CONCLUSÃO ===\n";
if ($tem_dados_reais) {
    echo "✅ O dashboard ESTÁ integrado com banco de dados e tem dados reais!\n";
} else {
    echo "❌ O dashboard NÃO tem dados reais no banco de dados!\n";
    echo "   As tabelas estão vazias ou não existem.\n";
}

$conn->close();
?>


