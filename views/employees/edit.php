<div class="container-fluid" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Sửa Hồ Sơ Nhân Viên</h4>
            <small class="text-muted">Cập nhật thông tin cho nhân sự [<?= Helper::e($employee['employee_code']) ?>] - <?= Helper::e($employee['full_name']) ?></small>
        </div>
        <a href="<?= Helper::url('employees', 'show', ['id' => $employee['id']]) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i> Chỉnh Sửa Hồ Sơ</h6>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('employees', 'update') ?>">
                <?= Helper::csrfField() ?>
                <input type="hidden" name="id" value="<?= $employee['id'] ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="code" class="form-label small fw-semibold">Mã nhân viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="employee_code" value="<?= Helper::e($employee['employee_code']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="lastName" class="form-label small fw-semibold">Họ và tên đệm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lastName" name="last_name" value="<?= Helper::e($employee['last_name']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="firstName" class="form-label small fw-semibold">Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="firstName" name="first_name" value="<?= Helper::e($employee['first_name']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold">Email chính <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= Helper::e($employee['email']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= Helper::e($employee['phone'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="gender" class="form-label small fw-semibold">Giới tính</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="male" <?= ($employee['gender'] === 'male') ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= ($employee['gender'] === 'female') ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= ($employee['gender'] === 'other') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="dob" class="form-label small fw-semibold">Ngày sinh</label>
                        <input type="date" class="form-control" id="dob" name="date_of_birth" value="<?= $employee['date_of_birth'] ?? '' ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="hireDate" class="form-label small fw-semibold">Ngày vào làm <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="hireDate" name="hire_date" value="<?= $employee['hire_date'] ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="dept" class="form-label small fw-semibold">Phòng ban</label>
                        <select class="form-select" id="dept" name="department_id">
                            <option value="">-- Chưa phân bổ --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" <?= ($employee['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                    <?= Helper::e($dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="pos" class="form-label small fw-semibold">Chức vụ</label>
                        <select class="form-select" id="pos" name="position_id">
                            <option value="">-- Chưa xếp chức vụ --</option>
                            <?php foreach ($positions as $pos): ?>
                                <option value="<?= $pos['id'] ?>" <?= ($employee['position_id'] == $pos['id']) ? 'selected' : '' ?>>
                                    <?= Helper::e($pos['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="salary" class="form-label small fw-semibold">Lương cơ bản (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="salary" name="base_salary" value="<?= number_format($employee['base_salary'], 0, ',', '.') ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-semibold">Trạng thái công tác</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= ($employee['status'] === 'active') ? 'selected' : '' ?>>Đang làm việc</option>
                            <option value="probation" <?= ($employee['status'] === 'probation') ? 'selected' : '' ?>>Thử việc</option>
                            <option value="on_leave" <?= ($employee['status'] === 'on_leave') ? 'selected' : '' ?>>Tạm nghỉ</option>
                            <option value="resigned" <?= ($employee['status'] === 'resigned') ? 'selected' : '' ?>>Đã thôi việc</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label small fw-semibold">Địa chỉ liên hệ</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?= Helper::e($employee['address'] ?? '') ?>">
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="<?= Helper::url('employees', 'show', ['id' => $employee['id']]) ?>" class="btn btn-outline-secondary me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Cập Nhật Hồ Sơ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

