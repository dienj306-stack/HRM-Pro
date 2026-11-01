<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Báo Cáo Chấm Công Tháng <?= $month ?>/<?= $year ?></h4>
            <small class="text-muted">Tổng hợp số lượt làm việc, đi muộn và thời lượng lao động từng nhân viên</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Helper::url('reports', 'attendance', ['month' => $month, 'year' => $year, 'department_id' => $departmentId, 'export' => 1]) ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Xuất Báo Cáo Excel (CSV)
            </a>
            <a href="<?= Helper::url('reports') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Báo Cáo Tổng Hợp
            </a>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2 align-items-center">
                <input type="hidden" name="controller" value="reports">
                <input type="hidden" name="action" value="attendance">

                <div class="col-md-3">
                    <select name="month" class="form-select form-select-sm">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="year" class="form-select form-select-sm">
                        <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                            <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>Năm <?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="">-- Tất cả phòng ban --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($departmentId == $dept['id']) ? 'selected' : '' ?>>
                                <?= Helper::e($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Xem Báo Cáo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng số liệu chấm công tháng -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã NV</th>
                        <th>Họ và Tên</th>
                        <th>Phòng Ban</th>
                        <th class="text-center">Số Ngày Có Chấm Công</th>
                        <th class="text-center">Đúng Giờ</th>
                        <th class="text-center">Đi Muộn</th>
                        <th class="text-center">Tổng Giờ Làm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendanceReport)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Không có dữ liệu trong kỳ báo cáo này.</td></tr>
                    <?php else: ?>
                        <?php foreach ($attendanceReport as $idx => $row): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($row['employee_code']) ?></span></td>
                                <td class="fw-bold text-dark"><?= Helper::e($row['full_name']) ?></td>
                                <td><?= Helper::e($row['department_name'] ?? '-') ?></td>
                                <td class="text-center fw-bold fs-6"><?= $row['total_checkins'] ?> ngày</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle me-1"></i><?= $row['total_on_time'] ?></td>
                                <td class="text-center <?= $row['total_late'] > 0 ? 'text-warning fw-bold' : 'text-muted' ?>">
                                    <?= $row['total_late'] > 0 ? '<i class="bi bi-exclamation-triangle me-1"></i>' . $row['total_late'] : '0' ?>
                                </td>
                                <td class="text-center fw-bold text-primary"><?= $row['total_work_hours'] ?> giờ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

