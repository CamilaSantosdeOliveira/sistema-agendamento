<?php
// Setup do Sistema EduConnect - Aulas Particulares
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup EduConnect - Sistema de Aulas</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #1e40af; text-align: center; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code { background: #f1f1f1; padding: 10px; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Setup EduConnect - Sistema de Aulas Particulares</h1>
        
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
            
            // Remove tabela antiga se existir
            $pdo->exec("DROP TABLE IF EXISTS agendamentos");
            echo '<div class="step">🔄 <strong>Passo 3:</strong> Limpando estrutura antiga...</div>';
            
            // Cria tabela para aulas particulares
            $sql = "
            CREATE TABLE agendamentos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL COMMENT 'Nome do aluno',
                email VARCHAR(100) NOT NULL COMMENT 'Email do aluno',
                telefone VARCHAR(20) NULL COMMENT 'WhatsApp/Telefone do aluno',
                professor VARCHAR(100) NULL COMMENT 'Professor preferido',
                data DATE NOT NULL COMMENT 'Data da aula',
                hora TIME NOT NULL COMMENT 'Horário da aula',
                servico VARCHAR(50) NOT NULL COMMENT 'Matéria/Disciplina',
                observacoes TEXT NULL COMMENT 'Observações adicionais',
                status VARCHAR(20) DEFAULT 'Pendente' COMMENT 'Status da solicitação',
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_data (data),
                INDEX idx_servico (servico),
                INDEX idx_status (status)
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
            ";
            
            $pdo->exec($sql);
            echo '<div class="step">✅ <strong>Passo 4:</strong> Tabela de aulas criada com sucesso!</div>';
            
            // Insere dados de exemplo
            $exemplos = [
                [
                    'nome' => 'João Silva',
                    'email' => 'joao@email.com',
                    'telefone' => '(11) 99999-1234',
                    'professor' => 'Prof. Maria Santos',
                    'data' => date('Y-m-d', strtotime('+1 day')),
                    'hora' => '14:00',
                    'servico' => 'matematica',
                    'observacoes' => 'Precisa de ajuda com equações do 2º grau e funções',
                    'status' => 'Confirmado'
                ],
                [
                    'nome' => 'Ana Costa',
                    'email' => 'ana@email.com',
                    'telefone' => '(11) 99999-5678',
                    'professor' => 'Prof. Carlos Lima',
                    'data' => date('Y-m-d', strtotime('+2 days')),
                    'hora' => '16:30',
                    'servico' => 'ingles',
                    'observacoes' => 'Foco em conversação e gramática básica',
                    'status' => 'Pendente'
                ],
                [
                    'nome' => 'Pedro Oliveira',
                    'email' => 'pedro@email.com',
                    'telefone' => '(11) 99999-9012',
                    'professor' => 'Qualquer',
                    'data' => date('Y-m-d', strtotime('+3 days')),
                    'hora' => '10:00',
                    'servico' => 'fisica',
                    'observacoes' => 'Dificuldades em cinemática e dinâmica',
                    'status' => 'Pendente'
                ]
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO agendamentos (nome, email, telefone, professor, data, hora, servico, observacoes, status, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            foreach($exemplos as $exemplo) {
                $stmt->execute([
                    $exemplo['nome'],
                    $exemplo['email'],
                    $exemplo['telefone'],
                    $exemplo['professor'],
                    $exemplo['data'],
                    $exemplo['hora'],
                    $exemplo['servico'],
                    $exemplo['observacoes'],
                    $exemplo['status']
                ]);
            }
            
            echo '<div class="step">✅ <strong>Passo 5:</strong> Dados de exemplo inseridos!</div>';
            
            echo '<div class="success">
                <h3>🎉 Sistema EduConnect configurado com sucesso!</h3>
                <p><strong>Próximos passos:</strong></p>
                <ol>
                    <li>Acesse: <a href="sistema_final.html" target="_blank">sistema_final.html</a></li>
                    <li>Teste o agendamento de aulas</li>
                    <li>Veja as aulas já agendadas como exemplo</li>
                </ol>
                
                <h4>📊 Estatísticas do Sistema:</h4>
                <ul>
                    <li>✅ Banco de dados: <code>sistema_agendamento</code></li>
                    <li>✅ Tabela: <code>agendamentos</code> (com campos para aulas particulares)</li>
                    <li>✅ Exemplos inseridos: 3 aulas agendadas</li>
                    <li>✅ Matérias disponíveis: 12 disciplinas</li>
                </ul>
            </div>';
            
        } catch(Exception $e) {
            echo '<div class="error">
                <h3>❌ Erro na configuração:</h3>
                <p><strong>Mensagem:</strong> ' . $e->getMessage() . '</p>
                <p><strong>Solução:</strong> Verifique se o XAMPP está rodando (Apache + MySQL)</p>
            </div>';
        }
        ?>
        
        <div class="step">
            <h3>🔧 Informações Técnicas:</h3>
            <div class="code">
                <strong>Estrutura da Tabela de Aulas:</strong><br>
                • id (chave primária)<br>
                • nome (nome do aluno)<br>
                • email (contato do aluno)<br>
                • telefone (WhatsApp/telefone)<br>
                • professor (professor preferido)<br>
                • data (data da aula)<br>
                • hora (horário da aula)<br>
                • servico (matéria/disciplina)<br>
                • observacoes (detalhes adicionais)<br>
                • status (Pendente/Confirmado/Cancelado)<br>
                • criado_em (timestamp)
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="sistema_final.html" style="background: #1e40af; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                🚀 Acessar Sistema EduConnect
            </a>
        </div>
    </div>
</body>
</html>
