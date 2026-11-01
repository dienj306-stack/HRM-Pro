<?php
/**
 * Controller quản lý Hồ sơ Nhân viên (Admin & HR)
 */

class EmployeeController extends Controller {
    private Employee $employeeModel;
    private Department $departmentModel;
    private Position $positionModel;

    public function __construct() {
        $this->employeeModel = new Employee();
        $this->departmentModel = new Department();
        $this->positionModel = new Position();
    }

    /**
     * Danh sách nhân viên có tìm kiếm, lọc và phân trang
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;

        $filters = [
            'keyword'       => trim($_GET['keyword'] ?? ''),
            'department_id' => $_GET['department_id'] ?? '',
            'status'        => $_GET['status'] ?? ''
        ];

        $employees = $this->employeeModel->getAll($filters, $page, $limit);
        $totalRecords = $this->employeeModel->countAll($filters);
        $totalPages = max(1, ceil($totalRecords / $limit));

        $departments = $this->departmentModel->getAll();

        $this->render('employees/index', [
            'pageTitle'    => 'Quản Lý Hồ Sơ Nhân Viên',
            'employees'    => $employees,
            'departments'  => $departments,
            'filters'      => $filters,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords
        ], 'main');
    }

    /**
     * Xem chi tiết hồ sơ nhân viên
     */
    public function show(): void {
        $this->requireRole(['admin', 'hr']);

        $id = (int)($_GET['id'] ?? 0);
        $employee = $this->employeeModel->findById($id);

        if (!$employee) {
            Session::setFlash('danger', 'Không tìm thấy hồ sơ nhân viên yêu cầu.');
            $this->redirect(Helper::url('employees', 'index'));
        }

        $contractModel = new Contract();
        $contracts = $contractModel->getByEmployee($id);

        $attendanceModel = new Attendance();
        $recentAttendance = $attendanceModel->getEmployeeHistory($id, (int)date('m'), (int)date('Y'));

        $salaryModel = new SalaryRecord();
        $payslips = $salaryModel->getByEmployee($id);

        $this->render('employees/show', [
            'pageTitle'        => 'Hồ Sơ: ' . $employee['full_name'],
            'employee'         => $employee,
            'contracts'        => $contracts,
            'recentAttendance' => $recentAttendance,
            'payslips'         => $payslips
        ], 'main');
    }

    /**
     * Giao diện thêm mới nhân viên
     */
    public function create(): void {
        $this->requireRole(['admin', 'hr']);

        $departments = $this->departmentModel->getAll();
        $positions = $this->positionModel->getAll();

        // Tự động gợi ý mã nhân viên kế tiếp (VD: NV006)
        $total = $this->employeeModel->countAll();
        $suggestedCode = sprintf('NV%03d', $total + 1);

        $this->render('employees/create', [
            'pageTitle'     => 'Thêm Mới Nhân Viên',
            'departments'   => $departments,
            'positions'     => $positions,
            'suggestedCode' => $suggestedCode
        ], 'main');
    }

