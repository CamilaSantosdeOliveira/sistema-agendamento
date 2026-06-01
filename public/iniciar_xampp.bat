@echo off
echo ========================================
echo    INICIALIZACAO COMPLETA DO XAMPP
echo ========================================
echo.

echo [1/8] Verificando se o XAMPP esta instalado...
if not exist "C:\xampp\xampp-control.exe" (
    echo ERRO: XAMPP nao encontrado em C:\xampp\
    echo Instale o XAMPP primeiro!
    pause
    exit /b 1
)

echo [2/8] Parando servicos existentes...
taskkill /f /im mysqld.exe >nul 2>&1
taskkill /f /im httpd.exe >nul 2>&1
timeout /t 3 /nobreak >nul

echo [3/8] Corrigindo arquivo my.ini...
if exist "C:\xampp\mysql\bin\my.ini" (
    echo Fazendo backup do my.ini...
    copy "C:\xampp\mysql\bin\my.ini" "C:\xampp\mysql\bin\my.ini.backup.%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%" >nul
    
    echo Comentando configuracoes de replicacao...
    powershell -Command "(Get-Content 'C:\xampp\mysql\bin\my.ini') -replace '^server-id\s*=.*', '# server-id = 1' | Set-Content 'C:\xampp\mysql\bin\my.ini'"
    powershell -Command "(Get-Content 'C:\xampp\mysql\bin\my.ini') -replace '^log-bin\s*=.*', '# log-bin = mysql-bin' | Set-Content 'C:\xampp\mysql\bin\my.ini'"
    echo Arquivo my.ini corrigido!
) else (
    echo AVISO: Arquivo my.ini nao encontrado
)

echo [4/8] Removendo arquivos de replicacao...
if exist "C:\xampp\mysql\data\master.info" del "C:\xampp\mysql\data\master.info" >nul 2>&1
if exist "C:\xampp\mysql\data\relay-log.info" del "C:\xampp\mysql\data\relay-log.info" >nul 2>&1
del "C:\xampp\mysql\data\mysql-relay-bin.*" >nul 2>&1
del "C:\xampp\mysql\data\mysql-bin.*" >nul 2>&1
echo Arquivos de replicacao removidos!

echo [5/8] Iniciando Apache...
start /b "" "C:\xampp\apache\bin\httpd.exe" -k start
timeout /t 5 /nobreak >nul

echo [6/8] Iniciando MySQL...
start /b "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
timeout /t 8 /nobreak >nul

echo [7/8] Verificando portas...
netstat -an | find ":80" >nul
if %errorlevel% equ 0 (
    echo [OK] Apache esta rodando na porta 80
) else (
    echo [ERRO] Apache nao esta rodando na porta 80
)

netstat -an | find ":3306" >nul
if %errorlevel% equ 0 (
    echo [OK] MySQL esta rodando na porta 3306
) else (
    echo [ERRO] MySQL nao esta rodando na porta 3306
)

echo [8/8] Abrindo navegador...
timeout /t 2 /nobreak >nul
start http://localhost

echo.
echo ========================================
echo    INICIALIZACAO CONCLUIDA!
echo ========================================
echo.
echo Teste acessando:
echo - http://localhost
echo - http://localhost/Sistema%%20De%%20Agendamento/public/
echo.
echo Se ainda houver problemas:
echo 1. Abra o XAMPP Control Panel
echo 2. Clique em "Start" para Apache e MySQL
echo 3. Verifique os logs se houver erros
echo.
pause
