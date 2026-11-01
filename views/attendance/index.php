<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Quản Lý Dữ Liệu Chấm Công</h4>
            <small class="text-muted">Theo dõi giờ vào, giờ ra và tình trạng đi muộn của toàn bộ nhân sự</small>
        </div>
        <a href="<?= Helper::url('attendance', 'myAttendance') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-clock me-1"></i> Chấm Công Của Tôi
        </a>
    </div>

    <!-- 4 Thẻ tóm tắt ngày được chọn -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Đã Chấm Công Vào</div>
                <h3 class="fw-bold my-1 text-primary"><?= $todayStats['checked_in'] ?> <small class="fs-6 text-muted">/ <?= $todayStats['total_active'] ?></small></h3>
                <small class="text-muted">Tỷ lệ: <?= $todayStats['total_active'] > 0 ? round(($todayStats['checked_in'] / $todayStats['total_active']) * 100) : 0 ?>%</small>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Đúng Giờ (&le; 08:30)</div>
                <h3 class="fw-bold my-1 text-success"><?= $todayStats['on_time'] ?></h3>
                <small class="text-success"><i class="bi bi-check-circle me-1"></i>Đạt chuẩn</small>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Đi Muộn (> 08:30)</div>
                <h3 class="fw-bold my-1 text-warning"><?= $todayStats['late'] ?></h3>
                <small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Cần nhắc nhở</small>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Chưa Chấm Công</div>
                <h3 class="fw-bold my-1 text-danger"><?= $todayStats['not_checked_in'] ?></h3>
                <small class="text-danger"><i class="bi bi-x-circle me-1"></i>Chưa ghi nhận</small>
            </div>
        </div>
    </div>

    <!-- Bộ lọc ngày và phòng ban -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="index.php" class="row g-2 align-items-center">
                <input type="hidden" name="controller" value="attendance">
                <input type="hidden" name="action" value="index">

                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i> Ngày xem:</span>
                        <input type="date" name="date" class="form-control" value="<?= Helper::e($selectedDate) ?>">
                    </div>
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

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i> Xem Dữ Liệu
                    </button>
                    <a href="<?= Helper::url('attendance') ?>" class="btn btn-outline-secondary btn-sm" title="Hôm nay">
                        Hôm nay
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng dữ liệu chấm công -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Mã NV</th>
                        <th>Họ và Tên</th>
                        <th>Phòng Ban</th>
                        <th>Ngày</th>
                        <th>Giờ Vào</th>
                        <th>Giờ Ra</th>
                        <th>Thời Gian Làm</th>
                        <th>Trạng Thái</th>
                        <th>Ghi Chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendanceList)): ?>
                        <tr><td colspan="10" class="text-center py-5 text-muted">Không có dữ liệu chấm công cho ngày <?= Helper::formatDate($selectedDate) ?>.</td></tr>
                    <?php else: ?>
                        <?php foreach ($attendanceList as $idx => $att): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($att['employee_code']) ?></span></td>
                                <td class="fw-bold text-dark"><?= Helper::e($att['full_name']) ?></td>
                                <td><?= Helper::e($att['department_name'] ?? '-') ?></td>
                                <td><?= Helper::formatDate($att['date']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-box-arrow-in-right text-success me-1"></i><?= Helper::formatTime($att['check_in']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($att['check_out']): ?>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-box-arrow-right text-danger me-1"></i><?= Helper::formatTime($att['check_out']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Chưa check-out</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $att['work_hours'] ?></strong> giờ
                                </td>
                                <td><?= Helper::statusBadge($att['status']) ?></td>
                                <td class="text-muted small"><?= Helper::e($att['notes'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

