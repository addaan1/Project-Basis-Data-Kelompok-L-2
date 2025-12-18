# Implementation Plan - Scaling Data for Showcase

To showcase the "Trend" and "Monthly Analysis" features of the Dashboard, we will populate the database with a significant volume of data (1200+ transactions) spread across the last 12 months.

## Proposed Changes

### Database Seeder
#### [MODIFY] [DashboardSeeder.php](file:///c:/xampp/htdocs/WarungPadi2/WarungPadi/database/seeders/DashboardSeeder.php)
- Increase transaction count from 100 to **1200**.
- Randomize dates across the last **12 months** (Carbon `subMonths(rand(0, 12))`).
- Ensure consistent pricing and product references.

## Verification Plan

### Automated Steps
1.  **Reset & Seed OLTP**: Run `php artisan db:seed --class=DashboardSeeder` to populate `db_warungpadi` with 1200 rows.
2.  **Clear DWH**: Execute `clean_dwh.sql` to truncate existing Fact tables.
3.  **Run ETL**: Execute `run_all_etl.bat` to process the 1200 rows into the DWH.
4.  **Verify SQL**: Run query `SELECT COUNT(*) FROM fact_transaksi` (Expected: 1200+).

### Manual Verification
- **Dashboard**: Refresh browser. Check "Trend Pendapatan" (Income Trend) card; it should show a curve spanning 12 months instead of just recent days.
