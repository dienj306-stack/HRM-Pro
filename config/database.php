<?php
/**
 * Cấu hình kết nối Cơ sở dữ liệu MySQL bằng PDO
 * Môi trường Laragon mặc định: host=127.0.0.1, user=root, password=''
 */

class Database {
    private static ?PDO $instance = null;

    private const DB_HOST = '127.0.0.1';
    private const DB_PORT = '3306';
    private const DB_NAME = 'hr_management';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_CHARSET = 'utf8mb4';

    /**
     * Lấy đối tượng PDO duy nhất (Singleton Pattern)
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                self::DB_HOST,
                self::DB_PORT,
                self::DB_NAME,
                self::DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true, // Bật chuẩn bị giả lập để hỗ trợ tái sử dụng tham số an toàn
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                // Hiển thị hướng dẫn trực quan nếu chưa import DB trên Laragon
                self::renderConnectionError($e->getMessage());
                exit;
            }
        }

        return self::$instance;
    }

    /**
     * Màn hình thông báo thân thiện khi chưa kết nối được CSDL
     */
    private static function renderConnectionError(string $error): void {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lỗi Kết Nối Cơ Sở Dữ Liệu - HRM System</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background: #f4f6f9; font-family: system-ui, -apple-system, sans-serif; }
                .error-card { max-width: 650px; margin: 60px auto; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="card error-card border-0">
                    <div class="card-header bg-danger text-white py-3 rounded-top-3">
                        <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i> Chưa thể kết nối Cơ sở dữ liệu MySQL</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning mb-4">
                            <strong>Chi tiết lỗi:</strong> <?= htmlspecialchars($error) ?>
                        </div>
                        <h5 class="fw-bold mb-3">Hướng dẫn khắc phục nhanh trên Laragon:</h5>
                        <ol class="list-group list-group-numbered mb-4">
                            <li class="list-group-item">Mở ứng dụng <strong>Laragon</strong> và bấm <strong>Start All</strong> để bật Apache & MySQL.</li>
                            <li class="list-group-item">Bấm nút <strong>Database</strong> (HeidiSQL) hoặc vào <code>http://localhost/phpmyadmin</code>.</li>
                            <li class="list-group-item">Tạo database mới tên: <code class="text-primary fw-bold">hr_management</code> (bảng mã <code>utf8mb4_unicode_ci</code>).</li>
                            <li class="list-group-item">Nhập (Import) file SQL tại đường dẫn: <br><code>C:\laragon\www\HRM\database\hr_management.sql</code></li>
                            <li class="list-group-item">Tải lại trang web này sau khi import xong.</li>
                        </ol>
                        <div class="text-center">
                            <a href="index.php" class="btn btn-primary px-4">Thử lại ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

