<div class="container-fluid" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Tạo Hợp Đồng Lao Động Mới</h4>
            <small class="text-muted">Lập hợp đồng và cam kết mức lương với nhân sự</small>
        </div>
        <a href="<?= Helper::url('contracts') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('contracts', 'store') ?>">
                <?= Helper::csrfField() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label small fw-semibold">Mã hợp đồng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="contract_code" value="<?= Helper::e($suggestedCode ?? 'HD-2026-001') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="emp" class="form-label small fw-semibold">Nhân viên ký kết <span class="text-danger">*</span></label>
                        <select class="form-select" id="emp" name="employee_id" required>
                            <option value="">-- Chọn nhân viên --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>"><?= Helper::e($emp['full_name']) ?> (<?= Helper::e($emp['employee_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label small fw-semibold">Loại hợp đồng <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="contract_type" required>
                            <option value="definite" selected>Hợp đồng xác định thời hạn (1 - 3 năm)</option>
                            <option value="indefinite">Hợp đồng không xác định thời hạn</option>
                            <option value="probation">Hợp đồng thử việc (1 - 2 tháng)</option>
                            <option value="seasonal">Hợp đồng thời vụ / Theo dự án</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-semibold">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" selected>Đang hiệu lực</option>
                            <option value="expired">Đã hết hạn</option>
                            <option value="terminated">Đã chấm dứt</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="sDate" class="form-label small fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="sDate" name="start_date" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="eDate" class="form-label small fw-semibold">Ngày kết thúc (Bỏ trống nếu không thời hạn)</label>
                        <input type="date" class="form-control" id="eDate" name="end_date">
                    </div>

                    <div class="col-md-6">
                        <label for="bSal" class="form-label small fw-semibold">Lương cơ bản thỏa thuận (VNĐ) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control money-input" id="bSal" name="basic_salary" value="15.000.000" required>
                    </div>

                    <div class="col-md-6">
                        <label for="iSal" class="form-label small fw-semibold">Lương đóng BHXH (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="iSal" name="insurance_salary" value="6.000.000">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label small fw-semibold">Điều khoản bổ sung / Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Ghi chú về chế độ đãi ngộ hoặc điều khoản riêng..."></textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="<?= Helper::url('contracts') ?>" class="btn btn-outline-secondary me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Lưu Hợp Đồng
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

