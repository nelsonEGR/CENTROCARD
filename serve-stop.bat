@echo off
setlocal
rem ============================================================
rem  Detiene el servidor independiente del proyecto CENTROCARD
rem  (nginx + php-cgi)
rem ============================================================

echo Deteniendo nginx...
taskkill /IM nginx.exe /F >nul 2>&1

echo Deteniendo php-cgi...
taskkill /IM php-cgi.exe /F >nul 2>&1

echo.
echo Servidor detenido.
endlocal
