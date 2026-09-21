@extends('layouts.app')

@section('title', 'Kelola Akun')
@section('logo', 'A')
@section('panel', 'AdminPanel')
@section('heading', 'Kelola Akun User')

@section('nav')
    <a href="{{ route('admin.dashboard') }}" class="nav-item">Dashboard</a>
    <a href="{{ route('admin.users') }}" class="nav-item active">Kelola Akun</a>
@endsection

@section('content')
    <div class="alert alert-success" id="flash" style="display:none;"></div>

    <div class="table-container">
        <div class="toolbar">
            <div class="toolbar-filters">
                <input type="search" id="search" class="form-control" placeholder="Cari nama / email...">
                <select id="filterRole" class="form-control"><option value="">Semua role</option></select>
            </div>
            <button type="button" class="btn-primary btn-add" id="btnAdd">+ Tambah Akun</button>
        </div>

        <table class="data-table">
            <thead>
                <tr><th>Nama</th><th>Email</th><th>Role</th><th>Kelas</th><th>Aksi</th></tr>
            </thead>
            <tbody id="usersBody"><tr><td colspan="5" class="empty-note">Memuat...</td></tr></tbody>
        </table>

        <div class="pagination">
            <span id="pageInfo"></span>
            <button type="button" class="btn-sm" id="prevPage">‹ Sebelumnya</button>
            <button type="button" class="btn-sm" id="nextPage">Berikutnya ›</button>
        </div>
    </div>

    <!-- Modal tambah / edit -->
    <div class="modal-backdrop" id="modal">
        <div class="modal">
            <h3 id="modalTitle">Tambah Akun</h3>
            <div class="alert alert-danger" id="formError" style="display:none;"></div>

            <div class="form-group">
                <label for="fName">Nama</label>
                <input type="text" id="fName" class="form-control">
                <div class="field-error" data-err="name"></div>
            </div>
            <div class="form-group">
                <label for="fEmail">Email</label>
                <input type="email" id="fEmail" class="form-control">
                <div class="field-error" data-err="email"></div>
            </div>
            <div class="form-group">
                <label for="fPassword">Password <small id="pwHint"></small></label>
                <input type="password" id="fPassword" class="form-control" autocomplete="new-password">
                <div class="field-error" data-err="password"></div>
            </div>
            <div class="form-group">
                <label for="fRole">Role</label>
                <select id="fRole" class="form-control"></select>
                <div class="field-error" data-err="role_id"></div>
            </div>
            <div class="form-group" id="kelasGroup" style="display:none;">
                <label for="fKelas">Kelas</label>
                <select id="fKelas" class="form-control"></select>
                <div class="field-error" data-err="kelas_id"></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-sm" id="btnCancel">Batal</button>
                <button type="button" class="btn-primary" id="btnSave">Simpan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const $ = (id) => document.getElementById(id);
    const state = { page: 1, q: '', roleId: '', lastPage: 1, editingId: null, roles: [], kelas: [], me: null, rows: {} };
    let searchTimer;

    KBM.boot('admin').then(async (user) => {
        if (!user) return;
        state.me = user;

        const opts = await KBM.api('/users/options');
        state.roles = opts.roles;
        state.kelas = opts.kelas;

        $('filterRole').innerHTML += state.roles.map((r) => '<option value="' + r.id + '">' + KBM.esc(r.label) + '</option>').join('');
        $('fRole').innerHTML = state.roles.map((r) => '<option value="' + r.id + '" data-name="' + KBM.esc(r.role_name) + '">' + KBM.esc(r.label) + '</option>').join('');
        $('fKelas').innerHTML = '<option value="">- Pilih kelas -</option>' +
            state.kelas.map((k) => '<option value="' + k.id + '">' + KBM.esc(k.nama_kelas) + '</option>').join('');

        loadUsers();
    });

    function flash(message) {
        const el = $('flash');
        el.textContent = message;
        el.style.display = 'block';
        setTimeout(() => (el.style.display = 'none'), 3500);
    }

    async function loadUsers() {
        const params = new URLSearchParams({ page: state.page });
        if (state.q) params.set('q', state.q);
        if (state.roleId) params.set('role_id', state.roleId);

        const res = await KBM.api('/users?' + params.toString());
        state.lastPage = res.last_page;
        state.rows = Object.fromEntries(res.data.map((u) => [u.id, u]));

        $('usersBody').innerHTML = res.data.length
            ? res.data.map((u) =>
                '<tr>' +
                '<td><strong>' + KBM.esc(u.name) + '</strong></td>' +
                '<td>' + KBM.esc(u.email) + '</td>' +
                '<td><span class="role-badge">' + KBM.esc(u.role_label) + '</span></td>' +
                '<td>' + KBM.esc(u.kelas ? u.kelas.nama_kelas : '-') + '</td>' +
                '<td>' +
                    '<button class="btn-sm" data-edit="' + u.id + '">Edit</button> ' +
                    (u.id === state.me.id ? '' : '<button class="btn-sm danger" data-del="' + u.id + '" data-name="' + KBM.esc(u.name) + '">Hapus</button>') +
                '</td></tr>').join('')
            : '<tr><td colspan="5" class="empty-note">Tidak ada akun ditemukan.</td></tr>';

        $('pageInfo').textContent = 'Halaman ' + res.current_page + ' dari ' + res.last_page + ' (' + res.total + ' akun)';
        $('prevPage').disabled = res.current_page <= 1;
        $('nextPage').disabled = res.current_page >= res.last_page;
    }

    function toggleKelas() {
        const name = $('fRole').selectedOptions[0] ? $('fRole').selectedOptions[0].dataset.name : '';
        $('kelasGroup').style.display = name === 'class_secretary' ? 'block' : 'none';
    }

    function clearErrors() {
        $('formError').style.display = 'none';
        document.querySelectorAll('[data-err]').forEach((el) => (el.textContent = ''));
    }

    function openModal(user) {
        clearErrors();
        state.editingId = user ? user.id : null;
        $('modalTitle').textContent = user ? 'Edit Akun' : 'Tambah Akun';
        $('pwHint').textContent = user ? '(kosongkan jika tidak diganti)' : '';
        $('fName').value = user ? user.name : '';
        $('fEmail').value = user ? user.email : '';
        $('fPassword').value = '';
        $('fRole').value = user ? user.role_id : (state.roles[0] ? state.roles[0].id : '');
        $('fRole').disabled = !!(user && user.id === state.me.id);
        $('fKelas').value = user && user.kelas_id ? user.kelas_id : '';
        toggleKelas();
        $('modal').classList.add('open');
        $('fName').focus();
    }

    function closeModal() { $('modal').classList.remove('open'); }

    async function save() {
        clearErrors();
        const body = {
            name: $('fName').value.trim(),
            email: $('fEmail').value.trim(),
            role_id: Number($('fRole').value),
            kelas_id: $('fKelas').value ? Number($('fKelas').value) : null,
        };
        if ($('fPassword').value) body.password = $('fPassword').value;

        $('btnSave').disabled = true;
        try {
            const editing = state.editingId !== null;
            const res = await KBM.api(editing ? '/users/' + state.editingId : '/users', {
                method: editing ? 'PUT' : 'POST',
                body,
            });
            closeModal();
            flash(res.message);
            loadUsers();
        } catch (err) {
            let shown = false;
            Object.entries(err.errors || {}).forEach(([field, msgs]) => {
                const el = document.querySelector('[data-err="' + field + '"]');
                if (el) { el.textContent = msgs[0]; shown = true; }
            });
            if (!shown) { $('formError').textContent = err.message; $('formError').style.display = 'block'; }
        } finally {
            $('btnSave').disabled = false;
        }
    }

    async function remove(id, name) {
        if (!confirm('Hapus akun "' + name + '"? Tindakan ini tidak dapat dibatalkan.')) return;
        try {
            const res = await KBM.api('/users/' + id, { method: 'DELETE' });
            flash(res.message);
            loadUsers();
        } catch (err) { alert(err.message); }
    }

    $('btnAdd').addEventListener('click', () => openModal(null));
    $('btnCancel').addEventListener('click', closeModal);
    $('btnSave').addEventListener('click', save);
    $('fRole').addEventListener('change', toggleKelas);
    $('modal').addEventListener('click', (e) => { if (e.target === $('modal')) closeModal(); });

    $('search').addEventListener('input', (e) => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { state.q = e.target.value.trim(); state.page = 1; loadUsers(); }, 300);
    });
    $('filterRole').addEventListener('change', (e) => { state.roleId = e.target.value; state.page = 1; loadUsers(); });
    $('prevPage').addEventListener('click', () => { if (state.page > 1) { state.page--; loadUsers(); } });
    $('nextPage').addEventListener('click', () => { if (state.page < state.lastPage) { state.page++; loadUsers(); } });

    $('usersBody').addEventListener('click', (e) => {
        const edit = e.target.closest('[data-edit]');
        const del = e.target.closest('[data-del]');
        if (edit) openModal(state.rows[edit.dataset.edit]);
        if (del) remove(del.dataset.del, del.dataset.name);
    });
</script>
@endpush
