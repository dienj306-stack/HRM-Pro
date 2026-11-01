<?php
/**
 * Controller quản lý Chấm công (Attendance)
 */

class AttendanceController extends Controller {
    private Attendance $attendanceModel;
    private Department $departmentModel;

    public function __construct() {
        $this->attendanceModel = new Attendance();
        $this->departmentModel = new Department();
    }

    /**
     * Quản lý chấm công toàn công ty (Admin & HR)
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        $departmentId = $_GET['department_id'] ?? '';

        $filters = [
            'date'          => $selectedDate,
            'department_id' => $departmentId
        ];

        $attendanceList = $this->attendanceModel->getAllAttendance($filters);
        $todayStats = $this->attendanceModel->getTodayStats();
        $departments = $this->departmentModel->getAll();

        $this->render('attendance/index', [
            'pageTitle'      => 'Quản Lý Dữ Liệu Chấm Công',
            'attendanceList' => $attendanceList,
            'todayStats'     => $todayStats,
            'departments'    => $departments,
            'selectedDate'   => $selectedDate,
            'departmentId'   => $departmentId
        ], 'main');
    }

    /**
     * Xem lịch sử chấm công của chính mình (Tất cả nhân viên)
     */
    public function myAttendance(): void {
        $this->requireAuth();
        $employeeId = $this->requireEmployee();

        $month = (int)($_GET['month'] ?? date('m'));
        $year = (int)($_GET['year'] ?? date('Y'));

        $history = $this->attendanceModel->getEmployeeHistory($employeeId, $month, $year);
        $todayRecord = $this->attendanceModel->getRecordByDate($employeeId, date('Y-m-d'));
        $totalWorkedDays = $this->attendanceModel->countWorkDays($employeeId, $month, $year);

        $this->render('attendance/my_attendance', [
            'pageTitle'       => 'Lịch Sử Chấm Công Cá Nhân',
            'history'         => $history,
            'todayRecord'     => $todayRecord,
            'totalWorkedDays' => $totalWorkedDays,
            'selectedMonth'   => $month,
            'selectedYear'    => $year
        ], 'main');
    }

    /**
     * Thực hiện chấm công vào (Check-in)
     */
    public function checkIn(): void {
        $this->requireAuth();
        $this->validateCsrf();
        $employeeId = $this->requireEmployee();

        $notes = trim($_POST['notes'] ?? '');
        $result = $this->attendanceModel->checkIn($employeeId, $notes);

        if ($result['success']) {
            Session::setFlash('success', $result['message']);
            $this->logAction('CHECK_IN', 'attendance', $employeeId, $result['message']);
        } else {
            Session::setFlash('warning', $result['message']);
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? Helper::url('dashboard', 'index');
        $this->redirect($referer);
    }

    /**
     * Thực hiện chấm công ra (Check-out)
     */
    public function checkOut(): void {
        $this->requireAuth();
        $this->validateCsrf();
        $employeeId = $this->requireEmployee();

        $notes = trim($_POST['notes'] ?? '');
        $result = $this->attendanceModel->checkOut($employeeId, $notes);

        if ($result['success']) {
            Session::setFlash('success', $result['message']);
            $this->logAction('CHECK_OUT', 'attendance', $employeeId, $result['message']);
        } else {
            Session::setFlash('warning', $result['message']);
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? Helper::url('dashboard', 'index');
        $this->redirect($referer);
    }
}

