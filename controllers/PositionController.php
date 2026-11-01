<?php
/**
 * Controller quản lý Chức vụ (positions)
 */

class PositionController extends Controller {
    private Position $posModel;

    public function __construct() {
        $this->posModel = new Position();
    }

    /**
     * Danh sách chức vụ
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $positions = $this->posModel->getAll();
        $this->render('positions/index', [
            'pageTitle' => 'Quản Lý Chức Vụ',
            'positions' => $positions
        ], 'main');
    }

    /**
     * Giao diện thêm mới chức vụ
     */
    public function create(): void {
        $this->requireRole(['admin', 'hr']);

        $this->render('positions/create', [
            'pageTitle' => 'Thêm Mới Chức Vụ'
        ], 'main');
    }

    /**
     * Lưu thông tin chức vụ mới
     */
    public function store(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $minSalary = (float)str_replace(['.', ','], '', $_POST['base_salary_min'] ?? '0');
        $maxSalary = (float)str_replace(['.', ','], '', $_POST['base_salary_max'] ?? '0');
        $status = $_POST['status'] ?? 'active';

        if (empty($code) || empty($name)) {
            Session::setFlash('danger', 'Vui lòng điền mã và tên chức vụ.');
            $this->redirect(Helper::url('positions', 'create'));
        }

        if ($this->posModel->isCodeExists($code)) {
            Session::setFlash('danger', "Mã chức vụ [{$code}] đã tồn tại.");
            $this->redirect(Helper::url('positions', 'create'));
        }

        $id = $this->posModel->create([
            'code'            => $code,
            'name'            => $name,
            'description'     => $description,
            'base_salary_min' => $minSalary,
            'base_salary_max' => $maxSalary,
            'status'          => $status
        ]);

        $this->logAction('CREATE_POSITION', 'positions', $id, "Thêm mới chức vụ [{$code}] {$name}");
        Session::setFlash('success', "Thêm chức vụ [{$name}] thành công!");
        $this->redirect(Helper::url('positions', 'index'));
    }

    /**
     * Giao diện sửa chức vụ
     */
    public function edit(): void {
        $this->requireRole(['admin', 'hr']);

        $id = (int)($_GET['id'] ?? 0);
        $position = $this->posModel->findById($id);

        if (!$position) {
            Session::setFlash('danger', 'Không tìm thấy chức vụ.');
            $this->redirect(Helper::url('positions', 'index'));
        }

        $this->render('positions/edit', [
            'pageTitle' => 'Sửa Chức Vụ: ' . $position['name'],
            'position'  => $position
        ], 'main');
    }

    /**
     * Cập nhật thông tin chức vụ
     */
    public function update(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $position = $this->posModel->findById($id);

        if (!$position) {
            Session::setFlash('danger', 'Không tìm thấy chức vụ.');
            $this->redirect(Helper::url('positions', 'index'));
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $minSalary = (float)str_replace(['.', ','], '', $_POST['base_salary_min'] ?? '0');
        $maxSalary = (float)str_replace(['.', ','], '', $_POST['base_salary_max'] ?? '0');
        $status = $_POST['status'] ?? 'active';

        if (empty($code) || empty($name)) {
            Session::setFlash('danger', 'Vui lòng điền mã và tên chức vụ.');
            $this->redirect(Helper::url('positions', 'edit', ['id' => $id]));
        }

        if ($this->posModel->isCodeExists($code, $id)) {
            Session::setFlash('danger', "Mã chức vụ [{$code}] đã trùng với chức vụ khác.");
            $this->redirect(Helper::url('positions', 'edit', ['id' => $id]));
        }

        $this->posModel->update($id, [
            'code'            => $code,
            'name'            => $name,
            'description'     => $description,
            'base_salary_min' => $minSalary,
            'base_salary_max' => $maxSalary,
            'status'          => $status
        ]);

        $this->logAction('UPDATE_POSITION', 'positions', $id, "Cập nhật chức vụ [{$code}] {$name}");
        Session::setFlash('success', "Cập nhật chức vụ [{$name}] thành công!");
        $this->redirect(Helper::url('positions', 'index'));
    }

    /**
     * Xóa chức vụ nếu không có nhân viên nào đang giữ
     */
    public function delete(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $empCount = $this->posModel->countEmployees($id);

        if ($empCount > 0) {
            Session::setFlash('danger', "Không thể xóa chức vụ này vì có {$empCount} nhân viên đang giữ chức vụ. Vui lòng chuyển chức vụ của nhân viên trước.");
            $this->redirect(Helper::url('positions', 'index'));
        }

        $this->posModel->delete($id);
        $this->logAction('DELETE_POSITION', 'positions', $id, "Xóa chức vụ ID: {$id}");
        Session::setFlash('success', 'Đã xóa chức vụ thành công.');
        $this->redirect(Helper::url('positions', 'index'));
    }
}

