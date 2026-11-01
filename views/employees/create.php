<div class="container-fluid" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Thêm Nhân Viên Mới</h4>
            <small class="text-muted">Nhập thông tin hồ sơ cho nhân sự mới gia nhập công ty</small>
        </div>
        <a href="<?= Helper::url('employees') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-fill-add me-2"></i> Thông Tin Hồ Sơ Nhân Sự</h6>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('employees', 'store') ?>">
                <?= Helper::csrfField() ?>

                <div class="row g-3">
                    <!-- Mã nhân viên -->
                    <div class="col-md-4">
                        <label for="code" class="form-label small fw-semibold">Mã nhân viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="employee_code" value="<?= Helper::e($suggestedCode ?? 'NV001') ?>" required>
                    </div>

                    <!-- Họ đệm -->
                    <div class="col-md-4">
                        <label for="lastName" class="form-label small fw-semibold">Họ và tên đệm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Nguyễn Văn" required>
                    </div>

                    <!-- Tên -->
                    <div class="col-md-4">
                        <label for="firstName" class="form-label small fw-semibold">Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="firstName" name="first_name" placeholder="An" required>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold">Email chính <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="an.nguyen@hrm.local" required>
                    </div>

                    <!-- Số điện thoại -->
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="0912345678">
                    </div>

                    <!-- Giới tính -->
                    <div class="col-md-4">
                        <label for="gender" class="form-label small fw-semibold">Giới tính</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>

                    <!-- Ngày sinh -->
                    <div class="col-md-4">
                        <label for="dob" class="form-label small fw-semibold">Ngày sinh</label>
                        <input type="date" class="form-control" id="dob" name="date_of_birth">
                    </div>

                    <!-- Ngày vào làm -->
                    <div class="col-md-4">
                        <label for="hireDate" class="form-label small fw-semibold">Ngày vào làm <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="hireDate" name="hire_date" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <!-- Phòng ban -->
                    <div class="col-md-6">
                        <label for="dept" class="form-label small fw-semibold">Phòng ban</label>
                        <select class="form-select" id="dept" name="department_id">
                            <option value="">-- Chưa phân bổ --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= Helper::e($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Chức vụ -->
                    <div class="col-md-6">
                        <label for="pos" class="form-label small fw-semibold">Chức vụ</label>
                        <select class="form-select" id="pos" name="position_id">
                            <option value="">-- Chưa xếp chức vụ --</option>
                            <?php foreach ($positions as $pos): ?>
                                <option value="<?= $pos['id'] ?>"><?= Helper::e($pos['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Lương cơ bản -->
                    <div class="col-md-6">
                        <label for="salary" class="form-label small fw-semibold">Lương cơ bản (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="salary" name="base_salary" placeholder="15.000.000" value="15.000.000">
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-semibold">Trạng thái công tác</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" selected>Đang làm việc</option>
                            <option value="probation">Thử việc</option>
                            <option value="on_leave">Tạm nghỉ</option>
                        </select>
                    </div>

                    <!-- Địa chỉ -->
                    <div class="col-12">
                        <label for="address" class="form-label small fw-semibold">Địa chỉ liên hệ</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Địa chỉ chi tiết...">
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="<?= Helper::url('employees') ?>" class="btn btn-outline-secondary me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Lưu Hồ Sơ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

