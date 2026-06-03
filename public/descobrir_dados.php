<?php
echo "<h2>🔍 DESCOBRINDO ONDE ESTÃO SEUS DADOS REAIS</h2>";

// Testar diferentes configurações de MySQL
$configuracoes = [
    ['localhost', 'root', '', 3306],
    ['localhost', 'root', 'root', 3306],
    ['localhost', 'root', 'admin', 3306],
    ['localhost', 'root', 'password', 3306],
    ['localhost', 'root', '123456', 3306],
    ['localhost', 'root', 'Cami7890#', 3306],
    ['127.0.0.1', 'root', '', 3306],
    ['127.0.0.1', 'root', 'root', 3306],
    ['127.0.0.1', 'root', 'admin', 3306],
    ['127.0.0.1', 'root', 'password', 3306],
    ['127.0.0.1', 'root', '123456', 3306],
    ['127.0.0.1', 'root', 'Cami7890#', 3306],
];

$dados_encontrados = [];

foreach ($configuracoes as $i => $config) {
    list($host, $user, $pass, $port) = $config;
    
    $senha_display = $pass ? $pass : '(sem senha)';
    echo "<h3>🔍 Teste " . ($i + 1) . ": $host:$port - $user - $senha_display</h3>";
    
    try {
        $conn = new mysqli($host, $user, $pass, '', $port);
        
        if ($conn->connect_error) {
            echo "❌ <strong>Falhou:</strong> " . $conn->connect_error . "<br><br>";
            continue;
        } else {
            echo "✅ <strong>SUCESSO!</strong> Conectado ao MySQL!<br>";
            
            // Listar TODOS os bancos de dados
            $result = $conn->query("SHOW DATABASES");
            
            if ($result) {
                echo "<h4>📋 Todos os bancos encontrados:</h4>";
                echo "<ul>";
                
                while ($row = $result->fetch_array()) {
                    $db_name = $row[0];
                    
                    // Pular bancos do sistema
                    if (in_array($db_name, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
                        continue;
                    }
                    
                    echo "<li><strong>$db_name</strong>";
                    
                    // Verificar se tem dados do sistema
                    $conn->select_db($db_name);
                    $tabelas = $conn->query("SHOW TABLES");
                    
                    if ($tabelas && $tabelas->num_rows > 0) {
                        echo "<br>&nbsp;&nbsp;📊 Tabelas: ";
                        $tabelas_array = [];
                        $tem_dados_sistema = false;
                        
                        while ($tabela = $tabelas->fetch_array()) {
                            $nome_tabela = $tabela[0];
                            $tabelas_array[] = $nome_tabela;
                            
                            // Verificar se é uma tabela do sistema
                            if (strpos(strtolower($nome_tabela), 'usuario') !== false ||
                                strpos(strtolower($nome_tabela), 'aluno') !== false ||
                                strpos(strtolower($nome_tabela), 'professor') !== false ||
                                strpos(strtolower($nome_tabela), 'curso') !== false ||
                                strpos(strtolower($nome_tabela), 'agendamento') !== false ||
                                strpos(strtolower($nome_tabela), 'certificado') !== false ||
                                strpos(strtolower($nome_tabela), 'avaliacao') !== false ||
                                strpos(strtolower($nome_tabela), 'pagamento') !== false ||
                                strpos(strtolower($nome_tabela), 'notificacao') !== false) {
                                $tem_dados_sistema = true;
                            }
                        }
                        
                        echo implode(', ', $tabelas_array);
                        
                        if ($tem_dados_sistema) {
                            echo " <span style='color: green; font-weight: bold;'>🎯 TEM DADOS DO SISTEMA!</span>";
                            
                            // Contar registros em cada tabela
                            echo "<br>&nbsp;&nbsp;📈 Registros: ";
                            $totais = [];
                            foreach ($tabelas_array as $tabela) {
                                $count = $conn->query("SELECT COUNT(*) as total FROM `$tabela`");
                                if ($count) {
                                    $total = $count->fetch_assoc()['total'];
                                    $totais[] = "$tabela: $total";
                                }
                            }
                            echo implode(', ', $totais);
                            
                            // Salvar informações
                            $dados_encontrados[] = [
                                'host' => $host,
                                'port' => $port,
                                'user' => $user,
                                'pass' => $pass,
                                'database' => $db_name,
                                'tabelas' => $tabelas_array,
                                'totais' => $totais
                            ];
                        }
                    }
                    
                    echo "</li>";
                }
                echo "</ul>";
            }
            
            $conn->close();
            break; // Parar no primeiro sucesso
        }
    } catch (Exception $e) {
        echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br><br>";
    }
}

// Mostrar resumo dos dados encontrados
if (!empty($dados_encontrados)) {
    echo "<h2>🎯 RESUMO - SEUS DADOS ENCONTRADOS:</h2>";
    
    foreach ($dados_encontrados as $i => $dados) {
        echo "<h3>📊 Opção " . ($i + 1) . ":</h3>";
        echo "<ul>";
        echo "<li><strong>Host:</strong> " . $dados['host'] . ":" . $dados['port'] . "</li>";
        echo "<li><strong>Usuário:</strong> " . $dados['user'] . "</li>";
        echo "<li><strong>Senha:</strong> " . ($dados['pass'] ? $dados['pass'] : '(sem senha)') . "</li>";
        echo "<li><strong>Banco:</strong> " . $dados['database'] . "</li>";
        echo "<li><strong>Tabelas:</strong> " . implode(', ', $dados['tabelas']) . "</li>";
        echo "<li><strong>Registros:</strong> " . implode(', ', $dados['totais']) . "</li>";
        echo "</ul>";
        
        echo "<h4>🔧 Configuração para db.php:</h4>";
        echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
        echo "&lt;?php\n";
        echo "\$host = '" . $dados['host'] . "';\n";
        echo "\$user = '" . $dados['user'] . "';\n";
        echo "\$pass = '" . $dados['pass'] . "';\n";
        echo "\$db   = '" . $dados['database'] . "';\n";
        echo "\$conn = new mysqli(\$host, \$user, \$pass, \$db, " . $dados['port'] . ");\n";
        echo "?&gt;";
        echo "</pre>";
    }
    
    echo "<h3>🎯 PRÓXIMOS PASSOS:</h3>";
    echo "<ol>";
    echo "<li>Escolha qual configuração usar</li>";
    echo "<li>Me informe qual opção você quer</li>";
    echo "<li>Vou atualizar o db.php automaticamente</li>";
    echo "<li>Vou implementar todos os botões funcionais</li>";
    echo "</ol>";
} else {
    echo "<h3>❌ Nenhum dado do sistema encontrado</h3>";
    echo "<p>Possíveis soluções:</p>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Verifique se o XAMPP está ativo</li>";
    echo "<li>Verifique se o MySQL Workbench está conectado</li>";
    echo "<li>Teste outras senhas</li>";
    echo "</ul>";
}
?>



