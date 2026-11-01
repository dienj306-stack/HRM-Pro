<?php
/**
 * Model quản lý Hồ sơ Nhân viên (employees)
 */

class Employee extends Model {
    /**
     * Lấy danh sách nhân viên có bộ lọc tìm kiếm và phân trang
     */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where = ["e.deleted_at IS NULL"];

        if (!empty($filters['keyword'])) {
            $where[] = "(e.full_name LIKE :kw1 OR e.employee_code LIKE :kw2 OR e.email LIKE :kw3 OR e.phone LIKE :kw4)";
            $kw = '%' . trim($filters['keyword']) . '%';
            $params['kw1'] = $kw;
            $params['kw2'] = $kw;
            $params['kw3'] = $kw;
            $params['kw4'] = $kw;
        }

        if (!empty($filters['department_id'])) {
            $where[] = "e.department_id = :dept_id";
            $params['dept_id'] = (int)$filters['department_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "e.status = :status";
            $params['status'] = $filters['status'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT e.*, 
                   d.name AS department_name, d.code AS department_code,
                   p.name AS position_name, p.code AS position_code,
                   u.id AS user_account_id, u.username, u.status AS user_status
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            LEFT JOIN users u ON e.id = u.employee_id
            WHERE {$whereClause}
            ORDER BY e.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Đếm tổng số nhân viên thỏa mãn bộ lọc (dùng cho phân trang)
     */
    public function countAll(array $filters = []): int {
        $params = [];
        $where = ["deleted_at IS NULL"];

        if (!empty($filters['keyword'])) {
            $where[] = "(full_name LIKE :kw1 OR employee_code LIKE :kw2 OR email LIKE :kw3 OR phone LIKE :kw4)";
            $kw = '%' . trim($filters['keyword']) . '%';
            $params['kw1'] = $kw;
            $params['kw2'] = $kw;
            $params['kw3'] = $kw;
            $params['kw4'] = $kw;
        }

        if (!empty($filters['department_id'])) {
            $where[] = "department_id = :dept_id";
            $params['dept_id'] = (int)$filters['department_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) FROM employees WHERE {$whereClause}";
        return (int)$this->fetchColumn($sql, $params);
    }

    /**
     * Lấy chi tiết nhân viên theo ID
     */
    public function findById(int $id): ?array {
        $sql = "
            SELECT e.*, 
                   d.name AS department_name, d.code AS department_code,
                   p.name AS position_name, p.code AS position_code,
                   u.id AS user_account_id, u.username, u.status AS user_status, u.role_id,
                   r.display_name AS role_display
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            LEFT JOIN users u ON e.id = u.employee_id
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE e.id = :id AND e.deleted_at IS NULL
            LIMIT 1
        ";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Lấy toàn bộ nhân viên hoạt động (dùng cho dropdown lựa chọn)
     */
    public function getActiveEmployees(): array {
        $sql = "
            SELECT id, employee_code, full_name, email, department_id, position_id, base_salary
            FROM employees
            WHERE deleted_at IS NULL AND status = 'active'
            ORDER BY full_name ASC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy các nhân viên chưa có tài khoản người dùng
     */
    public function getEmployeesWithoutUser(): array {
        $sql = "
            SELECT e.id, e.employee_code, e.full_name, e.email
            FROM employees e
            LEFT JOIN users u ON e.id = u.employee_id
            WHERE e.deleted_at IS NULL AND u.id IS NULL
            ORDER BY e.full_name ASC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Thêm mới nhân viên
     */
    public function create(array $data): int {
        $fullName = trim(($data['last_name'] ?? '') . ' ' . ($data['first_name'] ?? ''));
        $sql = "
            INSERT INTO employees (
                employee_code, first_name, last_name, full_name, gender, 
                date_of_birth, email, phone, address, department_id, 
                position_id, hire_date, base_salary, status, avatar, created_at
            ) VALUES (
                :code, :first_name, :last_name, :full_name, :gender,
                :dob, :email, :phone, :address, :dept_id,
                :pos_id, :hire_date, :base_salary, :status, :avatar, NOW()
            )
        ";
        $this->execute($sql, [
            'code'        => strtoupper(trim($data['employee_code'])),
            'first_name'  => trim($data['first_name']),
            'last_name'   => trim($data['last_name']),
            'full_name'   => $fullName,
            'gender'      => $data['gender'] ?? 'male',
            'dob'         => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
            'email'       => strtolower(trim($data['email'])),
            'phone'       => trim($data['phone'] ?? ''),
            'address'     => trim($data['address'] ?? ''),
            'dept_id'     => !empty($data['department_id']) ? (int)$data['department_id'] : null,
            'pos_id'      => !empty($data['position_id']) ? (int)$data['position_id'] : null,
            'hire_date'   => $data['hire_date'] ?? date('Y-m-d'),
            'base_salary' => (float)($data['base_salary'] ?? 0),
            'status'      => $data['status'] ?? 'active',
            'avatar'      => $data['avatar'] ?? 'assets/img/avatar-default.png'
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(int $id, array $data): bool {
        $fullName = trim(($data['last_name'] ?? '') . ' ' . ($data['first_name'] ?? ''));
        $sql = "
            UPDATE employees SET
                employee_code = :code,
                first_name    = :first_name,
                last_name     = :last_name,
                full_name     = :full_name,
                gender        = :gender,
                date_of_birth = :dob,
                email         = :email,
                phone         = :phone,
                address       = :address,
                department_id = :dept_id,
                position_id   = :pos_id,
                hire_date     = :hire_date,
                base_salary   = :base_salary,
                status        = :status,
                updated_at    = NOW()
            WHERE id = :id
        ";
        $this->execute($sql, [
            'id'          => $id,
            'code'        => strtoupper(trim($data['employee_code'])),
            'first_name'  => trim($data['first_name']),
            'last_name'   => trim($data['last_name']),
            'full_name'   => $fullName,
            'gender'      => $data['gender'] ?? 'male',
            'dob'         => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
            'email'       => strtolower(trim($data['email'])),
            'phone'       => trim($data['phone'] ?? ''),
            'address'     => trim($data['address'] ?? ''),
            'dept_id'     => !empty($data['department_id']) ? (int)$data['department_id'] : null,
            'pos_id'      => !empty($data['position_id']) ? (int)$data['position_id'] : null,
            'hire_date'   => $data['hire_date'],
            'base_salary' => (float)($data['base_salary'] ?? 0),
            'status'      => $data['status'] ?? 'active'
        ]);
        return true;
    }

    /**
     * Xóa mềm nhân viên (Soft Delete) để bảo toàn dữ liệu lịch sử chấm công, lương, hợp đồng
     */
    public function softDelete(int $id): bool {
        $sql = "UPDATE employees SET deleted_at = NOW(), status = 'resigned' WHERE id = :id";
        $this->execute($sql, ['id' => $id]);

        // Khóa tài khoản đăng nhập nếu có
        $sqlUser = "UPDATE users SET status = 'locked' WHERE employee_id = :id";
        $this->execute($sqlUser, ['id' => $id]);

        return true;
    }

    /**
     * Kiểm tra mã nhân viên đã tồn tại
     */
    public function isCodeExists(string $code, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM employees WHERE employee_code = :code AND deleted_at IS NULL";
        $params = ['code' => strtoupper(trim($code))];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }

    /**
     * Kiểm tra email nhân viên đã tồn tại
     */
    public function isEmailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM employees WHERE email = :email AND deleted_at IS NULL";
        $params = ['email' => strtolower(trim($email))];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }

    /**
     * Thống kê nhân sự theo phòng ban
     */
    public function getStatsByDepartment(): array {
        $sql = "
            SELECT d.name AS department_name, d.code, COUNT(e.id) AS total
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id AND e.deleted_at IS NULL
            GROUP BY d.id
            ORDER BY total DESC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Thống kê tổng số nhân viên và số đang làm việc
     */
    public function getOverviewStats(): array {
        $total = (int)$this->fetchColumn("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL");
        $active = (int)$this->fetchColumn("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL AND status = 'active'");
        $probation = (int)$this->fetchColumn("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL AND status = 'probation'");
        $onLeave = (int)$this->fetchColumn("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL AND status = 'on_leave'");
        return [
            'total'     => $total,
            'active'    => $active,
            'probation' => $probation,
            'on_leave'  => $onLeave
        ];
    }
}

