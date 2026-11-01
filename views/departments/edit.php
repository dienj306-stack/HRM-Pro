<div class="container-fluid" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Sửa Phòng Ban: <?= Helper::e($department['name']) ?></h4>
            <small class="text-muted">Cập nhật thông tin phòng ban</small>
        </div>
        <a href="<?= Helper::url('departments') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay Lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('departments', 'update') ?>">
                <?= Helper::csrfField() ?>
                <input type="hidden" name="id" value="<?= $department['id'] ?>">

                <div class="mb-3">
                    <label for="code" class="form-label small fw-semibold">Mã phòng ban <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" value="<?= Helper::e($department['code']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label small fw-semibold">Tên phòng ban <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= Helper::e($department['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="manager" class="form-label small fw-semibold">Trưởng phòng</label>
                    <select class="form-select" id="manager" name="manager_id">
                        <option value="">-- Chưa bổ nhiệm --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($department['manager_id'] == $emp['id']) ? 'selected' : '' ?>>
                                <?= Helper::e($emp['full_name']) ?> (<?= Helper::e($emp['employee_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="desc" class="form-label small fw-semibold">Mô tả chức năng nhiệm vụ</label>
                    <textarea class="form-control" id="desc" name="description" rows="3"><?= Helper::e($department['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label small fw-semibold">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= ($department['status'] === 'active') ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= ($department['status'] === 'inactive') ? 'selected' : '' ?>>Tạm ngưng</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= Helper::url('departments') ?>" class="btn btn-outline-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Cập Nhật Phòng Ban
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

