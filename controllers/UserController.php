<?php
/**
 * Controller quản lý Tài khoản người dùng (Chỉ dành cho Admin)
 */

class UserController extends Controller {
    private User $userModel;
    private Employee $empModel;

    public function __construct() {
        $this->userModel = new User();
        $this->empModel = new Employee();
    }

    /**
     * Danh sách tài khoản người dùng
     */
    public function index(): void {
        $this->requireRole('admin');

        $users = $this->userModel->getAll();
        $roles = $this->userModel->getRoles();
        $unlinkedEmployees = $this->empModel->getEmployeesWithoutUser();

        $this->render('users/index', [
            'pageTitle'         => 'Quản Lý Tài Khoản Người Dùng',
            'users'             => $users,
            'roles'             => $roles,
            'unlinkedEmployees' => $unlinkedEmployees
        ], 'main');
    }

    /**
     * Tạo tài khoản mới cho nhân viên
     */
    public function store(): void {
        $this->requireRole('admin');
        $this->validateCsrf();

        $username = strtolower(trim($_POST['username'] ?? ''));
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 3);
        $employeeId = !empty($_POST['employee_id']) ? (int)$_POST['employee_id'] : null;

        if (empty($username) || empty($email) || empty($password)) {
            Session::setFlash('danger', 'Vui lòng điền đầy đủ tên đăng nhập, email và mật khẩu.');
            $this->redirect(Helper::url('users', 'index'));
        }

        if (strlen($password) < 6) {
            Session::setFlash('danger', 'Mật khẩu phải có độ dài từ 6 ký tự trở lên.');
            $this->redirect(Helper::url('users', 'index'));
        }

        if ($this->userModel->isUsernameExists($username)) {
            Session::setFlash('danger', "Tên đăng nhập [{$username}] đã tồn tại.");
            $this->redirect(Helper::url('users', 'index'));
        }

        if ($this->userModel->isEmailExists($email)) {
            Session::setFlash('danger', "Email [{$email}] đã được đăng ký tài khoản khác.");
            $this->redirect(Helper::url('users', 'index'));
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $id = $this->userModel->create([
            'username'      => $username,
            'email'         => $email,
            'password_hash' => $passwordHash,
            'role_id'       => $roleId,
            'employee_id'   => $employeeId,
            'status'        => 'active'
        ]);

        $this->logAction('CREATE_USER', 'users', $id, "Admin tạo tài khoản [{$username}] với vai trò ID: {$roleId}");
        Session::setFlash('success', "Tạo tài khoản [{$username}] thành công!");
        $this->redirect(Helper::url('users', 'index'));
    }

    /**
     * Khóa hoặc mở khóa tài khoản
     */
    public function toggleStatus(): void {
        $this->requireRole('admin');
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $currentUser = $this->currentUser();

        // Không cho phép tự khóa tài khoản của chính mình
        if ($id === (int)$currentUser['id']) {
            Session::setFlash('danger', 'Bạn không thể tự khóa tài khoản quản trị đang đăng nhập của chính mình.');
            $this->redirect(Helper::url('users', 'index'));
        }

        $newStatus = $this->userModel->toggleStatus($id);
        $statusText = ($newStatus === 'locked') ? 'Khóa' : 'Mở khóa';

        $this->logAction('TOGGLE_USER_STATUS', 'users', $id, "Admin {$statusText} tài khoản ID: {$id}");
        Session::setFlash('success', "Đã {$statusText} tài khoản thành công.");
        $this->redirect(Helper::url('users', 'index'));
    }

    /**
     * Đặt lại mật khẩu cho tài khoản
     */
    public function resetPassword(): void {
        $this->requireRole('admin');
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';

        if (strlen($newPassword) < 6) {
            Session::setFlash('danger', 'Mật khẩu mới phải từ 6 ký tự trở lên.');
            $this->redirect(Helper::url('users', 'index'));
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userModel->updatePassword($id, $newHash);

        $this->logAction('RESET_USER_PASSWORD', 'users', $id, "Admin đặt lại mật khẩu cho tài khoản ID: {$id}");
        Session::setFlash('success', "Đã đặt lại mật khẩu mới thành công cho tài khoản ID: {$id}.");
        $this->redirect(Helper::url('users', 'index'));
    }
}

