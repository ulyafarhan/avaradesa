$ErrorActionPreference = "SilentlyContinue"
Clear-Host

function Write-Color($Text, $Color = "White") {
    Write-Host $Text -ForegroundColor $Color
}

Write-Color "==================================================" Cyan
Write-Color "      AvaraDesa - LITE DEVELOPER LAUNCHER        " Cyan
Write-Color "==================================================" Cyan
Write-Color ""

Write-Color "[1/3] Membangun Aset Frontend Vue..." Green
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Color "Gagal build. Periksa error di atas." Red
    pause
    exit
}
Write-Color " Aset berhasil di-build." Green
Write-Color ""

Write-Color "[2/3] Menjalankan Server Laravel..." Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "`$Host.UI.RawUI.WindowTitle = 'Laravel Server'; php artisan serve"
Start-Sleep 3

Write-Color "[3/3] Menjalankan Queue Worker..." Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "`$Host.UI.RawUI.WindowTitle = 'Laravel Queue'; php artisan queue:work --sleep=3 --tries=3"

Write-Color ""
Write-Color "==================================================" Yellow
Write-Color " SEMUA SISTEM TELAH AKTIF!" Green
Write-Color "==================================================" Yellow
Write-Color "  Portal Warga  : http://127.0.0.1:8000" Cyan
Write-Color "  Admin Panel   : http://127.0.0.1:8000/admin" Cyan
Write-Color "  API Base URL  : http://127.0.0.1:8000/api/v1" Cyan
Write-Color ""
Write-Color "  Untuk Flutter mobile (Chrome):" Yellow
Write-Color "  flutter run -d chrome --dart-define=API_BASE_URL=http://127.0.0.1:8000/api/v1" Cyan
Write-Color "==================================================" Yellow
Write-Color ""
Write-Color "Menu:" Yellow
Write-Color "  Ketik 'exit' + Enter -> Tutup semua server & keluar" Yellow
Write-Color "  Tekan Enter        -> Buka browser" Yellow
Write-Color "  Ctrl+C             -> Tutup paksa semua" Yellow

$choice = Read-Host
if ($choice -eq "exit") {
    Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
    Get-Process -Name "powershell" | Where-Object { $_.MainWindowTitle -like "Laravel*" } | Stop-Process -Force
    Write-Color "Semua server ditutup." Green
} else {
    Start-Process "http://127.0.0.1:8000"
    Write-Color "Browser dibuka. Jendela ini bisa ditutup." Green
}
