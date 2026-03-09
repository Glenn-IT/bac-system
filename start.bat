@echo off
title BAC System - Starting...
color 0A

echo ================================================
echo     BAC Eligibilities Record Keeping System
echo ================================================
echo.
echo  Starting services, please wait...
echo.

:: Check if XAMPP exists
if not exist "C:\xampp\apache\bin\httpd.exe" (
    color 0C
    echo  ERROR: XAMPP not found at C:\xampp\
    echo  Please install XAMPP first.
    echo.
    pause
    exit
)

:: Start Apache
echo  [1/3] Starting Apache Web Server...
"C:\xampp\apache\bin\httpd.exe" -k start >nul 2>&1
net start Apache2.4 >nul 2>&1
timeout /t 2 /nobreak >nul
echo        Apache        [ OK ]

:: Start MySQL
echo  [2/3] Starting MySQL Database...
net start MySQL >nul 2>&1
if %errorlevel% neq 0 (
    start /B "" "C:\xampp\mysql\bin\mysqld" --defaults-file="C:\xampp\mysql\bin\my.ini" >nul 2>&1
)
timeout /t 3 /nobreak >nul
echo        MySQL         [ OK ]

:: Wait for full initialization
echo  [3/3] Opening BAC System in browser...
timeout /t 2 /nobreak >nul

:: Open the system in default browser
start "" "http://localhost/bac-system/public/index.php"

echo.
echo ================================================
echo     BAC System is now RUNNING!
echo ================================================
echo.
echo   URL   : http://localhost/bac-system/public/index.php
echo   Status: ONLINE
echo.
echo  DO NOT close this window while using the system.
echo  To stop the system, run stop.bat
echo.
pause
