<?php
/**
 * Controller xem Nhật ký hoạt động hệ thống (Chỉ dành cho Admin)
 */

class AuditController extends Controller {
    private AuditLog $auditModel;

    public function __construct() {
        $this->auditModel = new AuditLog();
    }

    /**
     * Danh sách nhật ký hoạt động
     */
    public function index(): void {
        $this->requireRole('admin');

        $logs = $this->auditModel->getRecent(150);

        $this->render('audit/index', [
            'pageTitle' => 'Nhật Ký Hoạt Động Hệ Thống (Audit Logs)',
            'logs'      => $logs
        ], 'main');
    }
}

