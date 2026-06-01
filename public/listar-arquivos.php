<?php
// Listar arquivos do diretório atual
echo "<h2>📁 Arquivos no diretório: " . __DIR__ . "</h2>";
echo "<ul>";

$files = scandir(__DIR__);
foreach($files as $file) {
    if($file != "." && $file != "..") {
        $type = is_dir($file) ? "📁" : "📄";
        echo "<li>{$type} {$file}</li>";
    }
}

echo "</ul>";
echo "<h3>📊 Total de arquivos: " . (count($files) - 2) . "</h3>";

// Verificar se o diretório é acessível via web
echo "<h3>🌐 Verificação de Acesso Web:</h3>";
echo "<p>URL atual: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

// Listar arquivos importantes
echo "<h3>🔍 Arquivos Importantes:</h3>";
$importantes = [
    'index.html',
    'index.php',
    'certificados.php',
    'api/certificados.php',
    'db.php',
    'teste-ultra-simples.php',
    'teste-api-simples.php'
];

echo "<ul>";
foreach($importantes as $arquivo) {
    $caminho = __DIR__ . '/' . $arquivo;
    if (file_exists($caminho)) {
        echo "<li>✅ {$arquivo} - EXISTE</li>";
    } else {
        echo "<li>❌ {$arquivo} - NÃO ENCONTRADO</li>";
    }
}
echo "</ul>";

// Verificar permissões
echo "<h3>🔐 Verificação de Permissões:</h3>";
if (is_readable(__DIR__)) {
    echo "<p>✅ Diretório é legível</p>";
} else {
    echo "<p>❌ Diretório não é legível</p>";
}

if (is_writable(__DIR__)) {
    echo "<p>✅ Diretório é gravável</p>";
} else {
    echo "<p>❌ Diretório não é gravável</p>";
}

// Informações do servidor
echo "<h3>🖥️ Informações do Servidor:</h3>";
echo "<p>Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Porta: " . $_SERVER['SERVER_PORT'] . "</p>";
echo "<p>Host: " . $_SERVER['HTTP_HOST'] . "</p>";
?>
















