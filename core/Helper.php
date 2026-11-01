<?php
/**
 * Lớp Helper chứa các hàm tiện ích dùng chung
 */

class Helper {
    /**
     * Escape HTML ngăn chặn tấn công XSS
     */
    public static function e(?string $str): string {
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Định dạng tiền tệ Việt Nam (VNĐ)
     */
    public static function formatMoney(float|int|string|null $amount): string {
        if ($amount === null || $amount === '') {
            return '0 ₫';
        }
        return number_format((float)$amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Định dạng ngày tháng hiển thị tiếng Việt (d/m/Y)
     */
    public static function formatDate(?string $date, string $format = 'd/m/Y'): string {
        if (empty($date) || $date === '0000-00-00') {
            return '-';
        }
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Định dạng giờ (H:i)
     */
    public static function formatTime(?string $time, string $format = 'H:i'): string {
        if (empty($time) || $time === '00:00:00') {
            return '-';
        }
        $timestamp = strtotime($time);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Định dạng ngày giờ đầy đủ (H:i d/m/Y)
     */
    public static function formatDateTime(?string $datetime, string $format = 'H:i d/m/Y'): string {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return '-';
        }
        $timestamp = strtotime($datetime);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Sinh thẻ ẩn CSRF Token
     */
    public static function csrfField(): string {
        $token = Session::csrfToken();
        return '<input type="hidden" name="csrf_token" value="' . self::e($token) . '">';
    }

    /**
     * Tạo đường dẫn URL trong hệ thống
     */
    public static function url(string $controller = 'dashboard', string $action = 'index', array $params = []): string {
        $query = array_merge([
            'controller' => $controller,
            'action' => $action
        ], $params);
        return 'index.php?' . http_build_query($query);
    }

    /**
     * Tạo badge trạng thái Bootstrap đẹp mắt
     */
    public static function statusBadge(string $status, string $context = 'general'): string {
        $labels = [
            // Trạng thái nhân viên
            'active' => ['text' => 'Đang làm việc', 'class' => 'bg-success'],
            'probation' => ['text' => 'Thử việc', 'class' => 'bg-info text-dark'],
            'on_leave' => ['text' => 'Tạm nghỉ', 'class' => 'bg-warning text-dark'],
            'resigned' => ['text' => 'Đã thôi việc', 'class' => 'bg-secondary'],

            // Trạng thái tài khoản
            'locked' => ['text' => 'Bị khóa', 'class' => 'bg-danger'],

            // Chấm công
            'on_time' => ['text' => 'Đúng giờ', 'class' => 'bg-success'],
            'late' => ['text' => 'Đi muộn', 'class' => 'bg-warning text-dark'],
            'early_leave' => ['text' => 'Về sớm', 'class' => 'bg-orange text-white'],
            'absent' => ['text' => 'Vắng mặt', 'class' => 'bg-danger'],
            'holiday' => ['text' => 'Ngày lễ', 'class' => 'bg-info'],
            'leave' => ['text' => 'Nghỉ phép', 'class' => 'bg-primary'],

            // Nghỉ phép
            'pending' => ['text' => 'Chờ duyệt', 'class' => 'bg-warning text-dark'],
            'approved' => ['text' => 'Đã duyệt', 'class' => 'bg-success'],
            'rejected' => ['text' => 'Từ chối', 'class' => 'bg-danger'],
            'cancelled' => ['text' => 'Đã hủy', 'class' => 'bg-secondary'],

            // Hợp đồng
            'expired' => ['text' => 'Đã hết hạn', 'class' => 'bg-secondary'],
            'terminated' => ['text' => 'Đã chấm dứt', 'class' => 'bg-danger'],

            // Bảng lương
            'draft' => ['text' => 'Bản nháp', 'class' => 'bg-secondary'],
            'paid' => ['text' => 'Đã chi trả', 'class' => 'bg-success']
        ];

        $info = $labels[$status] ?? ['text' => ucfirst($status), 'class' => 'bg-secondary'];
        return sprintf('<span class="badge %s px-2 py-1">%s</span>', $info['class'], self::e($info['text']));
    }

    /**
     * Badge loại nghỉ phép
     */
    public static function leaveTypeBadge(string $type): string {
        $types = [
            'annual' => 'Nghỉ phép năm',
            'sick' => 'Nghỉ ốm đau',
            'unpaid' => 'Nghỉ không lương',
            'maternity' => 'Nghỉ thai sản',
            'wedding' => 'Nghỉ cưới hỏi',
            'other' => 'Nghỉ việc riêng khác'
        ];
        return self::e($types[$type] ?? $type);
    }

    /**
     * Tên loại hợp đồng tiếng Việt
     */
    public static function contractTypeName(string $type): string {
        $types = [
            'probation' => 'Hợp đồng thử việc',
            'definite' => 'Hợp đồng xác định thời hạn',
            'indefinite' => 'Hợp đồng không xác định thời hạn',
            'seasonal' => 'Hợp đồng thời vụ / Dự án'
        ];
        return self::e($types[$type] ?? $type);
    }

    /**
     * Làm sạch dữ liệu chuỗi nhập vào
     */
    public static function sanitize(mixed $data): mixed {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return is_string($data) ? trim($data) : $data;
    }

    /**
     * Lấy thông tin người dùng đang đăng nhập
     */
    public static function authUser(): ?array {
        return Session::get('user');
    }

    /**
     * Kiểm tra quyền hiện tại
     */
    public static function hasRole(string|array $roles): bool {
        $user = self::authUser();
        if (!$user || empty($user['role_name'])) {
            return false;
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array($user['role_name'], $roles, true);
    }
}

