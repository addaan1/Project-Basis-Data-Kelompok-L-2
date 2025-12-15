@extends('layouts.main')

@section('content')
<div class="container-fluid p-4">
    <!-- Hero Section (Admin) -->
    <div class="card border-0 mb-5 shadow-lg overflow-hidden" 
         style="border-radius: 20px; background: linear-gradient(135deg, #212121, #424242); color: white;">
        <div class="card-body p-4 p-lg-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-center text-md-start">
                <h1 class="display-6 fw-bold mb-2">
                    <i class="bi bi-shield-lock me-2 text-danger"></i>Admin Console
                </h1>
                <p class="lead mb-0 opacity-75">
                    Monitoring Kesehatan Sistem & Aktivitas User
                </p>
            </div>
            <div>
                <button class="btn btn-outline-light btn-lg rounded-pill px-4 shadow-sm hover-scale" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i> Live Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- System Health Stats (ETL + Realtime) -->
    <div class="row g-4 mb-4">
        <!-- GMV (Gross Merchandise Value) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-currency-dollar display-4 text-success"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Total GMV (System)</p>
                    <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($adminStats['gmv'] ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-success fw-bold">
                        <i class="bi bi-graph-up-arrow me-1"></i>
                        Perputaran Uang
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Total User Base -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-people display-4 text-primary"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Total Pengguna</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($adminStats['total_users'] ?? 0) }}</h3>
                    <small class="text-primary fw-bold">
                        <span class="badge bg-primary-subtle text-primary rounded-pill">+{{ $adminStats['new_users_today'] ?? 0 }} Hari Ini</span>
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Daily Transactions -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-receipt display-4 text-info"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Transaksi Hari Ini</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($adminStats['total_tx_today'] ?? 0) }}</h3>
                    <small class="text-info fw-bold">
                        Activity Feed
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Pending Disputes/Nego -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 opacity-10">
                        <i class="bi bi-exclamation-triangle display-4 text-warning"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-muted mb-1">Negosiasi Menunggu</p>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($adminStats['pending_nego'] ?? 0) }}</h3>
                    <small class="text-warning fw-bold">
                        Potential bottlenecks
                    </small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: 50%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Tools & Tables -->
    <div class="row g-4">
        <!-- User Management (Quick View) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Manajemen Pengguna</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                        <i class="bi bi-people-fill me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th class="border-0 rounded-start">User</th>
                                    <th class="border-0">Role</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 rounded-end text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dummy Data for Preview/Visual Consistency -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3">
                                                <i class="bi bi-person fw-bold"></i>
                                            </div>
                                            <div>
                                                <span class="d-block fw-bold">Admin System</span>
                                                <small class="text-muted">admin@warungpadi.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-dark rounded-pill">Admin</span></td>
                                    <td><span class="badge bg-success-subtle text-success rounded-pill">Active</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light text-muted"><i class="bi bi-three-dots"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-center mt-3 p-3 bg-light rounded-3">
                             <small class="text-muted">Fitur manajemen user lengkap tersedia di menu "Users"</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                 <div class="card-header bg-white border-bottom-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Control Panel</h5>
                </div>
                <div class="card-body p-4">
                     <div class="d-grid gap-3">
                        <a href="#" class="btn btn-outline-dark text-start p-3 border rounded-3 d-flex align-items-center hover-shadow">
                            <div class="bg-light rounded-circle p-2 me-3">
                                <i class="bi bi-database-check fs-5 text-secondary"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Database Backup</div>
                                <small class="text-muted">Download SQL Dump</small>
                            </div>
                        </a>

                        <a href="{{ route('dashboard.data') }}" class="btn btn-outline-dark text-start p-3 border rounded-3 d-flex align-items-center hover-shadow">
                            <div class="bg-light rounded-circle p-2 me-3">
                                <i class="bi bi-braces fs-5 text-secondary"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Raw JSON API</div>
                                <small class="text-muted">Debug dashboard data</small>
                            </div>
                        </a>
                        
                        <a href="javascript:void(0)" onclick="alert('SystemLogs module coming soon')" class="btn btn-outline-dark text-start p-3 border rounded-3 d-flex align-items-center hover-shadow">
                            <div class="bg-light rounded-circle p-2 me-3">
                                <i class="bi bi-terminal fs-5 text-secondary"></i>
                            </div>
                            <div>
                                <div class="fw-bold">System Logs</div>
                                <small class="text-muted">View laravel.log</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card { transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-5px); }
    .hover-shadow:hover { background-color: #f8f9fa; border-color: transparent; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
</style>
@endsection
