-- =====================================================================
-- HỆ THỐNG QUẢN LÝ NHÂN SỰ (HRM) - CƠ SỞ DỮ LIỆU MYSQL
-- Tương thích: MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+
-- Môi trường: Laragon trên Windows (PHP 8.x + MySQL)
-- Bảng mã: utf8mb4_unicode_ci (Hỗ trợ tiếng Việt đầy đủ)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `hr_management` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `hr_management`;

-- Tắt kiểm tra khóa ngoại tạm thời để khởi tạo/xóa bảng an toàn
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `salary_records`;
DROP TABLE IF EXISTS `leave_requests`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `contracts`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `positions`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `system_settings`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. Bảng Vai trò (roles)
-- ---------------------------------------------------------------------
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `display_name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. Bảng Phòng ban (departments)
-- ---------------------------------------------------------------------
CREATE TABLE `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `manager_id` INT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_dept_code` (`code`),
    INDEX `idx_dept_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Bảng Chức vụ (positions)
-- ---------------------------------------------------------------------
CREATE TABLE `positions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `base_salary_min` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `base_salary_max` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pos_code` (`code`),
    INDEX `idx_pos_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. Bảng Hồ sơ nhân viên (employees)
-- ---------------------------------------------------------------------
CREATE TABLE `employees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_code` VARCHAR(20) NOT NULL UNIQUE,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `gender` ENUM('male', 'female', 'other') NOT NULL DEFAULT 'male',
    `date_of_birth` DATE NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NULL,
    `address` VARCHAR(255) NULL,
    `department_id` INT NULL,
    `position_id` INT NULL,
    `hire_date` DATE NOT NULL,
    `base_salary` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('active', 'probation', 'on_leave', 'resigned') NOT NULL DEFAULT 'active',
    `avatar` VARCHAR(255) DEFAULT 'assets/img/avatar-default.png',
    `deleted_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_emp_code` (`employee_code`),
    INDEX `idx_emp_email` (`email`),
    INDEX `idx_emp_dept` (`department_id`),
    INDEX `idx_emp_pos` (`position_id`),
    INDEX `idx_emp_status` (`status`),
    INDEX `idx_emp_deleted` (`deleted_at`),
    CONSTRAINT `fk_emp_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_emp_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cập nhật khóa ngoại manager_id của departments tham chiếu tới employees(id)
ALTER TABLE `departments`
    ADD CONSTRAINT `fk_dept_manager` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ---------------------------------------------------------------------
-- 5. Bảng Tài khoản người dùng (users)
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `employee_id` INT NULL UNIQUE,
    `status` ENUM('active', 'locked') NOT NULL DEFAULT 'active',
    `last_login` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_username` (`username`),
    INDEX `idx_user_email` (`email`),
    INDEX `idx_user_role` (`role_id`),
    INDEX `idx_user_employee` (`employee_id`),
    INDEX `idx_user_status` (`status`),
    CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_user_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. Bảng Hợp đồng lao động (contracts)
-- ---------------------------------------------------------------------
CREATE TABLE `contracts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_code` VARCHAR(50) NOT NULL UNIQUE,
    `employee_id` INT NOT NULL,
    `contract_type` ENUM('probation', 'definite', 'indefinite', 'seasonal') NOT NULL DEFAULT 'definite',
    `start_date` DATE NOT NULL,
    `end_date` DATE NULL,
    `basic_salary` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `insurance_salary` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('active', 'expired', 'terminated') NOT NULL DEFAULT 'active',
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_contract_emp` (`employee_id`),
    INDEX `idx_contract_status` (`status`),
    INDEX `idx_contract_dates` (`start_date`, `end_date`),
    CONSTRAINT `fk_contract_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. Bảng Chấm công (attendance)
-- ---------------------------------------------------------------------
CREATE TABLE `attendance` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `date` DATE NOT NULL,
    `check_in` TIME NULL,
    `check_out` TIME NULL,
    `status` ENUM('on_time', 'late', 'early_leave', 'absent', 'holiday', 'leave') NOT NULL DEFAULT 'on_time',
    `work_hours` DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    `notes` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_employee_date` (`employee_id`, `date`),
    INDEX `idx_att_date` (`date`),
    INDEX `idx_att_status` (`status`),
    CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. Bảng Đơn xin nghỉ phép (leave_requests)
