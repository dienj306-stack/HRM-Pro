<?php
/**
 * Model quản lý Chấm công (attendance)
 */

class Attendance extends Model {
    /**
     * Lấy bản ghi chấm công của nhân viên trong ngày chỉ định (mặc định hôm nay)
     */
    public function getRecordByDate(int $employeeId, ?string $date = null): ?array {
        $date = $date ?: date('Y-m-d');
        $sql = "SELECT * FROM attendance WHERE employee_id = :emp_id AND date = :date LIMIT 1";
        return $this->fetchOne($sql, ['emp_id' => $employeeId, 'date' => $date]);
    }

    /**
     * Thực hiện chấm công vào (Check-in)
     */
    public function checkIn(int $employeeId, ?string $notes = null): array {
        $today = date('Y-m-d');
        $nowTime = date('H:i:s');

        // Kiểm tra xem hôm nay đã chấm công vào chưa
        $existing = $this->getRecordByDate($employeeId, $today);
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Bạn đã thực hiện chấm công vào hôm nay lúc ' . Helper::formatTime($existing['check_in']) . '.'
            ];
        }

        // Lấy giờ bắt đầu chuẩn từ cấu hình hoặc mặc định 08:30:00
        $standardStart = '08:30:00';
        $status = ($nowTime > $standardStart) ? 'late' : 'on_time';

        $sql = "
            INSERT INTO attendance (employee_id, date, check_in, status, notes, created_at)
            VALUES (:emp_id, :date, :check_in, :status, :notes, NOW())
        ";
        $this->execute($sql, [
            'emp_id'   => $employeeId,
            'date'     => $today,
            'check_in' => $nowTime,
            'status'   => $status,
            'notes'    => $notes
        ]);

        return [
            'success' => true,
            'status'  => $status,
            'time'    => $nowTime,
            'message' => 'Chấm công vào thành công lúc ' . date('H:i:s') . ($status === 'late' ? ' (Đi muộn)' : ' (Đúng giờ)') . '.'
        ];
    }

    /**
     * Thực hiện chấm công ra (Check-out)
     */
    public function checkOut(int $employeeId, ?string $notes = null): array {
        $today = date('Y-m-d');
        $nowTime = date('H:i:s');

        $existing = $this->getRecordByDate($employeeId, $today);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Bạn chưa chấm công vào hôm nay, không thể chấm công ra!'
            ];
        }

        if (!empty($existing['check_out'])) {
            return [
                'success' => false,
                'message' => 'Bạn đã chấm công ra hôm nay lúc ' . Helper::formatTime($existing['check_out']) . '.'
            ];
        }

        // Tính số giờ làm việc
        $inTimestamp = strtotime($today . ' ' . $existing['check_in']);
        $outTimestamp = strtotime($today . ' ' . $nowTime);
        $diffHours = max(0, round(($outTimestamp - $inTimestamp) / 3600, 2));

        // Trừ 1 tiếng nghỉ trưa nếu làm trên 5 tiếng
        if ($diffHours >= 5.0) {
            $diffHours = max(0, $diffHours - 1.0);
        }

        $sql = "
            UPDATE attendance 
            SET check_out = :check_out, work_hours = :work_hours, 
                notes = CONCAT_WS(' | ', notes, :notes), updated_at = NOW()
            WHERE id = :id
        ";
        $this->execute($sql, [
            'id'         => $existing['id'],
            'check_out'  => $nowTime,
            'work_hours' => $diffHours,
            'notes'      => $notes ?: 'Chấm công ra'
        ]);

        return [
            'success'    => true,
            'time'       => $nowTime,
            'work_hours' => $diffHours,
            'message'    => 'Chấm công ra thành công lúc ' . date('H:i:s') . '. Số giờ làm việc: ' . $diffHours . ' giờ.'
        ];
    }

    /**
     * Lấy lịch sử chấm công của 1 nhân viên trong tháng/năm
     */
    public function getEmployeeHistory(int $employeeId, int $month, int $year): array {
        $sql = "
            SELECT * FROM attendance
            WHERE employee_id = :emp_id 
              AND MONTH(date) = :month 
              AND YEAR(date) = :year
            ORDER BY date DESC
        ";
        return $this->fetchAll($sql, [
            'emp_id' => $employeeId,
            'month'  => $month,
            'year'   => $year
        ]);
    }

    /**
     * Lấy danh sách chấm công toàn công ty theo ngày/tháng/phòng ban
     */
    public function getAllAttendance(array $filters = []): array {
        $params = [];
        $where = ["e.deleted_at IS NULL"];

        if (!empty($filters['date'])) {
            $where[] = "a.date = :date";
            $params['date'] = $filters['date'];
        }

        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where[] = "MONTH(a.date) = :month AND YEAR(a.date) = :year";
            $params['month'] = (int)$filters['month'];
            $params['year'] = (int)$filters['year'];
        }

        if (!empty($filters['department_id'])) {
            $where[] = "e.department_id = :dept_id";
            $params['dept_id'] = (int)$filters['department_id'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "
            SELECT a.*, e.employee_code, e.full_name, d.name AS department_name
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            LEFT JOIN departments d ON e.department_id = d.id
            WHERE {$whereClause}
            ORDER BY a.date DESC, a.check_in ASC
        ";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Thống kê chấm công trong ngày hôm nay
     */
    public function getTodayStats(): array {
        $today = date('Y-m-d');
        $totalActive = (int)$this->fetchColumn("SELECT COUNT(*) FROM employees WHERE deleted_at IS NULL AND status = 'active'");
        
        $checkedIn = (int)$this->fetchColumn("SELECT COUNT(*) FROM attendance WHERE date = :today", ['today' => $today]);
        $late = (int)$this->fetchColumn("SELECT COUNT(*) FROM attendance WHERE date = :today AND status = 'late'", ['today' => $today]);
        $onTime = (int)$this->fetchColumn("SELECT COUNT(*) FROM attendance WHERE date = :today AND status = 'on_time'", ['today' => $today]);
        $notCheckedIn = max(0, $totalActive - $checkedIn);

        return [
            'total_active'   => $totalActive,
            'checked_in'     => $checkedIn,
            'on_time'        => $onTime,
            'late'           => $late,
            'not_checked_in' => $notCheckedIn
        ];
    }

    /**
     * Tính tổng ngày công thực tế của nhân viên trong tháng
     */
    public function countWorkDays(int $employeeId, int $month, int $year): float {
        $sql = "
            SELECT COUNT(*) FROM attendance 
            WHERE employee_id = :emp_id 
              AND MONTH(date) = :month 
              AND YEAR(date) = :year 
              AND check_in IS NOT NULL
        ";
        return (float)$this->fetchColumn($sql, [
            'emp_id' => $employeeId,
            'month'  => $month,
            'year'   => $year
        ]);
    }
}

