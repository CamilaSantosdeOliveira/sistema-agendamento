<?php
echo "<h2>🔍 PROCURANDO SEUS DADOS ESPECÍFICOS</h2>";
echo "<p><strong>Procurando por:</strong> 4 professores, 3 alunos, 6 cursos</p>";

// Configuração que sabemos que funciona
$host = 'localhost';
$user = 'root';
$pass = '';
$port = 3306;

try {
    $conn = new mysqli($host, $user, $pass, '', $port);
    
    if ($conn->connect_error) {
        die("❌ Erro de conexão: " . $conn->connect_error);
    }
    
    echo "✅ <strong>Conectado ao MySQL!</strong><br><br>";
    
    // Listar todos os bancos
    $result = $conn->query("SHOW DATABASES");
    $bancos = [];
    
    while ($row = $result->fetch_assoc()) {
        $bancos[] = $row['Database'];
    }
    
    echo "<h3>📋 Bancos encontrados:</h3>";
    echo "<ul>";
    foreach ($bancos as $banco) {
        echo "<li><strong>$banco</strong></li>";
    }
    echo "</ul>";
    
    // Procurar bancos que podem ter dados do sistema
    $bancos_sistema = ['educonnect', 'educonnectdb', 'sistema_agendamento', 'sistema', 'agendamento'];
    
    echo "<h3>🎯 ANALISANDO BANCOS DO SISTEMA:</h3>";
    
    foreach ($bancos_sistema as $banco) {
        if (in_array($banco, $bancos)) {
            echo "<h4>🔍 Analisando: <strong>$banco</strong></h4>";
            
            $conn->select_db($banco);
            
            // Listar tabelas
            $result = $conn->query("SHOW TABLES");
            $tabelas = [];
            
            while ($row = $result->fetch_array()) {
                $tabelas[] = $row[0];
            }
            
            echo "<p><strong>Tabelas:</strong> " . implode(', ', $tabelas) . "</p>";
            
            // Procurar por tabelas de usuários/professores/alunos
            $tabelas_usuarios = ['usuarios', 'professores', 'alunos', 'users', 'teachers', 'students'];
            $tabelas_cursos = ['cursos', 'courses', 'disciplinas'];
            
            $professores_count = 0;
            $alunos_count = 0;
            $cursos_count = 0;
            
            foreach ($tabelas_usuarios as $tabela) {
                if (in_array($tabela, $tabelas)) {
                    $result = $conn->query("SELECT COUNT(*) as total FROM $tabela");
                    if ($result) {
                        $count = $result->fetch_assoc()['total'];
                        
                        // Tentar identificar se são professores ou alunos
                        if (strpos($tabela, 'professor') !== false || strpos($tabela, 'teacher') !== false) {
                            $professores_count += $count;
                            echo "<p>👨‍🏫 <strong>$tabela:</strong> $count registros</p>";
                        } elseif (strpos($tabela, 'aluno') !== false || strpos($tabela, 'student') !== false) {
                            $alunos_count += $count;
                            echo "<p>👨‍🎓 <strong>$tabela:</strong> $count registros</p>";
                        } else {
                            // Tabela genérica de usuários
                            echo "<p>👤 <strong>$tabela:</strong> $count registros</p>";
                            
                            // Tentar identificar tipos
                            $result2 = $conn->query("DESCRIBE $tabela");
                            $tem_tipo = false;
                            while ($row = $result2->fetch_assoc()) {
                                if (strpos($row['Field'], 'tipo') !== false || strpos($row['Field'], 'type') !== false) {
                                    $tem_tipo = true;
                                    break;
                                }
                            }
                            
                            if ($tem_tipo) {
                                $result3 = $conn->query("SELECT tipo_usuario, COUNT(*) as total FROM $tabela GROUP BY tipo_usuario");
                                while ($row = $result3->fetch_assoc()) {
                                    if (strpos($row['tipo_usuario'], 'professor') !== false) {
                                        $professores_count += $row['total'];
                                        echo "<p>  └─ Professores: {$row['total']}</p>";
                                    } elseif (strpos($row['tipo_usuario'], 'aluno') !== false) {
                                        $alunos_count += $row['total'];
                                        echo "<p>  └─ Alunos: {$row['total']}</p>";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            foreach ($tabelas_cursos as $tabela) {
                if (in_array($tabela, $tabelas)) {
                    $result = $conn->query("SELECT COUNT(*) as total FROM $tabela");
                    if ($result) {
                        $count = $result->fetch_assoc()['total'];
                        $cursos_count += $count;
                        echo "<p>📚 <strong>$tabela:</strong> $count registros</p>";
                    }
                }
            }
            
            echo "<h4>📊 RESUMO - $banco:</h4>";
            echo "<ul>";
            echo "<li>👨‍🏫 <strong>Professores:</strong> $professores_count</li>";
            echo "<li>👨‍🎓 <strong>Alunos:</strong> $alunos_count</li>";
            echo "<li>📚 <strong>Cursos:</strong> $cursos_count</li>";
            echo "</ul>";
            
            // Verificar se corresponde aos dados esperados
            if ($professores_count == 4 && $alunos_count == 3 && $cursos_count == 6) {
                echo "<h3>🎉 ENCONTREI SEUS DADOS!</h3>";
                echo "<p><strong>Banco:</strong> $banco</p>";
                echo "<p><strong>Configuração db.php:</strong></p>";
                echo "<pre style='background: #e8f5e8; padding: 10px; border-radius: 5px;'>";
                echo "&lt;?php\n";
                echo "\$host = 'localhost';\n";
                echo "\$user = 'root';\n";
                echo "\$pass = '';\n";
                echo "\$db   = '$banco';\n";
                echo "\$conn = new mysqli(\$host, \$user, \$pass, \$db, 3306);\n";
                echo "?&gt;";
                echo "</pre>";
                
                echo "<h4>🔗 Links para testar:</h4>";
                echo "<ul>";
                echo "<li><a href='http://localhost:8080/dashboard_corrigido.php'>Dashboard</a></li>";
                echo "<li><a href='http://localhost:8080/alunos.php'>Alunos</a></li>";
                echo "<li><a href='http://localhost:8080/cursos.php'>Cursos</a></li>";
                echo "</ul>";
                
                break;
            } elseif ($professores_count > 0 || $alunos_count > 0 || $cursos_count > 0) {
                echo "<p>⚠️ <strong>Dados encontrados, mas não correspondem exatamente:</strong></p>";
                echo "<p>Esperado: 4 professores, 3 alunos, 6 cursos</p>";
                echo "<p>Encontrado: $professores_count professores, $alunos_count alunos, $cursos_count cursos</p>";
            }
            
            echo "<hr>";
        }
    }
    
    if ($professores_count == 0 && $alunos_count == 0 && $cursos_count == 0) {
        echo "<h3>❌ NENHUM DADO ENCONTRADO</h3>";
        echo "<p>Não encontrei dados que correspondam aos seus (4 professores, 3 alunos, 6 cursos).</p>";
        echo "<p><strong>Possíveis causas:</strong></p>";
        echo "<ul>";
        echo "<li>Os dados estão em outro banco</li>";
        echo "<li>Os dados foram apagados</li>";
        echo "<li>Os números estão diferentes</li>";
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Erro:</strong> " . $e->getMessage();
}

$conn->close();
?>


