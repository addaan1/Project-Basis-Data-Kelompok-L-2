# Laporan Implementasi Data Warehouse & Business Intelligence WarungPadi

Dokumen ini menjelaskan alur data (Data Flow) dan pembuktian teknis bahwa sistem telah menerapkan **ETL**, **Data Warehouse**, dan **OLAP** secara benar.

## 1. Arsitektur Sistem (Data Flow)

Data mengalir melalui 4 tahapan utama:

```mermaid
graph LR
    A[("Source OLTP\n(db_warungpadi)")] -->|Extract & Transform| B(Pentaho ETL\n.ktr Files)
    B -->|Load| C[("Data Warehouse\n(projek_basdat_dashboard)")]
    C -->|Query SQL (ROLAP)| D[WarungPadi Dashboard\n(Laravel App)]
    C -->|MDX Query| E[Mondrian OLAP\n(JSP Analytics)]
```

---

## 2. Rincian Implementasi & Bukti Kode

### A. ETL (Extract, Transform, Load)
Proses pemindahan dan pembersihan data dilakukan menggunakan **Pentaho Data Integration (Kettle)**.
*   **Bukti File**: Folder `/pentaho/`
    *   `ETL_fact_transaksi.ktr`: Memfilter transaksi status 'completed'/'disetujui' dan menghitung GMV.
    *   `ETL_fact_negosiasi.ktr`: Menghitung selisih harga tawar vs harga awal.
    *   `ETL_fact_stok_snapshot.ktr`: Merekam posisi stok harian.
*   **Automasi**: Batch script `run_all_etl.bat` mengeksekusi proses ini secara berurutan.

### B. Data Warehouse (DWH)
Database terpisah yang dirancang dengan skema **Star Schema**.
*   **Database**: `projek_basdat_dashboard` (Port 3308)
*   **Fact Tables**: 
    *   `fact_transaksi`: Menyimpan metrik penjualan.
    *   `fact_negosiasi`: Menyimpan riwayat tawar-menawar.
    *   `fact_stok_snapshot`: Menyimpan riwayat inventory.
*   **Dimension Tables**: `dim_users`, `dim_produk`, `dim_waktu`.

### C. Visualisasi Dashboard (Laravel)
Dashboard utama WarungPadi menampikan data yang bersumber dari DWH, bukan data transaksi langsung, untuk performa analitik yang lebih cepat.

**Bukti Kode (`app/Services/DashboardService.php`):**

1. **Koneksi Terpisah**:
   Service menggunakan koneksi `mysql_dashboard` (DWH), bukan koneksi default.
   ```php
   // Baris 28
   DB::connection('mysql_dashboard')->table('fact_transaksi')...
   ```

2. **Total Pendapatan (Income/Expense)**:
   Diambil dari `fact_transaksi`, bukan tabel `transaksis`.
   ```php
   // Baris 32-37
   $cashflow = DB::connection('mysql_dashboard')->table('fact_transaksi')
       ->selectRaw("SUM(...) as total_income")
       ...
   ```

3. **Stok & Kapasitas**:
   Diambil dari `fact_stok_snapshot` untuk melihat stok historis.
   ```php
   // Baris 50
   $inventory = DB::connection('mysql_dashboard')->table('fact_stok_snapshot')...
   ```

4. **Grafik Trend (Chart)**:
   Grafik bulanan/harian diambil melalui query agregasi ke `fact_transaksi`.
   ```php
   // Baris 299
   $query = DB::connection('mysql_dashboard')->table('fact_transaksi')
       ->selectRaw("DATE_FORMAT(created_at,...) as time_key, SUM(nilai_transaksi)...")
   ```

### D. OLAP (Mondrian)
Analitik lanjut (Slicing/Dicing) dilayani oleh Mondrian Cube.
*   **Schema**: `WEB-INF/queries/schema_updated.xml` mendefinisikan Cube `Sales`, `Inventory`, dll.
*   **Interface**: Halaman JSP (`dw_usermetrics.jsp`, dll) melakukan query MDX ke DWH.

---

## 3. Kesimpulan Verifikasi

Sistem dashboard Anda **VALID** sebagai implementasi BI karena:
1.  **Tidak membaca Operational Data (OLTP)** secara langsung untuk analitik, melainkan membaca `fact_transaksi`.
2.  Data di `fact_transaksi` terbukti hasil **Transformasi** (filter status & kalkulasi nilai) yang dilakukan oleh ETL Pentaho.
3.  Terdapat pemisahan database fisik antara Transaksi (`db_warungpadi`) dan Analitik (`projek_basdat_dashboard`).
