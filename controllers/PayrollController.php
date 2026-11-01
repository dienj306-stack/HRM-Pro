<?php
/**
 * Controller quản lý Lương & Phiếu lương (salary_records)
 */

class PayrollController extends Controller {
    private SalaryRecord $salaryModel;
    private Department $deptModel;
    private Employee $empModel;

    public function __construct() {
        $this->salaryModel = new SalaryRecord();
        $this->deptModel = new Department();
        $this->empModel = new Employee();
    }

    /**
     * Bảng lương toàn công ty (Admin & HR)
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $month = (int)($_GET['month'] ?? date('m', strtotime('-1 month')));
        $year = (int)($_GET['year'] ?? date('Y'));
        $departmentId = !empty($_GET['department_id']) ? (int)$_GET['department_id'] : null;

        $salaries = $this->salaryModel->getAll($month, $year, $departmentId);
        $departments = $this->deptModel->getAll();

        // Tính tổng quỹ lương của kỳ
        $totalPayroll = 0;
        $totalPaid = 0;
        foreach ($salaries as $item) {
            $totalPayroll += (float)$item['net_salary'];
            if ($item['status'] === 'paid') {
                $totalPaid += (float)$item['net_salary'];
            }
        }

        $this->render('payroll/index', [
            'pageTitle'    => "Bảng Lương Tháng {$month}/{$year}",
            'salaries'     => $salaries,
            'departments'  => $departments,
            'month'        => $month,
            'year'         => $year,
            'departmentId' => $departmentId,
            'totalPayroll' => $totalPayroll,
            'totalPaid'    => $totalPaid
        ], 'main');
    }

    /**
     * Tự động sinh bảng lương tháng cho toàn bộ nhân sự (Admin & HR)
     */
    public function generate(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $month = (int)($_POST['month'] ?? date('m'));
        $year = (int)($_POST['year'] ?? date('Y'));
        $user = $this->currentUser();

        $count = $this->salaryModel->generatePayrollForMonth($month, $year, (int)$user['id']);

        $this->logAction('GENERATE_PAYROLL', 'salary_records', null, "Tự động khởi tạo bảng lương tháng {$month}/{$year} cho {$count} nhân viên");
        Session::setFlash('success', "Đã tạo/cập nhật bảng lương tháng {$month}/{$year} thành công cho {$count} nhân viên!");
        $this->redirect(Helper::url('payroll', 'index', ['month' => $month, 'year' => $year]));
    }

    /**
     * Giao diện chỉnh sửa thông tin lương của 1 nhân viên
     */
    public function edit(): void {
        $this->requireRole(['admin', 'hr']);

        $id = (int)($_GET['id'] ?? 0);
        $record = $this->salaryModel->findById($id);

        if (!$record) {
            Session::setFlash('danger', 'Không tìm thấy phiếu lương.');
            $this->redirect(Helper::url('payroll', 'index'));
        }

        $this->render('payroll/edit', [
            'pageTitle' => "Điều Chỉnh Lương: {$record['employee_name']} (Tháng {$record['month']}/{$record['year']})",
            'record'    => $record
        ], 'main');
    }

    /**
     * Cập nhật điều chỉnh lương
     */
    public function update(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $record = $this->salaryModel->findById($id);

        if (!$record) {
            Session::setFlash('danger', 'Không tìm thấy phiếu lương.');
            $this->redirect(Helper::url('payroll', 'index'));
        }

        $baseSalary = (float)str_replace(['.', ','], '', $_POST['base_salary'] ?? '0');
        $stdDays = (float)($_POST['standard_work_days'] ?? 22.0);
        $actDays = (float)($_POST['actual_work_days'] ?? 22.0);
        $allowance = (float)str_replace(['.', ','], '', $_POST['allowance'] ?? '0');
        $bonus = (float)str_replace(['.', ','], '', $_POST['bonus'] ?? '0');
        $deduction = (float)str_replace(['.', ','], '', $_POST['deduction'] ?? '0');
        $status = $_POST['status'] ?? 'draft';
        $notes = trim($_POST['notes'] ?? '');

        $this->salaryModel->save([
            'id'                 => $id,
            'employee_id'        => $record['employee_id'],
            'month'              => $record['month'],
            'year'               => $record['year'],
            'base_salary'        => $baseSalary,
            'standard_work_days' => $stdDays,
            'actual_work_days'   => $actDays,
            'allowance'          => $allowance,
            'bonus'              => $bonus,
            'deduction'          => $deduction,
            'status'             => $status,
            'notes'              => $notes
        ]);

        $this->logAction('UPDATE_SALARY', 'salary_records', $id, "Cập nhật bảng lương {$record['record_code']} cho {$record['employee_name']}");
        Session::setFlash('success', "Cập nhật lương nhân viên {$record['employee_name']} thành công!");
        $this->redirect(Helper::url('payroll', 'index', ['month' => $record['month'], 'year' => $record['year']]));
    }

