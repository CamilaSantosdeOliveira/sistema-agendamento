@echo off
title Configurar Acesso Mobile - EduConnect
color 0A

echo.
echo  ███████╗██████╗ ██╗   ██╗ ██████╗██╗  ██╗███████╗███╗   ██╗██████╗ 
echo  ██╔════╝██╔══██╗██║   ██║██╔════╝██║ ██╔╝██╔════╝████╗  ██║██╔══██╗
echo  █████╗  ██║  ██║██║   ██║██║     █████╔╝ █████╗  ██╔██╗ ██║██████╔╝
echo  ██╔══╝  ██║  ██║██║   ██║██║     ██╔═██╗ ██╔══╝  ██║╚██╗██║██╔══██╗
echo  ███████╗██████╔╝╚██████╔╝╚██████╗██║  ██╗███████╗██║ ╚████║██║  ██║
echo  ╚══════╝╚═════╝  ╚═════╝  ╚═════╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═══╝╚═╝  ╚═╝
echo.
echo  Configurando Acesso Mobile
echo  ===========================
echo.

:: Verificar se o XAMPP está rodando
echo [INFO] Verificando se o XAMPP está rodando...
netstat -an | find "3306" >nul
if errorlevel 1 (
    echo [ERRO] MySQL não está rodando! Iniciando XAMPP...
    start "" "C:\xampp\xampp-control.exe"
    echo [INFO] Aguardando o servidor iniciar...
    timeout /t 10 /nobreak >nul
) else (
    echo [OK] MySQL está rodando!
)

:: Verificar Apache
echo [INFO] Verificando Apache...
netstat -an | find ":80" >nul
if errorlevel 1 (
    echo [ERRO] Apache não está rodando na porta 80!
    echo [INFO] Verifique se o XAMPP está configurado corretamente.
) else (
    echo [OK] Apache está rodando na porta 80!
)

:: Verificar porta 8080
echo [INFO] Verificando porta 8080...
netstat -an | find ":8080" >nul
if errorlevel 1 (
    echo [AVISO] Apache não está na porta 8080. Tentando porta 80...
    set PORT=80
) else (
    echo [OK] Apache está rodando na porta 8080!
    set PORT=8080
)

:: Mostrar IP
echo.
echo [INFO] Seu IP na rede: 192.168.31.196
echo [INFO] Porta do servidor: %PORT%
echo.

:: Testar conectividade
echo [INFO] Testando conectividade...
ping -n 1 192.168.31.196 >nul
if errorlevel 1 (
    echo [ERRO] Não foi possível conectar ao IP!
) else (
    echo [OK] IP acessível!
)

:: Configurar firewall (tentar)
echo [INFO] Tentando configurar firewall...
netsh advfirewall firewall add rule name="EduConnect HTTP" dir=in action=allow protocol=TCP localport=%PORT% >nul 2>&1
if errorlevel 1 (
    echo [AVISO] Não foi possível configurar firewall automaticamente.
    echo [INFO] Tente desativar o firewall temporariamente.
) else (
    echo [OK] Regra do firewall adicionada!
)

echo.
echo [SUCESSO] Configuração concluída!
echo.
echo [LINKS PARA CELULAR:]
echo Dashboard: http://192.168.31.196:%PORT%/Sistema%%20De%%20Agendamento/public/
echo App Mobile: http://192.168.31.196:%PORT%/Sistema%%20De%%20Agendamento/public/mobile_app.html
echo.
echo [INSTRUÇÕES:]
echo 1. Certifique-se de que PC e celular estão na mesma rede Wi-Fi
echo 2. Use os links acima no celular
echo 3. Se não funcionar, desative o firewall temporariamente
echo.
pause













