<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Hồ Sơ Cá Nhân</h4>
            <small class="text-muted">Quản lý và cập nhật thông tin tài khoản của bạn</small>
        </div>
        <a href="<?= Helper::url('auth', 'changePassword') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-key me-1"></i> Đổi Mật Khẩu
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card text-center p-4">
                <div class="mb-3">
                    <img src="<?= Helper::e($employee['avatar'] ?? 'assets/img/avatar-default.png') ?>" alt="Avatar" width="110" height="110" class="rounded-circle border border-3 border-primary p-1">
                </div>
                <h5 class="fw-bold mb-1"><?= Helper::e($employee['full_name'] ?? $user['username']) ?></h5>
                <p class="text-muted small mb-2"><?= Helper::e($user['email']) ?></p>
                <div>
                    <span class="badge bg-primary px-3 py-2"><?= Helper::e($user['role_display'] ?? '') ?></span>
                    <?php if (!empty($employee['status'])): ?>
                        <?= Helper::statusBadge($employee['status']) ?>
                    <?php endif; ?>
                </div>
                <hr class="my-4">
                <div class="text-start small">
                    <div class="mb-2"><strong class="text-muted">Mã nhân viên:</strong> <span class="fw-semibold"><?= Helper::e($employee['employee_code'] ?? 'Chưa liên kết') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Phòng ban:</strong> <span class="fw-semibold"><?= Helper::e($employee['department_name'] ?? '-') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Chức vụ:</strong> <span class="fw-semibold"><?= Helper::e($employee['position_name'] ?? '-') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Ngày vào làm:</strong> <span class="fw-semibold"><?= Helper::formatDate($employee['hire_date'] ?? null) ?></span></div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i> Thông Tin Chi Tiết & Liên Hệ</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= Helper::url('auth', 'profile') ?>">
                        <?= Helper::csrfField() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Tên đăng nhập</label>
                                <input type="text" class="form-control bg-light" value="<?= Helper::e($user['username']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email chính</label>
                                <input type="email" class="form-control bg-light" value="<?= Helper::e($user['email']) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Họ và tên</label>
                                <input type="text" class="form-control bg-light" value="<?= Helper::e($employee['full_name'] ?? '') ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Ngày sinh</label>
                                <input type="text" class="form-control bg-light" value="<?= Helper::formatDate($employee['date_of_birth'] ?? null) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="phoneInput" class="form-label small fw-semibold">Số điện thoại <span class="text-primary">(Được phép sửa)</span></label>
                                <input type="text" class="form-control" id="phoneInput" name="phone" value="<?= Helper::e($employee['phone'] ?? '') ?>" placeholder="Nhập số điện thoại...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Giới tính</label>
                                <input type="text" class="form-control bg-light" value="<?= ($employee['gender'] ?? '') === 'male' ? 'Nam' : (($employee['gender'] ?? '') === 'female' ? 'Nữ' : 'Khác') ?>" readonly>
                            </div>

                            <div class="col-12">
                                <label for="addressInput" class="form-label small fw-semibold">Địa chỉ liên hệ <span class="text-primary">(Được phép sửa)</span></label>
                                <input type="text" class="form-control" id="addressInput" name="address" value="<?= Helper::e($employee['address'] ?? '') ?>" placeholder="Địa chỉ thường trú / tạm trú...">
                            </div>

                            <?php if (!empty($employee)): ?>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i> Lưu Thay Đổi
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

