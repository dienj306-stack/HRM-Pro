<div class="container-fluid" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Điều Chỉnh Phiếu Lương</h4>
            <small class="text-muted"><?= Helper::e($record['employee_name']) ?> - Kỳ Tháng <?= $record['month'] ?>/<?= $record['year'] ?></small>
        </div>
        <a href="<?= Helper::url('payroll', 'index', ['month' => $record['month'], 'year' => $record['year']]) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Bảng Lương
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('payroll', 'update') ?>">
                <?= Helper::csrfField() ?>
                <input type="hidden" name="id" value="<?= $record['id'] ?>">

                <div class="alert alert-light border small mb-4">
                    <div class="row">
                        <div class="col-sm-6"><strong>Mã nhân viên:</strong> <?= Helper::e($record['employee_code']) ?></div>
                        <div class="col-sm-6"><strong>Họ và tên:</strong> <?= Helper::e($record['employee_name']) ?></div>
                        <div class="col-sm-6 mt-1"><strong>Phòng ban:</strong> <?= Helper::e($record['department_name'] ?? '-') ?></div>
                        <div class="col-sm-6 mt-1"><strong>Chức vụ:</strong> <?= Helper::e($record['position_name'] ?? '-') ?></div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="baseSal" class="form-label small fw-semibold">Lương cơ bản (VNĐ) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control money-input" id="baseSal" name="base_salary" value="<?= number_format($record['base_salary'], 0, ',', '.') ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label for="stdDays" class="form-label small fw-semibold">Ngày công chuẩn</label>
                        <input type="number" step="0.5" class="form-control" id="stdDays" name="standard_work_days" value="<?= $record['standard_work_days'] ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label for="actDays" class="form-label small fw-semibold">Ngày công thực tế</label>
                        <input type="number" step="0.5" class="form-control" id="actDays" name="actual_work_days" value="<?= $record['actual_work_days'] ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="allowance" class="form-label small fw-semibold">Phụ cấp ăn trưa, đi lại (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="allowance" name="allowance" value="<?= number_format($record['allowance'], 0, ',', '.') ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="bonus" class="form-label small fw-semibold">Thưởng hiệu quả / KPI (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="bonus" name="bonus" value="<?= number_format($record['bonus'], 0, ',', '.') ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="deduction" class="form-label small fw-semibold">Giảm trừ / Đi muộn (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="deduction" name="deduction" value="<?= number_format($record['deduction'], 0, ',', '.') ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-semibold">Trạng thái phiếu lương</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= ($record['status'] === 'draft') ? 'selected' : '' ?>>Bản nháp</option>
                            <option value="approved" <?= ($record['status'] === 'approved') ? 'selected' : '' ?>>Đã phê duyệt</option>
                            <option value="paid" <?= ($record['status'] === 'paid') ? 'selected' : '' ?>>Đã thanh toán</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Thực nhận hiện tại</label>
                        <div class="fs-4 fw-bold text-success pt-1"><?= Helper::formatMoney($record['net_salary']) ?></div>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label small fw-semibold">Ghi chú điều chỉnh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Lý do cộng thưởng hoặc trừ lương..."><?= Helper::e($record['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="<?= Helper::url('payroll', 'index', ['month' => $record['month'], 'year' => $record['year']]) ?>" class="btn btn-outline-secondary me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Cập Nhật Lương
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

