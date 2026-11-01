<?php
/**
 * Lớp Base Controller cơ bản
 */

abstract class Controller {
    /**
     * Render view và nhúng vào layout
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void {
        extract($data);

        // Nạp view con vào buffer
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("Không tìm thấy view: views/{$view}.php");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Nạp layout bọc ngoài
        $layoutFile = ROOT_PATH . '/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            die("Không tìm thấy layout: views/layouts/{$layout}.php");
        }

        require $layoutFile;
    }

    /**
     * Chuyển hướng trang
     */
    protected function redirect(string $url): void {
        header("Location: " . $url);
        exit;
    }

    /**
     * Trả về phản hồi JSON (dành cho AJAX / API)
     */
    protected function json(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Yêu cầu người dùng phải đăng nhập mới được truy cập
     */
    protected function requireAuth(): void {
        if (!Session::has('user')) {
            Session::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect(Helper::url('auth', 'login'));
        }
    }

    /**
     * Kiểm tra quyền truy cập nghiêm ngặt ở phía máy chủ
     * @param string|array $roles Các vai trò được phép (admin, hr, employee)
     */
    protected function requireRole(string|array $roles): void {
        $this->requireAuth();
        $user = Session::get('user');

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($user['role_name'], $roles, true)) {
            http_response_code(403);
            $this->render('errors/403', [
                'pageTitle' => '403 - Không Có Quyền Truy Cập',
                'message'   => 'Bạn không có quyền hạn cần thiết để truy cập chức năng này.'
            ], 'main');
            exit;
        }
    }

    /**
     * Yêu cầu tài khoản phải liên kết với một hồ sơ nhân viên
     */
    protected function requireEmployee(): int {
        $this->requireAuth();
        $user = Session::get('user');
        if (empty($user['employee_id'])) {
            Session::setFlash('danger', 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên nào.');
            $this->redirect(Helper::url('dashboard', 'index'));
        }
        return (int)$user['employee_id'];
    }

    /**
     * Kiểm tra mã bảo vệ chống tấn công CSRF
     */
    protected function validateCsrf(): void {
        $token = $_POST['csrf_token'] ?? '';
        if (!Session::validateCsrf($token)) {
            Session::setFlash('danger', 'Yêu cầu bảo mật không hợp lệ hoặc phiên làm việc đã hết hạn. Vui lòng thử lại.');
            $referer = $_SERVER['HTTP_REFERER'] ?? Helper::url('dashboard', 'index');
            $this->redirect($referer);
        }
    }

    /**
     * Lấy thông tin tài khoản hiện tại
     */
    protected function currentUser(): ?array {
        return Session::get('user');
    }

    /**
     * Ghi nhật ký kiểm toán hệ thống (Audit Log)
     */
    protected function logAction(string $action, string $entityType, ?int $entityId, string $description): void {
        try {
            $user = $this->currentUser();
            $userId = $user['id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);

            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $action, $entityType, $entityId, $description, $ip, $ua]);
        } catch (Exception $e) {
            // Không làm gián đoạn luồng chính nếu ghi log thất bại
            error_log("Lỗi ghi audit log: " . $e->getMessage());
        }
    }
}

