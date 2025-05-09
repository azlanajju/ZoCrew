<?php
require_once '../../config/config.php';
$current_page = 'employees';
$menu_path = '../';

$conn = getConnection();

// Fetch filter options
$departments = $conn->query("SELECT name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$roles = ['Admin', 'Manager', 'Employee'];
$statuses = ['Active', 'Inactive'];

// Handle filters
$where = [];
$params = [];
if (!empty($_GET['search'])) {
    $where[] = "(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR employee_code LIKE :search)";
    $params[':search'] = '%' . $_GET['search'] . '%';
}
if (!empty($_GET['department'])) {
    $where[] = "department_name = :department";
    $params[':department'] = $_GET['department'];
}
if (!empty($_GET['status'])) {
    $where[] = "status = :status";
    $params[':status'] = $_GET['status'];
}
if (!empty($_GET['role'])) {
    $where[] = "role = :role";
    $params[':role'] = $_GET['role'];
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Count total
$countStmt = $conn->prepare("SELECT COUNT(*) FROM employees $whereSql");
$countStmt->execute($params);
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);

// Fetch employees
$sql = "SELECT employee_id, employee_code, first_name, last_name, email, phone, department_name, designation_name, team_name, status, role FROM employees $whereSql ORDER BY employee_id DESC LIMIT $perPage OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - ZoCrew HRMS</title>
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
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-bottom: 18px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 18px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.04);
        }
        .filter-bar input, .filter-bar select {
            padding: 7px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1em;
            background: #fafbfc;
            transition: border 0.2s;
        }
        .filter-bar input:focus, .filter-bar select:focus {
            border: 1.5px solid var(--primary-color);
            outline: none;
            background: #fff;
        }
        .filter-bar label {
            font-weight: 500;
            color: #444;
            margin-right: 4px;
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
            min-width: 1100px;
            font-size: 13px;
        }
        th, td {
            padding: 10px 8px;
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
        .actions a {
            margin-right: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1em;
            padding: 4px 6px;
            border-radius: 4px;
            transition: background 0.15s, color 0.15s;
            background: none;
        }
        .actions a.delete {
            color: #e74c3c;
        }
        .actions a:hover {
            background: var(--primary-color);
            color: #fff;
        }
        .actions a:hover i {
            color: #fff;
        }
        .actions i {
            color: var(--hover-color);
            transition: color 0.2s;
        }
        .status-active {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.97em;
        }
        .status-inactive {
            color: #b2bec3;
            font-weight: 600;
            font-size: 0.97em;
        }
        .pagination {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
            margin: 18px 0 0 0;
        }
        .pagination a, .pagination span {
            display: inline-block;
            min-width: 32px;
            padding: 6px 12px;
            border-radius: 6px;
            background: #f8f9fa;
            color: #5f4b8b;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            font-size: 1em;
            transition: background 0.2s, color 0.2s;
        }
        .pagination a.active, .pagination span.active {
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: #fff;
        }
        .pagination a:hover:not(.active) {
            background: var(--primary-color);
            color: #fff;
        }
        @media (max-width: 900px) {
            .main-content {
                margin-left: 0 !important;
                padding: 15px 2vw 15px 2vw;
            }
            .table-container {
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    <div class="main-content">
        <div class="page-header">
            <h1>Employees</h1>
            <a href="./add/index.php" class="add-btn"><i class="fas fa-plus"></i> Add Employee</a>
        </div>
        <form class="filter-bar" method="get" action="">
            <label for="search">Search</label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Name, Email, Code...">
            <label for="department">Department</label>
            <select id="department" name="department">
                <option value="">All</option>
                <?php foreach($departments as $d): ?>
                    <option value="<?= htmlspecialchars($d) ?>" <?= (($_GET['department'] ?? '') == $d) ? 'selected' : '' ?>><?= htmlspecialchars($d) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="">All</option>
                <?php foreach($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= (($_GET['status'] ?? '') == $s) ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="">All</option>
                <?php foreach($roles as $r): ?>
                    <option value="<?= $r ?>" <?= (($_GET['role'] ?? '') == $r) ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="add-btn" style="padding:7px 18px;font-size:0.97em;"><i class="fas fa-filter"></i> Filter</button>
        </form>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Team</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($employees): foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['employee_id']) ?></td>
                        <td><?= htmlspecialchars($emp['employee_code']) ?></td>
                        <td><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                        <td><?= htmlspecialchars($emp['email']) ?></td>
                        <td><?= htmlspecialchars($emp['phone']) ?></td>
                        <td><?= htmlspecialchars($emp['department_name']) ?></td>
                        <td><?= htmlspecialchars($emp['designation_name']) ?></td>
                        <td><?= htmlspecialchars($emp['team_name']) ?></td>
                        <td class="status-<?= strtolower($emp['status']) ?>"><?= htmlspecialchars($emp['status']) ?></td>
                        <td><?= htmlspecialchars($emp['role']) ?></td>
                        <td class="actions">
                            <a href="view.php?id=<?= $emp['employee_id'] ?>" title="View"><i class="fas fa-eye"></i></a>
                            <a href="edit.php?id=<?= $emp['employee_id'] ?>" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $emp['employee_id'] ?>" class="delete" title="Delete" onclick="return confirm('Are you sure you want to delete this employee?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="11">No employees found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    <script>
    // Responsive main content margin for sidebar collapse/expand
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new MutationObserver(function() {
            if(document.body.classList.contains('sidebar-collapsed')) {
                document.querySelector('.main-content').style.marginLeft = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-collapsed-width');
            } else {
                document.querySelector('.main-content').style.marginLeft = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width');
            }
        });
        observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    });
    </script>
</body>
</html>
