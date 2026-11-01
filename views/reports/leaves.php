<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Báo Cáo Tình Hình Nghỉ Phép Năm <?= $year ?></h4>
            <small class="text-muted">Thống kê số ngày nghỉ phép năm, nghỉ ốm và nghỉ không lương theo từng nhân viên</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Helper::url('reports', 'leaves', ['year' => $year, 'export' => 1]) ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Xuất Báo Cáo Excel (CSV)
            </a>
            <a href="<?= Helper::url('reports') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Báo Cáo Tổng Hợp
            </a>
        </div>
    </div>

    <!-- Bộ lọc năm -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2 align-items-center">
                <input type="hidden" name="controller" value="reports">
                <input type="hidden" name="action" value="leaves">

                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">Chọn Năm:</span>
                        <select name="year" class="form-select">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>Năm <?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Xem Báo Cáo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng số liệu nghỉ phép -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã NV</th>
                        <th>Họ và Tên</th>
                        <th>Phòng Ban</th>
                        <th class="text-center">Số Đơn Gửi</th>
                        <th class="text-center">Tổng Ngày Phép Đã Duyệt</th>
                        <th class="text-center">Phép Năm</th>
                        <th class="text-center">Nghỉ Ốm</th>
                        <th class="text-center">Không Lương</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leaveReport)): ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">Không có dữ liệu trong Năm <?= $year ?>.</td></tr>
                    <?php else: ?>
                        <?php foreach ($leaveReport as $idx => $row): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($row['employee_code']) ?></span></td>
                                <td class="fw-bold text-dark"><?= Helper::e($row['full_name']) ?></td>
                                <td><?= Helper::e($row['department_name'] ?? '-') ?></td>
                                <td class="text-center"><?= $row['total_requests'] ?> đơn</td>
                                <td class="text-center fw-bold text-primary fs-6"><?= $row['approved_days'] ?: 0 ?> ngày</td>
                                <td class="text-center"><?= $row['annual_days'] ?: 0 ?></td>
                                <td class="text-center"><?= $row['sick_days'] ?: 0 ?></td>
                                <td class="text-center text-danger"><?= $row['unpaid_days'] ?: 0 ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

