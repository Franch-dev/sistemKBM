@extends('layouts.app')

@section('title', 'Dashboard Class Secretary')
@section('logo', 'C')
@section('panel', 'Sekretaris Kelas')
@section('heading', 'Dashboard Kelas')

@section('nav')
    <a href="{{ route('class_secretary.dashboard') }}" class="nav-item active">Dashboard</a>
@endsection

@section('content')
    <div class="stats-grid" id="stats"></div>
    <div class="alert alert-danger" id="noKelas" style="display:none;">
        Akun Anda belum terhubung dengan kelas manapun. Hubungi Admin.
    </div>
@endsection

@push('scripts')
<script>
    KBM.boot('class_secretary').then(async (user) => {
        if (!user) return;
        const d = await KBM.api('/dashboard');
        const k = d.kelas;

        if (!k) { document.getElementById('noKelas').style.display = 'block'; }

        document.getElementById('stats').innerHTML =
            KBM.card('Nama Kelas', k ? k.nama_kelas : '-', true) +
            KBM.card('Jurusan', k ? k.jurusan : '-') +
            KBM.card('Tingkat', k ? k.tingkat : '-') +
            KBM.card('Kelompok', k ? k.kelompok : '-');
    });
</script>
@endpush
