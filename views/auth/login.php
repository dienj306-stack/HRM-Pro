<div class="p-4 p-sm-5">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 56px; height: 56px;">
            <i class="bi bi-person-badge fs-2"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">HRM Pro</h3>
        <p class="text-muted small">Hệ thống Quản lý Nhân sự Doanh nghiệp</p>
    </div>

    <!-- Flash Messages -->
    <?php
    $flashes = Session::getFlashes();
    if (!empty($flashes)):
        foreach ($flashes as $type => $messages):
            foreach ($messages as $msg):
    ?>
        <div class="alert alert-<?= $type ?> alert-dismissible fade show small" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?= Helper::e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
            endforeach;
        endforeach;
    endif;
    ?>

    <form method="POST" action="<?= Helper::url('auth', 'login') ?>">
        <?= Helper::csrfField() ?>

        <div class="mb-3">
            <label for="loginInput" class="form-label fw-semibold small">Tên đăng nhập hoặc Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="loginInput" name="login" value="<?= Helper::e($login ?? '') ?>" placeholder="admin / hr_manager / emp_nam" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <label for="passwordInput" class="form-label fw-semibold small mb-0">Mật khẩu</label>
            </div>
            <div class="input-group mt-1">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Nhập mật khẩu..." required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3 shadow-sm">
            <i class="bi bi-box-arrow-in-right me-2"></i> Đăng Nhập
        </button>
    </form>

    <!-- Hộp thông tin tài khoản thử nghiệm nhanh (Hỗ trợ người mới) -->
    <div class="border-top pt-3 mt-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Tài khoản thử nghiệm sẵn sàng:</small>
        </div>
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm text-start py-1" onclick="fillLogin('admin', 'Admin@123')">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-shield-check me-1"></i> <strong>Admin:</strong> admin</span>
                    <span class="badge bg-primary">Admin@123</span>
                </div>
            </button>
            <button type="button" class="btn btn-outline-success btn-sm text-start py-1" onclick="fillLogin('hr_manager', 'Hr@123456')">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-gear me-1"></i> <strong>HR:</strong> hr_manager</span>
                    <span class="badge bg-success">Hr@123456</span>
                </div>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm text-start py-1" onclick="fillLogin('emp_nam', 'Emp@123456')">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person me-1"></i> <strong>Nhân viên:</strong> emp_nam</span>
                    <span class="badge bg-secondary">Emp@123456</span>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
function fillLogin(username, password) {
    document.getElementById('loginInput').value = username;
    document.getElementById('passwordInput').value = password;
}
</script>