    /**
     * Lưu thông tin nhân viên mới
     */
    public function store(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $code = strtoupper(trim($_POST['employee_code'] ?? ''));
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? 'male';
        $dob = $_POST['date_of_birth'] ?? null;
        $hireDate = $_POST['hire_date'] ?? date('Y-m-d');
        $deptId = $_POST['department_id'] ?? null;
        $posId = $_POST['position_id'] ?? null;
        $baseSalary = (float)str_replace(['.', ','], '', $_POST['base_salary'] ?? '0');
        $status = $_POST['status'] ?? 'active';
        $address = trim($_POST['address'] ?? '');

        // Kiểm tra dữ liệu đầu vào
        if (empty($code) || empty($firstName) || empty($lastName) || empty($email)) {
            Session::setFlash('danger', 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).');
            $this->redirect(Helper::url('employees', 'create'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('danger', 'Địa chỉ email không đúng định dạng hợp lệ.');
            $this->redirect(Helper::url('employees', 'create'));
        }

        if ($this->employeeModel->isCodeExists($code)) {
            Session::setFlash('danger', "Mã nhân viên [{$code}] đã tồn tại trong hệ thống.");
            $this->redirect(Helper::url('employees', 'create'));
        }

        if ($this->employeeModel->isEmailExists($email)) {
            Session::setFlash('danger', "Email [{$email}] đã được đăng ký bởi nhân viên khác.");
            $this->redirect(Helper::url('employees', 'create'));
        }

        $id = $this->employeeModel->create([
            'employee_code' => $code,
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'gender'        => $gender,
            'date_of_birth' => $dob,
            'email'         => $email,
            'phone'         => $phone,
            'address'       => $address,
            'department_id' => $deptId,
            'position_id'   => $posId,
            'hire_date'     => $hireDate,
            'base_salary'   => $baseSalary,
            'status'        => $status
        ]);

        $this->logAction('CREATE_EMPLOYEE', 'employees', $id, "Tạo hồ sơ nhân viên mới [{$code}] - {$lastName} {$firstName}");
        Session::setFlash('success', "Thêm nhân viên [{$code}] thành công!");
        $this->redirect(Helper::url('employees', 'show', ['id' => $id]));
    }

    /**
     * Giao diện chỉnh sửa thông tin nhân viên
     */
    public function edit(): void {
        $this->requireRole(['admin', 'hr']);

        $id = (int)($_GET['id'] ?? 0);
        $employee = $this->employeeModel->findById($id);

        if (!$employee) {
            Session::setFlash('danger', 'Không tìm thấy hồ sơ nhân viên.');
            $this->redirect(Helper::url('employees', 'index'));
        }

        $departments = $this->departmentModel->getAll();
        $positions = $this->positionModel->getAll();

        $this->render('employees/edit', [
            'pageTitle'   => 'Sửa Hồ Sơ: ' . $employee['full_name'],
            'employee'    => $employee,
            'departments' => $departments,
            'positions'   => $positions
        ], 'main');
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $employee = $this->employeeModel->findById($id);

        if (!$employee) {
            Session::setFlash('danger', 'Không tìm thấy nhân viên cần sửa.');
            $this->redirect(Helper::url('employees', 'index'));
        }

        $code = strtoupper(trim($_POST['employee_code'] ?? ''));
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? 'male';
        $dob = $_POST['date_of_birth'] ?? null;
        $hireDate = $_POST['hire_date'] ?? date('Y-m-d');
        $deptId = $_POST['department_id'] ?? null;
        $posId = $_POST['position_id'] ?? null;
        $baseSalary = (float)str_replace(['.', ','], '', $_POST['base_salary'] ?? '0');
        $status = $_POST['status'] ?? 'active';
        $address = trim($_POST['address'] ?? '');

        if (empty($code) || empty($firstName) || empty($lastName) || empty($email)) {
            Session::setFlash('danger', 'Vui lòng nhập đầy đủ các trường bắt buộc (*).');
            $this->redirect(Helper::url('employees', 'edit', ['id' => $id]));
        }

        if ($this->employeeModel->isCodeExists($code, $id)) {
            Session::setFlash('danger', "Mã nhân viên [{$code}] đã trùng với nhân viên khác.");
            $this->redirect(Helper::url('employees', 'edit', ['id' => $id]));
        }

        if ($this->employeeModel->isEmailExists($email, $id)) {
            Session::setFlash('danger', "Email [{$email}] đã tồn tại trong hệ thống.");
            $this->redirect(Helper::url('employees', 'edit', ['id' => $id]));
        }

        $this->employeeModel->update($id, [
            'employee_code' => $code,
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'gender'        => $gender,
            'date_of_birth' => $dob,
            'email'         => $email,
            'phone'         => $phone,
            'address'       => $address,
            'department_id' => $deptId,
            'position_id'   => $posId,
            'hire_date'     => $hireDate,
            'base_salary'   => $baseSalary,
            'status'        => $status
        ]);

        $this->logAction('UPDATE_EMPLOYEE', 'employees', $id, "Cập nhật hồ sơ nhân viên [{$code}]");
        Session::setFlash('success', 'Cập nhật thông tin nhân viên thành công!');
        $this->redirect(Helper::url('employees', 'show', ['id' => $id]));
    }

    /**
     * Ngừng hoạt động / Xóa mềm hồ sơ nhân viên
     */
    public function delete(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $employee = $this->employeeModel->findById($id);

        if ($employee) {
            $this->employeeModel->softDelete($id);
            $this->logAction('DEACTIVATE_EMPLOYEE', 'employees', $id, "Ngừng hoạt động nhân viên [{$employee['employee_code']}] - {$employee['full_name']}");
            Session::setFlash('success', "Đã chuyển trạng thái nhân viên [{$employee['full_name']}] sang Thôi việc & khóa tài khoản.");
        } else {
            Session::setFlash('danger', 'Không tìm thấy hồ sơ nhân viên.');
        }

        $this->redirect(Helper::url('employees', 'index'));
    }
}

