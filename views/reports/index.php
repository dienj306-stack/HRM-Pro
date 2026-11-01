<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Trung Tâm Báo Cáo & Thống Kê</h4>
            <small class="text-muted">Tổng hợp dữ liệu nhân sự, cơ cấu tổ chức và phân bổ nguồn lực</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Helper::url('reports', 'attendance') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-clock-history me-1"></i> Báo Cáo Chấm Công
            </a>
            <a href="<?= Helper::url('reports', 'leaves') ?>" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-calendar-event me-1"></i> Báo Cáo Nghỉ Phép
            </a>
        </div>
    </div>

    <!-- Hàng thống kê tổng quan -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Tổng Số Nhân Viên</div>
                <h3 class="fw-bold my-1 text-primary"><?= $overviewStats['total'] ?></h3>
                <small class="text-success"><i class="bi bi-check-circle me-1"></i><?= $overviewStats['active'] ?> đang làm việc</small>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Nhân Sự Thử Việc</div>
                <h3 class="fw-bold my-1 text-info"><?= $overviewStats['probation'] ?></h3>
                <small class="text-muted">Đang trong thời gian đánh giá</small>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Tạm Nghỉ / Chế Độ</div>
                <h3 class="fw-bold my-1 text-warning"><?= $overviewStats['on_leave'] ?></h3>
                <small class="text-muted">Nghỉ thai sản hoặc việc riêng</small>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Tổng Phòng Ban</div>
                <h3 class="fw-bold my-1 text-secondary"><?= count($deptStats) ?></h3>
                <small class="text-muted">Đang hoạt động</small>
            </div>
        </div>
    </div>

    <!-- Hàng bảng và biểu đồ cơ cấu -->
    <div class="row g-4">
        <!-- Bảng thống kê theo phòng ban -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-diagram-3-fill me-2 text-primary"></i> Phân Bổ Nhân Sự Theo Phòng Ban</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mã PB</th>
                                <th>Tên Phòng Ban</th>
                                <th class="text-center">Số Nhân Sự</th>
                                <th>Tỷ Lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deptStats as $dept): ?>
                                <?php $pct = $overviewStats['total'] > 0 ? round(($dept['total'] / $overviewStats['total']) * 100, 1) : 0; ?>
                                <tr>
                                    <td><span class="badge bg-light text-primary border font-monospace"><?= Helper::e($dept['code']) ?></span></td>
                                    <td class="fw-semibold"><?= Helper::e($dept['department_name']) ?></td>
                                    <td class="text-center fw-bold"><?= $dept['total'] ?> người</td>
                                    <td style="width: 180px;">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: <?= $pct ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $pct ?>%</small>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Biểu đồ cơ cấu giới tính -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-gender-ambiguous me-2 text-info"></i> Tỷ Lệ Giới Tính</h6>
                </div>
                <div class="card-body p-4 text-center">
                    <div style="max-height: 240px;">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('genderChart');
    if (ctx) {
        const rawGender = <?= json_encode($genderStats, JSON_UNESCAPED_UNICODE) ?>;
        const labels = rawGender.map(item => item.gender === 'male' ? 'Nam' : (item.gender === 'female' ? 'Nữ' : 'Khác'));
        const data = rawGender.map(item => parseInt(item.total));

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#2563eb', '#ec4899', '#94a3b8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>

