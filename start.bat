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

:: Start Apache using XAMPP shell
echo  [1/3] Starting Apache Web Server...
"C:\xampp\apache\bin\httpd.exe" -k install >nul 2>&1
"C:\xampp\apache\bin\httpd.exe" -k start >nul 2>&1
timeout /t 3 /nobreak >nul

:: Verify Apache is actually running
tasklist /FI "IMAGENAME eq httpd.exe" 2>nul | find /I "httpd.exe" >nul
if %errorlevel% neq 0 (
    color 0C
    echo        Apache        [ FAILED ]
    echo.
    echo  ERROR: Apache failed to start.
    echo  Please check if another program is using Port 80.
    echo.
    pause
    exit
)
echo        Apache        [ OK ]

:: Start MySQL using XAMPP shell
echo  [2/3] Starting MySQL Database...
"C:\xampp\mysql\bin\mysqld" --install >nul 2>&1
net start MySQL >nul 2>&1
timeout /t 4 /nobreak >nul

:: Verify MySQL is actually running
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if %errorlevel% neq 0 (
    color 0C
    echo        MySQL         [ FAILED ]
    echo.
    echo  ERROR: MySQL failed to start.
    echo  Please check if another program is using Port 3306.
    echo.
    pause
    exit
)
echo        MySQL         [ OK ]

:: Open browser
echo  [3/3] Opening BAC System in browser...
timeout /t 2 /nobreak >nul
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
