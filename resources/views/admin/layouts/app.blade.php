<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Asesmen CPL')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
        }
        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
            color: #3498db;
        }
        .sidebar .nav-header {
            color: #7f8c8d;
            padding: 10px 20px 5px;
            font-size: 12px;
            text-transform: uppercase;
        }
        /* Pagination styling - force Bootstrap 5 style */
        .pagination {
            display: flex !important;
            padding: 0;
            margin: 0;
            list-style: none;
            gap: 2px;
        }
        .pagination svg {
            display: none !important; /* Hide Tailwind SVG icons */
        }
        .pagination span, .pagination a {
            position: relative;
            display: block;
            padding: 0.375rem 0.75rem;
            font-size: 14px;
            line-height: 1.5;
            color: #0d6efd;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        .pagination .active span, .pagination .current {
            z-index: 3;
            color: #fff !important;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        .pagination .disabled span, .pagination .disabled a {
            color: #6c757d !important;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="p-3 text-white text-center border-bottom">
                    <h5><i class="fas fa-graduation-cap"></i> Asesmen CPL</h5>
                </div>

                <div class="mt-3">
                    <div class="nav-header">Master Data</div>
                    <a href="{{ route('admin.cpl.index') }}" class="{{ request()->routeIs('admin.cpl.*') ? 'active' : '' }}">
                        <i class="fas fa-book me-2"></i> CPL
                    </a>
                    <a href="{{ route('admin.cpmk.index') }}" class="{{ request()->routeIs('admin.cpmk.*') ? 'active' : '' }}">
                        <i class="fas fa-list-check me-2"></i> CPMK
                    </a>
                    <a href="{{ route('admin.matriks-cpmk.index') }}" class="{{ request()->routeIs('admin.matriks-cpmk.*') ? 'active' : '' }}">
                        <i class="fas fa-table me-2"></i> Matriks CPMK
                    </a>
                    <a href="{{ route('admin.import-master.index') }}" class="{{ request()->routeIs('admin.import-master.*') ? 'active' : '' }}">
                        <i class="fas fa-file-import me-2"></i> Import Data
                    </a>

                    <div class="nav-header mt-3">Transaksi</div>
                    <a href="{{ route('admin.pemetaan-mk.index') }}" class="{{ request()->routeIs('admin.pemetaan-mk.*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt me-2"></i> Pemetaan MK
                    </a>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="{{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate me-2"></i> Mahasiswa
                    </a>
                    <a href="{{ route('admin.nilai-mahasiswa.index') }}" class="{{ request()->routeIs('admin.nilai-mahasiswa.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line me-2"></i> Nilai
                    </a>

                    <div class="nav-header mt-3">Laporan</div>
                    <a href="{{ route('admin.capaian-cpl.index') }}" class="{{ request()->routeIs('admin.capaian-cpl.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar me-2"></i> Capaian CPL
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="col-md-10">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                    <div class="container-fluid">
                        <span class="navbar-text">
                            <strong>@yield('page-title', 'Dashboard')</strong>
                        </span>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <div class="p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
