<?php
/**
 * Controller: Auth
 * Platform PPEPP Fakultas — Per-Dosen System
 */

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Tampilkan halaman login
     */
    public function login(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
            return;
        }

        $flash = $this->getFlash();
        $this->render('auth/login', ['flash' => $flash], 'auth');
    }

    /**
     * Proses form login (username/email + password)
     */
    public function doLogin(): void
    {
        $usernameOrEmail = trim($this->post('username', ''));
        $password        = $this->post('password', '');

        if (empty($usernameOrEmail) || empty($password)) {
            $this->flash('error', 'Username/Email dan password wajib diisi.');
            $this->redirect('/auth/login');
            return;
        }

        $user = $this->userModel->authenticate($usernameOrEmail, $password);

        if ($user) {
            $this->createSession($user);
            $namaDisplay = $user['nama_lengkap'] ?: $user['nama_prodi'] ?: $user['username'];
            // Cek apakah sudah set API key
            if (empty($user['gemini_api_key'])) {
                $this->flash('warning', 'Selamat datang, ' . $namaDisplay . '! Silakan lengkapi API Key Gemini Anda agar dapat menggunakan fitur AI.');
            } else {
                $this->flash('success', 'Selamat datang, ' . $namaDisplay . '!');
            }
            $this->redirect('/dashboard');
        } else {
            $this->flash('error', 'Username/Email atau password salah. Silakan coba lagi.');
            $this->redirect('/auth/login');
        }
    }

    /**
     * Redirect ke Google OAuth login
     */
    public function googleLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
            return;
        }

        // CSRF state token
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'prompt'        => 'select_account',
            'hd'            => 'unika.ac.id', // Batasi hanya akun @unika.ac.id
        ]);

        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit;
    }

    /**
     * Callback dari Google OAuth
     */
    public function googleCallback(): void
    {
        $code  = $_GET['code']  ?? '';
        $state = $_GET['state'] ?? '';
        $error = $_GET['error'] ?? '';

        // Validasi CSRF state
        if ($error || $state !== ($_SESSION['oauth_state'] ?? '')) {
            unset($_SESSION['oauth_state']);
            $this->flash('error', 'Login Google gagal. Silakan coba lagi.');
            $this->redirect('/auth/login');
            return;
        }
        unset($_SESSION['oauth_state']);

        if (empty($code)) {
            $this->flash('error', 'Kode otorisasi Google tidak valid.');
            $this->redirect('/auth/login');
            return;
        }

        // Tukar code dengan access token
        $tokenData = $this->exchangeGoogleCode($code);
        if (!$tokenData || empty($tokenData['id_token'])) {
            $this->flash('error', 'Gagal mendapatkan token dari Google.');
            $this->redirect('/auth/login');
            return;
        }

        // Decode id_token (JWT) — tanpa verifikasi signature (cukup untuk keperluan internal)
        $googleUser = $this->decodeGoogleIdToken($tokenData['id_token']);
        if (!$googleUser || empty($googleUser['email'])) {
            $this->flash('error', 'Gagal membaca data akun Google.');
            $this->redirect('/auth/login');
            return;
        }

        // Cari atau buat user dari data Google
        $user = $this->userModel->findOrCreateFromGoogle($googleUser);
        if (!$user) {
            $this->flash('error', 'Akun Google Anda (' . htmlspecialchars($googleUser['email']) . ') tidak terdaftar di sistem PPEPP. Hubungi Kaprodi/Dekan untuk pendaftaran.');
            $this->redirect('/auth/login');
            return;
        }

        $this->createSession($user);
        $namaDisplay = $user['nama_lengkap'] ?: $user['username'];
        if (empty($user['gemini_api_key'])) {
            $this->flash('warning', 'Selamat datang, ' . $namaDisplay . '! Silakan lengkapi API Key Gemini Anda.');
        } else {
            $this->flash('success', 'Selamat datang, ' . $namaDisplay . '!');
        }
        $this->redirect('/dashboard');
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        $this->redirect('/auth/login');
    }

    // =========================================================
    // Private helpers
    // =========================================================

    /**
     * Buat session user setelah login berhasil
     */
    private function createSession(array $user): void
    {
        session_regenerate_id(true);

        // Ambil nama prodi dari DB jika ada prodi_id
        $prodiNama = '';
        if (!empty($user['prodi_id'])) {
            $prodiModel = new Prodi();
            $prodi = $prodiModel->find((int)$user['prodi_id']);
            $prodiNama = $prodi['nama'] ?? '';
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = [
            'id'             => $user['id'],
            'username'       => $user['username'],
            'nama_lengkap'   => $user['nama_lengkap'] ?? '',
            'nama_prodi'     => $prodiNama ?: ($user['nama_lengkap'] ?? ''),
            'email'          => $user['email'],
            'role'           => $user['role'] ?? 'dosen',
            'prodi_id'       => $user['prodi_id'] ?? null,
            'prodi_nama'     => $prodiNama,
            'avatar_url'     => $user['avatar_url'] ?? null,
            'gemini_api_key' => $user['gemini_api_key'] ?? '',
            'gemini_model'   => $user['gemini_model'] ?? DEFAULT_GEMINI_MODEL,
        ];
    }


    /**
     * Tukar authorization code dengan access_token via Google API
     */
    private function exchangeGoogleCode(string $code): ?array
    {
        $postData = http_build_query([
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($postData),
                'content' => $postData,
                'timeout' => 10,
            ],
            'ssl' => ['verify_peer' => false],
        ]);

        $response = @file_get_contents('https://oauth2.googleapis.com/token', false, $ctx);
        if ($response === false) return null;

        return json_decode($response, true);
    }

    /**
     * Decode payload dari Google ID Token (JWT) — bagian tengah base64
     */
    private function decodeGoogleIdToken(string $idToken): ?array
    {
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) return null;

        $payload = base64_decode(str_pad(
            strtr($parts[1], '-_', '+/'),
            strlen($parts[1]) % 4 ? strlen($parts[1]) + 4 - strlen($parts[1]) % 4 : strlen($parts[1]),
            '=', STR_PAD_RIGHT
        ));

        if (!$payload) return null;
        return json_decode($payload, true);
    }
}
