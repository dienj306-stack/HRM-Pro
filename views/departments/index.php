<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Quản Lý Phòng Ban</h4>
            <small class="text-muted">Cơ cấu tổ chức và các bộ phận trong doanh nghiệp</small>
        </div>
        <a href="<?= Helper::url('departments', 'create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm Phòng Ban
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã Phòng</th>
                        <th>Tên Phòng Ban</th>
                        <th>Mô Tả Chức Năng</th>
                        <th>Trưởng Phòng</th>
                        <th class="text-center">Số Nhân Viên</th>
                        <th>Trạng Thái</th>
                        <th class="text-end" style="width: 120px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($departments)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có phòng ban nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($departments as $idx => $dept): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($dept['code']) ?></span></td>
                                <td class="fw-bold text-dark"><?= Helper::e($dept['name']) ?></td>
                                <td class="text-muted small" style="max-width: 300px;"><?= Helper::e($dept['description'] ?: '-') ?></td>
                                <td>
                                    <?php if ($dept['manager_name']): ?>
                                        <i class="bi bi-person-badge text-primary me-1"></i><?= Helper::e($dept['manager_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted small">Chưa chỉ định</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-bold">
                                        <?= $dept['total_employees'] ?> người
                                    </span>
                                </td>
                                <td><?= Helper::statusBadge($dept['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Helper::url('departments', 'edit', ['id' => $dept['id']]) ?>" class="btn btn-outline-primary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= Helper::url('departments', 'delete') ?>" class="confirm-action d-inline" data-confirm="Bạn có chắc muốn xóa phòng ban [<?= Helper::e($dept['name']) ?>]?">
                                            <?= Helper::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

