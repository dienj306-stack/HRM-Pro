<?php
/**
 * Controller Báo cáo & Thống kê (reports)
 */

class ReportController extends Controller {
    private Employee $empModel;
    private Department $deptModel;
    private Attendance $attModel;
    private LeaveRequest $leaveModel;
    private SalaryRecord $salaryModel;

    public function __construct() {
        $this->empModel = new Employee();
        $this->deptModel = new Department();
        $this->attModel = new Attendance();
        $this->leaveModel = new LeaveRequest();
        $this->salaryModel = new SalaryRecord();
    }

    /**
     * Báo cáo tổng hợp nhân sự
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $deptStats = $this->empModel->getStatsByDepartment();
        $overviewStats = $this->empModel->getOverviewStats();

        // Thống kê giới tính
        $db = Database::getConnection();
        $genderStats = $db->query("
            SELECT gender, COUNT(*) AS total 
            FROM employees 
            WHERE deleted_at IS NULL 
            GROUP BY gender
        ")->fetchAll();

        $this->render('reports/index', [
            'pageTitle'     => 'Báo Cáo Tổng Hợp Nhân Sự',
            'deptStats'     => $deptStats,
            'overviewStats' => $overviewStats,
            'genderStats'   => $genderStats
        ], 'main');
    }

    /**
     * Báo cáo chuyên đề Chấm công theo tháng
     */
    public function attendance(): void {
        $this->requireRole(['admin', 'hr']);

        $month = (int)($_GET['month'] ?? date('m'));
        $year = (int)($_GET['year'] ?? date('Y'));
        $deptId = !empty($_GET['department_id']) ? (int)$_GET['department_id'] : null;

        $db = Database::getConnection();
        $sql = "
            SELECT e.id, e.employee_code, e.full_name, d.name AS department_name,
                   COUNT(a.id) AS total_checkins,
                   SUM(CASE WHEN a.status = 'on_time' THEN 1 ELSE 0 END) AS total_on_time,
                   SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) AS total_late,
                   COALESCE(SUM(a.work_hours), 0) AS total_work_hours
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN attendance a ON e.id = a.employee_id AND MONTH(a.date) = :month AND YEAR(a.date) = :year
            WHERE e.deleted_at IS NULL
        ";
        $params = ['month' => $month, 'year' => $year];

        if ($deptId) {
            $sql .= " AND e.department_id = :dept_id";
            $params['dept_id'] = $deptId;
        }

        $sql .= " GROUP BY e.id ORDER BY d.id ASC, e.employee_code ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $attendanceReport = $stmt->fetchAll();

        // Xuất file CSV nếu có tham số export=1
        if (isset($_GET['export']) && $_GET['export'] == '1') {
            $filename = "Bao_Cao_Cham_Cong_T{$month}_{$year}.csv";
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($out, ['Mã NV', 'Họ và Tên', 'Phòng Ban', 'Kỳ Báo Cáo', 'Lượt Chấm Công', 'Đúng Giờ', 'Đi Muộn', 'Tổng Giờ Làm']);
            foreach ($attendanceReport as $row) {
                fputcsv($out, [
                    $row['employee_code'],
                    $row['full_name'],
                    $row['department_name'] ?? '-',
                    "Tháng {$month}/{$year}",
                    $row['total_checkins'],
                    $row['total_on_time'],
                    $row['total_late'],
                    $row['total_work_hours']
                ]);
            }
            fclose($out);
            exit;
        }

        $departments = $this->deptModel->getAll();

        $this->render('reports/attendance', [
            'pageTitle'        => "Báo Cáo Chấm Công Tháng {$month}/{$year}",
            'attendanceReport' => $attendanceReport,
            'departments'      => $departments,
            'month'            => $month,
            'year'             => $year,
            'departmentId'     => $deptId
        ], 'main');
    }

    /**
     * Báo cáo chuyên đề Nghỉ phép
     */
    public function leaves(): void {
        $this->requireRole(['admin', 'hr']);

        $year = (int)($_GET['year'] ?? date('Y'));

        $db = Database::getConnection();
        $sql = "
            SELECT e.id, e.employee_code, e.full_name, d.name AS department_name,
                   COUNT(lr.id) AS total_requests,
                   SUM(CASE WHEN lr.status = 'approved' THEN lr.total_days ELSE 0 END) AS approved_days,
                   SUM(CASE WHEN lr.leave_type = 'annual' AND lr.status = 'approved' THEN lr.total_days ELSE 0 END) AS annual_days,
                   SUM(CASE WHEN lr.leave_type = 'sick' AND lr.status = 'approved' THEN lr.total_days ELSE 0 END) AS sick_days,
                   SUM(CASE WHEN lr.leave_type = 'unpaid' AND lr.status = 'approved' THEN lr.total_days ELSE 0 END) AS unpaid_days
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN leave_requests lr ON e.id = lr.employee_id AND YEAR(lr.start_date) = :year
            WHERE e.deleted_at IS NULL
            GROUP BY e.id
            ORDER BY approved_days DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['year' => $year]);
        $leaveReport = $stmt->fetchAll();

        // Xuất file CSV
        if (isset($_GET['export']) && $_GET['export'] == '1') {
            $filename = "Bao_Cao_Nghi_Phep_Nam_{$year}.csv";
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Mã NV', 'Họ và Tên', 'Phòng Ban', 'Năm', 'Số Đơn Đã Gửi', 'Tổng Ngày Phép Đã Duyệt', 'Phép Năm', 'Nghỉ Ốm', 'Không Lương']);
            foreach ($leaveReport as $row) {
                fputcsv($out, [
                    $row['employee_code'],
                    $row['full_name'],
                    $row['department_name'] ?? '-',
                    $year,
                    $row['total_requests'],
                    $row['approved_days'] ?: 0,
                    $row['annual_days'] ?: 0,
                    $row['sick_days'] ?: 0,
                    $row['unpaid_days'] ?: 0
                ]);
            }
            fclose($out);
            exit;
        }

        $this->render('reports/leaves', [
            'pageTitle'   => "Báo Cáo Nghỉ Phép Năm {$year}",
            'leaveReport' => $leaveReport,
            'year'        => $year
        ], 'main');
    }
}

