<?php
// Conectar ao banco de dados
include 'db.php';

echo "<h2>🎯 Inserindo Usuários de Teste</h2>";

try {
    // Inserir professores de teste
    $professores = [
        [
            'nome' => 'Prof. Maria Santos',
            'email' => 'maria.santos@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-1234',
            'tipo_usuario' => 'professor',
            'formacao' => 'Licenciatura em Matemática - USP',
            'experiencia' => '6-10 anos',
            'valor_hora' => 65.00,
            'descricao' => 'Professora especializada em matemática do ensino médio e pré-vestibular.',
            'ativo' => 1
        ],
        [
            'nome' => 'Prof. Carlos Lima',
            'email' => 'carlos.lima@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-5678',
            'tipo_usuario' => 'professor',
            'formacao' => 'Letras - Inglês/Português - PUC',
            'experiencia' => '10+ anos',
            'valor_hora' => 70.00,
            'descricao' => 'Professor experiente em inglês e português. Aulas dinâmicas.',
            'ativo' => 1
        ],
        [
            'nome' => 'Prof. Ana Silva',
            'email' => 'ana.silva@educonnect.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-9012',
            'tipo_usuario' => 'professor',
            'formacao' => 'Bacharelado em Química - UNICAMP',
            'experiencia' => '3-5 anos',
            'valor_hora' => 60.00,
            'descricao' => 'Química com especialização em química orgânica.',
            'ativo' => 1
        ]
    ];

    // Inserir alunos de teste
    $alunos = [
        [
            'nome' => 'João Pedro',
            'email' => 'joao.pedro@email.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 88888-1111',
            'tipo_usuario' => 'aluno',
            'formacao' => 'Ensino Médio',
            'experiencia' => 'Iniciante',
            'valor_hora' => 0.00,
            'descricao' => 'Aluno interessado em programação',
            'ativo' => 1
        ],
        [
            'nome' => 'Maria Clara',
            'email' => 'maria.clara@email.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 88888-2222',
            'tipo_usuario' => 'aluno',
            'formacao' => 'Técnico em Informática',
            'experiencia' => 'Intermediário',
            'valor_hora' => 0.00,
            'descricao' => 'Aluna com conhecimento básico em TI',
            'ativo' => 1
        ],
        [
            'nome' => 'Pedro Santos',
            'email' => 'pedro.santos@email.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'telefone' => '(11) 88888-3333',
            'tipo_usuario' => 'aluno',
            'formacao' => 'Graduação em Sistemas',
            'experiencia' => 'Avançado',
            'valor_hora' => 0.00,
            'descricao' => 'Aluno com experiência em desenvolvimento',
            'ativo' => 1
        ]
    ];

    // Inserir professores
    echo "<h3>👨‍🏫 Inserindo Professores...</h3>";
    foreach ($professores as $professor) {
        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario, formacao, experiencia, valor_hora, descricao, ativo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "❌ Erro ao preparar query: " . $conn->error . "<br>";
            continue;
        }
        
        $stmt->bind_param("sssssssdsi", 
            $professor['nome'], 
            $professor['email'], 
            $professor['senha'], 
            $professor['telefone'], 
            $professor['tipo_usuario'], 
            $professor['formacao'], 
            $professor['experiencia'], 
            $professor['valor_hora'], 
            $professor['descricao'], 
            $professor['ativo']
        );
        
        if ($stmt->execute()) {
            echo "✅ Professor {$professor['nome']} inserido com sucesso!<br>";
        } else {
            echo "❌ Erro ao inserir professor {$professor['nome']}: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }

    // Inserir alunos
    echo "<h3>👨‍🎓 Inserindo Alunos...</h3>";
    foreach ($alunos as $aluno) {
        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario, formacao, experiencia, valor_hora, descricao, ativo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "❌ Erro ao preparar query: " . $conn->error . "<br>";
            continue;
        }
        
        $stmt->bind_param("sssssssdsi", 
            $aluno['nome'], 
            $aluno['email'], 
            $aluno['senha'], 
            $aluno['telefone'], 
            $aluno['tipo_usuario'], 
            $aluno['formacao'], 
            $aluno['experiencia'], 
            $aluno['valor_hora'], 
            $aluno['descricao'], 
            $aluno['ativo']
        );
        
        if ($stmt->execute()) {
            echo "✅ Aluno {$aluno['nome']} inserido com sucesso!<br>";
        } else {
            echo "❌ Erro ao inserir aluno {$aluno['nome']}: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }

    // Verificar se a tabela pagamentos tem as colunas necessárias
    $result = $conn->query("DESCRIBE pagamentos");
    if ($result) {
        $colunas = [];
        while ($row = $result->fetch_assoc()) {
            $colunas[] = $row['Field'];
        }

        // Inserir pagamentos se a tabela existir
        if (in_array('valor', $colunas)) {
            echo "<h3>💳 Inserindo Pagamentos...</h3>";
            
            $pagamentos = [
                [
                    'valor' => 150.00,
                    'status' => 'aprovado',
                    'data_pagamento' => '2025-01-15',
                    'metodo' => 'cartao_credito'
                ],
                [
                    'valor' => 200.00,
                    'status' => 'aprovado',
                    'data_pagamento' => '2025-01-20',
                    'metodo' => 'pix'
                ],
                [
                    'valor' => 180.00,
                    'status' => 'pendente',
                    'data_pagamento' => '2025-01-25',
                    'metodo' => 'boleto'
                ]
            ];
            
            foreach ($pagamentos as $pagamento) {
                $sql = "INSERT INTO pagamentos (valor, status, data_pagamento, metodo) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                if (!$stmt) {
                    echo "❌ Erro ao preparar query de pagamento: " . $conn->error . "<br>";
                    continue;
                }
                
                $stmt->bind_param("dsss", 
                    $pagamento['valor'], 
                    $pagamento['status'], 
                    $pagamento['data_pagamento'], 
                    $pagamento['metodo']
                );
                
                if ($stmt->execute()) {
                    echo "✅ Pagamento de R$ {$pagamento['valor']} inserido com sucesso!<br>";
                } else {
                    echo "❌ Erro ao inserir pagamento: " . $stmt->error . "<br>";
                }
                $stmt->close();
            }
        } else {
            echo "<h3>⚠️ Tabela pagamentos não tem a estrutura esperada</h3>";
            echo "Colunas encontradas: " . implode(', ', $colunas) . "<br>";
        }
    } else {
        echo "<h3>⚠️ Não foi possível verificar a estrutura da tabela pagamentos</h3>";
    }

    echo "<br><h2>🎉 Processo Concluído!</h2>";
    echo "<p>Agora você pode acessar o <a href='dashboard_corrigido.php'>Dashboard</a> para ver os dados!</p>";
    echo "<p><strong>Credenciais de teste:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> maria.santos@educonnect.com</li>";
    echo "<li><strong>Senha:</strong> 123456</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>


