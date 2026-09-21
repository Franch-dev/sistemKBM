<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-url" content="{{ url('/') }}">
    <meta name="api-url" content="{{ url('/api') }}">
    <title>@yield('title') - Sistem KBM</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/extra.css">
</head>
<body class="booting">

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div>
                <div class="sidebar-logo">
                    <div class="logo-icon">@yield('logo', 'K')</div>
                    <span class="logo-text">@yield('panel')</span>
                </div>
                <nav class="sidebar-nav">
                    @yield('nav')
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-name" id="userName">-</div>
                    <div class="user-email" id="userEmail">-</div>
                </div>
                <button type="button" id="btnLogout" class="btn-logout">Logout</button>
            </div>
        </aside>

        <main class="main-content">
            <header class="navbar">
                <div>
                    <h1>@yield('heading')</h1>
                    <span class="user-email" id="userRole" style="font-weight: 600; color: var(--primary-clay);"></span>
                </div>
                <div class="navbar-right">
                    <span class="status-badge">Sistem Aktif</span>
                </div>
            </header>

            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="/js/app.js"></script>
    @stack('scripts')
</body>
</html>
