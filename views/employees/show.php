<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Chi Tiết Hồ Sơ Nhân Viên</h4>
            <small class="text-muted">Xem toàn bộ thông tin nhân sự, hợp đồng và lịch sử công tác</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Helper::url('employees') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Danh Sách
            </a>
            <a href="<?= Helper::url('employees', 'edit', ['id' => $employee['id']]) ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Chỉnh Sửa
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Tóm tắt thông tin cá nhân -->
        <div class="col-lg-4">
            <div class="card text-center p-4 mb-4">
                <div class="mb-3">
                    <img src="<?= Helper::e($employee['avatar'] ?: 'assets/img/avatar-default.png') ?>" alt="Avatar" width="110" height="110" class="rounded-circle border border-3 border-primary p-1">
                </div>
                <h5 class="fw-bold mb-1"><?= Helper::e($employee['full_name']) ?></h5>
                <p class="text-muted small mb-2 font-monospace"><?= Helper::e($employee['employee_code']) ?></p>
                <div>
                    <?= Helper::statusBadge($employee['status']) ?>
                </div>

                <hr class="my-4">

                <div class="text-start small">
                    <div class="mb-2"><strong class="text-muted">Phòng ban:</strong> <span class="fw-semibold"><?= Helper::e($employee['department_name'] ?? 'Chưa phân bổ') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Chức vụ:</strong> <span class="fw-semibold"><?= Helper::e($employee['position_name'] ?? 'Chưa xếp') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Ngày vào làm:</strong> <span class="fw-semibold"><?= Helper::formatDate($employee['hire_date']) ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Email:</strong> <span class="fw-semibold"><?= Helper::e($employee['email']) ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Điện thoại:</strong> <span class="fw-semibold"><?= Helper::e($employee['phone'] ?: '-') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Giới tính:</strong> <span class="fw-semibold"><?= $employee['gender'] === 'male' ? 'Nam' : ($employee['gender'] === 'female' ? 'Nữ' : 'Khác') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Ngày sinh:</strong> <span class="fw-semibold"><?= Helper::formatDate($employee['date_of_birth']) ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Địa chỉ:</strong> <span class="fw-semibold"><?= Helper::e($employee['address'] ?: '-') ?></span></div>
                    <div class="mb-2"><strong class="text-muted">Lương cơ bản:</strong> <span class="fw-bold text-success"><?= Helper::formatMoney($employee['base_salary']) ?></span></div>
                </div>

                <hr class="my-3">

                <!-- Tài khoản hệ thống liên kết -->
                <div class="text-start small">
                    <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Tài khoản đăng nhập:</h6>
                    <?php if ($employee['user_account_id']): ?>
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                            <div>
                                <span class="fw-bold"><i class="bi bi-shield-check text-success me-1"></i><?= Helper::e($employee['username']) ?></span>
                                <div class="text-muted" style="font-size: 0.7rem;"><?= Helper::e($employee['role_display']) ?></div>
                            </div>
                            <?= Helper::statusBadge($employee['user_status']) ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-2 mb-0 small">
                            Chưa có tài khoản.
                            <?php if (Helper::hasRole('admin')): ?>
                                <a href="<?= Helper::url('users') ?>" class="fw-bold text-decoration-none">Cấp tài khoản &rarr;</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Cột phải: Hợp đồng, Chấm công, Bảng lương -->
        <div class="col-lg-8">
            <!-- 1. Hợp đồng lao động -->
            <div class="card mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text-fill me-2 text-primary"></i> Hợp Đồng Lao Động</h6>
                    <a href="<?= Helper::url('contracts', 'create') ?>" class="btn btn-outline-primary btn-sm py-0">Thêm Hợp Đồng</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Mã HĐ</th>
                                <th>Loại hợp đồng</th>
                                <th>Thời hạn</th>
                                <th>Lương cơ bản</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contracts)): ?>
                                <tr><td colspan="5" class="text-center py-3 text-muted">Chưa có hợp đồng nào được lưu.</td></tr>
                            <?php else: ?>
                                <?php foreach ($contracts as $c): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace"><?= Helper::e($c['contract_code']) ?></td>
                                        <td><?= Helper::contractTypeName($c['contract_type']) ?></td>
                                        <td><?= Helper::formatDate($c['start_date']) ?> &rarr; <?= $c['end_date'] ? Helper::formatDate($c['end_date']) : 'Vô thời hạn' ?></td>
                                        <td class="fw-bold"><?= Helper::formatMoney($c['basic_salary']) ?></td>
                                        <td><?= Helper::statusBadge($c['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Lịch sử chấm công gần đây -->
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check-fill me-2 text-success"></i> Chấm Công Tháng <?= date('m/Y') ?></h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Giờ vào</th>
                                <th>Giờ ra</th>
                                <th>Số giờ</th>
                                <th>Trạng thái</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentAttendance)): ?>
                                <tr><td colspan="6" class="text-center py-3 text-muted">Chưa có dữ liệu chấm công tháng này.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentAttendance as $att): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= Helper::formatDate($att['date']) ?></td>
                                        <td><?= Helper::formatTime($att['check_in']) ?></td>
                                        <td><?= Helper::formatTime($att['check_out']) ?></td>
                                        <td><strong><?= $att['work_hours'] ?></strong> h</td>
                                        <td><?= Helper::statusBadge($att['status']) ?></td>
                                        <td class="text-muted"><?= Helper::e($att['notes'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Phiếu lương gần đây -->
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-warning"></i> Lịch Sử Chi Trả Lương</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Kỳ Lương</th>
                                <th>Lương Cơ Bản</th>
                                <th>Ngày Công</th>
                                <th>Thực Nhận</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payslips)): ?>
                                <tr><td colspan="5" class="text-center py-3 text-muted">Chưa có bảng lương nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($payslips as $sl): ?>
                                    <tr>
                                        <td class="fw-bold">Tháng <?= $sl['month'] ?>/<?= $sl['year'] ?></td>
                                        <td><?= Helper::formatMoney($sl['base_salary']) ?></td>
                                        <td><?= $sl['actual_work_days'] ?> / <?= $sl['standard_work_days'] ?></td>
                                        <td class="fw-bold text-success"><?= Helper::formatMoney($sl['net_salary']) ?></td>
                                        <td><?= Helper::statusBadge($sl['status']) ?></td>
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

