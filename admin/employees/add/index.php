<?php
require_once '../../../config/config.php';
$current_page = 'employees';
$menu_path = '../../';
// Fetch departments, designations, teams for dropdowns
$conn = getConnection();
$departments = $conn->query("SELECT name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$designations = $conn->query("SELECT name FROM designations ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$teams = $conn->query("SELECT name FROM teams ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee - ZoCrew HRMS</title>
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
        .form-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.07);
            max-width: 600px;
            margin: 0 auto;
            padding: 32px 28px 24px 28px;
        }
        .form-title {
            font-size: 1.2em;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 24px;
            letter-spacing: 0.5px;
        }
        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #444;
        }
        form input, form select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1em;
            margin-bottom: 0;
            background: #fafbfc;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        form input:focus, form select:focus {
            border: 1.5px solid var(--primary-color);
            outline: none;
            background: #fff;
        }
        .form-row {
            display: flex;
            gap: 18px;
            margin-bottom: 22px;
        }
        .form-row > div {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 18px;
        }
        .btn {
            padding: 10px 24px;
            border-radius: 7px;
            font-weight: 600;
            font-size: 1em;
            border: none;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .btn-cancel {
            background: #f5f6fa;
            color: #5f4b8b;
        }
        .btn-cancel:hover {
            background: #ede7f6;
            color: var(--primary-color);
        }
        .btn-save {
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: var(--text-color);
        }
        .btn-save:hover {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
        }
        @media (max-width: 700px) {
            .form-row { flex-direction: column; gap: 0; margin-bottom: 18px; }
            .form-container { padding: 18px 6vw; }
        }
    </style>
</head>
<body>
    <?php include '../../components/sidebar.php'; ?>
    <?php include '../../components/topbar.php'; ?>
    <div class="main-content">
        <div class="form-container">
            <div class="form-title"><i class="fas fa-user-plus" style="color:var(--primary-color);margin-right:8px;"></i>Add Employee</div>
            <form method="post" action="save.php" autocomplete="off">
                <div class="form-row">
                    <div>
                        <label for="employee_code">Employee Code *</label>
                        <input type="text" id="employee_code" name="employee_code" required maxlength="50">
                    </div>
                    <div>
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required maxlength="100">
                    </div>
                    <div>
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required maxlength="100">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required maxlength="150">
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" maxlength="20">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="department_name">Department</label>
                        <select id="department_name" name="department_name">
                            <option value="">Select</option>
                            <?php foreach($departments as $d): ?>
                                <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="designation_name">Designation</label>
                        <select id="designation_name" name="designation_name">
                            <option value="">Select</option>
                            <?php foreach($designations as $d): ?>
                                <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="team_name">Team</label>
                        <select id="team_name" name="team_name">
                            <option value="">Select</option>
                            <?php foreach($teams as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="Employee">Employee</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="../" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 