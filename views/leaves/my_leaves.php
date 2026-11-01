<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Đơn Nghỉ Phép Của Tôi</h4>
            <small class="text-muted">Theo dõi trạng thái phê duyệt các đơn xin nghỉ phép của bạn</small>
        </div>
        <a href="<?= Helper::url('leaves', 'create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tạo Đơn Xin Nghỉ Mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã Đơn</th>
                        <th>Loại Nghỉ Phép</th>
                        <th>Từ Ngày</th>
                        <th>Đến Ngày</th>
                        <th>Số Ngày</th>
                        <th>Lý Do Xin Nghỉ</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Gửi</th>
                        <th class="text-end" style="width: 100px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($myRequests)): ?>
                        <tr><td colspan="10" class="text-center py-5 text-muted">Bạn chưa gửi đơn xin nghỉ phép nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($myRequests as $idx => $lr): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($lr['request_code']) ?></span></td>
                                <td><?= Helper::leaveTypeBadge($lr['leave_type']) ?></td>
                                <td class="fw-semibold"><?= Helper::formatDate($lr['start_date']) ?></td>
                                <td class="fw-semibold"><?= Helper::formatDate($lr['end_date']) ?></td>
                                <td><strong><?= $lr['total_days'] ?></strong> ngày</td>
                                <td style="max-width: 250px;">
                                    <div><?= Helper::e($lr['reason']) ?></div>
                                    <?php if ($lr['rejection_reason']): ?>
                                        <div class="text-danger small mt-1"><strong>Lý do từ chối:</strong> <?= Helper::e($lr['rejection_reason']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= Helper::statusBadge($lr['status']) ?></td>
                                <td class="text-muted small"><?= Helper::formatDateTime($lr['created_at']) ?></td>
                                <td class="text-end">
                                    <?php if ($lr['status'] === 'pending'): ?>
                                        <form method="POST" action="<?= Helper::url('leaves', 'cancel') ?>" class="confirm-action d-inline" data-confirm="Bạn có chắc chắn muốn hủy đơn nghỉ phép này?">
                                            <?= Helper::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $lr['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hủy đơn">
                                                <i class="bi bi-trash"></i> Hủy
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
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

