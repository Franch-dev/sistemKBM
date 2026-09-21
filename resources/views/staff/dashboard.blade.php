@extends('layouts.app')

@section('title', 'Dashboard Staff')
@section('logo', 'S')
@section('panel', 'StaffPanel')
@section('heading', 'Dashboard Staff')

@section('nav')
    <a href="{{ route('staff.dashboard') }}" class="nav-item active">Dashboard</a>
@endsection

@section('content')
    <div class="stats-grid" id="stats"></div>

    <div class="table-container">
        <div class="table-header"><h3>Daftar Jurusan</h3></div>
        <table class="data-table">
            <thead><tr><th>Kode</th><th>Nama Jurusan</th><th>Jumlah Kelas</th></tr></thead>
            <tbody id="jurusanBody"><tr><td colspan="3" class="empty-note">Memuat...</td></tr></tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    KBM.boot('staff').then(async (user) => {
        if (!user) return;
        const d = await KBM.api('/dashboard');

        document.getElementById('stats').innerHTML =
            KBM.card('Role Anda', user.role_label, true) +
            KBM.card('Total Jurusan', d.stats.total_jurusan) +
            KBM.card('Total Kelas', d.stats.total_kelas);

        document.getElementById('jurusanBody').innerHTML = d.jurusans.length
            ? d.jurusans.map((j) =>
                '<tr><td><span class="role-badge">' + KBM.esc(j.kode) + '</span></td><td>' + KBM.esc(j.nama) + '</td><td>' + KBM.esc(j.jumlah_kelas) + '</td></tr>').join('')
            : '<tr><td colspan="3" class="empty-note">Belum ada data jurusan.</td></tr>';
    });
</script>
@endpush
