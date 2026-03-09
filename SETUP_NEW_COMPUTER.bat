@echo off
title BAC System Setup
color 0A

:: Must run as Administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    color 0C
    echo.
    echo  ERROR: Right-click this file and select "Run as administrator"
    echo.
    pause
    exit /b 1
)

cls
echo.
echo  ====================================================
echo      BAC System - New Computer Setup
echo  ====================================================
echo.
echo  Press any key to start...
pause >nul

:: Step 1: Start MySQL
echo.
echo  [1/4] Starting MySQL...
"C:\xampp\mysql\bin\mysqld" --install MySQL --defaults-file="C:\xampp\mysql\bin\my.ini" >nul 2>&1
net start MySQL >nul 2>&1
timeout /t 5 /nobreak >nul
echo        MySQL          [ OK ]

:: Step 2: Start Apache
echo  [2/4] Starting Apache...
"C:\xampp\apache\bin\httpd.exe" -k install >nul 2>&1
net start Apache2.4 >nul 2>&1
timeout /t 3 /nobreak >nul
echo        Apache         [ OK ]

:: Step 3: Import database
echo  [3/4] Importing database...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS bac_system CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;" >nul 2>&1
"C:\xampp\mysql\bin\mysql.exe" -u root bac_system < "C:\xampp\htdocs\bac-system\database\bac_system.sql"
if %errorlevel% neq 0 (
    color 0C
    echo.
    echo  ERROR: Database import failed!
    echo  Make sure MySQL is running and the SQL file exists.
    echo.
    pause
    exit /b 1
)
echo        Database       [ OK ]

:: Step 4: Set both to auto-start on boot
echo  [4/4] Setting auto-start on boot...
sc config Apache2.4 start= auto >nul 2>&1
sc config MySQL start= auto >nul 2>&1
echo        Auto-start     [ OK ]

:: Open browser
echo.
echo  Opening BAC System...
timeout /t 2 /nobreak >nul
start "" "http://localhost/bac-system/public/index.php"

cls
color 0A
echo.
echo  ====================================================
echo      SETUP COMPLETE!
echo  ====================================================
echo.
echo   URL : http://localhost/bac-system/public/index.php
echo.
echo   Apache and MySQL will AUTO-START on every boot.
echo   No need to open XAMPP manually.
echo.
echo  ====================================================
echo.
pause
