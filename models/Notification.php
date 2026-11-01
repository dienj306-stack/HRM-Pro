<?php
/**
 * Model quản lý Thông báo (notifications)
 */

class Notification extends Model {
    /**
     * Lấy các thông báo mới nhất của người dùng
     */
    public function getForUser(int $userId, int $limit = 10): array {
        $sql = "
            SELECT * FROM notifications 
            WHERE user_id = :user_id 
            ORDER BY id DESC 
            LIMIT {$limit}
        ";
        return $this->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Đếm số thông báo chưa đọc
     */
    public function countUnread(int $userId): int {
        $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0";
        return (int)$this->fetchColumn($sql, ['user_id' => $userId]);
    }

    /**
     * Tạo thông báo mới
     */
    public function create(int $userId, string $title, string $message, string $type = 'info', ?string $link = null): int {
        $sql = "
            INSERT INTO notifications (user_id, title, message, type, link, is_read, created_at)
            VALUES (:user_id, :title, :message, :type, :link, 0, NOW())
        ";
        $this->execute($sql, [
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'link'    => $link
        ]);
        return $this->lastInsertId();
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markAsRead(int $id, int $userId): bool {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $this->execute($sql, ['id' => $id, 'user_id' => $userId]);
        return true;
    }

    /**
     * Đánh dấu tất cả thông báo của người dùng là đã đọc
     */
    public function markAllAsRead(int $userId): bool {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id";
        $this->execute($sql, ['user_id' => $userId]);
        return true;
    }
}

