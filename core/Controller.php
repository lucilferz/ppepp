<?php
/**
 * Base Controller
 * Platform PPEPP Fakultas
 */

class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Render view dengan layout
     */
    protected function render(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        $this->view->render($viewPath, $data, $layout);
    }

    /**
     * Redirect ke URL lain
     */
    protected function redirect(string $url): void
    {
        if (!str_starts_with($url, 'http')) {
            if (BASE_URL !== '' && !str_starts_with($url, BASE_URL)) {
                $url = BASE_URL . '/' . ltrim($url, '/');
            }
        }
        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Cek apakah user sudah login
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
    }

    /**
     * Cek apakah request method adalah POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Ambil data POST dengan sanitasi
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        if (is_array($_POST[$key])) {
            return $_POST[$key];
        }
        return trim((string)$_POST[$key]);
    }

    /**
     * Ambil data GET dengan sanitasi
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        if (!isset($_GET[$key])) {
            return $default;
        }
        if (is_array($_GET[$key])) {
            return $_GET[$key];
        }
        return trim((string)$_GET[$key]);
    }

    /**
     * Set flash message
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Ambil dan hapus flash message
     */
    protected function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Data user yang sedang login
     */
    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Ambil role user yang sedang login
     */
    protected function currentRole(): string
    {
        return $_SESSION['user']['role'] ?? 'dosen';
    }

    /**
     * Wajib memiliki salah satu role yang ditentukan
     * Redirect ke dashboard jika tidak memenuhi
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        $userRole = $this->currentRole();
        if (!in_array($userRole, $roles, true)) {
            $this->flash('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            $this->redirect('/dashboard');
        }
    }
}

