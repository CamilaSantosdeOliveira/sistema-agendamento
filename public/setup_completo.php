<?php
// Setup Completo do Sistema EduConnect
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Completo - EduConnect</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #1e40af; text-align: center; margin-bottom: 30px; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code { background: #f1f1f1; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 0.9em; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
        .btn { display: inline-block; background: #1e40af; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Setup Completo - EduConnect</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">Sistema completo de aulas particulares com usuários, professores e agendamentos</p>
        
        <?php
        try {
            // Conecta com MySQL
            $pdo = new PDO('mysql:host=localhost;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="step">✅ <strong>Passo 1:</strong> Conexão com MySQL estabelecida!</div>';
            
            // Cria o banco se não existir
            $pdo->exec("CREATE DATABASE IF NOT EXISTS sistema_agendamento CHARACTER SET utf8 COLLATE utf8_general_ci");
            echo '<div class="step">✅ <strong>Passo 2:</strong> Banco de dados criado/verificado!</div>';
            
            // Conecta com o banco específico
            $pdo = new PDO('mysql:host=localhost;dbname=sistema_agendamento;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Remove tabelas antigas se existirem
            $pdo->exec("DROP TABLE IF EXISTS professor_materias");
            $pdo->exec("DROP TABLE IF EXISTS agendamentos");
            $pdo->exec("DROP TABLE IF EXISTS usuarios");
            echo '<div class="step">🔄 <strong>Passo 3:</strong> Limpando estrutura antiga...</div>';
            
            // Cria tabela de usuários
            $sql_usuarios = "
            CREATE TABLE usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                senha VARCHAR(255) NOT NULL,
                telefone VARCHAR(20),
                tipo_usuario ENUM('aluno', 'professor') NOT NULL,
                formacao VARCHAR(200) NULL COMMENT 'Formação do professor',
                experiencia VARCHAR(50) NULL COMMENT 'Anos de experiência',
                valor_hora DECIMAL(10,2) NULL COMMENT 'Valor por hora do professor',
                descricao TEXT NULL COMMENT 'Descrição/apresentação do professor',
                ativo BOOLEAN DEFAULT TRUE,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_tipo (tipo_usuario)
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
            ";
            
            $pdo->exec($sql_usuarios);
            echo '<div class="step">✅ <strong>Passo 4:</strong> Tabela de usuários criada!</div>';
            
            // Cria tabela de matérias dos professores
            $sql_materias = "
            CREATE TABLE professor_materias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                professor_id INT NOT NULL,
                materia ENUM('matematica', 'portugues', 'ingles', 'fisica', 'quimica', 'biologia', 'historia', 'geografia', 'filosofia', 'sociologia', 'redacao', 'informatica') NOT NULL,
                FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                UNIQUE KEY unique_professor_materia (professor_id, materia)
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
            ";
            
            $pdo->exec($sql_materias);
            echo '<div class="step">✅ <strong>Passo 5:</strong> Tabela de matérias dos professores criada!</div>';
            
            // Cria tabela de agendamentos (atualizada)
            $sql_agendamentos = "
            CREATE TABLE agendamentos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                aluno_id INT NULL COMMENT 'ID do aluno (se logado)',
                professor_id INT NULL COMMENT 'ID do professor escolhido',
                nome VARCHAR(100) NOT NULL COMMENT 'Nome do aluno',
                email VARCHAR(100) NOT NULL COMMENT 'Email do aluno',
                telefone VARCHAR(20) NULL COMMENT 'WhatsApp/Telefone do aluno',
                professor VARCHAR(100) NULL COMMENT 'Professor preferido (nome)',
                data DATE NOT NULL COMMENT 'Data da aula',
                hora TIME NOT NULL COMMENT 'Horário da aula',
                servico VARCHAR(50) NOT NULL COMMENT 'Matéria/Disciplina',
                observacoes TEXT NULL COMMENT 'Observações adicionais',
                status ENUM('Pendente', 'Confirmado', 'Cancelado', 'Concluído') DEFAULT 'Pendente',
                valor DECIMAL(10,2) NULL COMMENT 'Valor acordado da aula',
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE SET NULL,
                FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
                INDEX idx_data (data),
                INDEX idx_status (status),
                INDEX idx_aluno (aluno_id),
                INDEX idx_professor (professor_id)
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
            ";
            
            $pdo->exec($sql_agendamentos);
            echo '<div class="step">✅ <strong>Passo 6:</strong> Tabela de agendamentos atualizada!</div>';
            
            // Criar tabela de avaliações
            $sql_avaliacoes = "
            CREATE TABLE avaliacoes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                agendamento_id INT,
                aluno_nome VARCHAR(100),
                professor_nome VARCHAR(100),
                nota INT CHECK (nota >= 1 AND nota <= 5),
                comentario TEXT,
                criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
            ";
            $pdo->exec($sql_avaliacoes);
            echo '<div class="step">✅ <strong>Passo 7:</strong> Tabela de avaliações criada!</div>';
            
            // Criar tabela de notificações
            $sql_notificacoes = "
            CREATE TABLE notificacoes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                agendamento_id INT,
                tipo ENUM('confirmacao_enviada', 'lembrete_enviado') NOT NULL,
                enviado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
            ";
            $pdo->exec($sql_notificacoes);
            echo '<div class="step">✅ <strong>Passo 8:</strong> Tabela de notificações criada!</div>';
            
            // Criar tabela de pagamentos
            $sql_pagamentos = "
            CREATE TABLE pagamentos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                agendamento_id INT,
                transacao_id VARCHAR(100) UNIQUE,
                valor DECIMAL(8,2),
                metodo_pagamento ENUM('cartao', 'pix', 'boleto') DEFAULT 'cartao',
                status ENUM('pendente', 'aprovado', 'rejeitado', 'reembolsado') DEFAULT 'pendente',
                reembolso_id VARCHAR(100) NULL,
                motivo_reembolso TEXT NULL,
                criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                reembolsado_em TIMESTAMP NULL,
                FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
            ";
            $pdo->exec($sql_pagamentos);
            echo '<div class="step">✅ <strong>Passo 9:</strong> Tabela de pagamentos criada!</div>';
            
            // Inserir usuários de exemplo
            
            // Professores de exemplo
            $professores = [
                [
                    'nome' => 'Prof. Maria Santos',
                    'email' => 'maria.santos@educonnect.com',
                    'senha' => password_hash('123456', PASSWORD_DEFAULT),
                    'telefone' => '(11) 99999-1234',
                    'formacao' => 'Licenciatura em Matemática - USP',
                    'experiencia' => '6-10',
                    'valor_hora' => 65.00,
                    'descricao' => 'Professora especializada em matemática do ensino médio e pré-vestibular. Metodologia focada na resolução de exercícios e compreensão de conceitos fundamentais.',
                    'materias' => ['matematica', 'fisica']
                ],
                [
                    'nome' => 'Prof. Carlos Lima',
                    'email' => 'carlos.lima@educonnect.com',
                    'senha' => password_hash('123456', PASSWORD_DEFAULT),
                    'telefone' => '(11) 99999-5678',
                    'formacao' => 'Letras - Inglês/Português - PUC',
                    'experiencia' => '10+',
                    'valor_hora' => 70.00,
                    'descricao' => 'Professor experiente em inglês e português. Aulas dinâmicas com foco em conversação e gramática aplicada.',
                    'materias' => ['ingles', 'portugues', 'redacao']
                ],
                [
                    'nome' => 'Prof. Ana Costa',
                    'email' => 'ana.costa@educonnect.com',
                    'senha' => password_hash('123456', PASSWORD_DEFAULT),
                    'telefone' => '(11) 99999-9012',
                    'formacao' => 'Bacharelado em Química - UNICAMP',
                    'experiencia' => '3-5',
                    'valor_hora' => 60.00,
                    'descricao' => 'Química com especialização em química orgânica. Aulas práticas e teóricas adaptadas ao nível do aluno.',
                    'materias' => ['quimica', 'biologia']
                ]
            ];
            
            $stmt_usuario = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario, formacao, experiencia, valor_hora, descricao) VALUES (?, ?, ?, ?, 'professor', ?, ?, ?, ?)");
            $stmt_materia = $pdo->prepare("INSERT INTO professor_materias (professor_id, materia) VALUES (?, ?)");
            
            foreach ($professores as $prof) {
                $stmt_usuario->execute([
                    $prof['nome'],
                    $prof['email'],
                    $prof['senha'],
                    $prof['telefone'],
                    $prof['formacao'],
                    $prof['experiencia'],
                    $prof['valor_hora'],
                    $prof['descricao']
                ]);
                
                $professor_id = $pdo->lastInsertId();
                
                foreach ($prof['materias'] as $materia) {
                    $stmt_materia->execute([$professor_id, $materia]);
                }
            }
            
            // Alunos de exemplo
            $alunos = [
                [
                    'nome' => 'João Silva',
                    'email' => 'joao.silva@email.com',
                    'senha' => password_hash('123456', PASSWORD_DEFAULT),
                    'telefone' => '(11) 88888-1234'
                ],
                [
                    'nome' => 'Mariana Oliveira',
                    'email' => 'mariana.oliveira@email.com',
                    'senha' => password_hash('123456', PASSWORD_DEFAULT),
                    'telefone' => '(11) 88888-5678'
                ]
            ];
            
            $stmt_aluno = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario) VALUES (?, ?, ?, ?, 'aluno')");
            
            foreach ($alunos as $aluno) {
                $stmt_aluno->execute([
                    $aluno['nome'],
                    $aluno['email'],
                    $aluno['senha'],
                    $aluno['telefone']
                ]);
            }
            
            echo '<div class="step">✅ <strong>Passo 10:</strong> Usuários de exemplo inseridos!</div>';
            
            // Agendamentos de exemplo
            $agendamentos = [
                [
                    'aluno_id' => 4, // João Silva
                    'professor_id' => 1, // Prof. Maria Santos
                    'nome' => 'João Silva',
                    'email' => 'joao.silva@email.com',
                    'telefone' => '(11) 88888-1234',
                    'professor' => 'Prof. Maria Santos',
                    'data' => date('Y-m-d', strtotime('+1 day')),
                    'hora' => '14:00',
                    'servico' => 'matematica',
                    'observacoes' => 'Precisa de ajuda com equações do 2º grau',
                    'status' => 'Confirmado',
                    'valor' => 65.00
                ],
                [
                    'aluno_id' => 5, // Mariana Oliveira
                    'professor_id' => 2, // Prof. Carlos Lima
                    'nome' => 'Mariana Oliveira',
                    'email' => 'mariana.oliveira@email.com',
                    'telefone' => '(11) 88888-5678',
                    'professor' => 'Prof. Carlos Lima',
                    'data' => date('Y-m-d', strtotime('+2 days')),
                    'hora' => '16:30',
                    'servico' => 'ingles',
                    'observacoes' => 'Foco em conversação e gramática',
                    'status' => 'Pendente',
                    'valor' => 70.00
                ]
            ];
            
            $stmt_agendamento = $pdo->prepare("
                INSERT INTO agendamentos (aluno_id, professor_id, nome, email, telefone, professor, data, hora, servico, observacoes, status, valor, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            foreach ($agendamentos as $ag) {
                $stmt_agendamento->execute([
                    $ag['aluno_id'],
                    $ag['professor_id'],
                    $ag['nome'],
                    $ag['email'],
                    $ag['telefone'],
                    $ag['professor'],
                    $ag['data'],
                    $ag['hora'],
                    $ag['servico'],
                    $ag['observacoes'],
                    $ag['status'],
                    $ag['valor']
                ]);
            }
            
            echo '<div class="step">✅ <strong>Passo 11:</strong> Agendamentos de exemplo inseridos!</div>';
            
            // Inserir avaliações de exemplo
            $avaliacoes_exemplo = [
                [
                    'agendamento_id' => 1,
                    'aluno_nome' => 'João Silva',
                    'professor_nome' => 'Prof. Maria Santos',
                    'nota' => 5,
                    'comentario' => 'Excelente professora! Explica muito bem e tem muita paciência. Recomendo!'
                ],
                [
                    'agendamento_id' => 2,
                    'aluno_nome' => 'Mariana Oliveira',
                    'professor_nome' => 'Prof. Carlos Lima',
                    'nota' => 4,
                    'comentario' => 'Ótima aula de inglês. O professor é bem didático e as atividades são interessantes.'
                ]
            ];
            
            $stmt_avaliacao = $pdo->prepare("
                INSERT INTO avaliacoes (agendamento_id, aluno_nome, professor_nome, nota, comentario, criado_em) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            foreach ($avaliacoes_exemplo as $av) {
                $stmt_avaliacao->execute([
                    $av['agendamento_id'],
                    $av['aluno_nome'],
                    $av['professor_nome'],
                    $av['nota'],
                    $av['comentario']
                ]);
            }
            
            echo '<div class="step">✅ <strong>Passo 12:</strong> Avaliações de exemplo inseridas!</div>';
            
            // Inserir pagamentos de exemplo
            $pagamentos_exemplo = [
                [
                    'agendamento_id' => 1,
                    'transacao_id' => 'TXN_' . time() . '_1001',
                    'valor' => 65.00,
                    'metodo_pagamento' => 'cartao',
                    'status' => 'aprovado'
                ],
                [
                    'agendamento_id' => 2,
                    'transacao_id' => 'TXN_' . time() . '_1002',
                    'valor' => 70.00,
                    'metodo_pagamento' => 'pix',
                    'status' => 'pendente'
                ]
            ];
            
            $stmt_pagamento = $pdo->prepare("
                INSERT INTO pagamentos (agendamento_id, transacao_id, valor, metodo_pagamento, status, criado_em) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            foreach ($pagamentos_exemplo as $pag) {
                $stmt_pagamento->execute([
                    $pag['agendamento_id'],
                    $pag['transacao_id'],
                    $pag['valor'],
                    $pag['metodo_pagamento'],
                    $pag['status']
                ]);
            }
            
            echo '<div class="step">✅ <strong>Passo 13:</strong> Pagamentos de exemplo inseridos!</div>';
            
            echo '<div class="success">
                <h3>🎉 Sistema EduConnect configurado com sucesso!</h3>
                
                <div class="grid">
                    <div>
                        <h4>📊 Estatísticas:</h4>
                        <ul>
                            <li>✅ 3 Professores cadastrados</li>
                            <li>✅ 2 Alunos cadastrados</li>
                            <li>✅ 2 Agendamentos de exemplo</li>
                            <li>✅ 12 Matérias disponíveis</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4>🔑 Contas de Teste:</h4>
                        <div class="code">
                            <strong>Professores:</strong><br>
                            maria.santos@educonnect.com<br>
                            carlos.lima@educonnect.com<br>
                            ana.costa@educonnect.com<br><br>
                            
                            <strong>Alunos:</strong><br>
                            joao.silva@email.com<br>
                            mariana.oliveira@email.com<br><br>
                            
                            <strong>Senha para todos:</strong> 123456
                        </div>
                    </div>
                </div>
                
                <h4>🚀 Próximos Passos:</h4>
                <div style="text-align: center;">
                    <a href="index.html" class="btn">🏠 Página Inicial</a>
                    <a href="login.html" class="btn">🔑 Login</a>
                    <a href="sistema_final.html" class="btn">� Sistema de Aulas</a>
                    <a href="avaliacoes.html" class="btn">⭐ Avaliações</a>
                    <a href="admin.html" class="btn">�️ Painel Admin</a>
                </div>
            </div>';
            
        } catch(Exception $e) {
            echo '<div class="error">
                <h3>❌ Erro na configuração:</h3>
                <p><strong>Mensagem:</strong> ' . $e->getMessage() . '</p>
                <p><strong>Solução:</strong> Verifique se o XAMPP está rodando (Apache + MySQL)</p>
            </div>';
        }
        ?>
        
        <div class="info">
            <h3>🔧 Estrutura do Banco de Dados:</h3>
            <div class="code">
                <strong>usuarios:</strong> id, nome, email, senha, telefone, tipo_usuario, formacao, experiencia, valor_hora, descricao<br>
                <strong>professor_materias:</strong> id, professor_id, materia<br>
                <strong>agendamentos:</strong> id, aluno_id, professor_id, nome, email, telefone, professor, data, hora, servico, observacoes, status, valor
            </div>
        </div>
    </div>
</body>
</html>
