SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE projek_basdat_dashboard.fact_transaksi;
TRUNCATE TABLE projek_basdat_dashboard.fact_negosiasi;
TRUNCATE TABLE projek_basdat_dashboard.fact_stok_snapshot;
TRUNCATE TABLE projek_basdat_dashboard.fact_user_daily_metrics;
SET FOREIGN_KEY_CHECKS = 1;
SELECT 'Tables Truncated Successfully' as status;
