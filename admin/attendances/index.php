<?php
require_once '../../config/config.php';
$current_page = 'attendances';
$menu_path = '../';

$conn = getConnection();

// Get filters from URL parameters
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$department = isset($_GET['department']) ? $_GET['department'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query with filters
$query = "SELECT ea.*, e.first_name, e.last_name, e.department_name, e.designation_name 
          FROM employee_attendance ea 
          JOIN employees e ON ea.employee_id = e.employee_id 
          WHERE ea.attendance_date = :date";

$params = [':date' => $date];

if ($department) {
    $query .= " AND e.department_name = :department";
    $params[':department'] = $department;
}

if ($status) {
    $query .= " AND ea.log_status = :status";
    $params[':status'] = $status;
}

$query .= " ORDER BY e.first_name, e.last_name";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get departments for filter
$departments = $conn->query("SELECT DISTINCT department_name FROM employees WHERE department_name IS NOT NULL ORDER BY department_name")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Attendance - ZoCrew HRMS</title>
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
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-group label {
            font-weight: 500;
            color: #444;
            font-size: 0.9em;
        }
        .filter-group select, .filter-group input {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9em;
            background: #fafbfc;
            transition: border 0.2s;
            min-width: 150px;
        }
        .filter-group select:focus, .filter-group input:focus {
            border: 1.5px solid var(--primary-color);
            outline: none;
            background: #fff;
        }
        .apply-filters {
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: var(--text-color);
            padding: 8px 20px;
            border-radius: 7px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .apply-filters:hover {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
            min-width: 800px;
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
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-present {
            background: #e3fcef;
            color: #00a854;
        }
        .status-absent {
            background: #fff1f0;
            color: #f5222d;
        }
        .status-late {
            background: #fff7e6;
            color: #fa8c16;
        }
        .status-holiday {
            background: #f6ffed;
            color: #52c41a;
        }
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
        @media (max-width: 700px) {
            .main-content { padding: 15px 2vw 15px 2vw; }
            .table-container { border-radius: 0; box-shadow: none; }
            .filters { flex-direction: column; }
            .filter-group { width: 100%; }
            .filter-group select, .filter-group input { width: 100%; }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    <div class="main-content">
        <div class="page-header">
            <h1>Daily Attendance</h1>
        </div>
        
        <div class="filters">
            <div class="filter-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>">
            </div>
            <div class="filter-group">
                <label for="department">Department:</label>
                <select id="department" name="department">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                    <option value="<?= htmlspecialchars($dept) ?>" <?= $department === $dept ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dept) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="">All Status</option>
                    <option value="Present" <?= $status === 'Present' ? 'selected' : '' ?>>Present</option>
                    <option value="Absent" <?= $status === 'Absent' ? 'selected' : '' ?>>Absent</option>
                    <option value="Late" <?= $status === 'Late' ? 'selected' : '' ?>>Late</option>
                    <option value="Holiday" <?= $status === 'Holiday' ? 'selected' : '' ?>>Holiday</option>
                </select>
            </div>
            <button class="apply-filters" onclick="applyFilters()">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Punch In</th>
                        <th>Punch Out</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attendances): foreach ($attendances as $attendance): ?>
                    <tr>
                        <td><?= htmlspecialchars($attendance['first_name'] . ' ' . $attendance['last_name']) ?></td>
                        <td><?= htmlspecialchars($attendance['department_name']) ?></td>
                        <td><?= htmlspecialchars($attendance['designation_name']) ?></td>
                        <td><?= $attendance['punch_in'] ? date('h:i A', strtotime($attendance['punch_in'])) : '-' ?></td>
                        <td><?= $attendance['punch_out'] ? date('h:i A', strtotime($attendance['punch_out'])) : '-' ?></td>
                        <td><?= $attendance['effective_hours'] ?: '-' ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($attendance['log_status']) ?>">
                                <?= htmlspecialchars($attendance['log_status']) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="#" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" title="View Details"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8">No attendance records found for the selected date.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function applyFilters() {
        const date = document.getElementById('date').value;
        const department = document.getElementById('department').value;
        const status = document.getElementById('status').value;
        
        let url = 'index.php?';
        if (date) url += `date=${date}&`;
        if (department) url += `department=${encodeURIComponent(department)}&`;
        if (status) url += `status=${encodeURIComponent(status)}`;
        
        window.location.href = url;
    }
    </script>
</body>
</html> 