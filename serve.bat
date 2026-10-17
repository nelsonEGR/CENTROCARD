@echo off
setlocal
rem ============================================================
rem  CENTROCARD - arranque independiente (NO usa Laragon)
rem  nginx + php-cgi (FastCGI persistente)  ->  http://centrocar.test/
rem ============================================================

set "PHP_CGI=C:\php\php-cgi.exe"
set "NGINX_DIR=C:\CENTROCARD\nginx\nginx-1.26.2"
set "NGINX=%NGINX_DIR%\nginx.exe"
set "CONF=C:/CENTROCARD/pos/nginx.conf"

if not exist "%PHP_CGI%" ( echo [ERROR] No existe "%PHP_CGI%" & pause & exit /b 1 )
if not exist "%NGINX%"   ( echo [ERROR] No existe "%NGINX%" & pause & exit /b 1 )

rem Verifica que el puerto 80 este libre (Laragon debe estar cerrado)
netstat -ano | findstr ":80 " | findstr "LISTENING" >nul
if not errorlevel 1 (
    echo [AVISO] El puerto 80 ya esta en uso ^(p.ej. Laragon^).
    echo         Cierra Laragon e intenta de nuevo.
    pause
    exit /b 1
)

rem Workers FastCGI persistentes: mantienen la conexion a Neon reutilizada
set "PHP_FCGI_CHILDREN=4"
set "PHP_FCGI_MAX_REQUESTS=100000"

echo Iniciando php-cgi (FastCGI en 127.0.0.1:9000)...
powershell -NoProfile -Command "Start-Process -FilePath '%PHP_CGI%' -ArgumentList '-b','127.0.0.1:9000' -WindowStyle Hidden"

echo Iniciando nginx (http://centrocar.test/ -> 127.0.0.1:80)...
powershell -NoProfile -Command "Start-Process -FilePath '%NGINX%' -ArgumentList '-c','%CONF%','-p','%NGINX_DIR%' -WorkingDirectory '%NGINX_DIR%' -WindowStyle Hidden"

timeout /t 2 >nul
echo.
echo CENTROCARD arriba. Abre:  http://centrocar.test/
echo Para detener:             serve-stop.bat
endlocal
