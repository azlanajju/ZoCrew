<?php
require_once '../../config/config.php';
$current_page = 'leavesApprovals';
$menu_path = '../';

$conn = getConnection();

// Filters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$leave_type = isset($_GET['leave_type']) ? $_GET['leave_type'] : '';
$employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Get filter options
$departments = $conn->query("SELECT name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$leave_types = [
    'Casual Leave', 'Sick Leave', 'Earned Leave', 'Maternity Leave', 'Paternity Leave', 'Other'
];
$employees = $conn->query("SELECT employee_id, first_name, last_name FROM employees ORDER BY first_name, last_name")->fetchAll(PDO::FETCH_ASSOC);

// Build query
$where = [];
$params = [];
if ($status !== '') {
    $where[] = "l.status = :status";
    $params[':status'] = $status;
}
if ($leave_type) {
    $where[] = "l.leave_type = :leave_type";
    $params[':leave_type'] = $leave_type;
}
if ($employee_id) {
    $where[] = "l.employee_id = :employee_id";
    $params[':employee_id'] = $employee_id;
}
if ($department) {
    $where[] = "e.department_name = :department";
    $params[':department'] = $department;
}
if ($date_from) {
    $where[] = "l.start_date >= :date_from";
    $params[':date_from'] = $date_from;
}
if ($date_to) {
    $where[] = "l.end_date <= :date_to";
    $params[':date_to'] = $date_to;
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Fetch leave requests
$sql = "SELECT l.*, e.first_name, e.last_name, e.department_name FROM employee_leaves l
        JOIN employees e ON l.employee_id = e.employee_id
        $where_sql
        ORDER BY l.status = 'Pending' DESC, l.start_date DESC";
$leaves = $conn->prepare($sql);
$leaves->execute($params);
$leaves = $leaves->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Leave Approvals - ZoCrew HRMS</title>
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 90px;
            --primary-color: #3a7bd5;
            --secondary-color: #5f4b8b;
            --text-color: #f0f0f0;
            --hover-color: rgb(166, 108, 224);
            --transition-speed: 0.3s;
        }
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            font-size: 14px;
            color: #222;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 60px;
            padding: 32px 2vw 32px 2vw;
            min-height: calc(100vh - 60px);
            transition: margin-left var(--transition-speed) ease;
        }
        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .page-header h1 {
            font-size: 1.18em;
            font-weight: 700;
            color: #222;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .table-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.07);
            padding: 0 0 10px 0;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: transparent;
            min-width: 1000px;
            font-size: 13px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f2f2f2;
        }
        th {
            background: #f8f9fa;
            font-weight: 700;
            color: #5f4b8b;
            font-size: 0.97em;
            letter-spacing: 0.2px;
        }
        tr {
            transition: background 0.18s;
        }
        tr:nth-child(even) {
            background: #fafbfc;
        }
        tr:hover {
            background: #f3f7f4;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
            display: inline-block;
        }
        .badge-pending { background: #fffbe6; color: #faad14; }
        .badge-approved { background: #e3fcef; color: #00a854; }
        .badge-rejected { background: #fff1f0; color: #f5222d; }
        .actions a {
            margin-right: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1em;
            padding: 4px 6px;
            border-radius: 4px;
            transition: background 0.15s, color 0.15s;
        }
        .actions a:hover {
            background: var(--primary-color);
            color: #fff;
        }
        .actions i {
            color: var(--hover-color);
            transition: color 0.2s;
        }
        .actions a:hover i {
            color: #fff;
        }
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 18px;
        }
        .filter-bar select, .filter-bar input[type=\"date\"] {
            padding: 7px 12px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            font-size: 1em;
            background: #fafbfc;
            color: #333;
        }
        .filter-bar label { font-weight: 500; color: #444; margin-right: 6px; }
        @media (max-width: 700px) {
            .main-content { padding: 15px 2vw 15px 2vw; }
            .table-container { border-radius: 0; box-shadow: none; }
            .filter-bar { flex-direction: column; align-items: flex-start; gap: 10px; }
        }
        /* Modal Styles (reuse from timings) */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100vw; height: 100vh; overflow: auto; background: rgba(44, 75, 139, 0.18); }
        .modal-content { background: #fff; margin: 60px auto; border-radius: 12px; padding: 32px 28px 24px 28px; max-width: 500px; box-shadow: 0 8px 32px rgba(44,62,80,0.13); position: relative; }
        .modal-title { font-size: 1.1em; font-weight: 700; color: var(--secondary-color); margin-bottom: 18px; }
        .close { position: absolute; top: 18px; right: 18px; font-size: 1.2em; color: #aaa; cursor: pointer; transition: color 0.2s; }
        .close:hover { color: var(--primary-color); }
        .modal label { font-weight: 500; color: #444; margin-bottom: 6px; display: block; }
        .modal textarea { width: 100%; padding: 9px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 1em; margin-bottom: 18px; background: #fafbfc; transition: border 0.2s; box-sizing: border-box; }
        .modal textarea:focus { border: 1.5px solid var(--primary-color); outline: none; background: #fff; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 12px; }
        .modal .btn { padding: 9px 22px; border-radius: 7px; font-weight: 600; font-size: 1em; border: none; cursor: pointer; transition: background 0.2s, color 0.2s; }
        .modal .btn-cancel { background: #f5f6fa; color: #5f4b8b; }
        .modal .btn-cancel:hover { background: #ede7f6; color: var(--primary-color); }
        .modal .btn-approve { background: #00a854; color: #fff; }
        .modal .btn-approve:hover { background: #009245; color: #fff; }
        .modal .btn-reject { background: #f5222d; color: #fff; }
        .modal .btn-reject:hover { background: #c41d1d; color: #fff; }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    <div class="main-content">
        <div class="page-header">
            <h1>Leave Approvals</h1>
        </div>
        <div class="filter-bar">
            <form method="get" style="display:inline-flex;align-items:center;gap:10px;">
                <label for="status">Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $status == 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Rejected" <?= $status == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <label for="leave_type">Leave Type:</label>
                <select name="leave_type" id="leave_type" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($leave_types as $lt): ?>
                        <option value="<?= htmlspecialchars($lt) ?>" <?= $lt == $leave_type ? 'selected' : '' ?>><?= htmlspecialchars($lt) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="department">Department:</label>
                <select name="department" id="department" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= htmlspecialchars($d) ?>" <?= $d == $department ? 'selected' : '' ?>><?= htmlspecialchars($d) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="employee_id">Employee:</label>
                <select name="employee_id" id="employee_id" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['employee_id'] ?>" <?= $emp['employee_id'] == $employee_id ? 'selected' : '' ?>><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="date_from">From:</label>
                <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($date_from) ?>" onchange="this.form.submit()">
                <label for="date_to">To:</label>
                <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($date_to) ?>" onchange="this.form.submit()">
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($leaves): foreach ($leaves as $leave): ?>
                    <tr>
                        <td><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></td>
                        <td><?= htmlspecialchars($leave['department_name']) ?></td>
                        <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                        <td><?= date('d M Y', strtotime($leave['start_date'])) ?> - <?= date('d M Y', strtotime($leave['end_date'])) ?></td>
                        <td><?= $leave['days'] ?? ((strtotime($leave['end_date']) - strtotime($leave['start_date']))/86400 + 1) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($leave['status']) ?>">
                                <?= htmlspecialchars($leave['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($leave['reason']) ?></td>
                        <td class="actions">
                            <?php if ($leave['status'] === 'Pending'): ?>
                                <a href="#" title="Approve" onclick="openActionModal('Approve', <?= $leave['leave_id'] ?>)"><i class="fas fa-check"></i></a>
                                <a href="#" title="Reject" onclick="openActionModal('Reject', <?= $leave['leave_id'] ?>)"><i class="fas fa-times"></i></a>
                            <?php else: ?>
                                <span style="color:#aaa;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8">No leave requests found for the selected filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approve/Reject Modal -->
    <div id="actionModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeActionModal">&times;</span>
            <div class="modal-title">
                <span id="actionTitle">Approve Leave</span>
            </div>
            <form method="post" action="action.php" id="actionForm">
                <input type="hidden" name="leave_id" id="action_leave_id">
                <input type="hidden" name="action_type" id="action_type">
                <label for="remarks">Remarks (optional)</label>
                <textarea id="remarks" name="remarks" rows="2" maxlength="255"></textarea>
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" id="cancelActionModal">Cancel</button>
                    <button type="submit" class="btn btn-approve" id="approveBtn" style="display:none;">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button type="submit" class="btn btn-reject" id="rejectBtn" style="display:none;">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modal open/close logic
    function openActionModal(type, leaveId) {
        document.getElementById('actionTitle').textContent = type + ' Leave';
        document.getElementById('action_leave_id').value = leaveId;
        document.getElementById('action_type').value = type;
        document.getElementById('remarks').value = '';
        document.getElementById('actionModal').style.display = 'block';
        document.getElementById('approveBtn').style.display = (type === 'Approve') ? 'inline-block' : 'none';
        document.getElementById('rejectBtn').style.display = (type === 'Reject') ? 'inline-block' : 'none';
    }
    document.getElementById('closeActionModal').onclick = function() {
        document.getElementById('actionModal').style.display = 'none';
    };
    document.getElementById('cancelActionModal').onclick = function() {
        document.getElementById('actionModal').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('actionModal')) {
            document.getElementById('actionModal').style.display = 'none';
        }
    };
    </script>
</body>
</html> 