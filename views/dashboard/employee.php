<div class="container-fluid">
    <!-- Header Cá Nhân -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h4 class="mb-1 fw-bold">Xin chào, <?= Helper::e($employee['full_name'] ?? $user['username']) ?>! 👋</h4>
            <p class="text-muted small mb-0">
                <?= Helper::e($employee['position_name'] ?? 'Nhân viên') ?> &bull; 
                <?= Helper::e($employee['department_name'] ?? 'Phòng ban') ?> &bull; 
                Mã NV: <span class="badge bg-secondary"><?= Helper::e($employee['employee_code'] ?? 'NV') ?></span>
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= Helper::url('leaves', 'create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-calendar-plus me-1"></i> Gửi Đơn Nghỉ Phép
            </a>
        </div>
    </div>

    <!-- Hàng thẻ Chấm công và Thống kê tháng -->
    <div class="row g-4 mb-4">
        <!-- Hộp chấm công vào/ra trực tiếp -->
        <div class="col-lg-5">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-fingerprint me-2"></i> Chấm Công Hôm Nay (<?= date('d/m/Y') ?>)</h6>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="display-6 fw-bold text-dark" id="liveClockEmployee"><?= date('H:i:s') ?></div>
                        <small class="text-muted">Giờ làm việc chuẩn: 08:30 - 17:30</small>
                    </div>

                    <?php if (!$todayRecord): ?>
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i> Bạn chưa thực hiện chấm công vào hôm nay.
                        </div>
                        <form method="POST" action="<?= Helper::url('attendance', 'checkIn') ?>">
                            <?= Helper::csrfField() ?>
                            <div class="mb-3">
                                <input type="text" name="notes" class="form-control form-control-sm text-center" placeholder="Ghi chú (tùy chọn)...">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100 py-2 shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i> CHẤM CÔNG VÀO
                            </button>
                        </form>
                    <?php elseif (empty($todayRecord['check_out'])): ?>
                        <div class="alert alert-success py-2 small mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Đã vào lúc: <strong><?= Helper::formatTime($todayRecord['check_in']) ?></strong> (<?= Helper::statusBadge($todayRecord['status']) ?>)
                        </div>
                        <form method="POST" action="<?= Helper::url('attendance', 'checkOut') ?>">
                            <?= Helper::csrfField() ?>
                            <div class="mb-3">
                                <input type="text" name="notes" class="form-control form-control-sm text-center" placeholder="Ghi chú trước khi về...">
                            </div>
                            <button type="submit" class="btn btn-warning btn-lg w-100 py-2 text-dark shadow-sm">
                                <i class="bi bi-box-arrow-right me-2"></i> CHẤM CÔNG RA
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-success py-3 mb-0">
                            <i class="bi bi-check-circle-fill fs-3 text-success d-block mb-2"></i>
                            <h6 class="fw-bold mb-1">Đã hoàn thành chấm công hôm nay!</h6>
                            <div class="small text-muted mb-2">
                                Vào: <strong><?= Helper::formatTime($todayRecord['check_in']) ?></strong> &bull; 
                                Ra: <strong><?= Helper::formatTime($todayRecord['check_out']) ?></strong>
                            </div>
                            <span class="badge bg-primary px-3 py-2">Tổng thời gian: <?= $todayRecord['work_hours'] ?> giờ</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Thống kê tổng hợp cá nhân tháng này -->
        <div class="col-lg-7">
            <div class="row g-3 h-100">
                <div class="col-sm-6">
                    <div class="card stat-card p-3 h-100">
                        <div class="text-muted small fw-semibold">Ngày Công Tháng <?= date('m') ?></div>
                        <h2 class="fw-bold my-2 text-primary"><?= $currentMonthDays ?> <span class="fs-6 text-muted">/ 22 ngày</span></h2>
                        <small class="text-muted"><a href="<?= Helper::url('attendance', 'myAttendance') ?>" class="text-decoration-none">Chi tiết chấm công &rarr;</a></small>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card stat-card p-3 h-100">
                        <div class="text-muted small fw-semibold">Lương Cơ Bản Hợp Đồng</div>
                        <h3 class="fw-bold my-2 text-success"><?= Helper::formatMoney($employee['base_salary'] ?? 0) ?></h3>
                        <small class="text-muted"><a href="<?= Helper::url('payroll', 'myPayroll') ?>" class="text-decoration-none">Xem phiếu lương &rarr;</a></small>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold small text-muted text-uppercase">Phiếu lương gần nhất</h6>
                            <a href="<?= Helper::url('payroll', 'myPayroll') ?>" class="small text-decoration-none">Tất cả</a>
                        </div>
                        <div class="card-body p-3">
                            <?php if (empty($myPayslips)): ?>
                                <p class="text-muted small mb-0">Chưa có bảng lương nào được phát hành.</p>
                            <?php else: ?>
                                <?php $latestPayslip = $myPayslips[0]; ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Kỳ Lương Tháng <?= $latestPayslip['month'] ?>/<?= $latestPayslip['year'] ?></div>
                                        <small class="text-muted"><?= Helper::statusBadge($latestPayslip['status']) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fs-5 fw-bold text-success"><?= Helper::formatMoney($latestPayslip['net_salary']) ?></div>
                                        <small class="text-muted">Thực lĩnh</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng danh sách đơn nghỉ phép của tôi -->
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> Đơn Nghỉ Phép Của Tôi Gần Đây</h6>
            <a href="<?= Helper::url('leaves', 'myLeaves') ?>" class="small text-decoration-none">Xem tất cả &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Loại nghỉ</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th>Số ngày</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($myLeaves)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Bạn chưa gửi đơn xin nghỉ phép nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($myLeaves as $lr): ?>
                            <tr>
                                <td class="fw-semibold"><?= Helper::e($lr['request_code']) ?></td>
                                <td><?= Helper::leaveTypeBadge($lr['leave_type']) ?></td>
                                <td><?= Helper::formatDate($lr['start_date']) ?></td>
                                <td><?= Helper::formatDate($lr['end_date']) ?></td>
                                <td><strong><?= $lr['total_days'] ?></strong> ngày</td>
                                <td><?= Helper::e($lr['reason']) ?></td>
                                <td><?= Helper::statusBadge($lr['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
setInterval(function() {
    const clock = document.getElementById('liveClockEmployee');
    if (clock) {
        const now = new Date();
        clock.textContent = String(now.getHours()).padStart(2, '0') + ':' +
                            String(now.getMinutes()).padStart(2, '0') + ':' +
                            String(now.getSeconds()).padStart(2, '0');
    }
}, 1000);
</script>

