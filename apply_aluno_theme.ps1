$css = Get-Content -Raw 'c:\xampp\htdocs\Sistema De Agendamento\aluno_theme_override.css'
$toast = Get-Content -Raw 'c:\xampp\htdocs\Sistema De Agendamento\aluno_toast_inject.html'
$files = @(
    'dashboard_aluno.php',
    'meus_cursos_aluno.php',
    'minhas_aulas_aluno.php',
    'certificados_aluno.php',
    'detalhes_curso_aluno.php',
    'perfil_aluno.php',
    'buscar_cursos_aluno.php'
)
foreach ($f in $files) {
    $p = 'c:\xampp\htdocs\Sistema De Agendamento\public\' + $f
    if (-not (Test-Path $p)) { Write-Host "MISSING $f"; continue }
    $c = Get-Content -Raw $p

    # 1) Inject CSS theme override before first </style>
    if ($c -notmatch 'ALUNO_THEME_OVERRIDES_START') {
        $idx = $c.IndexOf('</style>')
        if ($idx -ge 0) {
            $c = $c.Substring(0,$idx) + "`r`n" + $css + "`r`n" + $c.Substring($idx)
            Write-Host "CSS+ $f"
        } else {
            Write-Host "NO STYLE $f"
        }
    } else {
        Write-Host "CSS SKIP $f"
    }

    # 2) Inject toast container/script before </body> if not present
    if ($c -notmatch 'ALUNO_TOAST_INJECT_START' -and $c -notmatch 'id="toastContainer"') {
        $idxBody = $c.LastIndexOf('</body>')
        if ($idxBody -ge 0) {
            $c = $c.Substring(0,$idxBody) + $toast + "`r`n" + $c.Substring($idxBody)
            Write-Host "TOAST+ $f"
        } else {
            Write-Host "NO BODY $f"
        }
    } else {
        Write-Host "TOAST SKIP $f"
    }

    Set-Content -Path $p -Value $c -NoNewline -Encoding UTF8
}
