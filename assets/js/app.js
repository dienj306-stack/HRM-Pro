/**
 * JavaScript cho ứng dụng HRM Pro
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Toggle Sidebar trên thiết bị di động
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // 2. Đồng hồ số thời gian thực (Live Clock)
    const liveClock = document.getElementById('liveClock');
    if (liveClock) {
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            liveClock.textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    }

    // 3. Tự động ẩn thông báo Flash sau 6 giây
    const flashAlerts = document.querySelectorAll('.alert-dismissible');
    flashAlerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 6000);
    });

    // 4. Hộp thoại xác nhận trước khi thực hiện thao tác xóa
    const deleteForms = document.querySelectorAll('form.confirm-action');
    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const message = form.getAttribute('data-confirm') || 'Bạn có chắc chắn muốn thực hiện hành động này?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // 5. Định dạng số tiền tệ khi nhập liệu (Money Input Mask)
    const moneyInputs = document.querySelectorAll('.money-input');
    moneyInputs.forEach(function (input) {
        input.addEventListener('input', function (e) {
            let val = this.value.replace(/\D/g, '');
            if (val) {
                this.value = new Intl.NumberFormat('vi-VN').format(val);
            }
        });
    });
});

