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
    
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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
            /* Global Background Image from Assets */
            background: url("{{ asset('images/Background.png') }}") no-repeat center center fixed;
            background-size: cover;
            position: relative;
            color: #333;
            overflow-x: hidden;
            min-height: 100vh;
            padding-top: 70px;
        }

        /* Overlay - Reduced opacity to let the 'ladang' colors shine */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.15); /* Very light overlay, almost transparent */
            z-index: -1;
            /* backdrop-filter: blur(2px);  Reduced blur to keep background details visible */
        }
        
        /* Typography Enhancements */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        
        /* Standardized Design Tokens based on 'Saldo' Page */
        :root {
            /* Rice Ecosystem Color Palette */
            --rice-gold: #FF9800; /* Matching Saldo Orange */
            --rice-green: #4CAF50; /* Matching Saldo Green */
            --rice-green-light: #81C784;
            --earth-brown: #8D6E63;
            --pure-white: #FFFFFF;
            --deep-green: #2E7D32;
            
            /* Gradients */
            --gradient-green: linear-gradient(135deg, #4CAF50, #81C784);
            --gradient-orange: linear-gradient(135deg, #FF9800, #FFB74D);
            
            /* Spacing System (8px grid) */
            --space-xs: 0.5rem;  /* 8px */
            --space-sm: 1rem;    /* 16px */
            --space-md: 1.5rem;  /* 24px */
            --space-lg: 2rem;    /* 32px */
            --space-xl: 3rem;    /* 48px */
            --space-2xl: 4rem;   /* 64px */
            
            /* Typography */
            --font-heading: 'Poppins', sans-serif;
            --font-body: 'Poppins', sans-serif;
            
            /* Shadows */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.15);
            --shadow-xl: 0 12px 48px rgba(0, 0, 0, 0.2);
            
            /* Glassmorphism */
            --glass-bg: rgba(255, 255, 255, 0.85); /* More opaque for contrast against img */
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-blur: blur(12px);
            --glass-strong: rgba(255, 255, 255, 0.95);
        }
        
        h1 { font-size: 2.5rem; line-height: 1.2; }
        h2 { font-size: 2rem; line-height: 1.3; }
        h3 { font-size: 1.75rem; line-height: 1.4; }
        h4 { font-size: 1.5rem; line-height: 1.4; }
        h5 { font-size: 1.25rem; line-height: 1.5; }
        h6 { font-size: 1rem; line-height: 1.5; }
        
        /* Navbar Upgrade: Enhanced Glassmorphism & Theme Integrated */
        .navbar-custom {
            /* Glassmorphism Green/Orange */
            background: linear-gradient(135deg, rgba(139, 195, 74, 0.15), rgba(244, 196, 48, 0.15));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: none; /* Removed border as requested */
            padding: var(--space-sm) 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
        }
        
        .navbar-custom.scrolled {
            background: rgba(255, 255, 255, 0.98);
            padding: var(--space-xs) 0;
            box-shadow: var(--shadow-md);
        }
        
        .navbar-brand img {
            height: 50px;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05);
        }
        
        .nav-link {
            color: #1B5E20 !important; /* Extremely dark green for contrast */
            font-weight: 700; /* Bolder */
            margin: 0 var(--space-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            font-family: var(--font-body);
            text-shadow: 0 1px 0 rgba(255,255,255,0.5); /* Subtle rim for legibility */
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: var(--rice-gold); /* Orange hint */
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::before {
            width: 80%;
        }
        
        .nav-link:hover {
            color: #E65100 !important; /* Deep Orange on hover */
            transform: translateY(-1px);
        }
        
        /* User Greeting Section */
        .user-greeting {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            color: var(--deep-green);
            font-weight: 600;
            background: rgba(255, 255, 255, 0.6);
            padding: var(--space-xs) var(--space-sm);
            border-radius: 50px;
            backdrop-filter: blur(5px);
            border: 1px solid var(--glass-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .user-greeting:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-green);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pure-white);
            font-weight: 700;
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
        
        /* Sidebar Upgrade: Glass Effect matching theme */
        /* Sidebar Upgrade: Glass Effect matching theme */
        .sidebar {
            width: 280px;
            /* Glassmorphism Green/Orange */
            background: linear-gradient(135deg, rgba(139, 195, 74, 0.15), rgba(244, 196, 48, 0.15));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(244, 196, 48, 0.3);
            padding: 2rem 0;
            position: fixed;
            left: 0;
            top: 60px; /* Adjust untuk navbar height */
            height: calc(100vh - 60px);
            overflow-y: auto;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        
        .logo {
            display: flex;
            flex-direction: column; /* Stack vertical */
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05); /* Subtle border */
            margin-bottom: 2rem;
            background: transparent;
            position: relative;
        }
        
        .logo img {
            width: 60px; /* Slightly larger for proportion */
            height: 60px;
            margin-right: 0;
            margin-bottom: 12px; /* Space between logo and text */
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }
        
        .logo span {
            font-size: 1.4rem;
            font-weight: 700;
            background: var(--gradient-green);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Tombol Collapse di Sidebar */
        .sidebar-collapse-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid var(--glass-border);
            color: var(--deep-green);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            cursor: pointer;
            font-size: 0.8rem;
        }
        
        .sidebar-collapse-btn:hover {
            background: var(--rice-green);
            color: white;
            transform: scale(1.1);
        }
        
        .sidebar h4 {
            padding: 0 1.5rem;
            margin: 1.5rem 0 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--rice-gold); /* Orange section headers */
            text-transform: uppercase;
            letter-spacing: 1.2px;
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
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: #1B5E20; /* Dark Green Text */
            transition: all 0.3s ease;
            font-weight: 600;
            margin: 4px 12px;
            border-radius: 12px; /* Rounded pill shape */
            border-left: none; /* Removing old left border style */
        }
        
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.6);
            color: #E65100; /* Deep Orange */
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .sidebar a.active {
            background: linear-gradient(135deg, #4CAF50, #2E7D32); /* Green Gradient Active */
            color: #fff !important; /* White Text */
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
        }
        
        .sidebar a i {
            width: 24px;
            margin-right: 16px;
            text-align: center;
            font-size: 1.2rem;
            color: #FF9800; /* Orange Icons by default */
            transition: color 0.3s ease;
        }
        
        .sidebar a:hover i {
            color: #E65100;
        }

        .sidebar a.active i {
            color: #fff; /* White Icons on Active */
        }

        .logout-container {
            padding: 1.5rem;
            margin-top: auto;
        }
        
        .logout {
            width: 100%;
            padding: 0.8rem;
            background-color: #fff;
            border: 1px solid #dc3545;
            color: #dc3545;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .logout:hover {
            background-color: #dc3545;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }
        
        /* Main Content Upgrade */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            min-height: 100vh;
            background: transparent; /* Transparent to show 'Background.png' */
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
        
        /* Global Card Style - 'Saldo' Theme */
        .card {
            background: linear-gradient(135deg, #4CAF50, #81C784); /* Green Gradient Body */
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            color: white; /* Default text white */
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 24px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #FF9800, #FFB74D); /* Orange Gradient Header */
            color: white;
            border-bottom: none;
            padding: 1.5rem;
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Adjusting text muted to be readable on green */
        .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .text-dark {
            color: white !important;
        }

        /* Inner containers like lists and tables need to handle the transparency */
        .list-group-item {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            margin-bottom: 8px;
            border-radius: 10px !important;
            color: white;
            backdrop-filter: blur(5px);
        }
        
        /* Table styles adjusted for green background */
        .table {
            color: white;
        }
        .table thead th {
            color: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
        }
        .table td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Badges need to pop */
        .badge {
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
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

        /* --- Global Form Theme Overrides (Green/Orange) --- */
        /* --- Global Form Theme Overrides (Green/Orange) --- */
        .form-control,
        .form-select {
            color: #000 !important; /* Force Black Text */
        }

        .form-control:focus, 
        .form-select:focus {
            border-color: #4CAF50 !important; /* Green Border */
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important; /* Green Glow */
            color: #000 !important; /* Ensure text remains black on focus */
        }
        
        .form-check-input:checked {
            background-color: #4CAF50 !important;
            border-color: #4CAF50 !important;
        }

        .form-select {
             background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%232E7D32' stroke='%232E7D32' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }
        
        /* Dropdown Optgroup Styling - Make categories visible */
        optgroup {
            color: #2E7D32; /* Dark Green */
            font-weight: 800;
            background-color: #fff;
        }

        option {
            color: #333; /* Dark text for items */
            font-weight: normal;
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
                        {{-- Logo removed for admin --}}
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
                @if(Auth::check() && Auth::user()->peran == 'petani')
                    <li><a href="{{ route('market.create') }}" class="{{ request()->routeIs('market.create') ? 'active' : '' }}"><i class="fas fa-plus-circle"></i> Jual Beras</a></li>
                @elseif(Auth::check() && Auth::user()->peran == 'pengepul')
                    <li><a href="{{ route('market.index') }}" class="{{ request()->routeIs('market.index') ? 'active' : '' }}"><i class="fas fa-shopping-basket"></i> Pasar Beras</a></li>
                @endif
                @if(Auth::user()->peran !== 'admin')
                    <li><a href="{{ route('saldo') }}" class="{{ request()->routeIs('saldo') ? 'active' : '' }}"><i class="fas fa-coins"></i> Saldo</a></li>
                    <li><a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.*') ? 'active' : '' }}"><i class="fas fa-exchange-alt"></i> Aktivitas Transaksi</a></li>
                    <li><a href="{{ route('negosiasi.index') }}" class="{{ request()->routeIs('negosiasi.*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Status Negosiasi</a></li>
                    <li><a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}"><i class="fas fa-boxes"></i> Inventaris</a></li>
                @endif
            </ul>

            <h4>Setting</h4>
            <ul>
                <li><a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}"><i class="fas fa-user-cog"></i> Pengaturan Akun</a></li>
                @if(Auth::user()->peran !== 'admin')
                    <li><a href="{{ route('ewallet') }}" class="{{ request()->routeIs('ewallet') ? 'active' : '' }}"><i class="fas fa-wallet"></i> Pengaturan E-Wallet</a></li>
                    <li><a href="{{ route('topup.index') }}" class="{{ request()->routeIs('topup.*') ? 'active' : '' }}"><i class="fas fa-wallet"></i> Top-up Saldo</a></li>
                @else
                    <li><a href="{{ route('topup.index') }}" class="{{ request()->routeIs('topup.*') ? 'active' : '' }}"><i class="fas fa-check-double"></i> Verifikasi Top Up</a></li>
                @endif
            </ul>

            <div class="logout-container">
                <form action="{{ url('/logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                </form>
            </div>
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
