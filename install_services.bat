@echo off
title BAC System - Install as Windows Services
color 0A

:: ============================================================
::  BAC Eligibilities Record Keeping System
::  Service Installer - Run this ONCE on a new computer
::  This makes Apache and MySQL start automatically on boot
::  so you don't need to open XAMPP every time.
::
::  REQUIREMENTS:
::    - XAMPP installed at C:\xampp\
::    - Run this file as Administrator
:: ============================================================

:: Check for Administrator privileges
net session >nul 2>&1
if %errorlevel% neq 0 (
    color 0C
    echo.
    echo  !! ERROR: This script must be run as Administrator !!
    echo.
    echo  Right-click this file and choose "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo.
echo  ================================================
echo      BAC Eligibilities Record Keeping System
echo      Windows Service Installer
echo  ================================================
echo.
echo  This will install Apache and MySQL as Windows
echo  Services so the BAC System runs automatically
echo  on startup without opening XAMPP.
echo.
pause

:: ----------------------------------------
:: Check if XAMPP is installed
:: ----------------------------------------
if not exist "C:\xampp\apache\bin\httpd.exe" (
    color 0C
    echo.
    echo  ERROR: XAMPP Apache not found at:
    echo         C:\xampp\apache\bin\httpd.exe
    echo.
    echo  Please install XAMPP first from https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)

if not exist "C:\xampp\mysql\bin\mysqld.exe" (
    color 0C
    echo.
    echo  ERROR: XAMPP MySQL not found at:
    echo         C:\xampp\mysql\bin\mysqld.exe
    echo.
    echo  Please install XAMPP first from https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)

echo.
echo  XAMPP found. Proceeding with installation...
echo.

:: ----------------------------------------
:: Install Apache as a Windows Service
:: ----------------------------------------
echo  [1/4] Installing Apache as a Windows Service...

:: Remove old service if it exists
sc query Apache2.4 >nul 2>&1
if %errorlevel% equ 0 (
    echo        Found existing Apache service. Removing it first...
    "C:\xampp\apache\bin\httpd.exe" -k uninstall >nul 2>&1
    timeout /t 2 /nobreak >nul
)

"C:\xampp\apache\bin\httpd.exe" -k install
if %errorlevel% neq 0 (
    color 0C
    echo.
    echo  ERROR: Failed to install Apache as a service.
    echo  Make sure nothing else is using Port 80 (like IIS).
    echo.
    pause
    exit /b 1
)

:: Set Apache to auto-start on boot
sc config Apache2.4 start= auto >nul 2>&1
echo        Apache service installed  [ OK ]

:: ----------------------------------------
:: Install MySQL as a Windows Service
:: ----------------------------------------
echo  [2/4] Installing MySQL as a Windows Service...

:: Remove old service if it exists
sc query MySQL >nul 2>&1
if %errorlevel% equ 0 (
    echo        Found existing MySQL service. Removing it first...
    net stop MySQL >nul 2>&1
    "C:\xampp\mysql\bin\mysqld" --remove >nul 2>&1
    timeout /t 2 /nobreak >nul
)

"C:\xampp\mysql\bin\mysqld" --install MySQL --defaults-file="C:\xampp\mysql\bin\my.ini"
if %errorlevel% neq 0 (
    color 0C
    echo.
    echo  ERROR: Failed to install MySQL as a service.
    echo  Make sure nothing else is using Port 3306.
    echo.
    pause
    exit /b 1
)

:: Set MySQL to auto-start on boot
sc config MySQL start= auto >nul 2>&1
echo        MySQL service installed    [ OK ]

:: ----------------------------------------
:: Start both services now
:: ----------------------------------------
echo.
echo  [3/4] Starting Apache...
net start Apache2.4 >nul 2>&1
timeout /t 3 /nobreak >nul

tasklist /FI "IMAGENAME eq httpd.exe" 2>nul | find /I "httpd.exe" >nul
if %errorlevel% neq 0 (
    color 0C
    echo        Apache   [ FAILED TO START ]
    echo  Check if Port 80 is in use by another program.
    pause
    exit /b 1
)
echo        Apache   [ RUNNING ]

echo  [4/4] Starting MySQL...
net start MySQL >nul 2>&1
timeout /t 4 /nobreak >nul

tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if %errorlevel% neq 0 (
    color 0C
    echo        MySQL    [ FAILED TO START ]
    echo  Check if Port 3306 is in use by another program.
    pause
    exit /b 1
)
echo        MySQL    [ RUNNING ]

:: ----------------------------------------
:: Done - Open the system in browser
:: ----------------------------------------
echo.
echo  ================================================
echo      Installation Complete!
echo  ================================================
echo.
echo   Apache and MySQL are now installed as Windows
echo   Services and will AUTO-START every time this
echo   computer boots up.
echo.
echo   You NO LONGER need to open XAMPP manually.
echo.
echo   URL: http://localhost/bac-system/public/index.php
echo.
echo  ================================================
echo.
echo  Opening BAC System in browser...
timeout /t 2 /nobreak >nul
start "" "http://localhost/bac-system/public/index.php"
echo.
pause
