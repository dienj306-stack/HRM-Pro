<?php
/**
 * Cấu hình chung cho ứng dụng HRM
 */

// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Bật thông báo lỗi trong quá trình phát triển (có thể tắt trên production)
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Đường dẫn thư mục gốc
define('ROOT_PATH', dirname(__DIR__));
define('APP_NAME', 'HRM Pro - Hệ Thống Quản Lý Nhân Sự');
define('APP_VERSION', '1.0.0');

// Tự động nhận diện Base URL linh hoạt chạy trên Laragon (http://localhost/HRM hoặc http://hrm.test)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');

define('BASE_URL', $baseUrl);

// Cấu hình Session an toàn
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    // Nếu dùng HTTPS thì bật secure cookie
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

