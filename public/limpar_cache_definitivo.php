<?php
// Script para limpar cache definitivamente

echo "🧹 LIMPANDO CACHE DEFINITIVAMENTE...\n\n";

// 1. Adicionar headers para prevenir cache
$headers = "
<?php
// Headers para prevenir cache
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
session_start();
include 'db.php';
";

// 2. Ler o arquivo atual
$content = file_get_contents('index.php');

// 3. Remover a primeira linha PHP e adicionar headers
$content = preg_replace('/^<\?php\s*/', $headers, $content);

// 4. Adicionar timestamp para cache-busting
$timestamp = time();
$content = str_replace(
    '<title>EduConnect - Dashboard de Cursos de Tecnologia</title>',
    '<title>EduConnect - Dashboard de Cursos de Tecnologia</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="version" content="' . $timestamp . '">',
    $content
);

// 5. Salvar arquivo atualizado
file_put_contents('index.php', $content);

echo "✅ Headers de cache adicionados!\n";
echo "✅ Timestamp adicionado: $timestamp\n";
echo "✅ Arquivo atualizado!\n\n";

echo "🎯 AGORA FAÇA ISSO:\n";
echo "1. Feche TODAS as abas do seu site\n";
echo "2. Pressione Ctrl + Shift + Delete\n";
echo "3. Selecione 'Limpar dados'\n";
echo "4. Abra: http://localhost:8080/Sistema%20De%20Agendamento/public/\n";
echo "5. Clique em '📊 Relatórios Detalhados'\n\n";

echo "🚀 Deve funcionar agora!\n";
?>















