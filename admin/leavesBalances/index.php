<?php
require_once '../../config/config.php';
$current_page = 'leavesBalances';
$menu_path = '../';

$conn = getConnection();

// Filters
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$department = isset($_GET['department']) ? $_GET['department'] : '';
$leave_type = isset($_GET['leave_type']) ? $_GET['leave_type'] : '';
$employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';

// Get filter options
$years = $conn->query("SELECT DISTINCT year FROM employee_leave_balances ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);
$departments = $conn->query("SELECT name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$leave_types = [
    'Casual Leave', 'Sick Leave', 'Earned Leave', 'Maternity Leave', 'Paternity Leave', 'Other'
];
$employees = $conn->query("SELECT employee_id, first_name, last_name FROM employees ORDER BY first_name, last_name")->fetchAll(PDO::FETCH_ASSOC);

// Build query
$where = ["elb.year = :year"];
$params = [':year' => $year];
if ($department) {
    $where[] = "e.department_name = :department";
    $params[':department'] = $department;
}
if ($leave_type) {
    $where[] = "elb.leave_type = :leave_type";
    $params[':leave_type'] = $leave_type;
}
if ($employee_id) {
    $where[] = "elb.employee_id = :employee_id";
    $params[':employee_id'] = $employee_id;
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Fetch leave balances
$sql = "SELECT elb.*, e.first_name, e.last_name, e.department_name FROM employee_leave_balances elb
        JOIN employees e ON elb.employee_id = e.employee_id
        $where_sql
        ORDER BY e.first_name, e.last_name, elb.leave_type";
$balances = $conn->prepare($sql);
$balances->execute($params);
$balances = $balances->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Balances - ZoCrew HRMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        .add-btn {
            display: flex;
            align-items: center;
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: var(--text-color);
            padding: 8px 20px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.98em;
            box-shadow: 0 2px 8px rgba(90, 123, 213, 0.10);
            transition: background 0.2s, box-shadow 0.2s;
            border: none;
            outline: none;
            letter-spacing: 0.2px;
            cursor: pointer;
        }
        .add-btn i {
            margin-right: 7px;
            font-size: 1em;
            color: var(--hover-color);
            transition: color 0.2s;
        }
        .add-btn:hover {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 4px 16px rgba(90, 123, 213, 0.13);
            color: #fff;
        }
        .add-btn:hover i {
            color: #fff;
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
            min-width: 900px;
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
        .badge-used { background: #fff1f0; color: #f5222d; }
        .badge-available { background: #e3fcef; color: #00a854; }
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
        .filter-bar select, .filter-bar input[type="text"] {
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
        .modal input, .modal select, .modal textarea { width: 100%; padding: 9px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 1em; margin-bottom: 18px; background: #fafbfc; transition: border 0.2s; box-sizing: border-box; }
        .modal input:focus, .modal select:focus, .modal textarea:focus { border: 1.5px solid var(--primary-color); outline: none; background: #fff; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 12px; }
        .modal .btn { padding: 9px 22px; border-radius: 7px; font-weight: 600; font-size: 1em; border: none; cursor: pointer; transition: background 0.2s, color 0.2s; }
        .modal .btn-cancel { background: #f5f6fa; color: #5f4b8b; }
        .modal .btn-cancel:hover { background: #ede7f6; color: var(--primary-color); }
        .modal .btn-save { background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%); color: var(--text-color); }
        .modal .btn-save:hover { background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: #fff; }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    <div class="main-content">
        <div class="page-header">
            <h1>Leave Balances</h1>
            <button class="add-btn" id="openModal"><i class="fas fa-plus"></i> Add Leave Balance</button>
        </div>
        <div class="filter-bar">
            <form method="get" style="display:inline-flex;align-items:center;gap:10px;">
                <label for="year">Year:</label>
                <select name="year" id="year" onchange="this.form.submit()">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= htmlspecialchars($y) ?>" <?= $y == $year ? 'selected' : '' ?>><?= htmlspecialchars($y) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="department">Department:</label>
                <select name="department" id="department" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= htmlspecialchars($d) ?>" <?= $d == $department ? 'selected' : '' ?>><?= htmlspecialchars($d) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="leave_type">Leave Type:</label>
                <select name="leave_type" id="leave_type" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($leave_types as $lt): ?>
                        <option value="<?= htmlspecialchars($lt) ?>" <?= $lt == $leave_type ? 'selected' : '' ?>><?= htmlspecialchars($lt) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="employee_id">Employee:</label>
                <select name="employee_id" id="employee_id" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['employee_id'] ?>" <?= $emp['employee_id'] == $employee_id ? 'selected' : '' ?>><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Total Leaves</th>
                        <th>Leaves Used</th>
                        <th>Available</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($balances): foreach ($balances as $bal): ?>
                    <tr>
                        <td><?= htmlspecialchars($bal['first_name'] . ' ' . $bal['last_name']) ?></td>
                        <td><?= htmlspecialchars($bal['department_name']) ?></td>
                        <td><?= htmlspecialchars($bal['leave_type']) ?></td>
                        <td><?= $bal['total_leaves'] ?></td>
                        <td><span class="badge badge-used"><?= $bal['leaves_used'] ?></span></td>
                        <td><span class="badge badge-available"><?= max(0, $bal['total_leaves'] - $bal['leaves_used']) ?></span></td>
                        <td><?= $bal['year'] ?></td>
                        <td class="actions">
                            <a href="#" title="Edit" onclick='editBalance(<?= json_encode($bal) ?>)'><i class="fas fa-edit"></i></a>
                            <a href="#" class="delete" title="Delete" onclick="deleteBalance(<?= $bal['balance_id'] ?>)"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8">No leave balances found for the selected filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="balanceModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-title">
                <i class="fas fa-balance-scale" style="color:var(--primary-color);margin-right:8px;"></i>
                <span id="modalTitle">Add Leave Balance</span>
            </div>
            <form method="post" action="save.php" id="balanceForm">
                <input type="hidden" name="balance_id" id="balance_id">
                <label for="employee_id_input">Employee *</label>
                <select id="employee_id_input" name="employee_id" required>
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['employee_id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="leave_type_input">Leave Type *</label>
                <select id="leave_type_input" name="leave_type" required>
                    <?php foreach ($leave_types as $lt): ?>
                        <option value="<?= htmlspecialchars($lt) ?>"><?= htmlspecialchars($lt) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="total_leaves">Total Leaves *</label>
                <input type="number" id="total_leaves" name="total_leaves" min="0" step="0.01" required>
                <label for="leaves_used">Leaves Used *</label>
                <input type="number" id="leaves_used" name="leaves_used" min="0" step="0.01" required>
                <label for="year_input">Year *</label>
                <input type="number" id="year_input" name="year" min="2000" max="2100" required>
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" id="cancelModal">Cancel</button>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modal open/close logic
    document.getElementById('openModal').onclick = function() {
        document.getElementById('modalTitle').textContent = 'Add Leave Balance';
        document.getElementById('balanceForm').reset();
        document.getElementById('balance_id').value = '';
        document.getElementById('balanceModal').style.display = 'block';
    };
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('balanceModal').style.display = 'none';
    };
    document.getElementById('cancelModal').onclick = function() {
        document.getElementById('balanceModal').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('balanceModal')) {
            document.getElementById('balanceModal').style.display = 'none';
        }
    };
    // Edit balance
    function editBalance(bal) {
        document.getElementById('modalTitle').textContent = 'Edit Leave Balance';
        document.getElementById('balance_id').value = bal.balance_id;
        document.getElementById('employee_id_input').value = bal.employee_id;
        document.getElementById('leave_type_input').value = bal.leave_type;
        document.getElementById('total_leaves').value = bal.total_leaves;
        document.getElementById('leaves_used').value = bal.leaves_used;
        document.getElementById('year_input').value = bal.year;
        document.getElementById('balanceModal').style.display = 'block';
    }
    // Delete balance
    function deleteBalance(balanceId) {
        if (confirm('Are you sure you want to delete this leave balance?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete.php';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'balance_id';
            input.value = balanceId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html> 