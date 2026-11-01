<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Danh Sách Nhân Viên</h4>
            <small class="text-muted">Quản lý toàn bộ hồ sơ nhân sự trong công ty</small>
        </div>
        <a href="<?= Helper::url('employees', 'create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Thêm Nhân Viên Mới
        </a>
    </div>

    <!-- Bộ lọc & Tìm kiếm -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2">
                <input type="hidden" name="controller" value="employees">
                <input type="hidden" name="action" value="index">

                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên, mã NV, email, SĐT..." value="<?= Helper::e($filters['keyword'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="">-- Tất cả phòng ban --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($filters['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                <?= Helper::e($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : '' ?>>Đang làm việc</option>
                        <option value="probation" <?= ($filters['status'] === 'probation') ? 'selected' : '' ?>>Thử việc</option>
                        <option value="on_leave" <?= ($filters['status'] === 'on_leave') ? 'selected' : '' ?>>Tạm nghỉ</option>
                        <option value="resigned" <?= ($filters['status'] === 'resigned') ? 'selected' : '' ?>>Đã thôi việc</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm w-100">
                        <i class="bi bi-filter me-1"></i> Lọc
                    </button>
                    <a href="<?= Helper::url('employees') ?>" class="btn btn-outline-secondary btn-sm" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách nhân viên -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã NV</th>
                        <th>Họ và Tên</th>
                        <th>Phòng Ban</th>
                        <th>Chức Vụ</th>
                        <th>Ngày Vào Làm</th>
                        <th>Trạng Thái</th>
                        <th class="text-end" style="width: 140px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Không tìm thấy hồ sơ nhân viên nào phù hợp.</td></tr>
                    <?php else: ?>
                        <?php foreach ($employees as $idx => $emp): ?>
                            <tr>
                                <td><?= ($currentPage - 1) * 10 + $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($emp['employee_code']) ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= Helper::e($emp['avatar'] ?: 'assets/img/avatar-default.png') ?>" alt="" class="rounded-circle me-2 border" width="36" height="36">
                                        <div>
                                            <a href="<?= Helper::url('employees', 'show', ['id' => $emp['id']]) ?>" class="fw-bold text-decoration-none text-dark">
                                                <?= Helper::e($emp['full_name']) ?>
                                            </a>
                                            <div class="text-muted small"><?= Helper::e($emp['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= Helper::e($emp['department_name'] ?? 'Chưa phân bổ') ?></td>
                                <td><?= Helper::e($emp['position_name'] ?? 'Chưa xếp') ?></td>
                                <td><?= Helper::formatDate($emp['hire_date']) ?></td>
                                <td><?= Helper::statusBadge($emp['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Helper::url('employees', 'show', ['id' => $emp['id']]) ?>" class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= Helper::url('employees', 'edit', ['id' => $emp['id']]) ?>" class="btn btn-outline-primary" title="Sửa thông tin">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= Helper::url('employees', 'delete') ?>" class="confirm-action d-inline" data-confirm="Bạn có chắc muốn thôi việc và ngừng hoạt động nhân viên này?">
                                            <?= Helper::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger" title="Ngừng hoạt động / Thôi việc">
                                                <i class="bi bi-person-x"></i>
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

        <!-- Phân trang -->
        <?php if ($totalPages > 1): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                <small class="text-muted">Hiển thị trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng cộng <?= $totalRecords ?> nhân viên)</small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= ($p == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= Helper::url('employees', 'index', array_merge($filters, ['page' => $p])) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

