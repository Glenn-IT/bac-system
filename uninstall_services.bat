@echo off
title BAC System - Uninstall Windows Services
color 0E

:: ============================================================
::  BAC Eligibilities Record Keeping System
::  Service Uninstaller - Removes auto-start from Windows
::  After running this, you will need to open XAMPP manually
::  to start Apache and MySQL.
::
::  REQUIREMENTS: Run this file as Administrator
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
echo      Windows Service Uninstaller
echo  ================================================
echo.
echo  This will STOP Apache and MySQL and remove them
echo  from auto-starting on Windows boot.
echo.
echo  After this, you will need to use the XAMPP
echo  Control Panel to start services manually.
echo.
pause

echo.
echo  [1/2] Stopping and removing Apache service...
net stop Apache2.4 >nul 2>&1
"C:\xampp\apache\bin\httpd.exe" -k uninstall >nul 2>&1
taskkill /F /IM httpd.exe >nul 2>&1
echo        Apache  [ STOPPED and REMOVED ]

echo  [2/2] Stopping and removing MySQL service...
net stop MySQL >nul 2>&1
"C:\xampp\mysql\bin\mysqld" --remove >nul 2>&1
taskkill /F /IM mysqld.exe >nul 2>&1
echo        MySQL   [ STOPPED and REMOVED ]

echo.
echo  ================================================
echo      Done! Services have been removed.
echo  ================================================
echo.
echo  Apache and MySQL will NO LONGER auto-start.
echo  Use XAMPP Control Panel to start them manually.
echo.
pause
