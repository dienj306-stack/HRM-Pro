<?php
/**
 * Model quản lý Nhật ký hoạt động hệ thống (audit_logs)
 */

class AuditLog extends Model {
    /**
     * Lấy danh sách nhật ký mới nhất kèm thông tin người thực hiện
     */
    public function getRecent(int $limit = 100): array {
        $sql = "
            SELECT a.*, u.username, r.display_name AS role_display, e.full_name AS employee_name
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON u.employee_id = e.id
            ORDER BY a.id DESC
            LIMIT {$limit}
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Ghi nhật ký hoạt động
     */
    public function record(?int $userId, string $action, string $entityType, ?int $entityId, string $description): int {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);
        $sql = "
            INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent, created_at)
            VALUES (:user_id, :action, :type, :entity_id, :desc, :ip, :ua, NOW())
        ";
        $this->execute($sql, [
            'user_id'   => $userId,
            'action'    => $action,
            'type'      => $entityType,
            'entity_id' => $entityId,
            'desc'      => $description,
            'ip'        => $ip,
            'ua'        => $ua
        ]);
        return $this->lastInsertId();
    }
}

