<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sekretaris - Sistem KBM</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div>
                <div class="sidebar-logo">
                    <div class="logo-icon">S</div>
                    <span class="logo-text">Sekretaris</span>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('sekretaris.dashboard') }}" class="nav-item active">Dashboard</a>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="navbar">
                <div>
                    <h1>Dashboard Kelas</h1>
                    <span class="user-email" style="font-weight: 600; color: var(--primary-clay);">
                        {{ $kelas->nama_kelas ?? 'Belum ada kelas' }}
                    </span>
                </div>
            </header>

            <div class="content-body">
                <div class="stats-grid">
                    <div class="card">
                        <div class="card-title">Nama Kelas</div>
                        <div class="card-value highlight">{{ $kelas->nama_kelas ?? '-' }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Jurusan</div>
                        <div class="card-value" style="font-size: 1.4rem;">{{ $kelas->jurusan->nama_jurusan ?? '-' }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Tingkat</div>
                        <div class="card-value">{{ $kelas->tingkat ?? '-' }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Kelompok</div>
                        <div class="card-value">{{ $kelas->kelompok ?? '-' }}</div>
                    </div>
                </div>

                @unless ($kelas)
                    <div class="alert alert-danger" style="display:block;">
                        Akun Anda belum terhubung dengan kelas manapun. Hubungi Admin.
                    </div>
                @endunless
            </div>
        </main>
    </div>

</body>
</html>
