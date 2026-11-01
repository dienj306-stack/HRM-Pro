<?php
$currentUser = Helper::authUser();
$currentController = strtolower($_GET['controller'] ?? 'dashboard');
$currentAction = strtolower($_GET['action'] ?? 'index');

// Đếm thông báo chưa đọc và đơn chờ duyệt
$pendingLeaveCount = 0;
$unreadNotifCount = 0;
$userNotifications = [];

if ($currentUser) {
    if (in_array($currentUser['role_name'], ['admin', 'hr'])) {
        $leaveModel = new LeaveRequest();
        $pendingLeaveCount = $leaveModel->countPending();
    }
    $notifModel = new Notification();
    $unreadNotifCount = $notifModel->countUnread((int)$currentUser['id']);
    $userNotifications = $notifModel->getForUser((int)$currentUser['id'], 5);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle ?? 'Quản Lý') ?> - HRM Pro</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="<?= Helper::url('dashboard') ?>" class="text-white text-decoration-none d-flex align-items-center">
                <i class="bi bi-person-badge-fill fs-3 text-primary me-2"></i>
                <div>
                    <h5 class="mb-0 fw-bold">HRM Pro</h5>
                    <small class="text-white-50" style="font-size: 0.75rem;">Quản Lý Nhân Sự</small>
                </div>
            </a>
        </div>

        <ul class="list-unstyled components">
            <!-- Chung: Dashboard -->
            <li class="<?= $currentController === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= Helper::url('dashboard') ?>">
                    <i class="bi bi-speedometer2"></i> Bảng điều khiển
                </a>
            </li>

            <!-- MENU DÀNH CHO ADMIN & HR -->
            <?php if (Helper::hasRole(['admin', 'hr'])): ?>
                <div class="sidebar-heading">Quản Lý Nhân Sự</div>

                <li class="<?= $currentController === 'employees' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('employees') ?>">
                        <i class="bi bi-people-fill"></i> Hồ sơ nhân viên
                    </a>
                </li>

                <li class="<?= $currentController === 'departments' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('departments') ?>">
                        <i class="bi bi-diagram-3-fill"></i> Phòng ban
                    </a>
                </li>

                <li class="<?= $currentController === 'positions' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('positions') ?>">
                        <i class="bi bi-briefcase-fill"></i> Chức vụ
                    </a>
                </li>

                <li class="<?= $currentController === 'contracts' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('contracts') ?>">
                        <i class="bi bi-file-earmark-text-fill"></i> Hợp đồng lao động
                    </a>
                </li>

                <div class="sidebar-heading">Nghiệp Vụ Hàng Ngày</div>

                <li class="<?= $currentController === 'attendance' && $currentAction === 'index' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('attendance') ?>">
                        <i class="bi bi-calendar-check-fill"></i> Quản lý chấm công
                    </a>
                </li>

                <li class="<?= $currentController === 'leaves' && $currentAction === 'index' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('leaves') ?>" class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-envelope-paper-fill"></i> Duyệt nghỉ phép</span>
                        <?php if ($pendingLeaveCount > 0): ?>
                            <span class="badge bg-warning text-dark rounded-pill"><?= $pendingLeaveCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="<?= $currentController === 'payroll' && $currentAction === 'index' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('payroll') ?>">
                        <i class="bi bi-cash-stack"></i> Bảng tính lương
                    </a>
                </li>

                <li class="<?= $currentController === 'reports' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('reports') ?>">
                        <i class="bi bi-bar-chart-line-fill"></i> Báo cáo & Thống kê
                    </a>
                </li>
            <?php endif; ?>

            <!-- MENU DÀNH CHO NHÂN VIÊN (VÀ BỔ SUNG CHO TẤT CẢ) -->
            <div class="sidebar-heading">Cá Nhân</div>

            <li class="<?= $currentController === 'attendance' && $currentAction === 'myattendance' ? 'active' : '' ?>">
                <a href="<?= Helper::url('attendance', 'myAttendance') ?>">
                    <i class="bi bi-clock-history"></i> Chấm công của tôi
                </a>
            </li>

            <li class="<?= $currentController === 'leaves' && $currentAction === 'myleaves' ? 'active' : '' ?>">
                <a href="<?= Helper::url('leaves', 'myLeaves') ?>">
                    <i class="bi bi-send-fill"></i> Đơn xin nghỉ phép
                </a>
            </li>

            <li class="<?= $currentController === 'payroll' && $currentAction === 'mypayroll' ? 'active' : '' ?>">
                <a href="<?= Helper::url('payroll', 'myPayroll') ?>">
                    <i class="bi bi-wallet2"></i> Phiếu lương của tôi
                </a>
            </li>

            <!-- MENU DÀNH RIÊNG ADMIN -->
            <?php if (Helper::hasRole('admin')): ?>
                <div class="sidebar-heading">Hệ Thống</div>

                <li class="<?= $currentController === 'users' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('users') ?>">
                        <i class="bi bi-shield-lock-fill"></i> Tài khoản & Phân quyền
                    </a>
                </li>

                <li class="<?= $currentController === 'audit' ? 'active' : '' ?>">
                    <a href="<?= Helper::url('audit') ?>">
                        <i class="bi bi-journal-text"></i> Nhật ký hoạt động
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content Wrapper -->
    <div class="flex-grow-1 d-flex flex-column" style="min-width: 0; min-height: 100vh;">
        <!-- Topbar -->
        <header class="topbar px-4 py-2 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light me-3 border">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-flex align-items-center text-muted">
                    <i class="bi bi-clock me-1 text-primary"></i>
                    <span id="liveClock" class="fw-bold me-2 text-dark">--:--:--</span>
                    <span class="badge bg-light text-secondary border"><?= date('l, d/m/Y') ?></span>
                </div>
            </div>

            <!-- Topbar Right Items -->
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light position-relative border rounded-circle p-2" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5 text-secondary"></i>
                        <?php if ($unreadNotifCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $unreadNotifCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 320px;">
                        <li class="dropdown-header fw-bold border-bottom pb-2">Thông báo mới</li>
                        <?php if (empty($userNotifications)): ?>
                            <li class="p-3 text-center text-muted small">Không có thông báo mới</li>
                        <?php else: ?>
                            <?php foreach ($userNotifications as $notif): ?>
                                <li>
                                    <a class="dropdown-item py-2 px-3 <?= $notif['is_read'] ? 'text-muted' : 'fw-bold bg-light' ?>" href="<?= Helper::e($notif['link'] ?: '#') ?>">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-<?= $notif['type'] ?> me-2" style="font-size: 0.65rem;"><?= ucfirst($notif['type']) ?></span>
                                            <small class="text-muted"><?= Helper::formatDateTime($notif['created_at']) ?></small>
                                        </div>
                                        <div class="small text-truncate"><?= Helper::e($notif['title']) ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 0.75rem;"><?= Helper::e($notif['message']) ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <img src="<?= Helper::e($currentUser['avatar'] ?? 'assets/img/avatar-default.png') ?>" alt="Avatar" width="36" height="36" class="rounded-circle me-2 border">
                        <div class="d-none d-sm-block text-start">
                            <div class="fw-semibold small lh-1"><?= Helper::e($currentUser['employee_name'] ?: $currentUser['username']) ?></div>
                            <small class="text-muted" style="font-size: 0.75rem;"><?= Helper::e($currentUser['role_display'] ?? '') ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold"><?= Helper::e($currentUser['employee_name'] ?: $currentUser['username']) ?></div>
                            <div class="small text-muted"><?= Helper::e($currentUser['email']) ?></div>
                            <span class="badge bg-primary mt-1"><?= Helper::e($currentUser['role_display'] ?? '') ?></span>
                        </li>
                        <li><a class="dropdown-item py-2" href="<?= Helper::url('auth', 'profile') ?>"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></li>
                        <li><a class="dropdown-item py-2" href="<?= Helper::url('auth', 'changePassword') ?>"><i class="bi bi-key me-2"></i> Đổi mật khẩu</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="<?= Helper::url('auth', 'logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-grow-1 p-3 p-md-4">
            <!-- Flash Messages -->
            <?php
            $flashes = Session::getFlashes();
            if (!empty($flashes)):
                foreach ($flashes as $type => $messages):
                    foreach ($messages as $msg):
            ?>
                <div class="alert alert-<?= $type ?> alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-<?= $type === 'success' ? 'check-circle-fill' : ($type === 'danger' ? 'exclamation-octagon-fill' : 'info-circle-fill') ?> me-2"></i>
                    <?= Helper::e($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
                    endforeach;
                endforeach;
            endif;
            ?>

            <!-- Page Injected Content -->
            <?= $content ?>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted small">
            <div>&copy; <?= date('Y') ?> <strong>HRM Pro</strong> - Hệ Thống Quản Lý Nhân Sự Toàn Diện. Thiết kế tối ưu cho Laragon & PHP 8+.</div>
        </footer>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom App JS -->
<script src="assets/js/app.js"></script>
</body>
</html>

