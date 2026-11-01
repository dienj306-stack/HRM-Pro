# HRM Pro - Hệ Thống Quản Lý Nhân Sự (PHP 8 + MySQL)

Website Quản lý Nhân sự (HRM) chuyên nghiệp, an toàn, dễ bảo trì, được xây dựng theo kiến trúc MVC tinh gọn bằng **PHP thuần (PHP 8+)**, **MySQL/MariaDB**, **Bootstrap 5** và **JavaScript**. Dự án được thiết kế chuẩn mực để chạy trực tiếp trên Windows thông qua **Laragon**.

---

## 1. Điểm nổi bật & Công nghệ sử dụng

- **Ngôn ngữ Backend**: PHP 8.0, 8.1, 8.2 trở lên (Tương thích hoàn toàn với PHP đi kèm Laragon).
- **Cơ sở dữ liệu**: MySQL 5.7+ / 8.0+ hoặc MariaDB 10.3+.
- **Frontend**: HTML5, CSS3, JavaScript ES6 và Bootstrap 5.3 (giao diện tiếng Việt 100%, hỗ trợ Responsive trên Desktop, Tablet và Mobile).
- **Kết nối CSDL**: Sử dụng **PDO** và **Prepared Statements** chống SQL Injection 100%.
- **Bảo mật**:
  - Mã hóa mật khẩu bằng thuật toán chuẩn `password_hash()` (Bcrypt).
  - Chống tấn công CSRF thông qua token xác thực động cho mọi biểu mẫu POST.
  - Chống tấn công XSS thông qua cơ chế lọc và escape đầu ra `htmlspecialchars()`.
  - Phân quyền nghiêm ngặt ở phía máy chủ (Server-side Role-based Access Control).
  - Chống Session Fixation bằng cơ chế `session_regenerate_id()`.
  - Cơ chế xóa mềm (Soft Delete) giúp bảo toàn lịch sử chấm công, hợp đồng và bảng lương.
  - Ghi nhật ký hệ thống (Audit Logs) cho mọi thao tác quan trọng.
- **Không phụ thuộc phức tạp**: Không bắt buộc dùng Docker, Composer hay thư viện trả phí để chạy phiên bản đầy đủ.

---

## 2. Các vai trò & Bảng tài khoản thử nghiệm

Hệ thống được thiết kế với 3 nhóm vai trò phân quyền rõ ràng:

| Vai trò | Tên đăng nhập | Mật khẩu mẫu | Nhân viên liên kết | Quyền hạn chính |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `Admin@123` | Nguyễn Văn An (EMP001) | Toàn quyền hệ thống, quản lý tài khoản, phân quyền, cấu hình, phòng ban, nhân viên, xem audit log. |
| **HR** | `hr_manager` | `Hr@123456` | Trần Thị Mai (EMP002) | Quản lý hồ sơ nhân sự, hợp đồng, chấm công, duyệt/từ chối đơn nghỉ phép, tính lương, báo cáo. |
| **Employee** | `emp_nam` | `Emp@123456` | Lê Quốc Nam (EMP003) | Chấm công vào/ra cá nhân, nộp đơn xin nghỉ, xem lịch sử chấm công, xem phiếu lương của chính mình. |
| **Employee** | `emp_ha` | `Emp@123456` | Phạm Thị Thu Hà (EMP004) | Xem thông tin cá nhân, chấm công, nộp đơn nghỉ phép, xem phiếu lương của mình. |
| **Employee** | `emp_hoang` | `Emp@123456` | Đỗ Minh Hoàng (EMP005) | Xem thông tin cá nhân, chấm công, nộp đơn nghỉ phép, xem phiếu lương của mình. |

> [!NOTE]
> Tất cả mật khẩu trong cơ sở dữ liệu đều được mã hóa bằng hàm `password_hash()` của PHP, đảm bảo tính bảo mật và có thể đăng nhập bằng `password_verify()`.

---

## 3. Cấu trúc thư mục dự án

