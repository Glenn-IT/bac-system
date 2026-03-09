@echo off
title BAC System - Stopping...
color 0C

echo ================================================
echo     BAC Eligibilities Record Keeping System
echo ================================================
echo.
echo  Stopping services, please wait...
echo.

:: Stop Apache
echo  [1/2] Stopping Apache Web Server...
"C:\xampp\apache\bin\httpd.exe" -k stop >nul 2>&1
net stop Apache2.4 >nul 2>&1
taskkill /F /IM httpd.exe >nul 2>&1
echo        Apache stopped!

:: Stop MySQL
echo  [2/2] Stopping MySQL Database...
net stop MySQL >nul 2>&1
"C:\xampp\mysql\bin\mysqladmin" -u root shutdown >nul 2>&1
taskkill /F /IM mysqld.exe >nul 2>&1
echo        MySQL stopped!

echo.
echo ================================================
echo     BAC System has been STOPPED.
echo ================================================
echo.
timeout /t 3 /nobreak >nul
