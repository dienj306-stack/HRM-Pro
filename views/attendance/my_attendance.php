<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Lịch Sử Chấm Công Cá Nhân</h4>
            <small class="text-muted">Theo dõi ngày công và thời gian làm việc hàng tháng</small>
        </div>
        <a href="<?= Helper::url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Bảng Điều Khiển
        </a>
    </div>

    <!-- Hàng thống kê nhanh -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Tổng Ngày Công Tháng <?= $selectedMonth ?>/<?= $selectedYear ?></div>
                <h3 class="fw-bold my-1 text-primary"><?= $totalWorkedDays ?> <span class="fs-6 text-muted">/ 22 ngày</span></h3>
                <small class="text-muted">Tính theo số lượt có chấm công</small>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Trạng Thái Hôm Nay</div>
                <h4 class="fw-bold my-1">
                    <?php if (!$todayRecord): ?>
                        <span class="text-danger small">Chưa chấm công</span>
                    <?php elseif (empty($todayRecord['check_out'])): ?>
                        <span class="text-warning small">Đang làm việc</span>
                    <?php else: ?>
                        <span class="text-success small">Đã hoàn thành</span>
                    <?php endif; ?>
                </h4>
                <small class="text-muted"><?= date('d/m/Y') ?></small>
            </div>
        </div>

        <div class="col-sm-12 col-md-6">
            <!-- Bộ lọc tháng / năm -->
            <div class="card p-3 h-100 justify-content-center">
                <form method="GET" action="index.php" class="row g-2 align-items-center">
                    <input type="hidden" name="controller" value="attendance">
                    <input type="hidden" name="action" value="myAttendance">

                    <div class="col-5">
                        <select name="month" class="form-select form-select-sm">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($selectedMonth == $m) ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-4">
                        <select name="year" class="form-select form-select-sm">
                            <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= ($selectedYear == $y) ? 'selected' : '' ?>>Năm <?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Xem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bảng chi tiết chấm công trong tháng -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-calendar3 me-2 text-primary"></i> Chi Tiết Chấm Công Tháng <?= $selectedMonth ?>/<?= $selectedYear ?></h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Ngày</th>
                        <th>Thứ</th>
                        <th>Giờ Vào</th>
                        <th>Giờ Ra</th>
                        <th>Thời Gian Làm</th>
                        <th>Trạng Thái</th>
                        <th>Ghi Chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Không có dữ liệu chấm công trong Tháng <?= $selectedMonth ?>/<?= $selectedYear ?>.</td></tr>
                    <?php else: ?>
                        <?php foreach ($history as $idx => $row): ?>
                            <?php
                                $dayOfWeek = date('l', strtotime($row['date']));
                                $daysVi = [
                                    'Monday'    => 'Thứ Hai',
                                    'Tuesday'   => 'Thứ Ba',
                                    'Wednesday' => 'Thứ Tư',
                                    'Thursday'  => 'Thứ Năm',
                                    'Friday'    => 'Thứ Sáu',
                                    'Saturday'  => 'Thứ Bảy',
                                    'Sunday'    => 'Chủ Nhật'
                                ];
                            ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-semibold"><?= Helper::formatDate($row['date']) ?></td>
                                <td><span class="badge bg-light text-secondary border"><?= $daysVi[$dayOfWeek] ?? $dayOfWeek ?></span></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= Helper::formatTime($row['check_in']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $row['check_out'] ? '<span class="badge bg-light text-dark border">' . Helper::formatTime($row['check_out']) . '</span>' : '-' ?>
                                </td>
                                <td><strong><?= $row['work_hours'] ?></strong> giờ</td>
                                <td><?= Helper::statusBadge($row['status']) ?></td>
                                <td class="text-muted small"><?= Helper::e($row['notes'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

