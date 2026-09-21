<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem KBM</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div>
                <div class="sidebar-logo">
                    <div class="logo-icon">A</div>
                    <span class="logo-text">AdminPanel</span>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item active">Dashboard</a>
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
                <h1>Overview Jurusan & Kelas</h1>
                <div class="navbar-right">
                    <span class="status-badge">Sistem Aktif</span>
                </div>
            </header>

            <div class="content-body">
                <div class="stats-grid">
                    <div class="card">
                        <div class="card-title">Total Jurusan</div>
                        <div class="card-value">{{ $totalJurusan }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Total Kelas</div>
                        <div class="card-value highlight">{{ $totalKelas }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Role Anda</div>
                        <div class="card-value" style="font-size: 1.5rem;">Admin</div>
                    </div>
                </div>

                <!-- Tabel Jurusan -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Daftar Jurusan</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Jurusan</th>
                                <th>Jumlah Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jurusans as $j)
                                <tr>
                                    <td><span class="role-badge">{{ $j->kode_jurusan }}</span></td>
                                    <td>{{ $j->nama_jurusan }}</td>
                                    <td>{{ $j->kelas_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;">Belum ada data jurusan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tabel Kelas -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Daftar Kelas</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Kelompok</th>
                                <th>Jurusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelas as $k)
                                <tr>
                                    <td><strong>{{ $k->nama_kelas }}</strong></td>
                                    <td>{{ $k->tingkat }}</td>
                                    <td>{{ $k->kelompok }}</td>
                                    <td>{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center;">Belum ada data kelas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
