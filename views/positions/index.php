<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Quản Lý Chức Vụ</h4>
            <small class="text-muted">Danh mục các chức danh, bậc lương và phân công nhiệm vụ</small>
        </div>
        <a href="<?= Helper::url('positions', 'create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm Chức Vụ
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã Chức Vụ</th>
                        <th>Tên Chức Vụ</th>
                        <th>Mô Tả Nhiệm Vụ</th>
                        <th>Khung Lương Cơ Bản</th>
                        <th class="text-center">Số Nhân Viên</th>
                        <th>Trạng Thái</th>
                        <th class="text-end" style="width: 120px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($positions)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có chức vụ nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($positions as $idx => $pos): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($pos['code']) ?></span></td>
                                <td class="fw-bold text-dark"><?= Helper::e($pos['name']) ?></td>
                                <td class="text-muted small" style="max-width: 250px;"><?= Helper::e($pos['description'] ?: '-') ?></td>
                                <td>
                                    <span class="small fw-semibold text-secondary">
                                        <?= Helper::formatMoney($pos['base_salary_min']) ?> - <?= Helper::formatMoney($pos['base_salary_max']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 fw-bold">
                                        <?= $pos['total_employees'] ?> người
                                    </span>
                                </td>
                                <td><?= Helper::statusBadge($pos['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Helper::url('positions', 'edit', ['id' => $pos['id']]) ?>" class="btn btn-outline-primary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= Helper::url('positions', 'delete') ?>" class="confirm-action d-inline" data-confirm="Bạn có chắc muốn xóa chức vụ [<?= Helper::e($pos['name']) ?>]?">
                                            <?= Helper::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $pos['id'] ?>">
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

