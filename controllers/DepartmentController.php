<?php
/**
 * Controller quản lý Phòng ban (departments)
 */

class DepartmentController extends Controller {
    private Department $deptModel;
    private Employee $empModel;

    public function __construct() {
        $this->deptModel = new Department();
        $this->empModel = new Employee();
    }

    /**
     * Danh sách phòng ban
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $departments = $this->deptModel->getAll();
        $this->render('departments/index', [
            'pageTitle'   => 'Quản Lý Phòng Ban',
            'departments' => $departments
        ], 'main');
    }

    /**
     * Giao diện thêm mới phòng ban
     */
    public function create(): void {
        $this->requireRole(['admin', 'hr']);

        $employees = $this->empModel->getActiveEmployees();
        $this->render('departments/create', [
            'pageTitle' => 'Thêm Mới Phòng Ban',
            'employees' => $employees
        ], 'main');
    }

    /**
     * Lưu thông tin phòng ban mới
     */
    public function store(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $managerId = $_POST['manager_id'] ?? null;
        $status = $_POST['status'] ?? 'active';

        if (empty($code) || empty($name)) {
            Session::setFlash('danger', 'Vui lòng điền mã và tên phòng ban.');
            $this->redirect(Helper::url('departments', 'create'));
        }

        if ($this->deptModel->isCodeExists($code)) {
            Session::setFlash('danger', "Mã phòng ban [{$code}] đã tồn tại.");
            $this->redirect(Helper::url('departments', 'create'));
        }

        $id = $this->deptModel->create([
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
            'manager_id'  => $managerId,
            'status'      => $status
        ]);

        $this->logAction('CREATE_DEPARTMENT', 'departments', $id, "Thêm mới phòng ban [{$code}] {$name}");
        Session::setFlash('success', "Thêm phòng ban [{$name}] thành công!");
        $this->redirect(Helper::url('departments', 'index'));
    }

    /**
     * Giao diện sửa phòng ban
     */
    public function edit(): void {
        $this->requireRole(['admin', 'hr']);

        $id = (int)($_GET['id'] ?? 0);
        $department = $this->deptModel->findById($id);

        if (!$department) {
            Session::setFlash('danger', 'Không tìm thấy phòng ban.');
            $this->redirect(Helper::url('departments', 'index'));
        }

        $employees = $this->empModel->getActiveEmployees();

        $this->render('departments/edit', [
            'pageTitle'  => 'Sửa Phòng Ban: ' . $department['name'],
            'department' => $department,
            'employees'  => $employees
        ], 'main');
    }

    /**
     * Cập nhật thông tin phòng ban
     */
    public function update(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $department = $this->deptModel->findById($id);

        if (!$department) {
            Session::setFlash('danger', 'Không tìm thấy phòng ban.');
            $this->redirect(Helper::url('departments', 'index'));
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $managerId = $_POST['manager_id'] ?? null;
        $status = $_POST['status'] ?? 'active';

        if (empty($code) || empty($name)) {
            Session::setFlash('danger', 'Vui lòng điền mã và tên phòng ban.');
            $this->redirect(Helper::url('departments', 'edit', ['id' => $id]));
        }

        if ($this->deptModel->isCodeExists($code, $id)) {
            Session::setFlash('danger', "Mã phòng ban [{$code}] đã trùng với phòng ban khác.");
            $this->redirect(Helper::url('departments', 'edit', ['id' => $id]));
        }

        $this->deptModel->update($id, [
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
            'manager_id'  => $managerId,
            'status'      => $status
        ]);

        $this->logAction('UPDATE_DEPARTMENT', 'departments', $id, "Cập nhật phòng ban [{$code}] {$name}");
        Session::setFlash('success', "Cập nhật phòng ban [{$name}] thành công!");
        $this->redirect(Helper::url('departments', 'index'));
    }

    /**
     * Xóa phòng ban (Ngăn xóa nếu đang có nhân viên)
     */
    public function delete(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $empCount = $this->deptModel->countEmployees($id);

        if ($empCount > 0) {
            Session::setFlash('danger', "Không thể xóa phòng ban này vì hiện có {$empCount} nhân viên đang trực thuộc. Vui lòng chuyển nhân viên sang phòng ban khác trước.");
            $this->redirect(Helper::url('departments', 'index'));
        }

        $this->deptModel->delete($id);
        $this->logAction('DELETE_DEPARTMENT', 'departments', $id, "Xóa phòng ban ID: {$id}");
        Session::setFlash('success', 'Đã xóa phòng ban thành công.');
        $this->redirect(Helper::url('departments', 'index'));
    }
}

