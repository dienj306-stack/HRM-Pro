<?php
/**
 * Model quản lý Tài khoản người dùng (users) & phân quyền
 */

class User extends Model {
    /**
     * Tìm người dùng theo username hoặc email
     */
    public function findByLogin(string $login): ?array {
        $sql = "
            SELECT u.*, r.name AS role_name, r.display_name AS role_display,
                   e.full_name AS employee_name, e.employee_code, e.avatar, e.department_id,
                   d.name AS department_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON u.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            WHERE u.username = :login_user OR u.email = :login_email
            LIMIT 1
        ";
        return $this->fetchOne($sql, [
            'login_user'  => $login,
            'login_email' => $login
        ]);
    }

    /**
     * Tìm theo ID
     */
    public function findById(int $id): ?array {
        $sql = "
            SELECT u.*, r.name AS role_name, r.display_name AS role_display,
                   e.full_name AS employee_name, e.employee_code, e.avatar, e.department_id,
                   d.name AS department_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON u.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            WHERE u.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Lấy danh sách tất cả tài khoản
     */
    public function getAll(): array {
        $sql = "
            SELECT u.id, u.username, u.email, u.status, u.last_login, u.created_at,
                   r.name AS role_name, r.display_name AS role_display,
                   e.id AS employee_id, e.employee_code, e.full_name AS employee_name,
                   d.name AS department_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON u.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            ORDER BY u.id DESC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh sách tất cả vai trò
     */
    public function getRoles(): array {
        return $this->fetchAll("SELECT * FROM roles ORDER BY id ASC");
    }

    /**
     * Tạo tài khoản mới
     */
    public function create(array $data): int {
        $sql = "
            INSERT INTO users (username, email, password_hash, role_id, employee_id, status, created_at)
            VALUES (:username, :email, :password_hash, :role_id, :employee_id, :status, NOW())
        ";
        $this->execute($sql, [
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => $data['password_hash'],
            'role_id'       => $data['role_id'],
            'employee_id'   => $data['employee_id'] ?: null,
            'status'        => $data['status'] ?? 'active'
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword(int $userId, string $newPasswordHash): bool {
        $sql = "UPDATE users SET password_hash = :hash, updated_at = NOW() WHERE id = :id";
        $this->execute($sql, ['hash' => $newPasswordHash, 'id' => $userId]);
        return true;
    }

    /**
     * Khóa hoặc mở khóa tài khoản
     */
    public function toggleStatus(int $userId): string {
        $user = $this->findById($userId);
        if (!$user) return '';

        $newStatus = ($user['status'] === 'active') ? 'locked' : 'active';
        $sql = "UPDATE users SET status = :status, updated_at = NOW() WHERE id = :id";
        $this->execute($sql, ['status' => $newStatus, 'id' => $userId]);
        return $newStatus;
    }

    /**
     * Cập nhật thời điểm đăng nhập cuối
     */
    public function updateLastLogin(int $userId): void {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $this->execute($sql, ['id' => $userId]);
    }

    /**
     * Kiểm tra username đã tồn tại chưa
     */
    public function isUsernameExists(string $username, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = ['username' => $username];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function isEmailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = ['email' => $email];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }
}