```text
c:\laragon\www\HRM\
├── .htaccess                      # Điều hướng Apache và bảo vệ file cấu hình
├── index.php                      # Front Controller & Router duy nhất
├── README.md                      # Tài liệu hướng dẫn cài đặt và kiểm thử
├── config/
│   ├── app.php                   # Cấu hình app, timezone, URL, session
│   └── database.php              # Kết nối PDO Singleton & màn hình bắt lỗi thân thiện
├── database/
│   └── hr_management.sql         # Script tạo CSDL & dữ liệu mẫu đầy đủ
├── core/
│   ├── Controller.php            # Base Controller (render, json, phân quyền, CSRF, audit)
│   ├── Model.php                 # Base Model (PDO wrapper, transaction)
│   ├── Router.php                # Điều hướng URL controller/action
│   ├── Session.php               # Quản lý session, flash message, CSRF token
│   └── Helper.php                # Định dạng tiền tệ, ngày giờ, escape HTML (e), badge
├── models/
│   ├── User.php                  # Tài khoản và phân quyền
│   ├── Employee.php              # Hồ sơ nhân sự
│   ├── Department.php            # Phòng ban
│   ├── Position.php              # Chức vụ
│   ├── Attendance.php            # Chấm công & tính ngày công
│   ├── LeaveRequest.php          # Đơn nghỉ phép & duyệt nghỉ
│   ├── Contract.php              # Hợp đồng lao động
│   ├── SalaryRecord.php          # Bảng lương & tự động tính lương
│   ├── Notification.php          # Thông báo người dùng
│   └── AuditLog.php              # Nhật ký hoạt động hệ thống
├── controllers/
│   ├── AuthController.php        # Đăng nhập, đăng xuất, đổi mật khẩu, hồ sơ
│   ├── DashboardController.php   # Dashboard quản trị & Dashboard nhân viên
│   ├── EmployeeController.php    # Quản lý nhân viên (CRUD, tìm kiếm, lọc, phân trang)
│   ├── DepartmentController.php  # Quản lý phòng ban
│   ├── PositionController.php    # Quản lý chức vụ
│   ├── AttendanceController.php  # Quản lý chấm công toàn công ty & cá nhân
│   ├── LeaveController.php       # Quản lý duyệt/từ chối đơn nghỉ phép
│   ├── ContractController.php    # Quản lý hợp đồng lao động
│   ├── PayrollController.php     # Tính lương, duyệt lương, chi trả & xuất CSV
│   ├── UserController.php        # Quản lý tài khoản, khóa/mở khóa & đặt lại mật khẩu
│   ├── ReportController.php      # Báo cáo tổng hợp, chấm công, nghỉ phép & xuất Excel
│   └── AuditController.php       # Xem lịch sử thao tác hệ thống
├── views/
│   ├── layouts/
│   │   ├── main.php              # Master Layout (Sidebar, Topbar, Footer, Clock)
│   │   └── auth.php              # Layout đăng nhập
│   ├── auth/                     # Views: Đăng nhập, Hồ sơ, Đổi mật khẩu
│   ├── dashboard/                # Views: Dashboard quản trị & Dashboard nhân viên
│   ├── employees/                # Views: Danh sách, Chi tiết, Thêm mới, Sửa
│   ├── departments/              # Views: Danh sách, Thêm mới, Sửa
│   ├── positions/                # Views: Danh sách, Thêm mới, Sửa
│   ├── attendance/               # Views: Chấm công công ty & Chấm công của tôi
│   ├── leaves/                   # Views: Duyệt đơn & Đơn của tôi, Tạo đơn mới
│   ├── contracts/                # Views: Danh sách, Thêm, Sửa hợp đồng
│   ├── payroll/                  # Views: Bảng lương, Điều chỉnh & Phiếu lương của tôi
│   ├── users/                    # Views: Quản trị tài khoản người dùng
│   ├── reports/                  # Views: Báo cáo nhân sự, chấm công, nghỉ phép
│   ├── audit/                    # Views: Nhật ký thao tác
│   └── errors/                   # Views: 403 Forbidden, 404 Not Found
└── assets/
    ├── css/style.css             # Định kiểu giao diện hiện đại
    ├── js/app.js                 # JS đồng hồ số, xác nhận xóa, định dạng tiền tệ
    └── img/avatar-default.png    # Ảnh đại diện mặc định
```

---

## 4. Hướng dẫn cài đặt và chạy thử trên Laragon

### Bước 1: Khởi động Laragon
1. Mở phần mềm **Laragon**.
2. Nhấn nút **Start All** để khởi động máy chủ Web Apache và MySQL.

### Bước 2: Nhập (Import) Cơ sở dữ liệu
Bạn có thể chọn một trong hai cách sau:

#### Cách A: Dùng HeidiSQL (Có sẵn trong Laragon)
1. Trên cửa sổ Laragon, bấm nút **Database**.
2. Chọn **Open** (User: `root`, Mật khẩu: để trống).
3. Bấm chuột phải vào danh sách CSDL bên trái &rarr; Chọn **Tạo mới (Create new)** &rarr; **Cơ sở dữ liệu (Database)** &rarr; Đặt tên là `hr_management` (Collation: `utf8mb4_unicode_ci`).
4. Vào menu **Tập tin (File)** &rarr; **Tải tập tin SQL (Load SQL file...)** &rarr; Chọn file `c:\laragon\www\HRM\database\hr_management.sql`.
5. Bấm phím **F9** (hoặc nút Run màu xanh) để thực thi nạp toàn bộ bảng và dữ liệu mẫu.

