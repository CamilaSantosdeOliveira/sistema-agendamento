@echo off
echo ==========================================
echo     SISTEMA DE AGENDAMENTO - INICIALIZAR
echo ==========================================
echo.

echo Verificando XAMPP...
tasklist /fi "imagename eq httpd.exe" 2>nul | find /i "httpd.exe" >nul
if "%ERRORLEVEL%"=="0" (
    echo ✅ Apache esta rodando
) else (
    echo ⚠️  Apache nao esta rodando
    echo Iniciando Apache...
    start "" "C:\xampp\xampp-control.exe"
    timeout /t 3 >nul
)

tasklist /fi "imagename eq mysqld.exe" 2>nul | find /i "mysqld.exe" >nul
if "%ERRORLEVEL%"=="0" (
    echo ✅ MySQL esta rodando
) else (
    echo ⚠️  MySQL nao esta rodando
    echo Iniciando MySQL...
    start "" "C:\xampp\xampp-control.exe"
    timeout /t 3 >nul
)

echo.
echo Configurando banco de dados...
C:\xampp\php\php.exe -f teste_sistema.php > nul 2>&1

echo.
echo ==========================================
echo Sistema configurado com sucesso!
echo.
echo Acesse o sistema em:
echo 🏠 Sistema Principal: http://localhost/Sistema%%20De%%20Agendamento/public/sistema_final_corrigido.html
echo 🎯 Central de Navegacao: http://localhost/Sistema%%20De%%20Agendamento/public/central.html
echo ✨ Interface Melhorada: http://localhost/Sistema%%20De%%20Agendamento/public/index_melhorado.html
echo 🔧 Teste do Sistema: http://localhost/Sistema%%20De%%20Agendamento/public/teste_sistema.php
echo ==========================================
echo.

echo Deseja abrir o sistema agora? (S/N)
set /p escolha=
if /i "%escolha%"=="S" (
    start http://localhost/Sistema%%20De%%20Agendamento/public/sistema_final_corrigido.html
)

pause
