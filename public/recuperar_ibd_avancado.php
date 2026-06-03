<?php
echo "<h1>🔧 RECUPERAÇÃO AVANÇADA DE DADOS .IBD</h1>";

include 'db.php';

if (!$conn) {
    echo "<p style='color: red;'>❌ Erro de conexão com banco</p>";
    exit;
}

echo "<h3>1️⃣ Parando MySQL para manipular arquivos...</h3>";

// Tentar parar MySQL via XAMPP
$stop_command = 'C:\xampp\xampp_stop.exe';
if (file_exists($stop_command)) {
    echo "<p style='color: blue;'>🔄 Parando MySQL...</p>";
    exec($stop_command . ' mysql', $output, $return_var);
    sleep(3);
}

echo "<h3>2️⃣ Verificando arquivos .ibd...</h3>";

$data_dir = 'C:\xampp\mysql\data\sistema_agendamento';
$ibd_files = [
    'usuarios.ibd' => 'usuarios',
    'cursos.ibd' => 'cursos', 
    'agendamentos.ibd' => 'agendamentos',
    'avaliacoes.ibd' => 'avaliacoes',
    'certificados.ibd' => 'certificados',
    'inscricoes.ibd' => 'inscricoes',
    'notificacoes.ibd' => 'notificacoes',
    'pagamentos.ibd' => 'pagamentos'
];

foreach ($ibd_files as $file => $table) {
    $full_path = $data_dir . '\\' . $file;
    if (file_exists($full_path)) {
        $size = filesize($full_path);
        echo "<p style='color: green;'>✅ $file existe ($size bytes)</p>";
    } else {
        echo "<p style='color: red;'>❌ $file não encontrado</p>";
    }
}

echo "<h3>3️⃣ Tentando recuperar dados com mysqldump...</h3>";

// Tentar usar mysqldump para recuperar
$mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';
if (file_exists($mysqldump)) {
    echo "<p style='color: blue;'>🔍 Tentando mysqldump...</p>";
    
    foreach ($ibd_files as $file => $table) {
        $dump_file = "backup_$table.sql";
        $command = "$mysqldump -u root -p --single-transaction --routines --triggers sistema_agendamento $table > $dump_file 2>&1";
        
        exec($command, $output, $return_var);
        
        if (file_exists($dump_file)) {
            $size = filesize($dump_file);
            echo "<p style='color: green;'>✅ Backup $table criado ($size bytes)</p>";
        } else {
            echo "<p style='color: red;'>❌ Falha ao criar backup $table</p>";
        }
    }
}

echo "<h3>4️⃣ Tentando reparo com mysqlcheck...</h3>";

// Tentar reparar com mysqlcheck
$mysqlcheck = 'C:\xampp\mysql\bin\mysqlcheck.exe';
if (file_exists($mysqlcheck)) {
    echo "<p style='color: blue;'>🔧 Executando mysqlcheck...</p>";
    
    $command = "$mysqlcheck -u root -p --repair --all-databases 2>&1";
    exec($command, $output, $return_var);
    
    echo "<p style='color: blue;'>📋 Resultado mysqlcheck:</p>";
    foreach ($output as $line) {
        echo "<p style='margin-left: 20px;'>$line</p>";
    }
}

echo "<h3>5️⃣ Iniciando MySQL novamente...</h3>";

// Tentar iniciar MySQL
$start_command = 'C:\xampp\xampp_start.exe';
if (file_exists($start_command)) {
    echo "<p style='color: blue;'>🔄 Iniciando MySQL...</p>";
    exec($start_command . ' mysql', $output, $return_var);
    sleep(5);
}

echo "<h3>6️⃣ Verificando dados após recuperação...</h3>";

// Reconectar e verificar
$conn = new mysqli('localhost', 'root', '', 'sistema_agendamento', 3306);

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Erro de conexão após recuperação</p>";
} else {
    echo "<p style='color: green;'>✅ Conexão restaurada!</p>";
    
    foreach ($ibd_files as $file => $table) {
        $result = $conn->query("SELECT COUNT(*) as total FROM $table");
        if ($result) {
            $total = $result->fetch_assoc()['total'];
            if ($total > 0) {
                echo "<p style='color: green;'>🎉 $table tem $total registros!</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ $table ainda vazia</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Erro ao verificar $table</p>";
        }
    }
}

echo "<h3>🎯 PRÓXIMOS PASSOS</h3>";
echo "<p><a href='dashboard_final.php' style='background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>🚀 Testar Dashboard</a></p>";
echo "<p><a href='verificar_dados_completos.php' style='background: #007bff; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>📊 Verificar Dados</a></p>";

if ($conn) $conn->close();
?>