#### Cách B: Dùng phpMyAdmin
1. Mở trình duyệt truy cập: `http://localhost/phpmyadmin`
2. Tạo cơ sở dữ liệu mới tên `hr_management` với bảng mã `utf8mb4_unicode_ci`.
3. Bấm vào tab **Nhập (Import)** &rarr; Chọn file `c:\laragon\www\HRM\database\hr_management.sql` &rarr; Bấm **Nhập (Go)**.

### Bước 3: Mở ứng dụng trên trình duyệt
Mở trình duyệt web (Chrome, Edge, Firefox) và truy cập một trong các địa chỉ sau:
- `http://localhost/HRM`
- hoặc `http://hrm.test` (nếu Laragon đã tạo Virtual Host tự động)

---

## 5. Tùy chỉnh cấu hình nếu cần thiết

Nếu bạn thay đổi cổng hoặc mật khẩu MySQL của Laragon, bạn chỉ cần sửa trong file [config/database.php](file:///c:/laragon/www/HRM/config/database.php):

```php
private const DB_HOST = '127.0.0.1'; // Địa chỉ máy chủ CSDL
private const DB_PORT = '3306';      // Cổng MySQL (mặc định 3306)
private const DB_NAME = 'hr_management';
private const DB_USER = 'root';      // Tên tài khoản MySQL
private const DB_PASS = '';          // Mật khẩu (mặc định trong Laragon là rỗng)
```

---

## 6. Danh sách các chức năng nghiệp vụ chi tiết

1. **Đăng nhập & Xác thực**:
   - Đăng nhập linh hoạt bằng Tên đăng nhập (`username`) hoặc `email`.
   - Chống Session Fixation, lưu thông tin an toàn trong Session.
   - Hộp gợi ý tài khoản mẫu ngay tại màn hình đăng nhập hỗ trợ kiểm thử 1-click.
2. **Dashboard động**:
   - Tự động nhận diện vai trò người dùng để hiển thị giao diện phù hợp.
   - Với **Admin / HR**: Thống kê số lượng nhân viên, chấm công trong ngày, đơn nghỉ phép chờ duyệt, biểu đồ phân bổ nhân sự theo phòng ban (Chart.js).
   - Với **Nhân viên (Employee)**: Hộp chấm công vào/ra trực tiếp, đồng hồ thời gian thực, tiến độ ngày công tháng, đơn xin nghỉ phép và phiếu lương gần nhất.
3. **Quản lý Hồ sơ Nhân viên (Admin & HR)**:
   - Thêm mới, xem chi tiết, sửa thông tin nhân viên.
   - Tìm kiếm đa năng theo từ khóa (tên, mã, email, SĐT), lọc theo phòng ban, trạng thái.
   - Tự động gợi ý mã nhân viên kế tiếp (`NV001`, `NV002`,...).
   - Kiểm tra trùng lặp email và mã nhân viên.
   - Xóa mềm (chuyển trạng thái sang "Đã thôi việc" và khóa tài khoản đăng nhập) để bảo vệ toàn vẹn dữ liệu kế toán và chấm công.
4. **Quản lý Phòng ban & Chức vụ**:
   - Thêm, sửa, xóa phòng ban và chức danh.
   - Chỉ định Trưởng phòng từ danh sách nhân viên.
   - **Ràng buộc an toàn**: Ngăn chặn xóa phòng ban hoặc chức vụ nếu vẫn còn nhân viên đang trực thuộc.
5. **Chấm công thời gian thực (Attendance)**:
   - Nhân viên bấm nút "Chấm Công Vào" / "Chấm Công Ra" ngay trên dashboard.
   - Ngăn chặn chấm công vào trùng lặp trong cùng một ngày.
   - Tự động đánh giá đi đúng giờ hoặc đi muộn dựa theo khung giờ chuẩn công ty (08:30).
   - Tự động tính toán số giờ làm việc thực tế và trừ thời gian nghỉ trưa.
   - Bảng theo dõi chấm công toàn công ty theo ngày/tháng/phòng ban dành cho HR.
   - Lịch sử chấm công cá nhân theo từng tháng cho nhân viên.
6. **Quản lý Đơn xin nghỉ phép (Leave Management)**:
   - Nhân viên tạo đơn xin nghỉ (phép năm, nghỉ ốm, không lương, thai sản, việc riêng).
   - Tự động tính toán số ngày nghỉ và kiểm tra trùng lặp khoảng thời gian nghỉ đã có.
   - HR / Admin xem danh sách đơn chờ duyệt, bấm nút Duyệt hoặc Từ chối kèm lý do.
   - Tự động phát sinh thông báo (Notification) thông báo kết quả cho nhân viên.
   - Nhân viên có quyền hủy đơn nghỉ phép của mình khi đơn vẫn ở trạng thái "Chờ duyệt".
7. **Hợp đồng lao động (Contracts)**:
   - Quản lý hợp đồng thử việc, xác định thời hạn, không xác định thời hạn, thời vụ.
   - Theo dõi ngày bắt đầu, ngày kết thúc, mức lương thỏa thuận và đóng BHXH.
8. **Bảng lương & Tự động tính lương (Payroll)**:
   - Chức năng tự động tổng hợp ngày công thực tế từ bảng chấm công và sinh bảng lương tháng cho toàn bộ nhân sự chỉ với 1 click.
   - Công thức tính lương minh bạch, rõ ràng:
     `Lương theo ngày công = (Lương cơ bản / Ngày công chuẩn) * Ngày công thực tế`
     `Thực nhận = Lương theo ngày công + Phụ cấp + Thưởng - Khấu trừ`
   - HR / Admin có thể tùy chỉnh thêm tiền thưởng, phụ cấp hoặc các khoản phạt đi muộn.
   - Quy trình: Bản nháp &rarr; Phê duyệt &rarr; Đã chi trả.
   - Nhân viên chỉ được xem phiếu lương của chính mình, bảo mật thu nhập.
   - Cho phép xuất bảng lương ra file CSV có **UTF-8 BOM** mở trực tiếp bằng Microsoft Excel không lỗi font tiếng Việt.
9. **Tài khoản & Phân quyền (Chỉ dành cho Admin)**:
   - Cấp tài khoản mới từ hồ sơ nhân viên chưa có tài khoản.
   - Khóa hoặc mở khóa tài khoản người dùng bất kỳ lúc nào (ngăn Admin tự khóa chính mình).
   - Chức năng đặt lại mật khẩu mới cho người dùng.
10. **Báo cáo & Xuất dữ liệu**:
    - Báo cáo cơ cấu nhân sự và biểu đồ tỷ lệ giới tính.
    - Báo cáo tổng hợp chấm công tháng (ngày công, đi muộn, tổng giờ làm).
    - Báo cáo tổng hợp số ngày nghỉ phép trong năm.
    - Hỗ trợ xuất dữ liệu ra file Excel/CSV chuẩn UTF-8.
11. **Nhật ký hệ thống (Audit Logs)**:
    - Ghi lại tự động người thực hiện, hành động, loại đối tượng, mô tả chi tiết, IP và thời gian cho mọi hoạt động trọng yếu.

---

## 7. Checklist Kiểm Thử Mã Nguồn & Xác Minh Hệ Thống

| TT | Hạng mục kiểm tra | Trạng thái | Ghi chú kỹ thuật |
| :---: | :--- | :---: | :--- |
| 1 | File cơ sở dữ liệu `hr_management.sql` | **ĐẠT (PASS)** | Cấu trúc chuẩn InnoDB, UTF8mb4, khóa ngoại, chỉ mục và dữ liệu mẫu đầy đủ. |
| 2 | Cú pháp toàn bộ mã nguồn PHP | **ĐẠT (PASS)** | Kiểm tra linter PHP 8.1 không có bất kỳ lỗi cú pháp nào. |
| 3 | Cơ chế xác thực mật khẩu (Bcrypt) | **ĐẠT (PASS)** | Đã kiểm tra thực thi `password_verify()` trùng khớp 100% với hash trong SQL. |
| 4 | Kiểm tra phân quyền Server-side | **ĐẠT (PASS)** | Sử dụng `$this->requireRole()` tại mọi Controller; trả về mã HTTP 403 nếu cố tình can thiệp. |
| 5 | Bảo mật CSRF trên mọi biểu mẫu POST | **ĐẠT (PASS)** | Tự động sinh `csrf_token` trong form và kiểm tra với `$this->validateCsrf()`. |
| 6 | Chống tấn công XSS | **ĐẠT (PASS)** | Mọi dữ liệu in ra màn hình đều qua hàm `Helper::e()` (`htmlspecialchars`). |
| 7 | Chống tấn công SQL Injection | **ĐẠT (PASS)** | 100% truy vấn CSDL qua PDO Prepared Statements với tham số ràng buộc. |
| 8 | Cách ly dữ liệu nhân viên (ID Isolation) | **ĐẠT (PASS)** | Nhân viên chỉ truy xuất dữ liệu có `employee_id` từ chính Session đăng nhập của họ. |
| 9 | Kiểm tra nghiệp vụ chấm công trùng | **ĐẠT (PASS)** | Ràng buộc Unique `(employee_id, date)` trên MySQL và kiểm tra trước khi ghi nhận. |
| 10 | Kiểm tra logic tính lương | **ĐẠT (PASS)** | Tự động tính theo ngày công thực tế, hỗ trợ phụ cấp, thưởng, giảm trừ và xuất CSV UTF-8. |
| 11 | Kiểm tra toàn vẹn khi xóa | **ĐẠT (PASS)** | Không cho xóa phòng ban/chức vụ khi đang có nhân viên; dùng Soft Delete cho nhân viên. |

