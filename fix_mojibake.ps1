$targets = @(
    'detalhes_curso_aluno.php',
    'meus_cursos_aluno.php',
    'minhas_aulas_aluno.php',
    'certificados_aluno.php',
    'perfil_aluno.php',
    'dashboard_aluno.php'
)
# Pares mojibake -> correto (Latin-1 lendo UTF-8)
$map = [ordered]@{
    'Ã§' = 'ç'; 'Ã£' = 'ã'; 'Ã¡' = 'á'; 'Ã©' = 'é'; 'Ã­' = 'í';
    'Ã³' = 'ó'; 'Ãº' = 'ú'; 'Ã¢' = 'â'; 'Ãª' = 'ê'; 'Ã´' = 'ô';
    'Ã±' = 'ñ'; 'Ã ' = 'à'; 'Ã¨' = 'è'; 'Ã¬' = 'ì'; 'Ã²' = 'ò';
    'Ã¼' = 'ü'; 'Ã¶' = 'ö'; 'Ã„' = 'Ä'; 'Ã‡' = 'Ç'; 'Ã‰' = 'É';
    'Ã"' = 'Ó'; 'Ãš' = 'Ú'; 'ÃŠ' = 'Ê'; 'Ã‚' = 'Â'; 'Ã”' = 'Ô';
    'Ã•' = 'Õ'; 'ÃƒO' = 'ÃO'; 'ÃƒE' = 'ÃE'; 'Ãƒ' = 'Ã';
    'ðŸ"š' = '📚'; 'ðŸ"–' = '📖'; 'ðŸŽ"' = '🎓'; 'ðŸ"Š' = '📊';
    'ðŸ"…' = '📅'; 'ðŸ'¥' = '👥'; 'ðŸ'¨â€ðŸ'¼' = '👨‍💼'
}
foreach ($f in $targets) {
    $p = 'c:\xampp\htdocs\Sistema De Agendamento\public\' + $f
    if (-not (Test-Path $p)) { Write-Host "MISSING $f"; continue }
    $c = Get-Content -Raw -Encoding UTF8 $p
    $orig = $c
    foreach ($k in $map.Keys) { $c = $c.Replace($k, $map[$k]) }
    if ($c -ne $orig) {
        Set-Content -Path $p -Value $c -NoNewline -Encoding UTF8
        Write-Host "FIXED $f"
    } else {
        Write-Host "CLEAN $f"
    }
}
