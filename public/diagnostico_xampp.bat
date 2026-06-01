@echo off
echo ========================================
echo    DIAGNOSTICO COMPLETO DO XAMPP
echo ========================================
echo.

echo [INFO] Verificando instalacao do XAMPP...
if exist "C:\xampp\xampp-control.exe" (
    echo [OK] XAMPP encontrado em C:\xampp\
) else (
    echo [ERRO] XAMPP nao encontrado em C:\xampp\
    echo Instale o XAMPP primeiro!
    pause
    exit /b 1
)

echo.
echo [INFO] Verificando processos ativos...
tasklist | find "httpd.exe" >nul
if %errorlevel% equ 0 (
    echo [OK] Processo Apache (httpd.exe) encontrado
) else (
    echo [ERRO] Processo Apache (httpd.exe) nao encontrado
)

tasklist | find "mysqld.exe" >nul
if %errorlevel% equ 0 (
    echo [OK] Processo MySQL (mysqld.exe) encontrado
) else (
    echo [ERRO] Processo MySQL (mysqld.exe) nao encontrado
)

echo.
echo [INFO] Verificando portas...
netstat -an | find ":80" >nul
if %errorlevel% equ 0 (
    echo [OK] Porta 80 (Apache) esta em uso
) else (
    echo [ERRO] Porta 80 (Apache) nao esta em uso
)

netstat -an | find ":3306" >nul
if %errorlevel% equ 0 (
    echo [OK] Porta 3306 (MySQL) esta em uso
) else (
    echo [ERRO] Porta 3306 (MySQL) nao esta em uso
)

echo.
echo [INFO] Verificando arquivos importantes...
if exist "C:\xampp\mysql\bin\my.ini" (
    echo [OK] Arquivo my.ini encontrado
) else (
    echo [ERRO] Arquivo my.ini nao encontrado
)

if exist "C:\xampp\apache\conf\httpd.conf" (
    echo [OK] Arquivo httpd.conf encontrado
) else (
    echo [ERRO] Arquivo httpd.conf nao encontrado
)

echo.
echo [INFO] Verificando arquivos de replicacao...
if exist "C:\xampp\mysql\data\master.info" (
    echo [AVISO] Arquivo master.info encontrado (pode causar problemas)
) else (
    echo [OK] Arquivo master.info nao encontrado
)

if exist "C:\xampp\mysql\data\relay-log.info" (
    echo [AVISO] Arquivo relay-log.info encontrado (pode causar problemas)
) else (
    echo [OK] Arquivo relay-log.info nao encontrado
)

echo.
echo [INFO] Verificando configuracoes do my.ini...
findstr "server-id" "C:\xampp\mysql\bin\my.ini" >nul
if %errorlevel% equ 0 (
    echo [AVISO] Configuracao server-id encontrada no my.ini
    findstr "server-id" "C:\xampp\mysql\bin\my.ini"
) else (
    echo [OK] Configuracao server-id nao encontrada
)

findstr "log-bin" "C:\xampp\mysql\bin\my.ini" >nul
if %errorlevel% equ 0 (
    echo [AVISO] Configuracao log-bin encontrada no my.ini
    findstr "log-bin" "C:\xampp\mysql\bin\my.ini"
) else (
    echo [OK] Configuracao log-bin nao encontrada
)

echo.
echo ========================================
echo    SOLUCOES RECOMENDADAS
echo ========================================
echo.
echo Se Apache nao esta funcionando:
echo 1. Execute: iniciar_xampp.bat
echo 2. Ou abra o XAMPP Control Panel e clique em "Start" para Apache
echo.
echo Se MySQL nao esta funcionando:
echo 1. Execute: iniciar_xampp.bat
echo 2. Ou abra o XAMPP Control Panel e clique em "Start" para MySQL
echo 3. Se houver erro, verifique os logs no XAMPP Control Panel
echo.
echo Se houver arquivos de replicacao:
echo 1. Execute: iniciar_xampp.bat (remove automaticamente)
echo 2. Ou delete manualmente os arquivos master.info e relay-log.info
echo.
echo ========================================
echo    COMANDOS UTEIS
echo ========================================
echo.
echo Para parar servicos:
echo taskkill /f /im httpd.exe
echo taskkill /f /im mysqld.exe
echo.
echo Para verificar portas:
echo netstat -an | find ":80"
echo netstat -an | find ":3306"
echo.
echo Para iniciar servicos:
echo "C:\xampp\apache\bin\httpd.exe" -k start
echo "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
echo.
pause
