<div class="container-fluid" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Sửa Hợp Đồng: <?= Helper::e($contract['contract_code']) ?></h4>
            <small class="text-muted">Cập nhật thời hạn hoặc điều chỉnh mức lương hợp đồng</small>
        </div>
        <a href="<?= Helper::url('contracts') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('contracts', 'update') ?>">
                <?= Helper::csrfField() ?>
                <input type="hidden" name="id" value="<?= $contract['id'] ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label small fw-semibold">Mã hợp đồng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="contract_code" value="<?= Helper::e($contract['contract_code']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="emp" class="form-label small fw-semibold">Nhân viên <span class="text-danger">*</span></label>
                        <select class="form-select" id="emp" name="employee_id" required>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>" <?= ($contract['employee_id'] == $emp['id']) ? 'selected' : '' ?>>
                                    <?= Helper::e($emp['full_name']) ?> (<?= Helper::e($emp['employee_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label small fw-semibold">Loại hợp đồng <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="contract_type" required>
                            <option value="definite" <?= ($contract['contract_type'] === 'definite') ? 'selected' : '' ?>>Hợp đồng xác định thời hạn</option>
                            <option value="indefinite" <?= ($contract['contract_type'] === 'indefinite') ? 'selected' : '' ?>>Hợp đồng không xác định thời hạn</option>
                            <option value="probation" <?= ($contract['contract_type'] === 'probation') ? 'selected' : '' ?>>Hợp đồng thử việc</option>
                            <option value="seasonal" <?= ($contract['contract_type'] === 'seasonal') ? 'selected' : '' ?>>Hợp đồng thời vụ / Theo dự án</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-semibold">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= ($contract['status'] === 'active') ? 'selected' : '' ?>>Đang hiệu lực</option>
                            <option value="expired" <?= ($contract['status'] === 'expired') ? 'selected' : '' ?>>Đã hết hạn</option>
                            <option value="terminated" <?= ($contract['status'] === 'terminated') ? 'selected' : '' ?>>Đã chấm dứt</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="sDate" class="form-label small fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="sDate" name="start_date" value="<?= $contract['start_date'] ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="eDate" class="form-label small fw-semibold">Ngày kết thúc</label>
                        <input type="date" class="form-control" id="eDate" name="end_date" value="<?= $contract['end_date'] ?? '' ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="bSal" class="form-label small fw-semibold">Lương cơ bản (VNĐ) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control money-input" id="bSal" name="basic_salary" value="<?= number_format($contract['basic_salary'], 0, ',', '.') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="iSal" class="form-label small fw-semibold">Lương đóng BHXH (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="iSal" name="insurance_salary" value="<?= number_format($contract['insurance_salary'], 0, ',', '.') ?>">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label small fw-semibold">Điều khoản bổ sung / Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= Helper::e($contract['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="<?= Helper::url('contracts') ?>" class="btn btn-outline-secondary me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Cập Nhật Hợp Đồng
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

