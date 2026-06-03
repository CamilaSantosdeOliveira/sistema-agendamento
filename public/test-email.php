<?php
// Teste de envio de email do EduCerto
header('Content-Type: text/html; charset=UTF-8');

echo "<h1>🧪 Teste de Email - EduCerto</h1>";
echo "<p>Data/Hora: " . date('d/m/Y H:i:s') . "</p>";

// Dados de teste
$testEmail = "usuario@teste.com"; // Substitua pelo seu email real
$testClass = [
    'subject' => 'Matemática - Teste',
    'date' => '10/08/2025',
    'time' => '14:00',
    'teacher' => 'Professor Teste',
    'description' => 'Aula de teste do sistema'
];

echo "<h2>📧 Configuração de Email</h2>";
echo "<p><strong>Para:</strong> $testEmail</p>";
echo "<p><strong>Assunto:</strong> ✅ Confirmação de Agendamento - EduCerto</p>";

// Função de envio de email (copiada da API)
function sendScheduleEmail($email, $classData, $username) {
    try {
        $to = $email;
        $subject = "✅ Confirmação de Agendamento - EduCerto";
        
        $message = "
        <html>
        <head>
            <title>Confirmação de Agendamento</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .class-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📚 EduCerto</h1>
                    <h2>Confirmação de Agendamento</h2>
                </div>
                <div class='content'>
                    <p>Olá <strong>" . htmlspecialchars($username) . "</strong>,</p>
                    
                    <p>Sua aula foi agendada com sucesso! Aqui estão os detalhes:</p>
                    
                    <div class='class-info'>
                        <h3>📅 Detalhes da Aula</h3>
                        <p><strong>📚 Matéria:</strong> " . htmlspecialchars($classData['subject']) . "</p>
                        <p><strong>📅 Data:</strong> " . htmlspecialchars($classData['date']) . "</p>
                        <p><strong>⏰ Horário:</strong> " . htmlspecialchars($classData['time']) . "</p>
                        <p><strong>👨‍🏫 Professor:</strong> " . htmlspecialchars($classData['teacher'] ?? 'A definir') . "</p>
                        " . (isset($classData['description']) && $classData['description'] ? "<p><strong>📝 Descrição:</strong> " . htmlspecialchars($classData['description']) . "</p>" : "") . "
                    </div>
                    
                    <p>🔔 <strong>Lembrete:</strong> Você receberá uma notificação 15 minutos antes do início da aula.</p>
                    
                    <p>Se você não pode comparecer, por favor cancele com antecedência através do sistema.</p>
                </div>
                <div class='footer'>
                    <p>Este é um email automático do sistema EduCerto v3.0</p>
                    <p>Data de envio: " . date('d/m/Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: EduCerto Sistema <noreply@educerto.com>" . "\r\n";
        $headers .= "Reply-To: suporte@educerto.com" . "\r\n";

        return mail($to, $subject, $message, $headers);
        
    } catch (Exception $e) {
        return false;
    }
}

echo "<h2>🧪 Teste de Envio</h2>";

// Tentar enviar email de teste
$emailSent = sendScheduleEmail($testEmail, $testClass, 'Usuario Teste');

if ($emailSent) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "✅ <strong>Email enviado com sucesso!</strong><br>";
    echo "📧 Verifique sua caixa de entrada em: <strong>$testEmail</strong><br>";
    echo "📝 Pode demorar alguns minutos para chegar.";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "❌ <strong>Não foi possível enviar o email.</strong><br>";
    echo "🔧 Possíveis causas:<br>";
    echo "• Configuração de SMTP não configurada no XAMPP<br>";
    echo "• Função mail() desabilitada<br>";
    echo "• Servidor de email local não configurado<br>";
    echo "• Antivírus bloqueando envio<br>";
    echo "</div>";
}

echo "<h2>📋 Verificação de Configuração</h2>";

// Verificar configurações PHP
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th style='padding: 8px; background: #f8f9fa;'>Configuração</th><th style='padding: 8px; background: #f8f9fa;'>Status</th></tr>";
echo "<tr><td style='padding: 8px;'>Função mail() habilitada</td><td style='padding: 8px;'>" . (function_exists('mail') ? '✅ Sim' : '❌ Não') . "</td></tr>";
echo "<tr><td style='padding: 8px;'>SMTP Host</td><td style='padding: 8px;'>" . ini_get('SMTP') . "</td></tr>";
echo "<tr><td style='padding: 8px;'>SMTP Port</td><td style='padding: 8px;'>" . ini_get('smtp_port') . "</td></tr>";
echo "<tr><td style='padding: 8px;'>Sendmail Path</td><td style='padding: 8px;'>" . ini_get('sendmail_path') . "</td></tr>";
echo "</table>";

echo "<h2>📝 Instruções para Configurar Email</h2>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p><strong>Para configurar o envio de emails no XAMPP:</strong></p>";
echo "<ol>";
echo "<li>Abra o arquivo <code>c:\\xampp\\php\\php.ini</code></li>";
echo "<li>Procure por <code>[mail function]</code></li>";
echo "<li>Configure:</li>";
echo "<ul>";
echo "<li><code>SMTP = smtp.gmail.com</code> (ou seu servidor SMTP)</li>";
echo "<li><code>smtp_port = 587</code></li>";
echo "<li><code>sendmail_from = seu-email@gmail.com</code></li>";
echo "</ul>";
echo "<li>Reinicie o Apache</li>";
echo "<li>Para Gmail, use uma senha de aplicativo</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🔗 Links Úteis</h2>";
echo "<p><a href='http://localhost/Sistema%20De%20Agendamento/public/educerto.html' target='_blank'>🎓 Voltar ao EduCerto</a></p>";
echo "<p><a href='http://localhost/phpmyadmin' target='_blank'>🗄️ phpMyAdmin</a></p>";
echo "<p><a href='http://localhost/dashboard' target='_blank'>🏠 XAMPP Dashboard</a></p>";

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}

table {
    margin: 10px 0;
}

th, td {
    text-align: left;
}

code {
    background: #f8f9fa;
    padding: 2px 4px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}
</style>


