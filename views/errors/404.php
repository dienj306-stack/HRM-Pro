<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không Tìm Thấy Trang - HRM Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center p-4" style="max-width: 500px;">
        <div class="display-1 fw-bold text-primary mb-2">404</div>
        <h4 class="fw-bold text-dark mb-2">Không Tìm Thấy Trang Yêu Cầu</h4>
        <p class="text-muted mb-4"><?= htmlspecialchars($message ?? 'Đường dẫn bạn truy cập không tồn tại hoặc đã được di chuyển.') ?></p>
        <a href="index.php" class="btn btn-primary px-4 py-2">
            <i class="bi bi-house-door me-2"></i> Trở Về Trang Chủ
        </a>
    </div>
</body>
</html>

