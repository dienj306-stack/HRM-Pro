<?php
/**
 * Model quản lý Đơn xin nghỉ phép (leave_requests)
 */

class LeaveRequest extends Model {
    /**
     * Lấy danh sách tất cả đơn nghỉ phép (dành cho Admin/HR)
     */
    public function getAll(array $filters = []): array {
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['status'])) {
            $where[] = "lr.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['department_id'])) {
            $where[] = "e.department_id = :dept_id";
            $params['dept_id'] = (int)$filters['department_id'];
        }

        if (!empty($filters['leave_type'])) {
            $where[] = "lr.leave_type = :leave_type";
            $params['leave_type'] = $filters['leave_type'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT lr.*, 
                   e.employee_code, e.full_name AS employee_name,
                   d.name AS department_name,
                   u.username AS approver_username
            FROM leave_requests lr
            JOIN employees e ON lr.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN users u ON lr.approver_id = u.id
            WHERE {$whereClause}
            ORDER BY lr.id DESC
        ";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Lấy danh sách đơn nghỉ phép của riêng một nhân viên
     */
    public function getByEmployee(int $employeeId): array {
        $sql = "
            SELECT lr.*, u.username AS approver_username
            FROM leave_requests lr
            LEFT JOIN users u ON lr.approver_id = u.id
            WHERE lr.employee_id = :emp_id
            ORDER BY lr.id DESC
        ";
        return $this->fetchAll($sql, ['emp_id' => $employeeId]);
    }

    /**
     * Lấy chi tiết đơn nghỉ phép
     */
    public function findById(int $id): ?array {
        $sql = "
            SELECT lr.*, 
                   e.employee_code, e.full_name AS employee_name, e.email AS employee_email,
                   d.name AS department_name,
                   u.username AS approver_username
            FROM leave_requests lr
            JOIN employees e ON lr.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN users u ON lr.approver_id = u.id
            WHERE lr.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Tạo đơn xin nghỉ phép mới
     */
    public function create(array $data): int {
        $requestCode = 'NP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $sql = "
            INSERT INTO leave_requests (
                request_code, employee_id, leave_type, start_date, end_date, 
                total_days, reason, status, created_at
            ) VALUES (
                :code, :emp_id, :type, :start_date, :end_date, 
                :total_days, :reason, 'pending', NOW()
            )
        ";
        $this->execute($sql, [
            'code'       => $requestCode,
            'emp_id'     => $data['employee_id'],
            'type'       => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'total_days' => (float)$data['total_days'],
            'reason'     => trim($data['reason'])
        ]);
        return $this->lastInsertId();
    }

    /**
     * Phê duyệt đơn nghỉ phép
     */
    public function approve(int $id, int $approverId): bool {
        $sql = "
            UPDATE leave_requests 
            SET status = 'approved', approver_id = :approver_id, 
                approved_at = NOW(), rejection_reason = NULL, updated_at = NOW()
            WHERE id = :id AND status = 'pending'
        ";
        $this->execute($sql, ['approver_id' => $approverId, 'id' => $id]);
        return true;
    }

    /**
     * Từ chối đơn nghỉ phép kèm lý do
     */
    public function reject(int $id, int $approverId, string $rejectionReason): bool {
        $sql = "
            UPDATE leave_requests 
            SET status = 'rejected', approver_id = :approver_id, 
                approved_at = NOW(), rejection_reason = :reason, updated_at = NOW()
            WHERE id = :id AND status = 'pending'
        ";
        $this->execute($sql, [
            'approver_id' => $approverId,
            'reason'      => trim($rejectionReason),
            'id'          => $id
        ]);
        return true;
    }

    /**
     * Nhân viên hủy đơn nghỉ phép của mình (chỉ hủy khi đang chờ duyệt)
     */
    public function cancel(int $id, int $employeeId): bool {
        $sql = "
            UPDATE leave_requests 
            SET status = 'cancelled', updated_at = NOW()
            WHERE id = :id AND employee_id = :emp_id AND status = 'pending'
        ";
        $this->execute($sql, ['id' => $id, 'emp_id' => $employeeId]);
        return true;
    }

    /**
     * Đếm số lượng đơn đang chờ duyệt (hiển thị trên dashboard và sidebar)
     */
    public function countPending(): int {
        return (int)$this->fetchColumn("SELECT COUNT(*) FROM leave_requests WHERE status = 'pending'");
    }

    /**
     * Kiểm tra trùng lặp khoảng thời gian nghỉ phép
     */
    public function hasConflictingLeave(int $employeeId, string $startDate, string $endDate, ?int $excludeId = null): bool {
        $sql = "
            SELECT COUNT(*) FROM leave_requests
            WHERE employee_id = :emp_id
              AND status IN ('pending', 'approved')
              AND NOT (end_date < :start_date OR start_date > :end_date)
        ";
        $params = [
            'emp_id'     => $employeeId,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ];
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int)$this->fetchColumn($sql, $params) > 0;
    }
}

