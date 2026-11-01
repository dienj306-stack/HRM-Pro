<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Phiếu Lương Của Tôi</h4>
            <small class="text-muted">Theo dõi chi tiết các khoản thu nhập, thưởng và khấu trừ theo từng kỳ</small>
        </div>
        <a href="<?= Helper::url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Bảng Điều Khiển
        </a>
    </div>

    <?php if (empty($myPayslips)): ?>
        <div class="card p-5 text-center shadow-sm">
            <i class="bi bi-wallet2 fs-1 text-muted mb-3"></i>
            <h5 class="fw-bold text-dark">Chưa có phiếu lương nào</h5>
            <p class="text-muted small">Bộ phận nhân sự chưa phát hành phiếu lương cho tài khoản của bạn.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($myPayslips as $slip): ?>
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <h6 class="mb-0 fw-bold text-primary">
                                    <i class="bi bi-receipt me-2"></i> Phiếu Lương Tháng <?= $slip['month'] ?>/<?= $slip['year'] ?>
                                </h6>
                                <small class="text-muted font-monospace"><?= Helper::e($slip['record_code']) ?></small>
                            </div>
                            <div>
                                <?= Helper::statusBadge($slip['status']) ?>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-2 mb-3 pb-3 border-bottom small">
                                <div class="col-6">
                                    <span class="text-muted">Lương cơ bản:</span>
                                    <div class="fw-bold"><?= Helper::formatMoney($slip['base_salary']) ?></div>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted">Ngày công:</span>
                                    <div class="fw-bold"><?= $slip['actual_work_days'] ?> / <?= $slip['standard_work_days'] ?> ngày</div>
                                </div>
                            </div>

                            <div class="list-group list-group-flush mb-3 small">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                    <span class="text-muted"><i class="bi bi-plus-circle text-success me-2"></i>Phụ cấp ăn trưa & đi lại</span>
                                    <span class="fw-semibold"><?= Helper::formatMoney($slip['allowance']) ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                    <span class="text-muted"><i class="bi bi-plus-circle text-success me-2"></i>Thưởng hiệu quả công việc</span>
                                    <span class="fw-semibold text-success">+<?= Helper::formatMoney($slip['bonus']) ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                    <span class="text-muted"><i class="bi bi-dash-circle text-danger me-2"></i>Các khoản giảm trừ</span>
                                    <span class="fw-semibold text-danger">-<?= Helper::formatMoney($slip['deduction']) ?></span>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small text-muted fw-bold text-uppercase">Thực Lĩnh Chuyển Khoản:</div>
                                    <?php if ($slip['status'] === 'paid'): ?>
                                        <small class="text-success"><i class="bi bi-check-all me-1"></i>Đã thanh toán ngày <?= Helper::formatDate($slip['paid_date']) ?></small>
                                    <?php else: ?>
                                        <small class="text-warning"><i class="bi bi-clock me-1"></i>Đang chờ giải ngân</small>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <h4 class="mb-0 fw-bold text-primary"><?= Helper::formatMoney($slip['net_salary']) ?></h4>
                                </div>
                            </div>

                            <?php if ($slip['notes']): ?>
                                <div class="mt-3 small text-muted">
                                    <strong>Ghi chú:</strong> <?= Helper::e($slip['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

