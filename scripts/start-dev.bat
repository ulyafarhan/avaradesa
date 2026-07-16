@echo off
title AvaraDesa - Launcher
color 0B

echo ==================================================
echo       AvaraDesa - LITE DEVELOPER LAUNCHER
echo ==================================================
echo.

echo [1/3] Membangun aset Vue...
call npm run build
if %errorlevel% neq 0 (
    echo [ERROR] Build gagal. Periksa error di atas.
    pause
    exit /b
)
echo [OK] Aset berhasil di-build.
echo.

echo [2/3] Menjalankan Server Laravel...
start "Laravel-Server" cmd /c "php artisan serve"
timeout /t 2 /nobreak >nul

echo [3/3] Menjalankan Queue Worker...
start "Laravel-Queue" cmd /c "php artisan queue:work --sleep=3 --tries=3"
timeout /t 1 /nobreak >nul

echo.
echo ==================================================
echo     SEMUA SISTEM TELAH AKTIF!
echo ==================================================
echo   Portal Warga  : http://127.0.0.1:8000
echo   Admin Panel   : http://127.0.0.1:8000/admin
echo   API Base URL  : http://127.0.0.1:8000/api/v1
echo.
echo   Untuk Flutter mobile (Chrome):
echo   flutter run -d chrome --dart-define=API_BASE_URL=http://127.0.0.1:8000/api/v1
echo ==================================================
echo.
echo  Buka browser otomatis dalam 3 detik...
timeout /t 3 /nobreak >nul

start http://127.0.0.1:8000
echo.
echo  Browser dibuka. Jangan tutup jendela ini.
echo  Tekan Ctrl+C untuk menghentikan semua server.
echo.
pause
