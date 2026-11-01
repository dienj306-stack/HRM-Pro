<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Quản Lý Tài Khoản & Phân Quyền</h4>
            <small class="text-muted">Quản lý người dùng, gán vai trò và thiết lập bảo mật đăng nhập</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Tạo Tài Khoản Mới
        </button>
    </div>

    <!-- Bảng danh sách tài khoản -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Tên Đăng Nhập</th>
                        <th>Email</th>
                        <th>Vai Trò (Role)</th>
                        <th>Nhân Viên Liên Kết</th>
                        <th>Đăng Nhập Cuối</th>
                        <th>Trạng Thái</th>
                        <th class="text-end" style="width: 150px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Chưa có người dùng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $idx => $u): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td>
                                    <span class="fw-bold text-dark"><i class="bi bi-person-circle me-1 text-primary"></i><?= Helper::e($u['username']) ?></span>
                                </td>
                                <td><?= Helper::e($u['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $u['role_name'] === 'admin' ? 'danger' : ($u['role_name'] === 'hr' ? 'success' : 'secondary') ?> px-2 py-1">
                                        <?= Helper::e($u['role_display']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($u['employee_name']): ?>
                                        <div class="fw-semibold"><?= Helper::e($u['employee_name']) ?></div>
                                        <small class="text-muted"><?= Helper::e($u['department_name'] ?? '') ?> (<?= Helper::e($u['employee_code']) ?>)</small>
                                    <?php else: ?>
                                        <span class="text-muted small">Tài khoản quản trị viên độc lập</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= Helper::formatDateTime($u['last_login']) ?></td>
                                <td><?= Helper::statusBadge($u['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Khóa/Mở khóa -->
                                        <form method="POST" action="<?= Helper::url('users', 'toggleStatus') ?>" class="d-inline confirm-action" data-confirm="Bạn có chắc muốn <?= $u['status'] === 'active' ? 'khóa' : 'mở khóa' ?> tài khoản [<?= Helper::e($u['username']) ?>]?">
                                            <?= Helper::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn btn-outline-<?= $u['status'] === 'active' ? 'warning' : 'success' ?>" title="<?= $u['status'] === 'active' ? 'Khóa tài khoản' : 'Mở khóa' ?>">
                                                <i class="bi bi-<?= $u['status'] === 'active' ? 'lock' : 'unlock' ?>"></i>
                                            </button>
                                        </form>

                                        <!-- Đặt lại mật khẩu -->
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?= $u['id'] ?>" title="Đặt lại mật khẩu">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </div>

                                    <!-- Modal Đặt lại mật khẩu -->
                                    <div class="modal fade" id="resetPasswordModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog text-start">
                                            <div class="modal-content">
                                                <form method="POST" action="<?= Helper::url('users', 'resetPassword') ?>">
                                                    <?= Helper::csrfField() ?>
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">Đặt Lại Mật Khẩu: <?= Helper::e($u['username']) ?></h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted mb-3">Tài khoản: <strong><?= Helper::e($u['username']) ?></strong> (<?= Helper::e($u['email']) ?>)</p>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Mật khẩu mới (Tối thiểu 6 ký tự) <span class="text-danger">*</span></label>
                                                            <input type="password" class="form-control" name="new_password" required minlength="6" placeholder="Nhập mật khẩu mới...">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary btn-sm px-3">Xác Nhận Đổi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tạo Tài Khoản Mới -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= Helper::url('users', 'store') ?>">
                <?= Helper::csrfField() ?>
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2 text-primary"></i> Tạo Tài Khoản Đăng Nhập Mới</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Liên kết với hồ sơ nhân viên</label>
                        <select class="form-select form-select-sm" name="employee_id" id="empSelect">
                            <option value="">-- Không liên kết (Tài khoản quản trị viên riêng) --</option>
                            <?php foreach ($unlinkedEmployees as $unEmp): ?>
                                <option value="<?= $unEmp['id'] ?>" data-email="<?= Helper::e($unEmp['email']) ?>">
                                    <?= Helper::e($unEmp['full_name']) ?> (<?= Helper::e($unEmp['employee_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Chỉ hiển thị các nhân sự chưa được cấp tài khoản.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="username" id="usernameInput" required placeholder="VD: an.nguyen">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email tài khoản <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-sm" name="email" id="emailInput" required placeholder="VD: an.nguyen@hrm.local">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mật khẩu ban đầu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-sm" name="password" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Phân quyền vai trò <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="role_id" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= ($r['name'] === 'employee') ? 'selected' : '' ?>>
                                    <?= Helper::e($r['display_name']) ?> (<?= Helper::e($r['name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Tạo Tài Khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('empSelect')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const email = selected.getAttribute('data-email');
    if (email) {
        document.getElementById('emailInput').value = email;
        const autoUser = email.split('@')[0];
        document.getElementById('usernameInput').value = autoUser;
    }
});
</script>

