<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Xét Duyệt Đơn Nghỉ Phép</h4>
            <small class="text-muted">Quản lý và phê duyệt đơn xin nghỉ phép của cán bộ nhân viên</small>
        </div>
        <a href="<?= Helper::url('leaves', 'myLeaves') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-lines-fill me-1"></i> Đơn Của Tôi
        </a>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2">
                <input type="hidden" name="controller" value="leaves">
                <input type="hidden" name="action" value="index">

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="pending" <?= ($filters['status'] === 'pending') ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="approved" <?= ($filters['status'] === 'approved') ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="rejected" <?= ($filters['status'] === 'rejected') ? 'selected' : '' ?>>Từ chối</option>
                        <option value="cancelled" <?= ($filters['status'] === 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
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
                    <select name="leave_type" class="form-select form-select-sm">
                        <option value="">-- Loại nghỉ phép --</option>
                        <option value="annual" <?= ($filters['leave_type'] === 'annual') ? 'selected' : '' ?>>Phép năm</option>
                        <option value="sick" <?= ($filters['leave_type'] === 'sick') ? 'selected' : '' ?>>Nghỉ ốm</option>
                        <option value="unpaid" <?= ($filters['leave_type'] === 'unpaid') ? 'selected' : '' ?>>Không lương</option>
                        <option value="maternity" <?= ($filters['leave_type'] === 'maternity') ? 'selected' : '' ?>>Thai sản</option>
                        <option value="wedding" <?= ($filters['leave_type'] === 'wedding') ? 'selected' : '' ?>>Cưới hỏi</option>
                        <option value="other" <?= ($filters['leave_type'] === 'other') ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter me-1"></i> Lọc
                    </button>
                    <a href="<?= Helper::url('leaves') ?>" class="btn btn-outline-secondary btn-sm" title="Đặt lại">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách đơn -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Nhân Viên</th>
                        <th>Loại Nghỉ</th>
                        <th>Thời Gian</th>
                        <th>Số Ngày</th>
                        <th>Lý Do</th>
                        <th>Trạng Thái</th>
                        <th>Người Xử Lý</th>
                        <th class="text-end" style="width: 160px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leaveRequests)): ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">Không có đơn xin nghỉ phép nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($leaveRequests as $lr): ?>
                            <tr>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($lr['request_code']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= Helper::e($lr['employee_name']) ?></div>
                                    <small class="text-muted"><?= Helper::e($lr['department_name'] ?? '') ?> (<?= Helper::e($lr['employee_code']) ?>)</small>
                                </td>
                                <td><?= Helper::leaveTypeBadge($lr['leave_type']) ?></td>
                                <td>
                                    <div><?= Helper::formatDate($lr['start_date']) ?></div>
                                    <small class="text-muted">đến <?= Helper::formatDate($lr['end_date']) ?></small>
                                </td>
                                <td><strong><?= $lr['total_days'] ?></strong> ngày</td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="<?= Helper::e($lr['reason']) ?>"><?= Helper::e($lr['reason']) ?></div>
                                    <?php if ($lr['rejection_reason']): ?>
                                        <div class="text-danger small mt-1"><strong>Lý do từ chối:</strong> <?= Helper::e($lr['rejection_reason']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= Helper::statusBadge($lr['status']) ?></td>
                                <td>
                                    <?php if ($lr['approver_username']): ?>
                                        <div class="small fw-semibold"><?= Helper::e($lr['approver_username']) ?></div>
                                        <small class="text-muted"><?= Helper::formatDateTime($lr['approved_at']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($lr['status'] === 'pending'): ?>
                                        <div class="btn-group btn-group-sm">
                                            <!-- Nút Phê duyệt -->
                                            <form method="POST" action="<?= Helper::url('leaves', 'approve') ?>" class="d-inline confirm-action" data-confirm="Phê duyệt cho nhân viên [<?= Helper::e($lr['employee_name']) ?>] nghỉ phép <?= $lr['total_days'] ?> ngày?">
                                                <?= Helper::csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $lr['id'] ?>">
                                                <button type="submit" class="btn btn-success" title="Duyệt đơn">
                                                    <i class="bi bi-check-lg"></i> Duyệt
                                                </button>
                                            </form>

                                            <!-- Nút Từ chối (mở modal) -->
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $lr['id'] ?>" title="Từ chối đơn">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        <!-- Modal từ chối -->
                                        <div class="modal fade" id="rejectModal<?= $lr['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog text-start">
                                                <div class="modal-content">
                                                    <form method="POST" action="<?= Helper::url('leaves', 'reject') ?>">
                                                        <?= Helper::csrfField() ?>
                                                        <input type="hidden" name="id" value="<?= $lr['id'] ?>">
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">Từ Chối Đơn Nghỉ Phép [<?= Helper::e($lr['request_code']) ?>]</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="small text-muted mb-2">Nhân viên: <strong><?= Helper::e($lr['employee_name']) ?></strong> (<?= Helper::leaveTypeBadge($lr['leave_type']) ?> - <?= $lr['total_days'] ?> ngày)</p>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold">Lý do từ chối <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" name="rejection_reason" rows="3" required placeholder="Nhập lý do không duyệt đơn này..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-danger btn-sm px-3">Xác Nhận Từ Chối</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Đã hoàn tất</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

