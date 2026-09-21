@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('logo', 'A')
@section('panel', 'AdminPanel')
@section('heading', 'Dashboard Admin')

@section('nav')
    <a href="{{ route('admin.dashboard') }}" class="nav-item active">Dashboard</a>
    <a href="{{ route('admin.users') }}" class="nav-item">Kelola Akun</a>
@endsection

@section('content')
    <div class="stats-grid" id="stats"></div>

    <div class="table-container">
        <div class="table-header"><h3>Jumlah Akun per Role</h3></div>
        <table class="data-table">
            <thead><tr><th>Role</th><th>Jumlah Akun</th></tr></thead>
            <tbody id="rolesBody"><tr><td colspan="2" class="empty-note">Memuat...</td></tr></tbody>
        </table>
    </div>

    <div class="table-container">
        <div class="table-header"><h3>Akun Terbaru</h3></div>
        <table class="data-table">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Kelas</th></tr></thead>
            <tbody id="recentBody"><tr><td colspan="4" class="empty-note">Memuat...</td></tr></tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    KBM.boot('admin').then(async (user) => {
        if (!user) return;
        const d = await KBM.api('/dashboard');

        document.getElementById('stats').innerHTML =
            KBM.card('Total Akun', d.stats.total_users, true) +
            KBM.card('Total Jurusan', d.stats.total_jurusan) +
            KBM.card('Total Kelas', d.stats.total_kelas);

        document.getElementById('rolesBody').innerHTML = d.users_per_role.map((r) =>
            '<tr><td><span class="role-badge">' + KBM.esc(r.label) + '</span></td><td>' + KBM.esc(r.total) + '</td></tr>'
        ).join('');

        document.getElementById('recentBody').innerHTML = d.recent_users.length
            ? d.recent_users.map((u) =>
                '<tr><td><strong>' + KBM.esc(u.name) + '</strong></td><td>' + KBM.esc(u.email) + '</td>' +
                '<td><span class="role-badge">' + KBM.esc(u.role_label) + '</span></td>' +
                '<td>' + KBM.esc(u.kelas ? u.kelas.nama_kelas : '-') + '</td></tr>').join('')
            : '<tr><td colspan="4" class="empty-note">Belum ada akun.</td></tr>';
    });
</script>
@endpush
