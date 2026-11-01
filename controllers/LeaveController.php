<?php
/**
 * Controller quản lý Nghỉ phép (leave_requests)
 */

class LeaveController extends Controller {
    private LeaveRequest $leaveModel;
    private Department $departmentModel;
    private Notification $notifModel;

    public function __construct() {
        $this->leaveModel = new LeaveRequest();
        $this->departmentModel = new Department();
        $this->notifModel = new Notification();
    }

    /**
     * Danh sách đơn nghỉ phép toàn công ty (Admin & HR)
     */
    public function index(): void {
        $this->requireRole(['admin', 'hr']);

        $filters = [
            'status'        => $_GET['status'] ?? '',
            'department_id' => $_GET['department_id'] ?? '',
            'leave_type'    => $_GET['leave_type'] ?? ''
        ];

        $leaveRequests = $this->leaveModel->getAll($filters);
        $departments = $this->departmentModel->getAll();
        $pendingCount = $this->leaveModel->countPending();

        $this->render('leaves/index', [
            'pageTitle'     => 'Quản Lý Đơn Nghỉ Phép',
            'leaveRequests' => $leaveRequests,
            'departments'   => $departments,
            'filters'       => $filters,
            'pendingCount'  => $pendingCount
        ], 'main');
    }

    /**
     * Danh sách đơn nghỉ phép của chính nhân viên
     */
    public function myLeaves(): void {
        $this->requireAuth();
        $employeeId = $this->requireEmployee();

        $myRequests = $this->leaveModel->getByEmployee($employeeId);

        $this->render('leaves/my_leaves', [
            'pageTitle'  => 'Đơn Nghỉ Phép Của Tôi',
            'myRequests' => $myRequests
        ], 'main');
    }

    /**
     * Giao diện gửi đơn nghỉ phép mới
     */
    public function create(): void {
        $this->requireAuth();
        $this->requireEmployee();

        $this->render('leaves/create', [
            'pageTitle' => 'Tạo Đơn Xin Nghỉ Phép'
        ], 'main');
    }

    /**
     * Lưu đơn xin nghỉ phép
     */
    public function store(): void {
        $this->requireAuth();
        $this->validateCsrf();
        $employeeId = $this->requireEmployee();
        $user = $this->currentUser();

        $leaveType = $_POST['leave_type'] ?? 'annual';
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $reason = trim($_POST['reason'] ?? '');

        if (empty($startDate) || empty($endDate) || empty($reason)) {
            Session::setFlash('danger', 'Vui lòng điền đầy đủ ngày bắt đầu, ngày kết thúc và lý do nghỉ phép.');
            $this->redirect(Helper::url('leaves', 'create'));
        }

        if ($startDate > $endDate) {
            Session::setFlash('danger', 'Ngày bắt đầu nghỉ không thể lớn hơn ngày kết thúc nghỉ.');
            $this->redirect(Helper::url('leaves', 'create'));
        }

        // Tính số ngày nghỉ
        $startTs = strtotime($startDate);
        $endTs = strtotime($endDate);
        $totalDays = round(($endTs - $startTs) / 86400) + 1;

        if ($totalDays <= 0) {
            Session::setFlash('danger', 'Khoảng thời gian nghỉ phép không hợp lệ.');
            $this->redirect(Helper::url('leaves', 'create'));
        }

        // Kiểm tra xem khoảng thời gian này đã có đơn nào trùng chưa
        if ($this->leaveModel->hasConflictingLeave($employeeId, $startDate, $endDate)) {
            Session::setFlash('danger', 'Bạn đã có đơn xin nghỉ phép khác trong khoảng thời gian này đang chờ duyệt hoặc đã được duyệt.');
            $this->redirect(Helper::url('leaves', 'create'));
        }

        $id = $this->leaveModel->create([
            'employee_id' => $employeeId,
            'leave_type'  => $leaveType,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'total_days'  => $totalDays,
            'reason'      => $reason
        ]);

        $this->logAction('SUBMIT_LEAVE', 'leave_requests', $id, "Nộp đơn xin nghỉ phép từ {$startDate} đến {$endDate} ({$totalDays} ngày)");
        
        // Gửi thông báo đến HR & Admin
        $db = Database::getConnection();
        $admins = $db->query("SELECT id FROM users WHERE role_id IN (1, 2) AND status = 'active'")->fetchAll();
        $empName = $user['employee_name'] ?: $user['username'];
        foreach ($admins as $admin) {
            $this->notifModel->create(
                (int)$admin['id'],
                'Đơn nghỉ phép mới cần duyệt',
                "Nhân viên {$empName} vừa nộp đơn xin nghỉ ({$totalDays} ngày).",
                'warning',
                Helper::url('leaves', 'index')
            );
        }

        Session::setFlash('success', 'Gửi đơn xin nghỉ phép thành công! Đang chờ Quản lý/Nhân sự phê duyệt.');
        $this->redirect(Helper::url('leaves', 'myLeaves'));
    }

