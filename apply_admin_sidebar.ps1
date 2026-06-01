$css = Get-Content -Raw 'c:\xampp\htdocs\Sistema De Agendamento\admin_sidebar_override.css'
$files = @('alunos.php','agendamentos.php','cursos_completo.php','professores.php','atribuicoes_cursos.php','relatorios_detalhados.php','certificados.php','sistema_usuarios.php','configuracoes.php')
foreach ($f in $files) {
    $p = 'c:\xampp\htdocs\Sistema De Agendamento\public\' + $f
    if (-not (Test-Path $p)) { Write-Host "MISSING $f"; continue }
    $c = Get-Content -Raw $p
    if ($c -match 'ADMIN_SIDEBAR_OVERRIDE_START') { Write-Host "SKIP $f"; continue }
    $idx = $c.IndexOf('</style>')
    if ($idx -lt 0) { Write-Host "NO STYLE $f"; continue }
    $new = $c.Substring(0,$idx) + "`r`n" + $css + "`r`n" + $c.Substring($idx)
    Set-Content -Path $p -Value $new -NoNewline -Encoding UTF8
    Write-Host "OK $f"
}
