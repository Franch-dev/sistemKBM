<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-url" content="{{ url('/') }}">
    <meta name="api-url" content="{{ url('/api') }}">
    <title>Login - Sistem KBM</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/extra.css">
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Selamat Datang</h1>
                <p>Silakan masuk ke akun Anda</p>
            </div>

            <div class="alert alert-danger" id="loginError" style="display:none;"></div>

            <form id="loginForm" novalidate>
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" class="form-control" placeholder="nama@email.com" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                        <button type="button" id="togglePasswordBtn" class="toggle-password" title="Lihat Kata Sandi">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="btnLogin">Masuk</button>
            </form>
        </div>
    </div>

    <script src="/js/app.js"></script>
    <script>
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');
        const errorBox = document.getElementById('loginError');
        const btnLogin = document.getElementById('btnLogin');

        document.getElementById('togglePasswordBtn').addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeOpen.style.display = isPassword ? 'none' : 'block';
            eyeClosed.style.display = isPassword ? 'block' : 'none';
        });

        // Sudah punya token yang masih valid? langsung ke dashboard-nya.
        if (KBM.getToken()) {
            KBM.api('/me')
                .then(({ user }) => KBM.go(user.dashboard))
                .catch(() => KBM.clearSession());
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            errorBox.style.display = 'none';
            btnLogin.disabled = true;
            btnLogin.textContent = 'Memproses...';

            try {
                await KBM.login(document.getElementById('email').value.trim(), passwordInput.value);
            } catch (err) {
                const first = Object.values(err.errors || {})[0];
                errorBox.textContent = (first && first[0]) || err.message;
                errorBox.style.display = 'block';
                btnLogin.disabled = false;
                btnLogin.textContent = 'Masuk';
            }
        });
    </script>
</body>
</html>
