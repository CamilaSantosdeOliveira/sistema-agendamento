<?php
echo "<h1>🔐 Configuração de Senha MySQL</h1>";

echo "<h2>❌ Problema Detectado:</h2>";
echo "<p>O usuário 'root' do seu MySQL Workbench precisa de senha!</p>";

echo "<hr>";
echo "<h2>🔧 Soluções Possíveis:</h2>";

echo "<h3>Opção 1: Usar senha do root</h3>";
echo "<p>Se você sabe a senha do usuário root:</p>";
echo "<ol>";
echo "<li>Edite o arquivo <code>configurar_banco.php</code></li>";
echo "<li>Altere a linha: <code>'pass' => '',</code></li>";
echo "<li>Para: <code>'pass' => 'SUA_SENHA_AQUI',</code></li>";
echo "<li>Execute novamente <code>configurar_banco.php</code></li>";
echo "</ol>";

echo "<h3>Opção 2: Criar novo usuário sem senha</h3>";
echo "<p>Se você tem acesso ao MySQL Workbench:</p>";
echo "<ol>";
echo "<li>Abra o MySQL Workbench</li>";
echo "<li>Execute este comando SQL:</li>";
echo "<pre style='background: #f1f5f9; padding: 15px; border-radius: 5px;'>";
echo "CREATE USER 'agendamento'@'localhost' IDENTIFIED BY '';\n";
echo "GRANT ALL PRIVILEGES ON *.* TO 'agendamento'@'localhost';\n";
echo "FLUSH PRIVILEGES;";
echo "</pre>";
echo "<li>Depois edite <code>configurar_banco.php</code></li>";
echo "<li>Altere: <code>'user' => 'agendamento',</code></li>";
echo "</ol>";

echo "<h3>Opção 3: Resetar senha do root</h3>";
echo "<p>Se você pode acessar o MySQL como administrador:</p>";
echo "<pre style='background: #f1f5f9; padding: 15px; border-radius: 5px;'>";
echo "ALTER USER 'root'@'localhost' IDENTIFIED BY '';\n";
echo "FLUSH PRIVILEGES;";
echo "</pre>";

echo "<hr>";
echo "<h2>📝 Arquivo de Configuração Atual:</h2>";
echo "<p>Arquivo: <code>configurar_banco.php</code></p>";
echo "<p>Linha para alterar:</p>";
echo "<pre style='background: #fee2e2; padding: 15px; border-radius: 5px;'>";
echo "// CONFIGURAÇÕES - AJUSTE AQUI COM SUAS INFORMAÇÕES\n";
echo "\$config = [\n";
echo "    'host' => 'localhost',      // Endereço do servidor MySQL\n";
echo "    'user' => 'root',           // Seu usuário MySQL\n";
echo "    'pass' => '',               // ← COLOQUE SUA SENHA AQUI\n";
echo "    'port' => 3307,             // Porta (deixe 3306 se não souber)\n";
echo "    'db_name' => 'sistema_agendamento'  // Nome do banco\n";
echo "];";
echo "</pre>";

echo "<hr>";
echo "<h2>🚀 Depois de configurar:</h2>";
echo "<p>1. Salve o arquivo <code>configurar_banco.php</code></p>";
echo "<p>2. Execute: <a href='configurar_banco.php' style='color: #3b82f6; text-decoration: none; font-weight: bold;'>configurar_banco.php</a></p>";
echo "<p>3. O sistema criará automaticamente:</p>";
echo "<ul>";
echo "<li>✅ Banco de dados 'sistema_agendamento'</li>";
echo "<li>✅ Tabelas: cursos, professores, agendamentos</li>";
echo "<li>✅ 6 cursos reais inseridos</li>";
echo "<li>✅ 5 professores reais inseridos</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>💡 Dica:</h2>";
echo "<p>Se você não lembra a senha do root, a <strong>Opção 2</strong> (criar usuário novo) é a mais segura!</p>";
?>






































