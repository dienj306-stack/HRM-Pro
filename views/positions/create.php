<div class="container-fluid" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Thêm Chức Vụ Mới</h4>
            <small class="text-muted">Khai báo chức danh và khung lương chuẩn</small>
        </div>
        <a href="<?= Helper::url('positions') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('positions', 'store') ?>">
                <?= Helper::csrfField() ?>

                <div class="mb-3">
                    <label for="code" class="form-label small fw-semibold">Mã chức vụ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" placeholder="VD: CV-DEV, CV-PM..." required>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label small fw-semibold">Tên chức vụ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="VD: Lập trình viên Backend" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="minSal" class="form-label small fw-semibold">Lương tối thiểu (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="minSal" name="base_salary_min" placeholder="10.000.000">
                    </div>
                    <div class="col-md-6">
                        <label for="maxSal" class="form-label small fw-semibold">Lương tối đa (VNĐ)</label>
                        <input type="text" class="form-control money-input" id="maxSal" name="base_salary_max" placeholder="25.000.000">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="desc" class="form-label small fw-semibold">Mô tả công việc</label>
                    <textarea class="form-control" id="desc" name="description" rows="3" placeholder="Mô tả tiêu chuẩn và nhiệm vụ..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label small fw-semibold">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" selected>Hoạt động</option>
                        <option value="inactive">Tạm ngưng</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= Helper::url('positions') ?>" class="btn btn-outline-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Lưu Chức Vụ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

