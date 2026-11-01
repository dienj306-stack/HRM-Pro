<?php
/**
 * Controller xử lý Đăng nhập, Đăng xuất, Đổi mật khẩu & Hồ sơ cá nhân
 */

class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Màn hình đăng nhập
     */
    public function login(): void {
        if (Session::has('user')) {
            $this->redirect(Helper::url('dashboard', 'index'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();

            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($login) || empty($password)) {
                Session::setFlash('danger', 'Vui lòng nhập tên đăng nhập/email và mật khẩu.');
                $this->render('auth/login', ['login' => $login], 'auth');
                return;
            }

            $user = $this->userModel->findByLogin($login);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                Session::setFlash('danger', 'Tên đăng nhập hoặc mật khẩu không chính xác.');
                $this->render('auth/login', ['login' => $login], 'auth');
                return;
            }

            if ($user['status'] === 'locked') {
                Session::setFlash('danger', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Quản trị viên.');
                $this->render('auth/login', ['login' => $login], 'auth');
                return;
            }

            // Tạo session mới để chống Session Fixation
            Session::regenerate();

            // Lưu thông tin đăng nhập vào session (không lưu hash mật khẩu)
            unset($user['password_hash']);
            Session::set('user', $user);

            // Cập nhật thời điểm đăng nhập cuối
            $this->userModel->updateLastLogin((int)$user['id']);

            // Ghi nhật ký kiểm toán
            $this->logAction('LOGIN', 'users', (int)$user['id'], "Người dùng [{$user['username']}] đăng nhập thành công");

            Session::setFlash('success', 'Chào mừng ' . ($user['employee_name'] ?: $user['username']) . ' quay trở lại!');
            $this->redirect(Helper::url('dashboard', 'index'));
        }

        $this->render('auth/login', [], 'auth');
    }

    /**
     * Đăng xuất khỏi hệ thống
     */
    public function logout(): void {
        $user = $this->currentUser();
        if ($user) {
            $this->logAction('LOGOUT', 'users', (int)$user['id'], "Người dùng [{$user['username']}] đăng xuất");
        }

        Session::destroy();
        session_start();
        Session::setFlash('info', 'Bạn đã đăng xuất an toàn khỏi hệ thống.');
        $this->redirect(Helper::url('auth', 'login'));
    }

    /**
     * Xem hồ sơ cá nhân và cập nhật thông tin được phép
     */
    public function profile(): void {
        $this->requireAuth();
        $user = $this->currentUser();

        $employeeModel = new Employee();
        $employee = null;
        if (!empty($user['employee_id'])) {
            $employee = $employeeModel->findById((int)$user['employee_id']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();

            if ($employee) {
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');

                // Cập nhật số điện thoại và địa chỉ cho nhân viên
                $db = Database::getConnection();
                $stmt = $db->prepare("UPDATE employees SET phone = :phone, address = :address, updated_at = NOW() WHERE id = :id");
                $stmt->execute([
                    'phone'   => $phone,
                    'address' => $address,
                    'id'      => $employee['id']
                ]);

                $this->logAction('UPDATE_PROFILE', 'employees', $employee['id'], "Cập nhật thông tin liên hệ cá nhân");
                Session::setFlash('success', 'Cập nhật thông tin hồ sơ thành công.');
                $this->redirect(Helper::url('auth', 'profile'));
            }
        }

        $this->render('auth/profile', [
            'pageTitle' => 'Hồ Sơ Cá Nhân',
            'user'      => $user,
            'employee'  => $employee
        ], 'main');
    }

    /**
     * Đổi mật khẩu cá nhân
     */
    public function changePassword(): void {
        $this->requireAuth();
        $user = $this->currentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Kiểm tra mật khẩu hiện tại trong DB
            $dbUser = $this->userModel->findById((int)$user['id']);
            if (!password_verify($currentPassword, $dbUser['password_hash'])) {
                Session::setFlash('danger', 'Mật khẩu hiện tại không đúng.');
                $this->redirect(Helper::url('auth', 'changePassword'));
            }

            if (strlen($newPassword) < 6) {
                Session::setFlash('danger', 'Mật khẩu mới phải có độ dài tối thiểu 6 ký tự.');
                $this->redirect(Helper::url('auth', 'changePassword'));
            }

            if ($newPassword !== $confirmPassword) {
                Session::setFlash('danger', 'Mật khẩu xác nhận không trùng khớp với mật khẩu mới.');
                $this->redirect(Helper::url('auth', 'changePassword'));
            }

            $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $this->userModel->updatePassword((int)$user['id'], $newHash);

            $this->logAction('CHANGE_PASSWORD', 'users', (int)$user['id'], "Đổi mật khẩu tài khoản thành công");
            Session::setFlash('success', 'Đổi mật khẩu thành công! Vui lòng ghi nhớ mật khẩu mới.');
            $this->redirect(Helper::url('dashboard', 'index'));
        }

        $this->render('auth/change_password', [
            'pageTitle' => 'Đổi Mật Khẩu'
        ], 'main');
    }
}

