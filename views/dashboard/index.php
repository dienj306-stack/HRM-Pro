<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Bảng Điều Khiển Quản Trị</h4>
            <small class="text-muted">Tổng quan tình hình nhân sự và chấm công thời gian thực</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Helper::url('attendance') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-clock me-1"></i> Bảng Chấm Công
            </a>
            <a href="<?= Helper::url('employees', 'create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Thêm Nhân Viên
            </a>
        </div>
    </div>

    <!-- 4 Thẻ Thống Kê Chính -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Tổng Nhân Viên</div>
                        <h2 class="fw-bold my-1 text-primary"><?= $empStats['total'] ?></h2>
                        <small class="text-success"><i class="bi bi-person-check-fill me-1"></i><?= $empStats['active'] ?> đang làm việc</small>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Chấm Công Hôm Nay</div>
                        <h2 class="fw-bold my-1 text-success"><?= $attendanceToday['checked_in'] ?></h2>
                        <small class="text-muted"><?= $attendanceToday['on_time'] ?> đúng giờ, <span class="text-warning fw-bold"><?= $attendanceToday['late'] ?> muộn</span></small>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Đơn Chờ Duyệt</div>
                        <h2 class="fw-bold my-1 text-warning"><?= $pendingLeaves ?></h2>
                        <small class="text-muted"><a href="<?= Helper::url('leaves') ?>" class="text-decoration-none">Xem và xét duyệt ngay &rarr;</a></small>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-envelope-exclamation-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Phòng Ban & Vị Trí</div>
                        <h2 class="fw-bold my-1 text-info"><?= $totalDepartments ?></h2>
                        <small class="text-muted"><?= $totalPositions ?> chức danh vị trí</small>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 2: Biểu đồ và Tình hình chấm công hôm nay -->
    <div class="row g-4 mb-4">
        <!-- Biểu đồ cơ cấu nhân sự theo phòng ban -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i> Cơ Cấu Nhân Sự Theo Phòng Ban</h6>
                    <span class="badge bg-light text-secondary border">Thực tế</span>
                </div>
                <div class="card-body p-3">
                    <div style="height: 260px;">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tỷ lệ chấm công hôm nay -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-success"></i> Chấm Công Ngày Hôm Nay (<?= date('d/m/Y') ?>)</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <span>Đã chấm công (<?= $attendanceToday['checked_in'] ?>/<?= $attendanceToday['total_active'] ?>)</span>
                            <?php $rate = $attendanceToday['total_active'] > 0 ? round(($attendanceToday['checked_in'] / $attendanceToday['total_active']) * 100) : 0; ?>
                            <span><?= $rate ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?= $rate ?>%"></div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush mb-0 small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-check-circle-fill text-success me-2"></i>Đúng giờ (trước 08:30)</span>
                            <span class="badge bg-success rounded-pill"><?= $attendanceToday['on_time'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-exclamation-circle-fill text-warning me-2"></i>Đi muộn (sau 08:30)</span>
                            <span class="badge bg-warning text-dark rounded-pill"><?= $attendanceToday['late'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-dash-circle-fill text-danger me-2"></i>Chưa chấm công</span>
                            <span class="badge bg-danger rounded-pill"><?= $attendanceToday['not_checked_in'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 3: Đơn nghỉ phép chờ duyệt và Lịch sử chấm công mới nhất -->
    <div class="row g-4">
        <!-- Đơn nghỉ phép cần duyệt -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-inbox-fill me-2 text-warning"></i> Đơn Nghỉ Phép Gần Đây</h6>
                    <a href="<?= Helper::url('leaves') ?>" class="small text-decoration-none">Xem tất cả &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Nhân viên</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentLeaves)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có đơn nghỉ phép nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentLeaves as $lr): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= Helper::e($lr['request_code']) ?></td>
                                        <td>
                                            <div class="fw-semibold"><?= Helper::e($lr['employee_name']) ?></div>
                                            <small class="text-muted"><?= Helper::e($lr['department_name'] ?? '') ?></small>
                                        </td>
                                        <td><?= Helper::formatDate($lr['start_date']) ?> - <?= Helper::formatDate($lr['end_date']) ?></td>
                                        <td><?= Helper::statusBadge($lr['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lượt chấm công hôm nay -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock me-2 text-primary"></i> Lượt Chấm Công Hôm Nay</h6>
                    <a href="<?= Helper::url('attendance') ?>" class="small text-decoration-none">Xem đầy đủ &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Giờ vào</th>
                                <th>Giờ ra</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($todayAttendanceList)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Hôm nay chưa có lượt chấm công nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($todayAttendanceList as $att): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= Helper::e($att['full_name']) ?></div>
                                            <small class="text-muted"><?= Helper::e($att['employee_code']) ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= Helper::formatTime($att['check_in']) ?></span></td>
                                        <td><?= $att['check_out'] ? '<span class="badge bg-light text-dark border">' . Helper::formatTime($att['check_out']) . '</span>' : '-' ?></td>
                                        <td><?= Helper::statusBadge($att['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Khởi tạo biểu đồ phòng ban -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('deptChart');
    if (ctx) {
        const labels = <?= json_encode(array_column($deptDistribution, 'department_name'), JSON_UNESCAPED_UNICODE) ?>;
        const data = <?= json_encode(array_map('intval', array_column($deptDistribution, 'total'))) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Số lượng nhân viên',
                    data: data,
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#6366f1', '#ec4899'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>

