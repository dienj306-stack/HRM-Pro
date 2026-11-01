<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Nhật Ký Hoạt Động Hệ Thống (Audit Logs)</h4>
            <small class="text-muted">Ghi nhận mọi thao tác quan trọng: đăng nhập, tạo, sửa, xóa, duyệt nghỉ phép và tính lương</small>
        </div>
        <a href="<?= Helper::url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Bảng Điều Khiển
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Thời Gian</th>
                        <th>Người Thực Hiện</th>
                        <th>Hành Động</th>
                        <th>Đối Tượng</th>
                        <th>Mô Tả Chi Tiết</th>
                        <th>Địa Chỉ IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có nhật ký hoạt động nào được ghi lại.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $idx => $log): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="text-nowrap fw-semibold"><?= Helper::formatDateTime($log['created_at']) ?></td>
                                <td>
                                    <?php if ($log['username']): ?>
                                        <div class="fw-bold text-dark"><?= Helper::e($log['employee_name'] ?: $log['username']) ?></div>
                                        <small class="text-muted"><?= Helper::e($log['username']) ?> (<?= Helper::e($log['role_display'] ?? '') ?>)</small>
                                    <?php else: ?>
                                        <span class="text-muted">Hệ thống tự động</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $badgeClass = 'bg-secondary';
                                        if (str_contains($log['action'], 'LOGIN')) $badgeClass = 'bg-info text-dark';
                                        elseif (str_contains($log['action'], 'CREATE')) $badgeClass = 'bg-success';
                                        elseif (str_contains($log['action'], 'UPDATE')) $badgeClass = 'bg-primary';
                                        elseif (str_contains($log['action'], 'DELETE') || str_contains($log['action'], 'REJECT') || str_contains($log['action'], 'DEACTIVATE')) $badgeClass = 'bg-danger';
                                        elseif (str_contains($log['action'], 'APPROVE')) $badgeClass = 'bg-success';
                                        elseif (str_contains($log['action'], 'CHECK')) $badgeClass = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> font-monospace"><?= Helper::e($log['action']) ?></span>
                                </td>
                                <td><span class="badge bg-light text-dark border font-monospace"><?= Helper::e($log['entity_type']) ?></span></td>
                                <td style="max-width: 350px;">
                                    <?= Helper::e($log['description']) ?>
                                </td>
                                <td class="font-monospace text-muted small"><?= Helper::e($log['ip_address'] ?? '127.0.0.1') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

