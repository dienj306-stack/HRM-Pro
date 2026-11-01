<?php
/**
 * Controller quản lý Hợp đồng lao động (contracts)
 */

class ContractController extends Controller {
    private Contract $contractModel;
    private Employee $empModel;

    public function __construct() {
        $this->contractModel = new Contract();
        $this->empModel = new Employee();
    }

    /**
     * Danh sách hợp đồng
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $filters = [
            'status'        => $_GET['status'] ?? '',
            'contract_type' => $_GET['contract_type'] ?? '',
            'employee_id'   => $_GET['employee_id'] ?? ''
        ];

        $contracts = $this->contractModel->getAll($filters);
        $employees = $this->empModel->getActiveEmployees();

        $this->render('contracts/index', [
            'pageTitle' => 'Quản Lý Hợp Đồng Lao Động',
            'contracts' => $contracts,
            'employees' => $employees,
            'filters'   => $filters
        ], 'main');
    }

    /**
     * Giao diện tạo hợp đồng mới
     */
    public function create(): void {
        $this->requireRole(['admin', 'hr']);

        $employees = $this->empModel->getActiveEmployees();
        $suggestedCode = 'HD-' . date('Y') . '-' . sprintf('%03d', count($this->contractModel->getAll()) + 1);

        $this->render('contracts/create', [
            'pageTitle'     => 'Tạo Mới Hợp Đồng Lao Động',
            'employees'     => $employees,
            'suggestedCode' => $suggestedCode
        ], 'main');
    }

    /**
     * Lưu thông tin hợp đồng mới
     */
    public function store(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $code = strtoupper(trim($_POST['contract_code'] ?? ''));
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $type = $_POST['contract_type'] ?? 'definite';
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? null;
        $basicSalary = (float)str_replace(['.', ','], '', $_POST['basic_salary'] ?? '0');
        $insuranceSalary = (float)str_replace(['.', ','], '', $_POST['insurance_salary'] ?? '0');
        $status = $_POST['status'] ?? 'active';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($code) || empty($employeeId) || empty($startDate)) {
            Session::setFlash('danger', 'Vui lòng điền mã hợp đồng, chọn nhân viên và ngày bắt đầu.');
            $this->redirect(Helper::url('contracts', 'create'));
        }

        if ($this->contractModel->isCodeExists($code)) {
            Session::setFlash('danger', "Mã hợp đồng [{$code}] đã tồn tại trong hệ thống.");
            $this->redirect(Helper::url('contracts', 'create'));
        }

        if (!empty($endDate) && $startDate > $endDate) {
            Session::setFlash('danger', 'Ngày bắt đầu hợp đồng không thể lớn hơn ngày kết thúc.');
            $this->redirect(Helper::url('contracts', 'create'));
        }

        $id = $this->contractModel->create([
            'contract_code'    => $code,
            'employee_id'      => $employeeId,
            'contract_type'    => $type,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'basic_salary'     => $basicSalary,
            'insurance_salary' => $insuranceSalary,
            'status'           => $status,
            'notes'            => $notes
        ]);

        $this->logAction('CREATE_CONTRACT', 'contracts', $id, "Tạo hợp đồng [{$code}] cho nhân viên ID: {$employeeId}");
        Session::setFlash('success', "Tạo hợp đồng [{$code}] thành công!");
        $this->redirect(Helper::url('contracts', 'index'));
    }

    /**
     * Giao diện chỉnh sửa hợp đồng
     */
    public function edit(): void {
        $this->requireRole(['admin', 'hr']);

        $id = (int)($_GET['id'] ?? 0);
        $contract = $this->contractModel->findById($id);

        if (!$contract) {
            Session::setFlash('danger', 'Không tìm thấy hợp đồng.');
            $this->redirect(Helper::url('contracts', 'index'));
        }

        $employees = $this->empModel->getActiveEmployees();

        $this->render('contracts/edit', [
            'pageTitle' => 'Sửa Hợp Đồng: ' . $contract['contract_code'],
            'contract'  => $contract,
            'employees' => $employees
        ], 'main');
    }

    /**
     * Cập nhật thông tin hợp đồng
     */
    public function update(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $contract = $this->contractModel->findById($id);

        if (!$contract) {
            Session::setFlash('danger', 'Không tìm thấy hợp đồng.');
            $this->redirect(Helper::url('contracts', 'index'));
        }

        $code = strtoupper(trim($_POST['contract_code'] ?? ''));
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $type = $_POST['contract_type'] ?? 'definite';
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? null;
        $basicSalary = (float)str_replace(['.', ','], '', $_POST['basic_salary'] ?? '0');
        $insuranceSalary = (float)str_replace(['.', ','], '', $_POST['insurance_salary'] ?? '0');
        $status = $_POST['status'] ?? 'active';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($code) || empty($employeeId) || empty($startDate)) {
            Session::setFlash('danger', 'Vui lòng điền mã hợp đồng, chọn nhân viên và ngày bắt đầu.');
            $this->redirect(Helper::url('contracts', 'edit', ['id' => $id]));
        }

        if ($this->contractModel->isCodeExists($code, $id)) {
            Session::setFlash('danger', "Mã hợp đồng [{$code}] đã trùng với hợp đồng khác.");
            $this->redirect(Helper::url('contracts', 'edit', ['id' => $id]));
        }

        $this->contractModel->update($id, [
            'contract_code'    => $code,
            'employee_id'      => $employeeId,
            'contract_type'    => $type,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'basic_salary'     => $basicSalary,
            'insurance_salary' => $insuranceSalary,
            'status'           => $status,
            'notes'            => $notes
        ]);

        $this->logAction('UPDATE_CONTRACT', 'contracts', $id, "Cập nhật hợp đồng [{$code}]");
        Session::setFlash('success', "Cập nhật hợp đồng [{$code}] thành công!");
        $this->redirect(Helper::url('contracts', 'index'));
    }

    /**
     * Xóa hợp đồng
     */
    public function delete(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $this->contractModel->delete($id);
        $this->logAction('DELETE_CONTRACT', 'contracts', $id, "Xóa hợp đồng ID: {$id}");
        Session::setFlash('success', 'Đã xóa hợp đồng thành công.');
        $this->redirect(Helper::url('contracts', 'index'));
    }
}

