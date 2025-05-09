<?php
require_once '../../config/config.php';
$current_page = 'employees';
$menu_path = '../';

$conn = getConnection();

// Fetch all employees
$stmt = $conn->prepare("SELECT employee_id, employee_code, first_name, last_name, email, phone, department_name, designation_name, team_name, status, role FROM employees ORDER BY employee_id DESC");
$stmt->execute();
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
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            font-size: 14px;
            color: #222;
        }
        .main-content {
            margin-left: 270px;
            margin-top: 60px;
            padding: 32px 2vw 32px 2vw;
            min-height: calc(100vh - 60px);
            transition: margin-left 0.3s;
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
            background: #27ae60;
            color: #fff;
            padding: 8px 20px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.98em;
            box-shadow: 0 2px 8px rgba(39,174,96,0.10);
            transition: background 0.2s, box-shadow 0.2s;
            border: none;
            outline: none;
            letter-spacing: 0.2px;
        }
        .add-btn i {
            margin-right: 7px;
            font-size: 1em;
        }
        .add-btn:hover {
            background: #219150;
            box-shadow: 0 4px 16px rgba(39,174,96,0.13);
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
            color: #6c7a89;
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
            color: #3498db;
            text-decoration: none;
            font-size: 1em;
            padding: 4px 6px;
            border-radius: 4px;
            transition: background 0.15s;
        }
        .actions a.delete {
            color: #e74c3c;
        }
        .actions a:hover {
            background: #f0f8f5;
        }
        .status-active {
            color: #27ae60;
            font-weight: 600;
            font-size: 0.97em;
        }
        .status-inactive {
            color: #b2bec3;
            font-weight: 600;
            font-size: 0.97em;
        }
        td, th {
            border-right: 1px solid #f2f2f2;
        }
        td:last-child, th:last-child {
            border-right: none;
        }
        table thead tr th:first-child {
            border-top-left-radius: 10px;
        }
        table thead tr th:last-child {
            border-top-right-radius: 10px;
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
    </div>
</body>
</html>