-- ---------------------------------------------------------------------
CREATE TABLE `leave_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_code` VARCHAR(50) NOT NULL UNIQUE,
    `employee_id` INT NOT NULL,
    `leave_type` ENUM('annual', 'sick', 'unpaid', 'maternity', 'wedding', 'other') NOT NULL DEFAULT 'annual',
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `total_days` DECIMAL(4,1) NOT NULL DEFAULT 1.0,
    `reason` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    `approver_id` INT NULL,
    `approved_at` DATETIME NULL,
    `rejection_reason` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_leave_emp` (`employee_id`),
    INDEX `idx_leave_status` (`status`),
    INDEX `idx_leave_dates` (`start_date`, `end_date`),
    CONSTRAINT `fk_leave_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_leave_approver` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. Bảng Lương (salary_records)
-- ---------------------------------------------------------------------
CREATE TABLE `salary_records` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `record_code` VARCHAR(50) NOT NULL UNIQUE,
    `employee_id` INT NOT NULL,
    `month` TINYINT NOT NULL,
    `year` SMALLINT NOT NULL,
    `base_salary` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `standard_work_days` DECIMAL(4,1) NOT NULL DEFAULT 22.0,
    `actual_work_days` DECIMAL(4,1) NOT NULL DEFAULT 22.0,
    `allowance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `bonus` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `deduction` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `net_salary` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('draft', 'approved', 'paid') NOT NULL DEFAULT 'draft',
    `paid_date` DATE NULL,
    `created_by` INT NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_emp_month_year` (`employee_id`, `month`, `year`),
    INDEX `idx_salary_period` (`year`, `month`),
    INDEX `idx_salary_status` (`status`),
    CONSTRAINT `fk_salary_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_salary_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. Bảng Thông báo (notifications)
