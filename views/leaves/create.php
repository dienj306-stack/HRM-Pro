<div class="container-fluid" style="max-width: 650px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Tạo Đơn Xin Nghỉ Phép</h4>
            <small class="text-muted">Gửi đề xuất nghỉ phép tới Quản lý và Bộ phận Nhân sự</small>
        </div>
        <a href="<?= Helper::url('leaves', 'myLeaves') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Đơn Của Tôi
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= Helper::url('leaves', 'store') ?>">
                <?= Helper::csrfField() ?>

                <div class="mb-3">
                    <label for="type" class="form-label small fw-semibold">Loại hình nghỉ phép <span class="text-danger">*</span></label>
                    <select class="form-select" id="type" name="leave_type" required>
                        <option value="annual" selected>Nghỉ phép thường niên (Hưởng nguyên lương)</option>
                        <option value="sick">Nghỉ ốm đau / Khám chữa bệnh</option>
                        <option value="unpaid">Nghỉ việc riêng không hưởng lương</option>
                        <option value="wedding">Nghỉ kết hôn / Gia đình (Có hưởng lương)</option>
                        <option value="maternity">Nghỉ thai sản / Chăm sóc con</option>
                        <option value="other">Lý do chính đáng khác</option>
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="startDate" class="form-label small fw-semibold">Nghỉ từ ngày <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="startDate" name="start_date" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="endDate" class="form-label small fw-semibold">Đến hết ngày <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="endDate" name="end_date" min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="alert alert-light border small py-2 mb-3" id="calcDaysBox" style="display: none;">
                    <i class="bi bi-calendar-event me-1 text-primary"></i> Tổng số ngày đăng ký nghỉ: <strong id="calcDaysText" class="text-primary">0</strong> ngày
                </div>

                <div class="mb-4">
                    <label for="reason" class="form-label small fw-semibold">Lý do xin nghỉ chi tiết <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Nêu rõ lý do nghỉ phép và công việc bàn giao tạm thời (nếu có)..." required></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= Helper::url('leaves', 'myLeaves') ?>" class="btn btn-outline-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send-fill me-1"></i> Gửi Đơn Xin Nghỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sInput = document.getElementById('startDate');
    const eInput = document.getElementById('endDate');
    const box = document.getElementById('calcDaysBox');
    const txt = document.getElementById('calcDaysText');

    function calculate() {
        if (sInput.value && eInput.value) {
            const start = new Date(sInput.value);
            const end = new Date(eInput.value);
            if (end >= start) {
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                txt.textContent = diffDays;
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }
    }

    sInput.addEventListener('change', calculate);
    eInput.addEventListener('change', calculate);
});
</script>

