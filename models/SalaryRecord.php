<?php
/**
 * Model quản lý Bảng lương (salary_records)
 */

class SalaryRecord extends Model {
    /**
     * Lấy danh sách bảng lương theo kỳ tháng/năm và phòng ban
     */
    public function getAll(?int $month = null, ?int $year = null, ?int $departmentId = null): array {
        $params = [];
        $where = ["1=1"];

        if ($month) {
            $where[] = "sr.month = :month";
            $params['month'] = $month;
        }

        if ($year) {
            $where[] = "sr.year = :year";
            $params['year'] = $year;
        }

        if ($departmentId) {
            $where[] = "e.department_id = :dept_id";
            $params['dept_id'] = $departmentId;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT sr.*, 
                   e.employee_code, e.full_name AS employee_name,
                   d.name AS department_name, p.name AS position_name,
                   u.username AS creator_username
            FROM salary_records sr
            JOIN employees e ON sr.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            LEFT JOIN users u ON sr.created_by = u.id
            WHERE {$whereClause}
            ORDER BY sr.year DESC, sr.month DESC, sr.id DESC
        ";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Lấy chi tiết phiếu lương theo ID
     */
    public function findById(int $id): ?array {
        $sql = "
            SELECT sr.*, 
                   e.employee_code, e.full_name AS employee_name, e.email AS employee_email,
                   d.name AS department_name, p.name AS position_name,
                   u.username AS creator_username
            FROM salary_records sr
            JOIN employees e ON sr.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            LEFT JOIN users u ON sr.created_by = u.id
            WHERE sr.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Lấy phiếu lương của 1 nhân viên
     */
    public function getByEmployee(int $employeeId): array {
        $sql = "
            SELECT sr.*, 
                   e.employee_code, e.full_name AS employee_name,
                   d.name AS department_name, p.name AS position_name
            FROM salary_records sr
            JOIN employees e ON sr.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            WHERE sr.employee_id = :emp_id
            ORDER BY sr.year DESC, sr.month DESC
        ";
        return $this->fetchAll($sql, ['emp_id' => $employeeId]);
    }

    /**
     * Tính toán và tạo mới / cập nhật phiếu lương
     */
    public function save(array $data): int {
        $baseSalary = (float)$data['base_salary'];
        $stdDays = (float)($data['standard_work_days'] ?: 22.0);
        $actDays = (float)($data['actual_work_days'] ?: 22.0);
        $allowance = (float)($data['allowance'] ?? 0);
        $bonus = (float)($data['bonus'] ?? 0);
        $deduction = (float)($data['deduction'] ?? 0);

        // Công thức tính lương thực nhận:
        // Lương theo ngày công = (Lương cơ bản / Ngày công chuẩn) * Ngày công thực tế
        // Lương thực nhận = Lương ngày công + Phụ cấp + Thưởng - Các khoản giảm trừ
        $salaryByWorkDays = ($stdDays > 0) ? round(($baseSalary / $stdDays) * $actDays) : $baseSalary;
        $netSalary = max(0, round($salaryByWorkDays + $allowance + $bonus - $deduction));

        $recordCode = sprintf('BL-%02d%d-%03d', $data['month'], $data['year'], $data['employee_id']);

        if (!empty($data['id'])) {
            $sql = "
                UPDATE salary_records SET
                    base_salary        = :base_salary,
                    standard_work_days = :std_days,
                    actual_work_days   = :act_days,
                    allowance          = :allowance,
                    bonus              = :bonus,
                    deduction          = :deduction,
                    net_salary         = :net_salary,
                    status             = :status,
                    notes              = :notes,
                    updated_at         = NOW()
                WHERE id = :id
            ";
            $this->execute($sql, [
                'id'          => (int)$data['id'],
                'base_salary' => $baseSalary,
                'std_days'    => $stdDays,
                'act_days'    => $actDays,
                'allowance'   => $allowance,
                'bonus'       => $bonus,
                'deduction'   => $deduction,
                'net_salary'  => $netSalary,
                'status'      => $data['status'] ?? 'draft',
                'notes'       => $data['notes'] ?? null
            ]);
            return (int)$data['id'];
        } else {
            $sql = "
                INSERT INTO salary_records (
                    record_code, employee_id, month, year, base_salary,
                    standard_work_days, actual_work_days, allowance, bonus,
                    deduction, net_salary, status, created_by, notes, created_at
                ) VALUES (
                    :code, :emp_id, :month, :year, :base_salary,
                    :std_days, :act_days, :allowance, :bonus,
                    :deduction, :net_salary, :status, :created_by, :notes, NOW()
                )
                ON DUPLICATE KEY UPDATE
                    base_salary        = VALUES(base_salary),
                    standard_work_days = VALUES(standard_work_days),
                    actual_work_days   = VALUES(actual_work_days),
                    allowance          = VALUES(allowance),
                    bonus              = VALUES(bonus),
                    deduction          = VALUES(deduction),
                    net_salary         = VALUES(net_salary),
                    status             = VALUES(status),
                    updated_at         = NOW()
            ";
            $this->execute($sql, [
                'code'        => $recordCode,
                'emp_id'      => (int)$data['employee_id'],
                'month'       => (int)$data['month'],
                'year'        => (int)$data['year'],
                'base_salary' => $baseSalary,
                'std_days'    => $stdDays,
                'act_days'    => $actDays,
                'allowance'   => $allowance,
                'bonus'       => $bonus,
                'deduction'   => $deduction,
                'net_salary'  => $netSalary,
                'status'      => $data['status'] ?? 'draft',
                'created_by'  => !empty($data['created_by']) ? (int)$data['created_by'] : null,
                'notes'       => $data['notes'] ?? null
            ]);
            return $this->lastInsertId();
        }
    }

    /**
     * Tự động sinh bảng lương tháng cho toàn bộ nhân viên đang làm việc
     */
    public function generatePayrollForMonth(int $month, int $year, int $createdBy): int {
        // Lấy danh sách nhân viên đang làm việc
        $sqlEmployees = "
            SELECT e.id, e.base_salary,
                   (SELECT COUNT(*) FROM attendance a 
                    WHERE a.employee_id = e.id AND MONTH(a.date) = :month AND YEAR(a.date) = :year AND a.check_in IS NOT NULL) AS worked_days
            FROM employees e
            WHERE e.deleted_at IS NULL AND e.status IN ('active', 'probation')
        ";
        $employees = $this->fetchAll($sqlEmployees, ['month' => $month, 'year' => $year]);

        $count = 0;
        foreach ($employees as $emp) {
            $stdDays = 22.0;
            // Nếu có chấm công thì lấy ngày công thực tế, nếu không mặc định 22 ngày
            $actDays = ($emp['worked_days'] > 0) ? (float)$emp['worked_days'] : $stdDays;
            
            $this->save([
                'employee_id'        => $emp['id'],
                'month'              => $month,
                'year'               => $year,
                'base_salary'        => $emp['base_salary'],
                'standard_work_days' => $stdDays,
                'actual_work_days'   => $actDays,
                'allowance'          => 1000000, // Phụ cấp ăn trưa mặc định
                'bonus'              => 0,
                'deduction'          => 0,
                'status'             => 'draft',
                'created_by'         => $createdBy,
                'notes'              => 'Khởi tạo tự động từ hệ thống chấm công'
            ]);
            $count++;
        }
        return $count;
    }

    /**
     * Cập nhật trạng thái bảng lương (Duyệt / Thanh toán)
     */
    public function updateStatus(int $id, string $status): bool {
        $paidDate = ($status === 'paid') ? date('Y-m-d') : null;
        $sql = "UPDATE salary_records SET status = :status, paid_date = :paid_date, updated_at = NOW() WHERE id = :id";
        $this->execute($sql, [
            'status'    => $status,
            'paid_date' => $paidDate,
            'id'        => $id
        ]);
        return true;
    }
}

