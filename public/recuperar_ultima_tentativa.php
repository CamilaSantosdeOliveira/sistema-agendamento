<?php
echo "<h1>🚨 ÚLTIMA TENTATIVA DE RECUPERAÇÃO</h1>";

echo "<h3>1️⃣ Verificando se MySQL está parado...</h3>";

// Verificar se MySQL está rodando
$conn = @new mysqli('localhost', 'root', '', 'sistema_agendamento', 3306);
if ($conn->connect_error) {
    echo "<p style='color: green;'>✅ MySQL está parado (bom para recuperação)</p>";
} else {
    echo "<p style='color: red;'>❌ MySQL ainda está rodando</p>";
    $conn->close();
}

echo "<h3>2️⃣ Verificando arquivos de dados...</h3>";

$data_dir = 'C:\xampp\mysql\data\sistema_agendamento';
$files = [
    'usuarios.ibd' => 'usuarios',
    'cursos.ibd' => 'cursos',
    'agendamentos.ibd' => 'agendamentos',
    'pagamentos.ibd' => 'pagamentos',
    'notificacoes.ibd' => 'notificacoes'
];

foreach ($files as $file => $table) {
    $full_path = $data_dir . '\\' . $file;
    if (file_exists($full_path)) {
        $size = filesize($full_path);
        $date = date('d/m/Y H:i:s', filemtime($full_path));
        echo "<p style='color: green;'>✅ $file: $size bytes ($date)</p>";
    } else {
        echo "<p style='color: red;'>❌ $file não encontrado</p>";
    }
}

echo "<h3>3️⃣ Tentando técnica de recuperação InnoDB...</h3>";

// Tentar usar innodb_force_recovery temporariamente
$my_ini_path = 'C:\xampp\mysql\bin\my.ini';
if (file_exists($my_ini_path)) {
    echo "<p style='color: blue;'>🔧 Modificando my.ini para recuperação...</p>";
    
    // Ler arquivo atual
    $content = file_get_contents($my_ini_path);
    
    // Adicionar innodb_force_recovery se não existir
    if (strpos($content, 'innodb_force_recovery') === false) {
        $content = str_replace('[mysqld]', "[mysqld]\ninnodb_force_recovery=1", $content);
        file_put_contents($my_ini_path, $content);
        echo "<p style='color: green;'>✅ innodb_force_recovery=1 adicionado</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ innodb_force_recovery já existe</p>";
    }
}

echo "<h3>4️⃣ Iniciando MySQL com recuperação...</h3>";

// Tentar iniciar MySQL
$start_command = 'C:\xampp\xampp_start.exe';
if (file_exists($start_command)) {
    echo "<p style='color: blue;'>🔄 Iniciando MySQL com recuperação...</p>";
    exec($start_command . ' mysql', $output, $return_var);
    sleep(10); // Aguardar mais tempo
}

echo "<h3>5️⃣ Testando conexão...</h3>";

// Tentar conectar
$conn = @new mysqli('localhost', 'root', '', 'sistema_agendamento', 3306);
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Erro de conexão: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Conexão estabelecida!</p>";
    
    echo "<h3>6️⃣ Verificando dados recuperados...</h3>";
    
    foreach ($files as $file => $table) {
        $result = $conn->query("SELECT COUNT(*) as total FROM $table");
        if ($result) {
            $total = $result->fetch_assoc()['total'];
            if ($total > 0) {
                echo "<p style='color: green;'>🎉 $table tem $total registros!</p>";
                
                // Mostrar amostra
                $sample = $conn->query("SELECT * FROM $table LIMIT 2");
                if ($sample) {
                    echo "<p style='color: blue;'>📋 Amostra de $table:</p>";
                    while ($row = $sample->fetch_assoc()) {
                        echo "<p style='margin-left: 20px;'>• " . json_encode($row) . "</p>";
                    }
                }
            } else {
                echo "<p style='color: orange;'>⚠️ $table ainda vazia</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Erro ao verificar $table: " . $conn->error . "</p>";
        }
    }
    
    $conn->close();
}

echo "<h3>7️⃣ Removendo configuração de recuperação...</h3>";

// Remover innodb_force_recovery
if (file_exists($my_ini_path)) {
    $content = file_get_contents($my_ini_path);
    $content = preg_replace('/innodb_force_recovery=\d+/', '', $content);
    file_put_contents($my_ini_path, $content);
    echo "<p style='color: green;'>✅ Configuração de recuperação removida</p>";
}

echo "<h3>🎯 RESULTADO FINAL</h3>";
echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🚀 Testar Dashboard</a></p>";
echo "<p><a href='verificar_dados_completos.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>📊 Verificar Dados</a></p>";

echo "<h3>💡 SE NÃO FUNCIONOU:</h3>";
echo "<p>Infelizmente, os dados podem ter sido perdidos irreversivelmente. Neste caso, precisaremos:</p>";
echo "<p>1. Recriar as tabelas</p>";
echo "<p>2. Inserir dados de demonstração</p>";
echo "<p>3. Continuar com o sistema funcionando</p>";
?>











