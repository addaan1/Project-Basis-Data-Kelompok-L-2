<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Warung Padi</title>
    
    <!-- Bootstrap 5.3 untuk konsistensi dan upgrade tampilan -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Elegant Typography: Playfair Display + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons untuk icons modern -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Font Awesome jika diperlukan, tapi prioritas Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS untuk upgrade tampilan (glassmorphism, animasi, dll.) -->
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <style>
        :root {
            /* Rice Ecosystem Color Palette */
            --rice-gold: #F4C430;
            --rice-green: #8BC34A;
            --earth-brown: #8D6E63;
            --pure-white: #FFFFFF;
            --deep-green: #2E7D32;
            --soft-cream: #FFF8DC;
            
            /* Spacing System (8px grid) */
            --space-xs: 0.5rem;  /* 8px */
            --space-sm: 1rem;    /* 16px */
            --space-md: 1.5rem;  /* 24px */
            --space-lg: 2rem;    /* 32px */
            --space-xl: 3rem;    /* 48px */
            --space-2xl: 4rem;   /* 64px */
            
            /* Typography */
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Inter', sans-serif;
            
            /* Shadows */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.15);
            --shadow-xl: 0 12px 48px rgba(0, 0, 0, 0.2);
            
            /* Glassmorphism */
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-blur: blur(20px);
        }
        
        body {
            font-family: var(--font-body);
            background: linear-gradient(135deg, #FFF8DC 0%, #F4E4C1 50%, #E8D4A0 100%);
            position: relative;
            color: #333;
            overflow-x: hidden;
            min-height: 100vh;
            padding-top: 70px;
        }
        
        /* Dynamic Rice Field Pattern Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(90deg, rgba(139, 195, 74, 0.03) 0px, transparent 1px, transparent 40px, rgba(139, 195, 74, 0.03) 41px),
                repeating-linear-gradient(0deg, rgba(139, 195, 74, 0.03) 0px, transparent 1px, transparent 40px, rgba(139, 195, 74, 0.03) 41px);
            opacity: 0.4;
            z-index: -1;
            animation: ricePattern 20s linear infinite;
        }
        
        @keyframes ricePattern {
            0% { transform: translateY(0); }
            100% { transform: translateY(40px); }
        }
        
        /* Animated Gradient Overlay */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                rgba(244, 196, 48, 0.1) 0%, 
                rgba(139, 195, 74, 0.1) 50%, 
                rgba(244, 196, 48, 0.1) 100%);
            z-index: -1;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }
        
        /* Typography Enhancements */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        
        h1 { font-size: 2.5rem; line-height: 1.2; }
        h2 { font-size: 2rem; line-height: 1.3; }
        h3 { font-size: 1.75rem; line-height: 1.4; }
        h4 { font-size: 1.5rem; line-height: 1.4; }
        h5 { font-size: 1.25rem; line-height: 1.5; }
        h6 { font-size: 1rem; line-height: 1.5; }
        
        /* Navbar Upgrade: Enhanced Glassmorphism */
        .navbar-custom {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-bottom: 1px solid var(--glass-border);
            padding: var(--space-sm) 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
        }
        
        .navbar-custom.scrolled {
            background: rgba(255, 248, 220, 0.95);
            padding: var(--space-xs) 0;
            box-shadow: var(--shadow-lg);
        }
        
        .navbar-brand img {
            height: 50px;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05);
        }
        
        .nav-link {
            color: var(--deep-green) !important;
            font-weight: 600;
            margin: 0 var(--space-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            font-family: var(--font-body);
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: -100%;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--rice-gold), var(--rice-green));
            transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link:hover::before {
            left: 0;
        }
        
        .nav-link:hover {
            color: var(--rice-gold) !important;
            transform: translateY(-2px);
        }
        
        /* User Greeting Section */
        .user-greeting {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            color: var(--deep-green);
            font-weight: 600;
            background: var(--glass-bg);
            padding: var(--space-xs) var(--space-sm);
            border-radius: 50px;
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .user-greeting:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rice-gold), var(--rice-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pure-white);
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: var(--shadow-sm);
        }
        
        /* Toggle Button Upgrade */
        .toggle-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: #fff;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
            box-shadow: var(--shadow-light);
        }
        
        /* Container Flex untuk Layout */
        .container-fluid-custom {
            display: flex;
            min-height: 100vh;
            padding-top: 0;
        }
        
        /* Sidebar Upgrade: Lebih Smooth, Glass Effect */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid var(--glass-border);
            padding: 2rem 0;
            position: fixed;
            left: 0;
            top: 60px; /* Adjust untuk navbar height */
            height: calc(100vh - 60px);
            overflow-y: auto;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 1000;
            box-shadow: var(--shadow-heavy);
        }
        
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        
        .logo {
            display: flex;
            align-items: center;
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0 0 20px 20px;
            position: relative;
        }
        
        .logo img {
            width: 45px;
            height: 45px;
            margin-right: 12px;
            border-radius: 10px;
            box-shadow: var(--shadow-light);
        }
        
        .logo span {
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Tombol Collapse di Sidebar */
        .sidebar-collapse-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: #333;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            cursor: pointer;
            font-size: 1rem;
        }
        
        .sidebar-collapse-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
            box-shadow: var(--shadow-light);
            color: var(--primary-green);
        }
        
        .sidebar h4 {
            padding: 0 1.5rem;
            margin: 1.5rem 0 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d; /* Darker color for white background */
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar li {
            margin: 0;
        }
        
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: #333; /* Darker color for white background */
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent); /* Subtle hover effect */
            transition: left 0.5s;
        }
        
        .sidebar a:hover::before {
            left: 100%;
        }
        
        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(0, 0, 0, 0.05); /* Subtle background on hover/active */
            color: var(--primary-green); /* Green color for active/hover */
            border-left-color: var(--primary-green);
            transform: translateX(5px);
            box-shadow: none; /* Remove shadow for simple links */
        }
        
        .sidebar a i {
            margin-right: 12px;
            width: 20px;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }
        
        .sidebar a:hover i {
            transform: scale(1.1);
        }

        .sidebar-button {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem 1.5rem;
            text-decoration: none;
            color: #fff;
            border-radius: 25px;
            background: linear-gradient(to right, #FFD700, #FFA500); /* Yellow to Orange gradient */
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
            transition: all 0.3s ease;
        }

        .sidebar-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
        }

        .sidebar-button i {
            margin-right: 12px;
            width: 20px;
            font-size: 1.1rem;
            color: #ff12;
        }
        
        .logout {
            width: calc(100% - 3rem); /* Adjust width for padding */
            margin: 2rem 1.5rem 0;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #28a745, #20c997); /* Green gradient */
            border: none;
            color: #fff;
            text-align: center; /* Center text for button */
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center; /* Center content */
            transition: all 0.3s ease;
            border-radius: 25px; /* Rounded corners */
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            background: linear-gradient(135deg, #20c997, #28a745); /* Slightly different gradient on hover */
        }
        
        /* Main Content Upgrade */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        .header-content {
            /* Hapus/Ubah bagian ini */
            background: var(--glass-bg); /* Hapus */
            backdrop-filter: blur(20px); /* Hapus */
            border-radius: 20px; /* Opsional: Jaga jika ingin border radius tanpa background */
            border: 1px solid var(--glass-border); /* Hapus */
            box-shadow: var(--shadow-heavy); /* Hapus/Ubah */
            /* ... properti lainnya ... */
        }
        
        .header-content h2 {
            color: #fff;
            font-weight: 600;
            margin: 0;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .header-content p {
            color: #fff; /* Warna teks putih untuk kontras yang lebih baik */
            margin-top: 0.5rem;
            font-size: 1rem;
            opacity: 0.9; /* Sedikit transparansi untuk efek visual */
            font-weight: 300; /* Font weight light untuk kesan elegan */
            line-height: 1.5; /* Line height yang lebih baik untuk keterbacaan */
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7); /* Tambahkan bayangan teks agar tetap terbaca di background terang */
        }
        
        .card {
            color: #6c757d; /* Darker color for white background */
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .card {
            color: #6c757d; /* Darker color for white background */
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .card {
            color: #6c757d; /* Warna teks yang kontras dengan latar belakang putih */
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 2.5rem;
            box-shadow: var(--shadow-heavy);
            transition: all 0.3s ease;
            min-height: 500px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        /* Animasi Loading/Entrance */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card, .header-content {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Responsive: Mobile-First Upgrade */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                top: 70px;
                height: calc(100vh - 70px);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .navbar-custom .navbar-nav {
                text-align: center;
            }
            
            .user-greeting {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .toggle-btn {
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 1001;
            }
            
            .sidebar {
                width: 100%;
                left: 0;
            }
        }
        
        /* Scroll Effect untuk Navbar */
        .navbar-scrolled {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 999;
        }
    </style>
</head>
<body>

    <!-- Navbar Upgrade: Mirip halaman sebelum login, tapi untuk user logged in -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="navbar">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Warung Padi Logo">
            </a>
            
            <!-- Nav Links untuk User yang Sudah Login -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ Auth::check() ? route('dashboard') : route('welcome') }}"><i class="bi bi-house me-1"></i>HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('market.index') }}"><i class="bi bi-shop me-1"></i>MARKET</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('about') }}"><i class="bi bi-info-circle me-1"></i>ABOUT US</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact-us') }}"><i class="bi bi-envelope me-1"></i>CONTACT US</a></li>
                </ul>
            </div>
            
            <!-- User Greeting di Kanan (Hello, Nama User) -->
                       <!-- User Greeting di Kanan (Hello, Nama User) -->
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ substr(Auth::user()->name ?? 'User  ', 0, 1) }} <!-- Inisial nama -->
                </div>
                <span>Hello, {{ Auth::user()->name ?? 'User  ' }}</span>
                <!-- Dropdown User dengan background putih dan efek blur -->
                <div class="dropdown position-relative">
                    <button class="btn btn-transparent p-0 ms-2 dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-0" style="background: white; border-radius: 10px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);">
                        <div class="dropdown-header p-3 border-bottom">
                            <h6 class="mb-0 text-dark">{{ Auth::user()->name ?? 'User ' }}</h6>
                            <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" aria-label="Close" onclick="document.getElementById('userDropdown').click()"></button>
                        </div>
                        <li><a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ url('/logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center py-2 px-3 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>

                <!-- Overlay untuk efek blur ketika dropdown terbuka -->
                <div class="dropdown-overlay" id="dropdownOverlay"></div>
            </div>
        </div>
    </nav>

    <div class="container-fluid-custom">
        <!-- Sidebar (Sama seperti sebelumnya, tapi di-upgrade dengan tombol collapse) -->
        <div class="sidebar" id="sidebar">
            <div class="logo">
                @auth
                    @if(Auth::user()->peran == 'admin')
                        <img src="{{ asset('images/logo default.png') }}" alt="Logo Admin">
                        <span>ADMIN</span>
                     @elseif(Auth::user()->peran == 'petani')
                        <img src="{{ asset('images/logo petani.png') }}" alt="Logo Petani">
                        <span>PETANI</span>
                    @elseif(Auth::user()->peran == 'pengepul')
                        <img src="{{ asset('images/logo pengepul.png') }}" alt="Logo Pengepul">
                        <span>PENGEPUL</span>
                    @elseif(Auth::user()->peran == 'distributor')
                        <img src="{{ asset('images/logo distributor.png') }}" alt="Logo Distributor">
                        <span>DISTRIBUTOR</span>
                    @else
                        <img src="{{ asset('images/logo default.png') }}" alt="Logo Warung Padi">
                        <span>WARUNG PADI</span>
                    @endif
                @else
                    <img src="{{ asset('images/logo default.png') }}" alt="Logo Warung Padi">
                    <span>WARUNG PADI</span>
                @endauth
                <!-- Tombol Collapse Sidebar -->
                <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Tutup Sidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <h4>Dashboard</h4>
            <ul>
                @if(Auth::check() && Auth::user()->peran == 'admin')
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard Admin</a></li>
                    <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="{{ route('admin.transactions.index') }}" class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
                @endif
            </ul>

            <h4>List Menu</h4>
            <ul>
                <li><a href="{{ route('saldo') }}" class="sidebar-button"><i class="fas fa-coins"></i> Saldo</a></li>
                <li><a href="{{ route('transaksi.index') }}" class="sidebar-button"><i class="fas fa-exchange-alt"></i> Aktivitas Transaksi</a></li>
                <li><a href="{{ route('negosiasi') }}" class="sidebar-button"><i class="fas fa-comments"></i> Status Negosiasi</a></li>
                <li><a href="{{ route('inventory.index') }}" class="sidebar-button"><i class="fas fa-boxes"></i> Inventaris</a></li>
            </ul>

            <h4>Setting</h4>
            <ul>
                <li><a href="{{ route('settings.index') }}" class="sidebar-button"><i class="fas fa-user-cog"></i> Pengaturan Akun</a></li>
                <li><a href="{{ route('ewallet') }}" class="sidebar-button"><i class="fas fa-wallet"></i> Pengaturan E-Wallet</a></li>
                <!-- Tambahkan link berikut di sidebar menu -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('topup.index') }}">
                        <i class="fas fa-wallet"></i>
                        <span>Top-up Saldo</span>
                    </a>
                </li>
            </ul>

            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div> <!-- Close sidebar -->

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <div class="header-content">
            </div>
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
            const body = document.body;

            // Fungsi untuk toggle sidebar
            function toggleSidebar() {
                sidebar.classList.toggle('hidden');
                mainContent.classList.toggle('expanded');
                body.classList.toggle('sidebar-collapsed');

                // Update icon pada tombol toggle di header
                const icon = sidebarToggle.querySelector('i');
                if (sidebar.classList.contains('hidden')) {
                    icon.className = 'bi bi-list';
                } else {
                    icon.className = 'bi bi-x-lg';
                }

                // Update icon pada tombol collapse di sidebar
                if (sidebarCollapseBtn) {
                    const collapseIcon = sidebarCollapseBtn.querySelector('i');
                    if (sidebar.classList.contains('hidden')) {
                        collapseIcon.className = 'bi bi-x-lg'; // Icon close saat terbuka
                    } else {
                        collapseIcon.className = 'bi bi-arrow-right'; // Icon open saat tertutup, tapi karena tombol di sidebar, sesuaikan
                    }
                }
            }

            // Event listener untuk tombol toggle di header
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            // Event listener untuk tombol collapse di sidebar
            if (sidebarCollapseBtn) {
                sidebarCollapseBtn.addEventListener('click', toggleSidebar);
            }

            // Responsive: Sembunyikan sidebar di mobile secara default
            function handleResize() {
                if (window.innerWidth <= 992) {
                    sidebar.classList.add('hidden');
                    mainContent.classList.add('expanded');
                    body.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('hidden');
                    mainContent.classList.remove('expanded');
                    body.classList.remove('sidebar-collapsed');
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize(); // Panggil sekali saat load

            // Navbar scroll effect (opsional, jika diperlukan)
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
</body>
</html>
