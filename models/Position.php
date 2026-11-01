<?php
/**
 * Model quản lý Chức vụ (positions)
 */

class Position extends Model {
    /**
     * Lấy toàn bộ chức vụ kèm số lượng nhân viên
     */
    public function getAll(): array {
        $sql = "
            SELECT p.*, COUNT(e.id) AS total_employees
            FROM positions p
            LEFT JOIN employees e ON p.id = e.position_id AND e.deleted_at IS NULL
            GROUP BY p.id
            ORDER BY p.id ASC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy chi tiết chức vụ theo ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM positions WHERE id = :id LIMIT 1";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Thêm mới chức vụ
     */
    public function create(array $data): int {
        $sql = "
            INSERT INTO positions (code, name, description, base_salary_min, base_salary_max, status, created_at)
            VALUES (:code, :name, :description, :base_salary_min, :base_salary_max, :status, NOW())
        ";
        $this->execute($sql, [
            'code'            => strtoupper(trim($data['code'])),
            'name'            => trim($data['name']),
            'description'     => $data['description'] ?? null,
            'base_salary_min' => (float)($data['base_salary_min'] ?? 0),
            'base_salary_max' => (float)($data['base_salary_max'] ?? 0),
            'status'          => $data['status'] ?? 'active'
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin chức vụ
     */
    public function update(int $id, array $data): bool {
        $sql = "
            UPDATE positions 
            SET code = :code, name = :name, description = :description, 
                base_salary_min = :base_salary_min, base_salary_max = :base_salary_max, 
                status = :status, updated_at = NOW()
            WHERE id = :id
        ";
        $this->execute($sql, [
            'id'              => $id,
            'code'            => strtoupper(trim($data['code'])),
            'name'            => trim($data['name']),
            'description'     => $data['description'] ?? null,
            'base_salary_min' => (float)($data['base_salary_min'] ?? 0),
            'base_salary_max' => (float)($data['base_salary_max'] ?? 0),
            'status'          => $data['status'] ?? 'active'
        ]);
        return true;
    }

    /**
     * Đếm số nhân viên đang giữ chức vụ này
     */
    public function countEmployees(int $id): int {
        $sql = "SELECT COUNT(*) FROM employees WHERE position_id = :id AND deleted_at IS NULL";
        return (int)$this->fetchColumn($sql, ['id' => $id]);
    }

    /**
     * Xóa chức vụ nếu chưa có nhân viên nào giữ chức vụ này
     */
    public function delete(int $id): bool {
        if ($this->countEmployees($id) > 0) {
            return false;
        }
        $sql = "DELETE FROM positions WHERE id = :id";
        $this->execute($sql, ['id' => $id]);
        return true;
    }

    /**
     * Kiểm tra mã chức vụ trùng lặp
     */
    public function isCodeExists(string $code, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM positions WHERE code = :code";
        $params = ['code' => strtoupper(trim($code))];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }
}

