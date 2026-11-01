<?php
/**
 * Model quản lý Hợp đồng lao động (contracts)
 */

class Contract extends Model {
    /**
     * Lấy danh sách hợp đồng kèm bộ lọc
     */
    public function getAll(array $filters = []): array {
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['employee_id'])) {
            $where[] = "c.employee_id = :emp_id";
            $params['emp_id'] = (int)$filters['employee_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "c.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['contract_type'])) {
            $where[] = "c.contract_type = :type";
            $params['type'] = $filters['contract_type'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT c.*, 
                   e.employee_code, e.full_name AS employee_name,
                   d.name AS department_name, p.name AS position_name
            FROM contracts c
            JOIN employees e ON c.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            WHERE {$whereClause}
            ORDER BY c.id DESC
        ";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Lấy chi tiết hợp đồng
     */
    public function findById(int $id): ?array {
        $sql = "
            SELECT c.*, 
                   e.employee_code, e.full_name AS employee_name, e.email AS employee_email,
                   d.name AS department_name, p.name AS position_name
            FROM contracts c
            JOIN employees e ON c.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            WHERE c.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Lấy hợp đồng của 1 nhân viên
     */
    public function getByEmployee(int $employeeId): array {
        $sql = "
            SELECT c.*, 
                   e.employee_code, e.full_name AS employee_name,
                   d.name AS department_name, p.name AS position_name
            FROM contracts c
            JOIN employees e ON c.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            WHERE c.employee_id = :emp_id
            ORDER BY c.start_date DESC
        ";
        return $this->fetchAll($sql, ['emp_id' => $employeeId]);
    }

    /**
     * Thêm mới hợp đồng
     */
    public function create(array $data): int {
        $sql = "
            INSERT INTO contracts (
                contract_code, employee_id, contract_type, start_date, 
                end_date, basic_salary, insurance_salary, status, notes, created_at
            ) VALUES (
                :code, :emp_id, :type, :start_date, 
                :end_date, :basic_salary, :insurance_salary, :status, :notes, NOW()
            )
        ";
        $this->execute($sql, [
            'code'             => strtoupper(trim($data['contract_code'])),
            'emp_id'           => (int)$data['employee_id'],
            'type'             => $data['contract_type'],
            'start_date'       => $data['start_date'],
            'end_date'         => !empty($data['end_date']) ? $data['end_date'] : null,
            'basic_salary'     => (float)($data['basic_salary'] ?? 0),
            'insurance_salary' => (float)($data['insurance_salary'] ?? 0),
            'status'           => $data['status'] ?? 'active',
            'notes'            => $data['notes'] ?? null
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật hợp đồng
     */
    public function update(int $id, array $data): bool {
        $sql = "
            UPDATE contracts SET
                contract_code    = :code,
                employee_id      = :emp_id,
                contract_type    = :type,
                start_date       = :start_date,
                end_date         = :end_date,
                basic_salary     = :basic_salary,
                insurance_salary = :insurance_salary,
                status           = :status,
                notes            = :notes,
                updated_at       = NOW()
            WHERE id = :id
        ";
        $this->execute($sql, [
            'id'               => $id,
            'code'             => strtoupper(trim($data['contract_code'])),
            'emp_id'           => (int)$data['employee_id'],
            'type'             => $data['contract_type'],
            'start_date'       => $data['start_date'],
            'end_date'         => !empty($data['end_date']) ? $data['end_date'] : null,
            'basic_salary'     => (float)($data['basic_salary'] ?? 0),
            'insurance_salary' => (float)($data['insurance_salary'] ?? 0),
            'status'           => $data['status'] ?? 'active',
            'notes'            => $data['notes'] ?? null
        ]);
        return true;
    }

    /**
     * Xóa hợp đồng
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM contracts WHERE id = :id";
        $this->execute($sql, ['id' => $id]);
        return true;
    }

    /**
     * Kiểm tra mã hợp đồng trùng lặp
     */
    public function isCodeExists(string $code, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM contracts WHERE contract_code = :code";
        $params = ['code' => strtoupper(trim($code))];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }
}

