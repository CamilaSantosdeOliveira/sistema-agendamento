<?php
// Forçar atualização - sem cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conectar ao banco de dados
include 'db.php';

echo "<h1>🔄 Atualizando Dados dos Alunos</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f0f8ff;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .info{color:blue;font-weight:bold;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;}</style>";

// Verificar se a tabela tem as colunas necessárias
echo "<h2>📋 Verificando estrutura da tabela...</h2>";

$result = $conn->query("DESCRIBE usuarios");
$colunas = [];
while ($row = $result->fetch_assoc()) {
    $colunas[] = $row['Field'];
}

echo "<div class='info'>Colunas encontradas: " . implode(', ', $colunas) . "</div>";

// Adicionar colunas se não existirem
$colunas_necessarias = [
    'telefone' => 'VARCHAR(20)',
    'data_nascimento' => 'DATE',
    'endereco' => 'TEXT',
    'cidade' => 'VARCHAR(100)',
    'estado' => 'VARCHAR(2)',
    'cep' => 'VARCHAR(10)'
];

foreach ($colunas_necessarias as $coluna => $tipo) {
    if (!in_array($coluna, $colunas)) {
        $sql = "ALTER TABLE usuarios ADD COLUMN $coluna $tipo";
        if ($conn->query($sql)) {
            echo "<div class='success'>✅ Coluna '$coluna' adicionada com sucesso!</div>";
        } else {
            echo "<div class='error'>❌ Erro ao adicionar coluna '$coluna': " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ Coluna '$coluna' já existe</div>";
    }
}

// Dados dos alunos para atualizar
$dados_alunos = [
    [
        'nome' => 'Ana Costa',
        'telefone' => '(11) 98765-4321',
        'data_nascimento' => '1995-03-15',
        'endereco' => 'Rua das Flores, 123',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '01234-567'
    ],
    [
        'nome' => 'João Silva',
        'telefone' => '(11) 91234-5678',
        'data_nascimento' => '1992-07-22',
        'endereco' => 'Av. Paulista, 1000',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '01310-100'
    ],
    [
        'nome' => 'Maria Santos',
        'telefone' => '(11) 94567-8901',
        'data_nascimento' => '1998-11-08',
        'endereco' => 'Rua Augusta, 500',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '01205-000'
    ],
    [
        'nome' => 'Pedro Oliveira',
        'telefone' => '(11) 92345-6789',
        'data_nascimento' => '1990-05-12',
        'endereco' => 'Rua Oscar Freire, 200',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '01426-000'
    ],
    [
        'nome' => 'Lucia Mendes',
        'telefone' => '(11) 95678-9012',
        'data_nascimento' => '1993-09-30',
        'endereco' => 'Rua Haddock Lobo, 150',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '01414-000'
    ],
    [
        'nome' => 'Carlos Ferreira',
        'telefone' => '(11) 93456-7890',
        'data_nascimento' => '1991-12-03',
        'endereco' => 'Rua Bela Cintra, 300',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '01415-000'
    ],
    [
        'nome' => 'Fernanda Lima',
        'telefone' => '(11) 97890-1234',
        'data_nascimento' => '1996-01-25',
        'endereco' => 'Rua Teodoro Sampaio, 400',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '05406-000'
    ],
    [
        'nome' => 'Gabriel Souza',
        'telefone' => '(11) 96789-0123',
        'data_nascimento' => '1994-08-17',
        'endereco' => 'Rua Cardeal Arcoverde, 250',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '05407-000'
    ],
    [
        'nome' => 'Isabela Martins',
        'telefone' => '(11) 98901-2345',
        'data_nascimento' => '1997-04-10',
        'endereco' => 'Rua Harmonia, 180',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '05435-000'
    ],
    [
        'nome' => 'Roberto Alves',
        'telefone' => '(11) 90123-4567',
        'data_nascimento' => '1989-06-28',
        'endereco' => 'Rua Fradique Coutinho, 350',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep' => '05416-000'
    ]
];

echo "<h2>👨‍🎓 Atualizando dados dos alunos...</h2>";

$atualizados = 0;
$erros = 0;

foreach ($dados_alunos as $dados) {
    $nome = $dados['nome'];
    $telefone = $dados['telefone'];
    $data_nascimento = $dados['data_nascimento'];
    $endereco = $dados['endereco'];
    $cidade = $dados['cidade'];
    $estado = $dados['estado'];
    $cep = $dados['cep'];
    
    $sql = "UPDATE usuarios SET 
            telefone = ?, 
            data_nascimento = ?, 
            endereco = ?, 
            cidade = ?, 
            estado = ?, 
            cep = ? 
            WHERE nome = ? AND tipo_usuario = 'aluno'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $telefone, $data_nascimento, $endereco, $cidade, $estado, $cep, $nome);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<div class='success'>✅ $nome - Dados atualizados!</div>";
            $atualizados++;
        } else {
            echo "<div class='info'>ℹ️ $nome - Nenhuma alteração necessária</div>";
        }
    } else {
        echo "<div class='error'>❌ $nome - Erro: " . $stmt->error . "</div>";
        $erros++;
    }
}

echo "<h2>📊 Resumo da Atualização</h2>";
echo "<div class='success'>✅ Alunos atualizados: $atualizados</div>";
if ($erros > 0) {
    echo "<div class='error'>❌ Erros: $erros</div>";
}

// Verificar resultado
echo "<h2>🔍 Verificando dados atualizados...</h2>";
$result = $conn->query("
    SELECT nome, telefone, data_nascimento, endereco, cidade, estado, cep 
    FROM usuarios 
    WHERE tipo_usuario = 'aluno' 
    ORDER BY nome
");

if ($result && $result->num_rows > 0) {
    echo "<table style='border-collapse:collapse;width:100%;margin-top:20px;'>";
    echo "<tr style='background:#f2f2f2;'>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Nome</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Telefone</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Data Nasc.</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Endereço</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Cidade</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>Estado</th>";
    echo "<th style='border:1px solid #ddd;padding:8px;'>CEP</th>";
    echo "</tr>";
    
    while ($aluno = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='border:1px solid #ddd;padding:8px;'><strong>{$aluno['nome']}</strong></td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$aluno['telefone']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>" . date('d/m/Y', strtotime($aluno['data_nascimento'])) . "</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$aluno['endereco']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$aluno['cidade']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$aluno['estado']}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$aluno['cep']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>🎯 PRÓXIMOS PASSOS:</h2>";
echo "<div style='margin:20px 0;'>";
echo "<a href='alunos.php' style='background:green;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>👨‍🎓 Ver Página Alunos</a>";
echo "<a href='dashboard_final.php' style='background:blue;color:white;padding:10px;text-decoration:none;border-radius:5px;margin:5px;'>📊 Dashboard</a>";
echo "</div>";

$conn->close();
?>