-- ---------------------------------------------------------------------
CREATE TABLE `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'danger') NOT NULL DEFAULT 'info',
    `link` VARCHAR(255) NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_user` (`user_id`, `is_read`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. Bảng Nhật ký hoạt động (audit_logs)
-- ---------------------------------------------------------------------
CREATE TABLE `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT NULL,
    `description` TEXT NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_created` (`created_at`),
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 12. Bảng Cấu hình hệ thống (system_settings)
-- ---------------------------------------------------------------------
CREATE TABLE `system_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- DỮ LIỆU MẪU BAN ĐẦU (SEED DATA)
-- =====================================================================

-- 1. Vai trò
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES
(1, 'admin', 'Quản trị viên', 'Toàn quyền quản trị hệ thống, quản lý người dùng, phân quyền, cấu hình và báo cáo'),
(2, 'hr', 'Chuyên viên Nhân sự', 'Quản lý nhân viên, phòng ban, chấm công, duyệt nghỉ phép, hợp đồng và tính lương'),
(3, 'employee', 'Nhân viên', 'Chấm công cá nhân, nộp đơn nghỉ phép, xem phiếu lương và cập nhật hồ sơ');

-- 2. Cấu hình hệ thống
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('company_name', 'Công ty Cổ phần Công nghệ & Giải pháp Số HRM', 'Tên công ty hiển thị trên hệ thống'),
('work_start_time', '08:30:00', 'Giờ bắt đầu làm việc chuẩn buổi sáng'),
('work_end_time', '17:30:00', 'Giờ kết thúc làm việc chuẩn buổi chiều'),
('standard_monthly_days', '22', 'Số ngày làm việc tiêu chuẩn trong tháng'),
('standard_work_hours', '8.0', 'Số giờ làm việc tiêu chuẩn mỗi ngày'),
('company_email', 'contact@hrm.local', 'Email liên hệ công ty'),
('company_phone', '024-8888-9999', 'Số điện thoại đường dây nóng');

-- 3. Phòng ban (chưa gán manager_id vì nhân viên chưa tạo)
INSERT INTO `departments` (`id`, `code`, `name`, `description`, `manager_id`, `status`) VALUES
(1, 'PB-IT', 'Phòng Công nghệ Thông tin', 'Nghiên cứu, phát triển và vận hành hệ thống phần mềm công nghệ', NULL, 'active'),
(2, 'PB-HR', 'Phòng Nhân sự & Hành chính', 'Quản lý nhân sự, tuyển dụng, đào tạo, chế độ phúc lợi và hành chính', NULL, 'active'),
(3, 'PB-ACC', 'Phòng Tài chính - Kế toán', 'Quản lý tài chính, hạch toán kế toán, ngân sách và chi trả lương', NULL, 'active'),
(4, 'PB-SALE', 'Phòng Kinh doanh & Tiếp thị', 'Phát triển khách hàng, mở rộng thị trường và xúc tiến bán hàng', NULL, 'active');

-- 4. Chức vụ
INSERT INTO `positions` (`id`, `code`, `name`, `description`, `base_salary_min`, `base_salary_max`, `status`) VALUES
(1, 'CV-DIR', 'Giám đốc Điều hành', 'Lãnh đạo và điều hành toàn bộ chiến lược công ty', 30000000.00, 60000000.00, 'active'),
(2, 'CV-HRM', 'Trưởng phòng Nhân sự', 'Chịu trách nhiệm quản lý toàn diện khối nhân sự hành chính', 20000000.00, 35000000.00, 'active'),
(3, 'CV-IT-SR', 'Kỹ sư Phần mềm Cao cấp', 'Phát triển kiến trúc và lập trình các hệ thống cốt lõi', 22000000.00, 40000000.00, 'active'),
(4, 'CV-ACC', 'Chuyên viên Kế toán', 'Hạch toán sổ sách chứng từ, thuế và hỗ trợ tính lương', 12000000.00, 20000000.00, 'active'),
(5, 'CV-SALE', 'Chuyên viên Kinh doanh', 'Tìm kiếm khách hàng doanh nghiệp và chốt hợp đồng dự án', 10000000.00, 25000000.00, 'active');

-- 5. Nhân viên
INSERT INTO `employees` (`id`, `employee_code`, `first_name`, `last_name`, `full_name`, `gender`, `date_of_birth`, `email`, `phone`, `address`, `department_id`, `position_id`, `hire_date`, `base_salary`, `status`, `avatar`) VALUES
(1, 'NV001', 'An', 'Nguyễn Văn', 'Nguyễn Văn An', 'male', '1988-05-15', 'admin@hrm.local', '0901234567', 'Tòa nhà Landmark 72, Nam Từ Liêm, Hà Nội', 1, 1, '2020-01-01', 35000000.00, 'active', 'assets/img/avatar-default.png'),
(2, 'NV002', 'Mai', 'Trần Thị', 'Trần Thị Mai', 'female', '1992-08-20', 'hr@hrm.local', '0912345678', 'Số 45 Lê Văn Lương, Cầu Giấy, Hà Nội', 2, 2, '2021-03-15', 25000000.00, 'active', 'assets/img/avatar-default.png'),
(3, 'NV003', 'Nam', 'Lê Quốc', 'Lê Quốc Nam', 'male', '1995-11-10', 'nam.le@hrm.local', '0923456789', 'Số 12 Duy Tân, Cầu Giấy, Hà Nội', 1, 3, '2022-06-01', 24000000.00, 'active', 'assets/img/avatar-default.png'),
(4, 'NV004', 'Hà', 'Phạm Thị Thu', 'Phạm Thị Thu Hà', 'female', '1996-03-25', 'ha.pham@hrm.local', '0934567890', 'Số 88 Hoàng Quốc Việt, Cầu Giấy, Hà Nội', 3, 4, '2023-02-15', 15000000.00, 'active', 'assets/img/avatar-default.png'),
(5, 'NV005', 'Hoàng', 'Đỗ Minh', 'Đỗ Minh Hoàng', 'male', '1998-09-08', 'hoang.do@hrm.local', '0945678901', 'Số 20 Hồ Tùng Mậu, Bắc Từ Liêm, Hà Nội', 4, 5, '2023-08-01', 13000000.00, 'active', 'assets/img/avatar-default.png');

-- Cập nhật Trưởng phòng cho departments
UPDATE `departments` SET `manager_id` = 1 WHERE `id` = 1;
UPDATE `departments` SET `manager_id` = 2 WHERE `id` = 2;
UPDATE `departments` SET `manager_id` = 4 WHERE `id` = 3;
UPDATE `departments` SET `manager_id` = 5 WHERE `id` = 4;

-- 6. Người dùng (Users)
-- Mật khẩu mẫu:
-- admin / Admin@123 -> $2y$10$cDwIPejPtcDRvBVSpRD/bu3tT0q4g1dAOtHKGLOAdcmdqlZeb3w06
-- hr_manager / Hr@123456 -> $2y$10$7z4qDMI5Jtiie9tpGIm9Qej0grAZO8ykA2MeQUBpaaghM.kocClRi
-- emp_nam, emp_ha, emp_hoang / Emp@123456 -> $2y$10$j4v1gnVH5FnTJdWe01l0J.QXu3eMkc1OZx08fHzmXpy13ubJwN0pu
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role_id`, `employee_id`, `status`) VALUES
(1, 'admin', 'admin@hrm.local', '$2y$10$cDwIPejPtcDRvBVSpRD/bu3tT0q4g1dAOtHKGLOAdcmdqlZeb3w06', 1, 1, 'active'),
(2, 'hr_manager', 'hr@hrm.local', '$2y$10$7z4qDMI5Jtiie9tpGIm9Qej0grAZO8ykA2MeQUBpaaghM.kocClRi', 2, 2, 'active'),
(3, 'emp_nam', 'nam.le@hrm.local', '$2y$10$j4v1gnVH5FnTJdWe01l0J.QXu3eMkc1OZx08fHzmXpy13ubJwN0pu', 3, 3, 'active'),
(4, 'emp_ha', 'ha.pham@hrm.local', '$2y$10$j4v1gnVH5FnTJdWe01l0J.QXu3eMkc1OZx08fHzmXpy13ubJwN0pu', 3, 4, 'active'),
(5, 'emp_hoang', 'hoang.do@hrm.local', '$2y$10$j4v1gnVH5FnTJdWe01l0J.QXu3eMkc1OZx08fHzmXpy13ubJwN0pu', 3, 5, 'active');

