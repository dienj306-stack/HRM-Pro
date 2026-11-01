<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Quản Lý Bảng Tính Lương</h4>
            <small class="text-muted">Tính toán và quản lý chi trả lương theo kỳ tháng cho toàn bộ nhân sự</small>
        </div>
        <div class="d-flex gap-2">
            <!-- Xuất CSV mở Excel -->
            <a href="<?= Helper::url('payroll', 'export', ['month' => $month, 'year' => $year]) ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel (CSV)
            </a>

            <!-- Nút Khởi tạo bảng lương tự động -->
            <form method="POST" action="<?= Helper::url('payroll', 'generate') ?>" class="confirm-action d-inline" data-confirm="Hệ thống sẽ tự động tính toán số ngày công và khởi tạo bảng lương cho toàn bộ nhân sự đang làm việc trong Tháng <?= $month ?>/<?= $year ?>. Tiếp tục?">
                <?= Helper::csrfField() ?>
                <input type="hidden" name="month" value="<?= $month ?>">
                <input type="hidden" name="year" value="<?= $year ?>">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-gear-wide-connected me-1"></i> Tính Lương Tháng <?= $month ?>/<?= $year ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Hàng thống kê tổng quỹ lương -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Tổng Quỹ Lương Tháng <?= $month ?>/<?= $year ?></div>
                <h3 class="fw-bold my-1 text-primary"><?= Helper::formatMoney($totalPayroll) ?></h3>
                <small class="text-muted"><?= count($salaries) ?> nhân viên trong kỳ</small>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="text-muted small fw-semibold">Đã Chi Trả (Thực Tế)</div>
                <h3 class="fw-bold my-1 text-success"><?= Helper::formatMoney($totalPaid) ?></h3>
                <small class="text-success"><i class="bi bi-check-circle me-1"></i>Đã giải ngân</small>
            </div>
        </div>

        <div class="col-sm-12 col-md-4">
            <!-- Bộ lọc tháng và phòng ban -->
            <div class="card p-3 h-100 justify-content-center">
                <form method="GET" action="index.php" class="row g-2 align-items-center">
                    <input type="hidden" name="controller" value="payroll">
                    <input type="hidden" name="action" value="index">

                    <div class="col-5">
                        <select name="month" class="form-select form-select-sm">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-4">
                        <select name="year" class="form-select form-select-sm">
                            <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>Năm <?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-3">
                        <button type="submit" class="btn btn-secondary btn-sm w-100">Lọc</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bảng lương chi tiết -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead>
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Nhân Viên</th>
                        <th>Lương Cơ Bản</th>
                        <th>Ngày Công</th>
                        <th>Phụ Cấp</th>
                        <th>Thưởng</th>
                        <th>Khấu Trừ</th>
                        <th>Thực Nhận</th>
                        <th>Trạng Thái</th>
                        <th class="text-end" style="width: 170px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($salaries)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                Chưa có bảng lương cho Tháng <?= $month ?>/<?= $year ?>.<br>
                                <span class="small">Bấm nút <strong>"Tính Lương Tháng <?= $month ?>/<?= $year ?>"</strong> phía trên để khởi tạo tự động.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($salaries as $s): ?>
                            <tr>
                                <td><span class="badge bg-light text-primary border font-monospace fw-bold"><?= Helper::e($s['record_code']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= Helper::e($s['employee_name']) ?></div>
                                    <small class="text-muted"><?= Helper::e($s['department_name'] ?? '') ?> (<?= Helper::e($s['employee_code']) ?>)</small>
                                </td>
                                <td><?= Helper::formatMoney($s['base_salary']) ?></td>
                                <td><strong><?= $s['actual_work_days'] ?></strong> / <?= $s['standard_work_days'] ?></td>
                                <td class="text-muted"><?= Helper::formatMoney($s['allowance']) ?></td>
                                <td class="text-success"><?= Helper::formatMoney($s['bonus']) ?></td>
                                <td class="text-danger"><?= Helper::formatMoney($s['deduction']) ?></td>
                                <td><span class="fw-bold text-primary fs-6"><?= Helper::formatMoney($s['net_salary']) ?></span></td>
                                <td><?= Helper::statusBadge($s['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Sửa điều chỉnh lương -->
                                        <a href="<?= Helper::url('payroll', 'edit', ['id' => $s['id']]) ?>" class="btn btn-outline-secondary" title="Điều chỉnh lương">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <?php if ($s['status'] === 'draft'): ?>
                                            <!-- Nút Duyệt lương -->
                                            <form method="POST" action="<?= Helper::url('payroll', 'approve') ?>" class="d-inline confirm-action" data-confirm="Xác nhận phê duyệt phiếu lương cho nhân viên [<?= Helper::e($s['employee_name']) ?>]?">
                                                <?= Helper::csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                <button type="submit" class="btn btn-outline-success" title="Phê duyệt">
                                                    <i class="bi bi-check-lg"></i> Duyệt
                                                </button>
                                            </form>
                                        <?php elseif ($s['status'] === 'approved'): ?>
                                            <!-- Nút Thanh toán -->
                                            <form method="POST" action="<?= Helper::url('payroll', 'pay') ?>" class="d-inline confirm-action" data-confirm="Xác nhận đã chi trả chuyển khoản tiền lương cho nhân viên [<?= Helper::e($s['employee_name']) ?>]?">
                                                <?= Helper::csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                <button type="submit" class="btn btn-success" title="Xác nhận đã thanh toán">
                                                    <i class="bi bi-cash"></i> Chi trả
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-light text-success border align-self-center"><i class="bi bi-check-all me-1"></i>Đã trả</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

