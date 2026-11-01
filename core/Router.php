<?php
/**
 * Lớp điều hướng URL (Router) đơn giản, bảo mật và dễ hiểu
 */

class Router {
    public static function dispatch(): void {
        $controllerParam = $_GET['controller'] ?? 'dashboard';
        $actionParam = $_GET['action'] ?? 'index';

        // Chuẩn hóa tên controller (VD: 'employees' -> 'EmployeeController')
        $controllerMap = [
            'auth'        => 'AuthController',
            'dashboard'   => 'DashboardController',
            'employees'   => 'EmployeeController',
            'employee'    => 'EmployeeController',
            'departments' => 'DepartmentController',
            'department'  => 'DepartmentController',
            'positions'   => 'PositionController',
            'position'    => 'PositionController',
            'attendance'  => 'AttendanceController',
            'leaves'      => 'LeaveController',
            'leave'       => 'LeaveController',
            'contracts'   => 'ContractController',
            'contract'    => 'ContractController',
            'payroll'     => 'PayrollController',
            'users'       => 'UserController',
            'user'        => 'UserController',
            'reports'     => 'ReportController',
            'report'      => 'ReportController',
            'audit'       => 'AuditController'
        ];

        $controllerName = $controllerMap[strtolower($controllerParam)] ?? null;

        if (!$controllerName) {
            self::render404("Không tìm thấy trang hoặc module [{$controllerParam}].");
            return;
        }

        $controllerFile = ROOT_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            self::render404("Chưa cài đặt bộ xử lý {$controllerName}.");
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            self::render404("Lớp điều khiển {$controllerName} không tồn tại.");
            return;
        }

        $controller = new $controllerName();

        // Chuẩn hóa tên action (hỗ trợ camelCase: e.g. 'my_attendance' -> 'myAttendance')
        $action = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $actionParam))));

        if (!method_exists($controller, $action)) {
            self::render404("Hành động [{$actionParam}] không tồn tại trên hệ thống.");
            return;
        }

        // Gọi action tương ứng
        $controller->$action();
    }

    private static function render404(string $message = 'Trang bạn tìm kiếm không tồn tại.'): void {
        http_response_code(404);
        require_once ROOT_PATH . '/views/errors/404.php';
        exit;
    }
}

