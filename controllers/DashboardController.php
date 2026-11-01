<?php
/**
 * Controller hiển thị Bảng điều khiển (Dashboard)
 * Tự động phân nhánh giao diện theo vai trò (Admin/HR vs Employee)
 */

class DashboardController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $user = $this->currentUser();

        // Nếu là vai trò Nhân viên (Employee), điều hướng đến Dashboard cá nhân
        if ($user['role_name'] === 'employee') {
            $this->employeeDashboard();
            return;
        }

        // Với Admin và HR, lấy số liệu thống kê thực tế từ Database
        $employeeModel = new Employee();
        $departmentModel = new Department();
        $positionModel = new Position();
        $attendanceModel = new Attendance();
        $leaveModel = new LeaveRequest();

        $empStats = $employeeModel->getOverviewStats();
        $departments = $departmentModel->getAll();
        $positions = $positionModel->getAll();
        $attendanceToday = $attendanceModel->getTodayStats();
        $pendingLeaves = $leaveModel->countPending();
        $deptDistribution = $employeeModel->getStatsByDepartment();

        // 5 đơn nghỉ phép gần nhất
        $recentLeaves = $leaveModel->getAll(['limit' => 5]);
        $recentLeaves = array_slice($recentLeaves, 0, 5);

        // 5 lượt chấm công hôm nay
        $todayAttendanceList = $attendanceModel->getAllAttendance(['date' => date('Y-m-d')]);
        $todayAttendanceList = array_slice($todayAttendanceList, 0, 5);

        $this->render('dashboard/index', [
            'pageTitle'           => 'Bảng Điều Khiển Quản Trị',
            'user'                => $user,
            'empStats'            => $empStats,
            'totalDepartments'    => count($departments),
            'totalPositions'      => count($positions),
            'attendanceToday'     => $attendanceToday,
            'pendingLeaves'       => $pendingLeaves,
            'deptDistribution'    => $deptDistribution,
            'recentLeaves'        => $recentLeaves,
            'todayAttendanceList' => $todayAttendanceList
        ], 'main');
    }

    /**
     * Dashboard dành riêng cho nhân viên
     */
    private function employeeDashboard(): void {
        $user = $this->currentUser();
        $employeeId = (int)$user['employee_id'];

        $employeeModel = new Employee();
        $attendanceModel = new Attendance();
        $leaveModel = new LeaveRequest();
        $salaryModel = new SalaryRecord();
        $notifModel = new Notification();

        $employee = $employeeModel->findById($employeeId);
        $todayRecord = $attendanceModel->getRecordByDate($employeeId, date('Y-m-d'));
        $myLeaves = array_slice($leaveModel->getByEmployee($employeeId), 0, 5);
        $myPayslips = array_slice($salaryModel->getByEmployee($employeeId), 0, 3);
        $notifications = $notifModel->getForUser((int)$user['id'], 5);

        // Số ngày công trong tháng này
        $currentMonthDays = $attendanceModel->countWorkDays($employeeId, (int)date('m'), (int)date('Y'));

        $this->render('dashboard/employee', [
            'pageTitle'         => 'Bảng Điều Khiển Cá Nhân',
            'user'              => $user,
            'employee'          => $employee,
            'todayRecord'       => $todayRecord,
            'myLeaves'          => $myLeaves,
            'myPayslips'        => $myPayslips,
            'notifications'     => $notifications,
            'currentMonthDays'  => $currentMonthDays
        ], 'main');
    }
}

