@echo off
title Corrigir MySQL Shutdown Unexpectedly
color 0C

echo.
echo  ========================================
echo  CORREÇÃO MYSQL SHUTDOWN UNEXPECTEDLY
echo  ========================================
echo.

echo [INFO] Iniciando correção automática do MySQL...
echo.

:: PASSO 1: Encerrar todos os processos MySQL
echo [PASSO 1] Encerrando processos MySQL conflitantes...
taskkill /F /IM mysqld.exe >nul 2>&1
taskkill /F /IM mysql.exe >nul 2>&1
taskkill /F /IM mysqld-nt.exe >nul 2>&1
taskkill /F /IM mysqld-5.7.exe >nul 2>&1
taskkill /F /IM mysqld-8.0.exe >nul 2>&1
echo [OK] Processos MySQL encerrados.
echo.

:: PASSO 2: Aguardar liberação de recursos
echo [PASSO 2] Aguardando liberação de recursos...
timeout /t 5 /nobreak >nul
echo [OK] Recursos liberados.
echo.

:: PASSO 3: Verificar se a porta 3306 está livre
echo [PASSO 3] Verificando porta 3306...
netstat -an | find ":3306" >nul
if %errorlevel% equ 0 (
    echo [ERRO] Porta 3306 ainda está em uso!
    echo [INFO] Aguardando mais tempo...
    timeout /t 10 /nobreak >nul
) else (
    echo [OK] Porta 3306 está livre.
)
echo.

:: PASSO 4: Fazer backup do my.ini
echo [PASSO 4] Fazendo backup do arquivo my.ini...
if exist "C:\xampp\mysql\bin\my.ini" (
    if not exist "C:\xampp\mysql\bin\my.ini.backup" (
        copy "C:\xampp\mysql\bin\my.ini" "C:\xampp\mysql\bin\my.ini.backup" >nul
        echo [OK] Backup criado: my.ini.backup
    ) else (
        echo [INFO] Backup já existe.
    )
) else (
    echo [ERRO] Arquivo my.ini não encontrado!
    echo [INFO] Verifique se o XAMPP está instalado corretamente.
)
echo.

:: PASSO 5: Remover arquivos de log corrompidos
echo [PASSO 5] Removendo arquivos de log corrompidos...
if exist "C:\xampp\mysql\data\ib_logfile0" del "C:\xampp\mysql\data\ib_logfile0" >nul 2>&1
if exist "C:\xampp\mysql\data\ib_logfile1" del "C:\xampp\mysql\data\ib_logfile1" >nul 2>&1
if exist "C:\xampp\mysql\data\ibdata1" del "C:\xampp\mysql\data\ibdata1" >nul 2>&1
if exist "C:\xampp\mysql\data\mysql-bin.index" del "C:\xampp\mysql\data\mysql-bin.index" >nul 2>&1
echo [OK] Arquivos de log removidos.
echo.

:: PASSO 6: Tentar iniciar MySQL
echo [PASSO 6] Tentando iniciar MySQL...
if exist "C:\xampp\mysql\bin\mysqld.exe" (
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini"
    echo [INFO] MySQL iniciado. Aguardando inicialização...
    timeout /t 10 /nobreak >nul
    
    :: Verificar se está rodando
    netstat -an | find ":3306" >nul
    if %errorlevel% equ 0 (
        echo [SUCESSO] MySQL está rodando na porta 3306!
        echo.
        echo [INFO] Próximos passos:
        echo [INFO] 1. Abra o XAMPP Control Panel
        echo [INFO] 2. Verifique se o MySQL está com luz verde
        echo [INFO] 3. Teste o phpMyAdmin: http://localhost/phpmyadmin
        echo [INFO] 4. Volte para o sistema: http://localhost:8080/Sistema%%20De%%20Agendamento/public/
        echo.
    ) else (
        echo [ERRO] MySQL não conseguiu iniciar automaticamente.
        echo [INFO] Tente iniciar manualmente pelo XAMPP Control Panel.
    )
) else (
    echo [ERRO] Executável MySQL não encontrado!
    echo [INFO] Verifique se o XAMPP está instalado corretamente.
)
echo.

:: PASSO 7: Abrir XAMPP Control Panel
echo [PASSO 7] Abrindo XAMPP Control Panel...
if exist "C:\xampp\xampp-control.exe" (
    start "" "C:\xampp\xampp-control.exe"
    echo [OK] XAMPP Control Panel aberto.
) else (
    echo [ERRO] XAMPP Control Panel não encontrado!
)
echo.

echo [INFO] Correção concluída!
echo [INFO] Se o MySQL ainda não funcionar, execute o script PHP de correção.
echo.
echo [INFO] Pressione qualquer tecla para abrir o script PHP de correção...
pause >nul

:: Abrir o script PHP de correção
start "" "http://localhost:8080/Sistema%%20De%%20Agendamento/public/corrigir_mysql_shutdown.php"

echo [INFO] Script PHP aberto no navegador.
echo [INFO] Pressione qualquer tecla para fechar...
pause >nul











