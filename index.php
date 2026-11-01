<?php
/**
 * Entry Point (Front Controller) của ứng dụng HRM
 * Chạy trực tiếp trên Laragon (PHP 8+)
 */

// 1. Nạp cấu hình ứng dụng và kết nối CSDL
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// 2. Nạp các lớp cốt lõi (Core)
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Helper.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Router.php';

// 3. Tự động nạp Models (Autoload)
spl_autoload_register(function ($class) {
    $modelFile = __DIR__ . '/models/' . $class . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
    }
});

// 4. Điều hướng yêu cầu người dùng
Router::dispatch();

