<?php
/**
 * Sistema de Notificações por E-mail
 * Gerencia envio de confirmações e lembretes
 */

require_once 'config.php';

class EmailNotification {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Envia e-mail de confirmação de agendamento
     */
    public function enviarConfirmacaoAgendamento($agendamentoId) {
        try {
            // Buscar dados do agendamento
            $stmt = $this->pdo->prepare("
                SELECT a.*, u.nome as professor_nome, u.email as professor_email
                FROM agendamentos a
                LEFT JOIN usuarios u ON u.nome = a.professor AND u.tipo_usuario = 'professor'
                WHERE a.id = ?
            ");
            $stmt->execute([$agendamentoId]);
            $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$agendamento) {
                return false;
            }
            
            // E-mail para o aluno
            $this->enviarEmailAluno($agendamento, 'confirmacao');
            
            // E-mail para o professor (se encontrado)
            if ($agendamento['professor_email']) {
                $this->enviarEmailProfessor($agendamento, 'nova_aula');
            }
            
            // Registrar notificação no banco
            $this->registrarNotificacao($agendamentoId, 'confirmacao_enviada');
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erro ao enviar confirmação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envia lembrete 24h antes da aula
     */
    public function enviarLembretes() {
        try {
            // Buscar aulas para amanhã que ainda não receberam lembrete
            $amanha = date('Y-m-d', strtotime('+1 day'));
            
            $stmt = $this->pdo->prepare("
                SELECT a.*, u.nome as professor_nome, u.email as professor_email
                FROM agendamentos a
                LEFT JOIN usuarios u ON u.nome = a.professor AND u.tipo_usuario = 'professor'
                WHERE a.data = ? 
                AND a.status IN ('Pendente', 'Confirmada')
                AND a.id NOT IN (
                    SELECT agendamento_id FROM notificacoes 
                    WHERE tipo = 'lembrete_enviado'
                )
            ");
            $stmt->execute([$amanha]);
            $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $enviados = 0;
            foreach ($agendamentos as $agendamento) {
                // E-mail para o aluno
                $this->enviarEmailAluno($agendamento, 'lembrete');
                
                // E-mail para o professor
                if ($agendamento['professor_email']) {
                    $this->enviarEmailProfessor($agendamento, 'lembrete');
                }
                
                // Registrar lembrete enviado
                $this->registrarNotificacao($agendamento['id'], 'lembrete_enviado');
                $enviados++;
            }
            
            return $enviados;
            
        } catch (Exception $e) {
            error_log("Erro ao enviar lembretes: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Envia e-mail para o aluno
     */
    private function enviarEmailAluno($agendamento, $tipo) {
        $data = date('d/m/Y', strtotime($agendamento['data']));
        $hora = $agendamento['hora'];
        $materia = ucfirst($agendamento['servico']);
        $professor = $agendamento['professor'] ?: 'A definir';
        
        if ($tipo === 'confirmacao') {
            $assunto = "✅ Confirmação de Agendamento - EduConnect";
            $mensagem = "
                <h2 style='color: #1e40af;'>🎉 Aula Agendada com Sucesso!</h2>
                <p>Olá <strong>{$agendamento['nome']}</strong>,</p>
                <p>Sua aula foi agendada com sucesso! Aqui estão os detalhes:</p>
                
                <div style='background: #f8fafc; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                    <p><strong>📚 Matéria:</strong> {$materia}</p>
                    <p><strong>👨‍🏫 Professor:</strong> {$professor}</p>
                    <p><strong>📅 Data:</strong> {$data}</p>
                    <p><strong>⏰ Horário:</strong> {$hora}</p>
                    <p><strong>📋 Status:</strong> {$agendamento['status']}</p>
                </div>
                
                <p>Em breve, o professor entrará em contato para confirmar os detalhes finais.</p>
                <p>Você receberá um lembrete 24 horas antes da aula.</p>
                
                <p style='margin-top: 30px;'>
                    <a href='http://localhost/Sistema%20De%20Agendamento/public/login.html' 
                       style='background: #1e40af; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px;'>
                        🔗 Acessar Minha Conta
                    </a>
                </p>
                
                <hr style='margin: 30px 0;'>
                <p style='color: #666; font-size: 0.9em;'>
                    EduConnect - Conectando alunos e professores para aulas particulares de qualidade<br>
                    Sistema de Agendamento de Aulas Particulares
                </p>
            ";
        } else {
            $assunto = "⏰ Lembrete: Sua aula é amanhã!";
            $mensagem = "
                <h2 style='color: #f59e0b;'>⏰ Lembrete de Aula</h2>
                <p>Olá <strong>{$agendamento['nome']}</strong>,</p>
                <p>Este é um lembrete de que você tem uma aula agendada para <strong>amanhã</strong>!</p>
                
                <div style='background: #fef3c7; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #f59e0b;'>
                    <p><strong>📚 Matéria:</strong> {$materia}</p>
                    <p><strong>👨‍🏫 Professor:</strong> {$professor}</p>
                    <p><strong>📅 Data:</strong> {$data}</p>
                    <p><strong>⏰ Horário:</strong> {$hora}</p>
                </div>
                
                <p>💡 <strong>Dicas para aproveitar melhor sua aula:</strong></p>
                <ul>
                    <li>Prepare suas dúvidas com antecedência</li>
                    <li>Tenha material de anotação em mãos</li>
                    <li>Esteja em um ambiente tranquilo</li>
                </ul>
                
                <p style='margin-top: 30px;'>
                    <a href='http://localhost/Sistema%20De%20Agendamento/public/login.html' 
                       style='background: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px;'>
                        📱 Ver Detalhes da Aula
                    </a>
                </p>
                
                <hr style='margin: 30px 0;'>
                <p style='color: #666; font-size: 0.9em;'>
                    EduConnect - Conectando alunos e professores<br>
                    Sistema de Agendamento de Aulas Particulares
                </p>
            ";
        }
        
        $this->enviarEmail($agendamento['email'], $assunto, $mensagem);
    }
    
    /**
     * Envia e-mail para o professor
     */
    private function enviarEmailProfessor($agendamento, $tipo) {
        $data = date('d/m/Y', strtotime($agendamento['data']));
        $hora = $agendamento['hora'];
        $materia = ucfirst($agendamento['servico']);
        
        if ($tipo === 'nova_aula') {
            $assunto = "🎯 Nova Aula Agendada - EduConnect";
            $mensagem = "
                <h2 style='color: #10b981;'>🎯 Nova Aula Agendada!</h2>
                <p>Olá <strong>{$agendamento['professor']}</strong>,</p>
                <p>Você tem uma nova aula agendada! Aqui estão os detalhes:</p>
                
                <div style='background: #ecfdf5; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #10b981;'>
                    <p><strong>👤 Aluno:</strong> {$agendamento['nome']}</p>
                    <p><strong>📧 E-mail do aluno:</strong> {$agendamento['email']}</p>
                    <p><strong>📱 Telefone:</strong> " . ($agendamento['telefone'] ?: 'Não informado') . "</p>
                    <p><strong>📚 Matéria:</strong> {$materia}</p>
                    <p><strong>📅 Data:</strong> {$data}</p>
                    <p><strong>⏰ Horário:</strong> {$hora}</p>
                </div>
                
                " . ($agendamento['observacoes'] ? "
                <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin: 15px 0;'>
                    <p><strong>📝 Observações do aluno:</strong></p>
                    <p style='font-style: italic;'>{$agendamento['observacoes']}</p>
                </div>
                " : "") . "
                
                <p>Entre em contato com o aluno para confirmar os detalhes finais da aula.</p>
                
                <p style='margin-top: 30px;'>
                    <a href='http://localhost/Sistema%20De%20Agendamento/public/login.html' 
                       style='background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px;'>
                        📊 Acessar Dashboard
                    </a>
                </p>
                
                <hr style='margin: 30px 0;'>
                <p style='color: #666; font-size: 0.9em;'>
                    EduConnect - Sistema de Agendamento<br>
                    Conectando professores e alunos
                </p>
            ";
        } else {
            $assunto = "⏰ Lembrete: Você tem uma aula amanhã";
            $mensagem = "
                <h2 style='color: #f59e0b;'>⏰ Lembrete de Aula</h2>
                <p>Olá <strong>{$agendamento['professor']}</strong>,</p>
                <p>Este é um lembrete de que você tem uma aula agendada para <strong>amanhã</strong>!</p>
                
                <div style='background: #fef3c7; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #f59e0b;'>
                    <p><strong>👤 Aluno:</strong> {$agendamento['nome']}</p>
                    <p><strong>📚 Matéria:</strong> {$materia}</p>
                    <p><strong>📅 Data:</strong> {$data}</p>
                    <p><strong>⏰ Horário:</strong> {$hora}</p>
                </div>
                
                <p>📚 <strong>Prepare-se para a aula:</strong></p>
                <ul>
                    <li>Revise o conteúdo da matéria</li>
                    <li>Prepare exercícios e exemplos</li>
                    <li>Teste sua conexão e equipamentos</li>
                </ul>
                
                <p style='margin-top: 30px;'>
                    <a href='http://localhost/Sistema%20De%20Agendamento/public/login.html' 
                       style='background: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px;'>
                        📊 Ver Detalhes
                    </a>
                </p>
                
                <hr style='margin: 30px 0;'>
                <p style='color: #666; font-size: 0.9em;'>
                    EduConnect - Sistema de Agendamento<br>
                    Conectando professores e alunos
                </p>
            ";
        }
        
        $this->enviarEmail($agendamento['professor_email'], $assunto, $mensagem);
    }
    
    /**
     * Envia o e-mail usando configuração PHP nativa
     */
    private function enviarEmail($para, $assunto, $mensagem) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: EduConnect <noreply@educonnect.com>',
            'Reply-To: suporte@educonnect.com',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // Em ambiente de desenvolvimento, apenas simula o envio
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
            error_log("=== EMAIL SIMULADO ===");
            error_log("Para: $para");
            error_log("Assunto: $assunto");
            error_log("Mensagem: " . strip_tags($mensagem));
            error_log("====================");
            return true;
        }
        
        return mail($para, $assunto, $mensagem, implode("\r\n", $headers));
    }
    
    /**
     * Registra notificação no banco de dados
     */
    private function registrarNotificacao($agendamentoId, $tipo) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notificacoes (agendamento_id, tipo, enviado_em) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$agendamentoId, $tipo]);
        } catch (Exception $e) {
            error_log("Erro ao registrar notificação: " . $e->getMessage());
        }
    }
}

// Função para uso em outros arquivos
function enviarNotificacaoAgendamento($agendamentoId) {
    global $pdo;
    $emailService = new EmailNotification($pdo);
    return $emailService->enviarConfirmacaoAgendamento($agendamentoId);
}

// Script para execução via cron (lembretes)
if (php_sapi_name() === 'cli') {
    echo "Enviando lembretes...\n";
    $emailService = new EmailNotification($pdo);
    $enviados = $emailService->enviarLembretes();
    echo "Lembretes enviados: $enviados\n";
}
?>
