<div class="container-fluid text-center py-5">
    <div class="py-5" style="max-width: 550px; margin: auto;">
        <div class="display-1 fw-bold text-danger mb-3">403</div>
        <h3 class="fw-bold text-dark mb-2">Truy Cập Bị Từ Chối</h3>
        <p class="text-muted mb-4">
            <?= Helper::e($message ?? 'Bạn không có quyền truy cập vào tài nguyên hoặc hành động này trên hệ thống.') ?>
        </p>
        <a href="<?= Helper::url('dashboard') ?>" class="btn btn-primary px-4 py-2">
            <i class="bi bi-house-door me-2"></i> Trở Về Bảng Điều Khiển
        </a>
    </div>
</div>

