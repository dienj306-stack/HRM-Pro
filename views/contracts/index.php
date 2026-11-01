<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Quản Lý Hợp Đồng Lao Động</h4>
            <small class="text-muted">Lưu trữ và theo dõi thời hạn hợp đồng, mức lương thỏa thuận</small>
        </div>
        <a href="<?= Helper::url('contracts', 'create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-file-earmark-plus-fill me-1"></i> Tạo Hợp Đồng Mới
        </a>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2">
                <input type="hidden" name="controller" value="contracts">
                <input type="hidden" name="action" value="index">

                <div class="col-md-4">
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">-- Tất cả nhân viên --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($filters['employee_id'] == $emp['id']) ? 'selected' : '' ?>>
                                <?= Helper::e($emp['full_name']) ?> (<?= Helper::e($emp['employee_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="contract_type" class="form-select form-select-sm">
                        <option value="">-- Tất cả loại hợp đồng --</option>
                        <option value="probation" <?= ($filters['contract_type'] === 'probation') ? 'selected' : '' ?>>Thử việc</option>
                        <option value="definite" <?= ($filters['contract_type'] === 'definite') ? 'selected' : '' ?>>Xác định thời hạn</option>
                        <option value="indefinite" <?= ($filters['contract_type'] === 'indefinite') ? 'selected' : '' ?>>Không xác định thời hạn</option>
                        <option value="seasonal" <?= ($filters['contract_type'] === 'seasonal') ? 'selected' : '' ?>>Thời vụ / Dự án</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Trạng thái --</option>
                        <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : '' ?>>Đang hiệu lực</option>
                        <option value="expired" <?= ($filters['status'] === 'expired') ? 'selected' : '' ?>>Đã hết hạn</option>
                        <option value="terminated" <?= ($filters['status'] === 'terminated') ? 'selected' : '' ?>>Đã chấm dứt</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter"></i> Lọc</button>
                    <a href="<?= Helper::url('contracts') ?>" class="btn btn-outline-secondary btn-sm" title="Đặt lại"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách hợp đồng -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã Hợp Đồng</th>
                        <th>Nhân Viên</th>
                        <th>Loại Hợp Đồng</th>
                        <th>Thời Hạn</th>
                        <th>Lương Cơ Bản</th>
                        <th>Trạng Thái</th>
                        <th class="text-end" style="width: 120px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contracts)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Không tìm thấy hợp đồng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($contracts as $idx => $c): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($c['contract_code']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= Helper::e($c['employee_name']) ?></div>
                                    <small class="text-muted"><?= Helper::e($c['department_name'] ?? '') ?> - <?= Helper::e($c['employee_code']) ?></small>
                                </td>
                                <td><?= Helper::contractTypeName($c['contract_type']) ?></td>
                                <td>
                                    <div><?= Helper::formatDate($c['start_date']) ?></div>
                                    <small class="text-muted">&rarr; <?= $c['end_date'] ? Helper::formatDate($c['end_date']) : 'Vô thời hạn' ?></small>
                                </td>
                                <td class="fw-bold text-success"><?= Helper::formatMoney($c['basic_salary']) ?></td>
                                <td><?= Helper::statusBadge($c['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Helper::url('contracts', 'edit', ['id' => $c['id']]) ?>" class="btn btn-outline-primary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= Helper::url('contracts', 'delete') ?>" class="confirm-action d-inline" data-confirm="Bạn có chắc chắn muốn xóa hợp đồng [<?= Helper::e($c['contract_code']) ?>]?">
                                            <?= Helper::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
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

