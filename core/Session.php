<?php
/**
 * Lớp quản lý Session và Flash Messages
 */

class Session {
    /**
     * Gán giá trị vào session
     */
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Lấy giá trị từ session
     */
    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Kiểm tra tồn tại
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Xóa một key trong session
     */
    public static function remove(string $key): void {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Hủy toàn bộ session khi đăng xuất
     */
    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Đổi Session ID sau khi đăng nhập (chống Session Fixation)
     */
    public static function regenerate(): void {
        session_regenerate_id(true);
    }

    /**
     * Đặt thông báo tạm thời (Flash Message)
     * $type: 'success', 'danger', 'warning', 'info'
     */
    public static function setFlash(string $type, string $message): void {
        $_SESSION['_flash'][$type][] = $message;
    }

    /**
     * Lấy và xóa flash messages
     */
    public static function getFlashes(): array {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }

    /**
     * Khởi tạo hoặc lấy CSRF token
     */
    public static function csrfToken(): string {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Kiểm tra tính hợp lệ của CSRF token
     */
    public static function validateCsrf(?string $token): bool {
        if (empty($token) || empty($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}

