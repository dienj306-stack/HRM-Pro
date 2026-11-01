<div class="container-fluid" style="max-width: 600px;">
    <div class="mb-4">
        <h4 class="mb-0 fw-bold">Đổi Mật Khẩu</h4>
        <small class="text-muted">Bảo vệ an toàn cho tài khoản cá nhân của bạn</small>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('auth', 'changePassword') ?>">
                <?= Helper::csrfField() ?>

                <div class="mb-3">
                    <label for="currentPassword" class="form-label small fw-semibold">Mật khẩu hiện tại</label>
                    <input type="password" class="form-control" id="currentPassword" name="current_password" required placeholder="Nhập mật khẩu bạn đang dùng...">
                </div>

                <div class="mb-3">
                    <label for="newPassword" class="form-label small fw-semibold">Mật khẩu mới (Tối thiểu 6 ký tự)</label>
                    <input type="password" class="form-control" id="newPassword" name="new_password" required minlength="6" placeholder="Nhập mật khẩu mới...">
                </div>

                <div class="mb-4">
                    <label for="confirmPassword" class="form-label small fw-semibold">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required minlength="6" placeholder="Nhập lại mật khẩu mới...">
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= Helper::url('dashboard') ?>" class="btn btn-outline-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-shield-lock me-1"></i> Cập Nhật Mật Khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

