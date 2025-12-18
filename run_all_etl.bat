@echo off
echo ==========================================
echo      WARUNGPADI ETL RUNNER
echo ==========================================
echo.

echo [1/6] Running ETL_dim_users...
call C:\Pentaho\data-integration\Pan.bat /file:"c:\xampp\htdocs\WarungPadi2\WarungPadi\pentaho\ETL_dim_users.ktr" /level:Basic
if %ERRORLEVEL% NEQ 0 (
    echo Error running ETL_dim_users
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo [2/6] Running ETL_dim_produk...
call C:\Pentaho\data-integration\Pan.bat /file:"c:\xampp\htdocs\WarungPadi2\WarungPadi\pentaho\ETL_dim_produk.ktr" /level:Basic
if %ERRORLEVEL% NEQ 0 (
    echo Error running ETL_dim_produk
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo [3/6] Running ETL_fact_transaksi...
call C:\Pentaho\data-integration\Pan.bat /file:"c:\xampp\htdocs\WarungPadi2\WarungPadi\pentaho\ETL_fact_transaksi.ktr" /level:Basic
if %ERRORLEVEL% NEQ 0 (
    echo Error running ETL_fact_transaksi
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo [4/6] Running ETL_fact_negosiasi...
call C:\Pentaho\data-integration\Pan.bat /file:"c:\xampp\htdocs\WarungPadi2\WarungPadi\pentaho\ETL_fact_negosiasi.ktr" /level:Basic
if %ERRORLEVEL% NEQ 0 (
    echo Error running ETL_fact_negosiasi
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo [5/6] Running ETL_fact_stok_snapshot...
call C:\Pentaho\data-integration\Pan.bat /file:"c:\xampp\htdocs\WarungPadi2\WarungPadi\pentaho\ETL_fact_stok_snapshot.ktr" /level:Basic
if %ERRORLEVEL% NEQ 0 (
    echo Error running ETL_fact_stok_snapshot
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo [6/6] Running ETL_fact_user_daily_metrics...
call C:\Pentaho\data-integration\Pan.bat /file:"c:\xampp\htdocs\WarungPadi2\WarungPadi\pentaho\ETL_fact_user_daily_metrics.ktr" /level:Basic
if %ERRORLEVEL% NEQ 0 (
    echo Error running ETL_fact_user_daily_metrics
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo ==========================================
echo      ALL ETL PROCESSES COMPLETED!
echo ==========================================
pause