-- 7. Hợp đồng lao động
INSERT INTO `contracts` (`id`, `contract_code`, `employee_id`, `contract_type`, `start_date`, `end_date`, `basic_salary`, `insurance_salary`, `status`, `notes`) VALUES
(1, 'HD-2020-001', 1, 'indefinite', '2020-01-01', NULL, 35000000.00, 10000000.00, 'active', 'Hợp đồng không xác định thời hạn - Ban Giám đốc'),
(2, 'HD-2021-002', 2, 'indefinite', '2021-03-15', NULL, 25000000.00, 8000000.00, 'active', 'Hợp đồng lao động chính thức Trưởng phòng'),
(3, 'HD-2022-003', 3, 'definite', '2022-06-01', '2025-05-31', 24000000.00, 7000000.00, 'active', 'Hợp đồng lao động 3 năm - Kỹ sư phần mềm'),
(4, 'HD-2023-004', 4, 'definite', '2023-02-15', '2025-02-14', 15000000.00, 6000000.00, 'active', 'Hợp đồng lao động 2 năm - Kế toán viên'),
(5, 'HD-2023-005', 5, 'definite', '2023-08-01', '2024-07-31', 13000000.00, 5000000.00, 'active', 'Hợp đồng lao động 1 năm - Chuyên viên kinh doanh');

-- 8. Dữ liệu chấm công mẫu
-- Chấm công cho ngày làm việc gần nhất
INSERT INTO `attendance` (`employee_id`, `date`, `check_in`, `check_out`, `status`, `work_hours`, `notes`) VALUES
(1, CURDATE(), '08:25:00', '17:35:00', 'on_time', 8.00, 'Chấm công đúng giờ'),
(2, CURDATE(), '08:28:00', '17:32:00', 'on_time', 8.00, 'Chấm công đúng giờ'),
(3, CURDATE(), '08:45:00', '17:40:00', 'late', 7.75, 'Đi muộn 15 phút do tắc đường'),
(4, CURDATE(), '08:20:00', '17:30:00', 'on_time', 8.00, 'Chấm công đúng giờ'),
(5, CURDATE(), '08:30:00', NULL, 'on_time', 4.00, 'Đã chấm công vào buổi sáng');

