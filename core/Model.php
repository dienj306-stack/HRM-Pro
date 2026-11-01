<?php
/**
 * Lớp Base Model cơ bản cung cấp các phương thức thao tác CSDL chuẩn
 */

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Thực thi câu lệnh SQL với tham số chuẩn bị (Prepared Statement)
     */
    protected function execute(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Lấy một bản ghi duy nhất
     */
    protected function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->execute($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Lấy danh sách nhiều bản ghi
     */
    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy một giá trị đơn lẻ (VD: COUNT(*))
     */
    protected function fetchColumn(string $sql, array $params = []): mixed {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Lấy ID bản ghi vừa thêm
     */
    protected function lastInsertId(): int {
        return (int)$this->db->lastInsertId();
    }

    /**
     * Bắt đầu giao dịch
     */
    public function beginTransaction(): bool {
        return $this->db->beginTransaction();
    }

    /**
     * Xác nhận giao dịch
     */
    public function commit(): bool {
        return $this->db->commit();
    }

    /**
     * Hủy giao dịch nếu có lỗi
     */
    public function rollBack(): bool {
        return $this->db->rollBack();
    }
}

