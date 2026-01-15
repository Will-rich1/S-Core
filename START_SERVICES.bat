@echo off
REM Start MySQL and Apache Services for S-Core Application

echo ============================================
echo Starting S-Core Services...
echo ============================================

REM Start Apache
echo Starting Apache...
net start Apache2.4
if %errorlevel% neq 0 (
    echo Failed to start Apache. Trying alternative method...
    cd C:\xampp\apache\bin
    httpd.exe -k start
)

REM Start MySQL
echo Starting MySQL...
net start MySQL80
if %errorlevel% neq 0 (
    echo Failed to start MySQL80. Trying MySQL57...
    net start MySQL57
    if %errorlevel% neq 0 (
        echo Attempting manual MySQL startup...
        cd C:\xampp\mysql\bin
        start mysqld.exe
    )
)

echo ============================================
echo Services started. Please wait a moment...
echo ============================================
timeout /t 3

echo.
echo Services should now be running!
echo.
echo Access the application at: http://127.0.0.1:8000/admin
echo.
pause