    /**
     * Phê duyệt đơn nghỉ phép (Chỉ Admin & HR)
     */
    public function approve(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $user = $this->currentUser();
        $leave = $this->leaveModel->findById($id);

        if (!$leave || $leave['status'] !== 'pending') {
            Session::setFlash('danger', 'Đơn nghỉ phép không tồn tại hoặc đã được xử lý trước đó.');
            $this->redirect(Helper::url('leaves', 'index'));
        }

        $this->leaveModel->approve($id, (int)$user['id']);
        $this->logAction('APPROVE_LEAVE', 'leave_requests', $id, "Duyệt đơn nghỉ phép {$leave['request_code']} của {$leave['employee_name']}");

        // Gửi thông báo cho nhân viên nếu có tài khoản
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE employee_id = :emp_id LIMIT 1");
        $stmt->execute(['emp_id' => $leave['employee_id']]);
        $empUser = $stmt->fetch();
        if ($empUser) {
            $this->notifModel->create(
                (int)$empUser['id'],
                'Đơn nghỉ phép đã được phê duyệt',
                "Đơn xin nghỉ phép mã {$leave['request_code']} ({$leave['start_date']} đến {$leave['end_date']}) của bạn đã được phê duyệt.",
                'success',
                Helper::url('leaves', 'myLeaves')
            );
        }

        Session::setFlash('success', "Đã phê duyệt đơn nghỉ phép [{$leave['request_code']}] thành công!");
        $this->redirect(Helper::url('leaves', 'index'));
    }

    /**
     * Từ chối đơn nghỉ phép (Chỉ Admin & HR)
     */
    public function reject(): void {
        $this->requireRole(['admin', 'hr']);
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $reason = trim($_POST['rejection_reason'] ?? '');
        $user = $this->currentUser();
        $leave = $this->leaveModel->findById($id);

        if (!$leave || $leave['status'] !== 'pending') {
            Session::setFlash('danger', 'Đơn nghỉ phép không tồn tại hoặc đã được xử lý.');
            $this->redirect(Helper::url('leaves', 'index'));
        }

        if (empty($reason)) {
            Session::setFlash('danger', 'Vui lòng nhập lý do từ chối đơn nghỉ phép.');
            $this->redirect(Helper::url('leaves', 'index'));
        }

        $this->leaveModel->reject($id, (int)$user['id'], $reason);
        $this->logAction('REJECT_LEAVE', 'leave_requests', $id, "Từ chối đơn nghỉ phép {$leave['request_code']}: {$reason}");

        // Gửi thông báo cho nhân viên
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE employee_id = :emp_id LIMIT 1");
        $stmt->execute(['emp_id' => $leave['employee_id']]);
        $empUser = $stmt->fetch();
        if ($empUser) {
            $this->notifModel->create(
                (int)$empUser['id'],
                'Đơn nghỉ phép bị từ chối',
                "Đơn xin nghỉ phép mã {$leave['request_code']} bị từ chối. Lý do: {$reason}",
                'danger',
                Helper::url('leaves', 'myLeaves')
            );
        }

        Session::setFlash('success', "Đã từ chối đơn nghỉ phép [{$leave['request_code']}].");
        $this->redirect(Helper::url('leaves', 'index'));
    }

    /**
     * Nhân viên tự hủy đơn nghỉ phép của mình (chỉ khi đang chờ duyệt)
     */
    public function cancel(): void {
        $this->requireAuth();
        $this->validateCsrf();
        $employeeId = $this->requireEmployee();

        $id = (int)($_POST['id'] ?? 0);
        $leave = $this->leaveModel->findById($id);

        if (!$leave || $leave['employee_id'] != $employeeId || $leave['status'] !== 'pending') {
            Session::setFlash('danger', 'Không thể hủy đơn nghỉ phép này.');
            $this->redirect(Helper::url('leaves', 'myLeaves'));
        }

        $this->leaveModel->cancel($id, $employeeId);
        $this->logAction('CANCEL_LEAVE', 'leave_requests', $id, "Hủy đơn nghỉ phép {$leave['request_code']}");
        Session::setFlash('success', 'Đã hủy đơn xin nghỉ phép thành công.');
        $this->redirect(Helper::url('leaves', 'myLeaves'));
    }
}

