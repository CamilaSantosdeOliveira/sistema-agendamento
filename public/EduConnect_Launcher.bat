@echo off
title EduConnect Dashboard
echo 🚀 Iniciando EduConnect Dashboard...
echo.

:: Verificar se o XAMPP está rodando
netstat -an | find "3306" >nul
if errorlevel 1 (
    echo ❌ MySQL não está rodando! Iniciando XAMPP...
    start "" "C:\xampp\xampp-control.exe"
    timeout /t 10 /nobreak >nul
)

:: Abrir o dashboard em uma janela dedicada
echo ✅ Abrindo EduConnect Dashboard...
start "" "http://localhost:8080/Sistema%%20De%%20Agendamento/public/"

:: Manter a janela aberta
echo.
echo 🎯 Dashboard aberto! Feche esta janela quando quiser.
echo 💡 Dica: Você pode fixar o site no navegador para acesso mais rápido.
pause













