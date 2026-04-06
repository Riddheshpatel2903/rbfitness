<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RB Admin - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/fevicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/fevicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/fevicon.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --gym-yellow: #ffdf00;
            --gym-dark: #0a0a0a;
            --gym-darker: #050505;
            --gym-card: rgba(255, 255, 255, 0.03);
            --sidebar-width: 280px;
            --accent-glow: 0 0 15px rgba(255, 223, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at top right, #1a1a1a, #050505);
            background-attachment: fixed;
            color: #fff; 
            display: flex; 
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #000;
            position: fixed;
            left: 0;
            top: 0;
            padding: 2.5rem 1.5rem;
            border-right: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            z-index: 1001;
        }

        .sidebar-logo {
            margin-bottom: 4rem;
            text-align: center;
        }
        .sidebar-logo img { height: 45px; filter: drop-shadow(0 0 10px rgba(255,223,0,0.2)); }

        .nav-menu { list-style: none; flex-grow: 1; }
        .nav-item { margin-bottom: 0.75rem; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1rem 1.25rem;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            border-radius: 1rem;
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        .nav-link.active {
            background: linear-gradient(135deg, rgba(255, 223, 0, 0.15), rgba(255, 223, 0, 0.05));
            color: var(--gym-yellow);
            box-shadow: inset 0 0 0 1px rgba(255, 223, 0, 0.2);
            text-shadow: 0 0 10px rgba(255, 223, 0, 0.3);
        }

        .logout-form { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 2rem; }
        .btn-logout {
            width: 100%;
            background: rgba(255, 77, 77, 0.05);
            color: #ff4d4d;
            border: 1px solid rgba(255, 77, 77, 0.1);
            padding: 1rem;
            border-radius: 1rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-logout:hover { background: #ff4d4d; color: #fff; box-shadow: 0 0 20px rgba(255, 77, 77, 0.3); }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 3rem;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4rem;
        }

        .page-title {
            font-family: 'Oswald', sans-serif;
            font-size: 2.25rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 700;
        }
        .page-title span { color: var(--gym-yellow); text-shadow: 0 0 20px rgba(255,223,0,0.3); }

        /* Card System (Glassmorphism) */
        .card {
            background: var(--gym-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.75rem;
            padding: 2.5rem;
            border: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .card:hover { border-color: rgba(255,255,255,0.15); }

        .grid-dashboard {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2.5rem;
        }

        .grid-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 4rem;
        }

        /* Stat Card Refined System */
        .stat-card-inner { display: flex; justify-content: space-between; align-items: flex-start; }
        .stat-label { opacity: 0.5; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; font-weight: 600; }
        .stat-value { font-size: 2.5rem; font-family: 'Oswald', sans-serif; font-weight: 700; line-height: 1.2; }
        .stat-icon-wrapper { background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 1.25rem; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; }
        .stat-indicator { position: absolute; bottom: 0; left: 0; right: 0; height: 2px; }

        @media (max-width: 1400px) {
            .grid-stats { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 1200px) {
            .grid-dashboard { grid-template-columns: 1fr; gap: 2rem; }
        }

        @media (max-width: 768px) {
            .grid-stats { grid-template-columns: 1fr; }
        }

        /* Buttons & Utility */
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--gym-yellow), #e6c200); 
            color: #000; 
            box-shadow: 0 4px 15px rgba(255, 223, 0, 0.3);
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(255, 223, 0, 0.4); }

        .btn-ghost { background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); }

        .btn-sms {
            background: linear-gradient(135deg, #075E54, #128C7E);
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(7, 94, 84, 0.3);
        }
        .btn-sms:hover { 
            transform: translateY(-3px) scale(1.02); 
            box-shadow: 0 8px 25px rgba(18, 140, 126, 0.5); 
            background: linear-gradient(135deg, #128C7E, #25D366);
            border-color: rgba(255,255,255,0.2);
        }
        .btn-sms i { font-size: 0.9rem; }

        /* Menu Toggle — Hamburger */
        .btn-menu {
            display: none;
            background: linear-gradient(135deg, rgba(255,223,0,0.15), rgba(255,223,0,0.05));
            border: 1px solid rgba(255,223,0,0.3);
            color: var(--gym-yellow);
            width: 40px;
            height: 40px;
            padding: 0;
            border-radius: 0.65rem;
            cursor: pointer;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 12px rgba(255,223,0,0.15);
            transition: all 0.3s ease;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
        }
        .btn-menu:hover, .btn-menu:active {
            background: rgba(255,223,0,0.25);
            box-shadow: 0 0 20px rgba(255,223,0,0.3);
        }
        /* Hamburger lines */
        .hamburger {
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 20px;
        }
        .hamburger span {
            display: block;
            height: 2px;
            background: var(--gym-yellow);
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .hamburger span:nth-child(2) { width: 14px; }
        .btn-menu.open .hamburger span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .btn-menu.open .hamburger span:nth-child(2) { opacity: 0; width: 0; }
        .btn-menu.open .hamburger span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            z-index: 999;
        }

        table { width: 100%; border-collapse: separate; border-spacing: 0 0.5rem; margin-top: 1.5rem; }
        th { text-align: left; opacity: 0.4; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 1rem 1.25rem; }
        td { padding: 1.5rem 1.25rem; background: rgba(255,255,255,0.02); first-child { border-radius: 1rem 0 0 1rem; } last-child { border-radius: 0 1rem 1rem 0; } font-size: 0.9375rem; }
        tr:hover td { background: rgba(255,255,255,0.04); }

        .status-badge {
            padding: 0.35rem 1rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            border: 1px solid currentColor;
        }
        .bg-active { background: rgba(77, 255, 77, 0.1); color: #4dff4d; box-shadow: 0 0 10px rgba(77, 255, 77, 0.1); }
        .bg-expired { background: rgba(255, 179, 77, 0.1); color: #ffb34d; box-shadow: 0 0 10px rgba(255, 179, 77, 0.1); }
        .bg-blocked { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; box-shadow: 0 0 10px rgba(255, 77, 77, 0.1); }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
            .sidebar.active { transform: translateX(0); }
            .btn-menu { display: inline-flex; align-items: center; justify-content: center; }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0; width: 100%; padding: 2rem; overflow-x: hidden; }
            .header { margin-bottom: 2rem; }
            .page-title { font-size: 1.75rem; }
            .card { padding: 2rem; border-radius: 1.5rem; width: 100%; }
            .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            td, th { white-space: nowrap; padding: 1.25rem 1rem; }
            .hide-mobile { display: none; }
        }

        @media (max-width: 576px) {
            .main-content { padding: 1rem 0; }
            .header { margin-bottom: 1.5rem; flex-direction: row; align-items: center; justify-content: flex-start; gap: 0.75rem; padding: 0 1rem; flex-wrap: nowrap; }
            .page-title { font-size: 1.1rem; letter-spacing: 0.05em; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .card { padding: 1rem 0.875rem; border-radius: 1.25rem; margin: 0 auto 1.25rem !important; width: 94% !important; max-width: 94%; box-sizing: border-box; display: block; }
            .card h2, .card h3 { font-size: 1rem !important; }
            .card label { font-size: 0.75rem !important; }
            .card input, .card select, .card textarea { font-size: 0.85rem !important; padding: 0.6rem 0.75rem !important; }
            .grid-stats { display: flex; flex-direction: column; gap: 0; margin-bottom: 0; }
            .grid-dashboard { display: flex; flex-direction: column; gap: 0; }
            .btn { font-size: 0.8rem; padding: 0.6rem 0.9rem; }
            /* Only action-stack buttons stretch full width */
            .actions-stack .btn { width: 100%; justify-content: center; text-align: center; }
            /* Header action buttons stay compact */
            .header .btn { width: auto !important; padding: 0.55rem 0.85rem; font-size: 0.75rem; }
            .status-badge { padding: 0.25rem 0.75rem; font-size: 0.65rem; }
            /* Compact table for mobile */
            td, th { padding: 0.75rem 0.4rem !important; font-size: 0.72rem !important; white-space: nowrap; }
            .btn-sms { padding: 0.4rem 0.65rem; font-size: 0.7rem; justify-content: center; text-align: center; width: 100%; gap: 0.3rem; }
            .btn-text-mobile { display: none; }
            .actions-stack { display: flex !important; flex-direction: column !important; gap: 0.4rem !important; align-items: stretch !important; width: 100% !important; }
            .actions-stack .btn, .actions-stack .btn-sms { width: 100% !important; justify-content: center !important; text-align: center !important; }
            .filter-container { flex-direction: column !important; align-items: stretch !important; width: 100% !important; gap: 1rem !important; }
            .filter-container input { width: 100% !important; max-width: 100% !important; box-sizing: border-box !important; }
            .responsive-grid { grid-template-columns: 1fr !important; gap: 1.25rem !important; }
            /* Form cards mobile fix */
            .form-group { margin-bottom: 1rem !important; }
            .form-row { flex-direction: column !important; gap: 0 !important; }
        }

        @media (max-width: 400px) {
            .main-content { padding: 0.5rem 0; }
            .header { padding: 0 0.75rem; margin-bottom: 1rem; gap: 0.5rem; }
            .page-title { font-size: 0.9rem !important; }
            .card { padding: 0.75rem 0.6rem !important; border-radius: 0.85rem; width: 96% !important; max-width: 96%; }
            .card h2, .card h3 { font-size: 0.95rem !important; }
            .stat-value { font-size: 1.25rem !important; }
            .stat-icon-wrapper { padding: 0.5rem; font-size: 0.85rem; }
            td, th { padding: 0.6rem 0.35rem !important; font-size: 0.68rem !important; }
            .btn { font-size: 0.75rem; padding: 0.6rem 0.75rem; }
            .btn-menu { padding: 0.55rem 0.7rem; font-size: 1rem; }
        }

        .stack-column { display: flex; flex-direction: column; gap: 2rem; }
        .card-accent-orange { border-top: 4px solid #ffb34d !important; }
        .card-accent-red { border-top: 4px solid #ff4d4d !important; }
        .actions-stack { display: flex; gap: 0.5rem; align-items: center; }
        .filter-container { display: flex; gap: 1rem; align-items: center; justify-content: space-between; flex-wrap: wrap; }
        .responsive-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        html, body { overflow-x: hidden; width: 100%; position: relative; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/logo.png') }}" alt="RB Fitness">
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">Members</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.plan_categories.index') }}" class="nav-link {{ request()->routeIs('admin.plan_categories.*') ? 'active' : '' }}">Plan Categories</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">Membership Plans</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">Payments</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.trainers.index') }}" class="nav-link {{ request()->routeIs('admin.trainers.*') ? 'active' : '' }}">Trainers</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.facilities.index') }}" class="nav-link {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">Facilities</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Settings</a>
            </li>
        </ul>

        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <div class="header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button type="button" class="btn-menu" id="toggleSidebar" aria-label="Toggle menu">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <h1 class="page-title">@yield('title_prefix', 'RB') <span>@yield('title_suffix', 'Admin')</span></h1>
            </div>
            @yield('header_actions')
        </div>

        @if(session('success'))
            <div class="card" style="background: rgba(77, 255, 77, 0.1); border-color: rgba(77, 255, 77, 0.2); color: #4dff4d; margin-bottom: 2rem;">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('toggleSidebar');

        if (toggle) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                toggle.classList.toggle('open');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                toggle.classList.remove('open');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