    /**
     * Phê duyệt bảng lương (Admin & HR)
     */
    public function approve(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $this->salaryModel->updateStatus($id, 'approved');
        $this->logAction('APPROVE_SALARY', 'salary_records', $id, "Phê duyệt phiếu lương ID: {$id}");
        Session::setFlash('success', 'Đã phê duyệt phiếu lương.');

        $referer = $_SERVER['HTTP_REFERER'] ?? Helper::url('payroll', 'index');
        $this->redirect($referer);
    }

    /**
     * Đánh dấu đã chi trả lương (Admin & HR)
     */
    public function pay(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $this->salaryModel->updateStatus($id, 'paid');
        $this->logAction('PAY_SALARY', 'salary_records', $id, "Xác nhận đã thanh toán lương ID: {$id}");
        Session::setFlash('success', 'Đã xác nhận chi trả lương thành công!');

        $referer = $_SERVER['HTTP_REFERER'] ?? Helper::url('payroll', 'index');
        $this->redirect($referer);
    }

    /**
     * Xem phiếu lương cá nhân (Mọi nhân viên chỉ xem của chính mình)
     */
    public function myPayroll(): void {
        $this->requireAuth();
        $employeeId = $this->requireEmployee();

        $myPayslips = $this->salaryModel->getByEmployee($employeeId);

        $this->render('payroll/my_payroll', [
            'pageTitle'  => 'Phiếu Lương Của Tôi',
            'myPayslips' => $myPayslips
        ], 'main');
    }

    /**
     * Xuất danh sách bảng lương ra file CSV mở bằng Excel (UTF-8 BOM)
     */
    public function export(): void {
        $this->requireRole(['admin', 'hr']);

        $month = (int)($_GET['month'] ?? date('m', strtotime('-1 month')));
        $year = (int)($_GET['year'] ?? date('Y'));
        $salaries = $this->salaryModel->getAll($month, $year);

        $filename = "Bang_Luong_T{$month}_{$year}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Ghi Byte Order Mark (BOM) UTF-8 để Microsoft Excel trên Windows mở không bị lỗi font tiếng Việt
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Tiêu đề cột
        fputcsv($output, [
            'Mã Bảng Lương',
            'Mã NV',
            'Họ và Tên',
            'Phòng Ban',
            'Chức Vụ',
            'Kỳ Lương',
            'Lương Cơ Bản (VNĐ)',
            'Ngày Chuẩn',
            'Ngày Thực Tế',
            'Phụ Cấp (VNĐ)',
            'Thưởng (VNĐ)',
            'Giảm Trừ (VNĐ)',
            'Thực Nhận (VNĐ)',
            'Trạng Thái',
            'Ghi Chú'
        ]);

        foreach ($salaries as $s) {
            fputcsv($output, [
                $s['record_code'],
                $s['employee_code'],
                $s['employee_name'],
                $s['department_name'] ?? '-',
                $s['position_name'] ?? '-',
                "Tháng {$s['month']}/{$s['year']}",
                $s['base_salary'],
                $s['standard_work_days'],
                $s['actual_work_days'],
                $s['allowance'],
                $s['bonus'],
                $s['deduction'],
                $s['net_salary'],
                $s['status'],
                $s['notes'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }
}