-- 9. Đơn xin nghỉ phép mẫu
INSERT INTO `leave_requests` (`id`, `request_code`, `employee_id`, `leave_type`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `approver_id`, `approved_at`, `rejection_reason`) VALUES
(1, 'NP-2026-001', 3, 'annual', DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 6 DAY), 2.0, 'Nghỉ phép thường niên đi du lịch cùng gia đình', 'approved', 2, NOW(), NULL),
(2, 'NP-2026-002', 5, 'sick', CURDATE(), CURDATE(), 1.0, 'Bị sốt cảm cúm cần đi khám bác sĩ và nghỉ ngơi', 'pending', NULL, NULL, NULL),
(3, 'NP-2026-003', 4, 'unpaid', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 9 DAY), 2.0, 'Việc cá nhân gia đình tại quê', 'rejected', 2, DATE_SUB(NOW(), INTERVAL 9 DAY), 'Trùng đợt kiểm toán quyết toán thuế quý, không thể nghỉ');

-- 10. Bảng lương mẫu (Tháng trước)
INSERT INTO `salary_records` (`id`, `record_code`, `employee_id`, `month`, `year`, `base_salary`, `standard_work_days`, `actual_work_days`, `allowance`, `bonus`, `deduction`, `net_salary`, `status`, `paid_date`, `created_by`, `notes`) VALUES
(1, 'BL-102026-001', 1, 10, 2026, 35000000.00, 22.0, 22.0, 3000000.00, 5000000.00, 0.00, 43000000.00, 'paid', '2026-11-05', 2, 'Lương kỳ tháng 10 - Đã thanh toán chuyển khoản'),
(2, 'BL-102026-002', 2, 10, 2026, 25000000.00, 22.0, 22.0, 2000000.00, 2000000.00, 0.00, 29000000.00, 'paid', '2026-11-05', 2, 'Lương kỳ tháng 10 - Đã thanh toán chuyển khoản'),
(3, 'BL-102026-003', 3, 10, 2026, 24000000.00, 22.0, 21.0, 1500000.00, 1500000.00, 200000.00, 25709090.00, 'paid', '2026-11-05', 2, 'Trừ 200k đi muộn và 1 ngày phép trừ công'),
(4, 'BL-102026-004', 4, 10, 2026, 15000000.00, 22.0, 22.0, 1000000.00, 1000000.00, 0.00, 17000000.00, 'approved', NULL, 2, 'Đã phê duyệt, chờ chuyển khoản'),
(5, 'BL-102026-005', 5, 10, 2026, 13000000.00, 22.0, 22.0, 1000000.00, 3500000.00, 0.00, 17500000.00, 'approved', NULL, 2, 'Thưởng vượt chỉ tiêu doanh số tháng 10');

-- 11. Thông báo mẫu
INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `link`, `is_read`, `created_at`) VALUES
(1, 3, 'Đơn nghỉ phép đã được duyệt', 'Đơn xin nghỉ phép mã NP-2026-001 của bạn đã được phê duyệt.', 'success', 'index.php?controller=leaves&action=myLeaves', 0, NOW()),
(2, 2, 'Có đơn xin nghỉ phép mới cần duyệt', 'Nhân viên Đỗ Minh Hoàng vừa gửi đơn xin nghỉ phép mã NP-2026-002.', 'warning', 'index.php?controller=leaves&action=index', 0, NOW()),
(3, 4, 'Đơn nghỉ phép bị từ chối', 'Đơn xin nghỉ phép mã NP-2026-003 bị từ chối: Trùng đợt kiểm toán quyết toán thuế quý.', 'danger', 'index.php?controller=leaves&action=myLeaves', 1, NOW()),
(4, 1, 'Chào mừng đến với hệ thống HRM', 'Hệ thống quản lý nhân sự hoàn thiện sẵn sàng vận hành.', 'info', 'index.php?controller=dashboard', 1, NOW());

-- 12. Nhật ký hệ thống mẫu (Audit logs)
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'SYSTEM_INIT', 'system', 1, 'Khởi tạo hệ thống và cấu hình cơ sở dữ liệu HRM ban đầu', '127.0.0.1', NOW()),
(2, 2, 'APPROVE_LEAVE', 'leave_requests', 1, 'Phê duyệt đơn nghỉ phép NP-2026-001 cho nhân viên Lê Quốc Nam', '127.0.0.1', NOW()),
(3, 2, 'CALCULATE_SALARY', 'salary_records', NULL, 'Tính toán bảng lương tháng 10/2026 cho toàn bộ nhân viên', '127.0.0.1', NOW());

