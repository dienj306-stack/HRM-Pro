<?php
/**
 * Model quản lý Phòng ban (departments)
 */

class Department extends Model {
    /**
     * Lấy toàn bộ phòng ban kèm số lượng nhân viên và thông tin trưởng phòng
     */
    public function getAll(): array {
        $sql = "
            SELECT d.*, 
                   e.full_name AS manager_name, e.employee_code AS manager_code,
                   COUNT(emp.id) AS total_employees
            FROM departments d
            LEFT JOIN employees e ON d.manager_id = e.id
            LEFT JOIN employees emp ON d.id = emp.department_id AND emp.deleted_at IS NULL
            GROUP BY d.id
            ORDER BY d.id ASC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy chi tiết phòng ban theo ID
     */
    public function findById(int $id): ?array {
        $sql = "
            SELECT d.*, e.full_name AS manager_name
            FROM departments d
            LEFT JOIN employees e ON d.manager_id = e.id
            WHERE d.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Thêm mới phòng ban
     */
    public function create(array $data): int {
        $sql = "
            INSERT INTO departments (code, name, description, manager_id, status, created_at)
            VALUES (:code, :name, :description, :manager_id, :status, NOW())
        ";
        $this->execute($sql, [
            'code'        => strtoupper(trim($data['code'])),
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? null,
            'manager_id'  => !empty($data['manager_id']) ? (int)$data['manager_id'] : null,
            'status'      => $data['status'] ?? 'active'
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin phòng ban
     */
    public function update(int $id, array $data): bool {
        $sql = "
            UPDATE departments 
            SET code = :code, name = :name, description = :description, 
                manager_id = :manager_id, status = :status, updated_at = NOW()
            WHERE id = :id
        ";
        $this->execute($sql, [
            'id'          => $id,
            'code'        => strtoupper(trim($data['code'])),
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? null,
            'manager_id'  => !empty($data['manager_id']) ? (int)$data['manager_id'] : null,
            'status'      => $data['status'] ?? 'active'
        ]);
        return true;
    }

    /**
     * Kiểm tra số nhân viên đang thuộc phòng ban trước khi xóa
     */
    public function countEmployees(int $id): int {
        $sql = "SELECT COUNT(*) FROM employees WHERE department_id = :id AND deleted_at IS NULL";
        return (int)$this->fetchColumn($sql, ['id' => $id]);
    }

    /**
     * Xóa phòng ban (chỉ xóa khi không có nhân viên)
     */
    public function delete(int $id): bool {
        if ($this->countEmployees($id) > 0) {
            return false;
        }
        $sql = "DELETE FROM departments WHERE id = :id";
        $this->execute($sql, ['id' => $id]);
        return true;
    }

    /**
     * Kiểm tra mã phòng ban đã tồn tại
     */
    public function isCodeExists(string $code, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM departments WHERE code = :code";
        $params = ['code' => strtoupper(trim($code))];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }
}

