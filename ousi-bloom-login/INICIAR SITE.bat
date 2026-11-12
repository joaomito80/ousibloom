@echo off
setlocal
cd /d "%~dp0"
title Ousi Bloom - Servidor local
set "PHP_CMD=php"
where php >nul 2>&1
if errorlevel 1 (
  if exist "%~dp0php\php.exe" (
    set "PHP_CMD=%~dp0php\php.exe"
  ) else if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
  ) else (
    echo.
    echo PHP nao encontrado neste computador.
    echo Instale o PHP ou o XAMPP e execute este arquivo novamente.
    echo Se usar XAMPP, nao precisa iniciar o Apache.
    echo.
    pause
    exit /b 1
  )
)
echo.
echo Iniciando Ousi Bloom em http://localhost:8000
 echo Para encerrar, feche esta janela.
start "" "http://localhost:8000"
"%PHP_CMD%" -S 127.0.0.1:8000 router.php
if errorlevel 1 (
 echo.
 echo Nao foi possivel iniciar. Talvez a porta 8000 ja esteja em uso.
 pause
)
