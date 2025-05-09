<?php
require_once '../../config/config.php';
$current_page = 'index';
$menu_path = '../';

$conn = getConnection();

// Fetch key metrics
$total_employees = $conn->query("SELECT COUNT(*) FROM employees WHERE status = 'Active'")->fetchColumn();
$total_departments = $conn->query("SELECT COUNT(*) FROM departments")->fetchColumn();
$total_teams = $conn->query("SELECT COUNT(*) FROM teams")->fetchColumn();
$total_leaves = $conn->query("SELECT COUNT(*) FROM employee_leaves WHERE status = 'Pending'")->fetchColumn();

// Fetch recent leaves
$recent_leaves = $conn->query("
    SELECT el.*, e.first_name, e.last_name, e.employee_code 
    FROM employee_leaves el 
    JOIN employees e ON el.employee_id = e.employee_id 
    WHERE el.status = 'Pending' 
    ORDER BY el.created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch attendance stats for today
$today = date('Y-m-d');
$present_count = $conn->query("SELECT COUNT(*) FROM employee_attendance WHERE attendance_date = '$today' AND log_status = 'Present'")->fetchColumn();
$absent_count = $conn->query("SELECT COUNT(*) FROM employee_attendance WHERE attendance_date = '$today' AND log_status = 'Absent'")->fetchColumn();
$late_count = $conn->query("SELECT COUNT(*) FROM employee_attendance WHERE attendance_date = '$today' AND is_late = 1")->fetchColumn();

// Fetch department-wise employee count
$dept_stats = $conn->query("
    SELECT d.name, COUNT(e.employee_id) as count 
    FROM departments d 
    LEFT JOIN employees e ON d.name = e.department_name 
    WHERE e.status = 'Active' 
    GROUP BY d.name 
    ORDER BY count DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ZoCrew HRMS</title>
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
            background: #f5f6fa;
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
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.07);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: #fff;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 8px;
        }
        .stat-label {
            color: #666;
            font-size: 0.95em;
        }
        .dashboard-section {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.07);
            margin-bottom: 24px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--secondary-color);
        }
        .view-all {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9em;
        }
        .recent-leaves {
            display: grid;
            gap: 12px;
        }
        .leave-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .leave-item:hover {
            background: #f0f2f5;
        }
        .leave-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .leave-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .leave-details {
            display: flex;
            flex-direction: column;
        }
        .leave-name {
            font-weight: 600;
            color: #333;
        }
        .leave-type {
            font-size: 0.9em;
            color: #666;
        }
        .leave-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .dept-stats {
            display: grid;
            gap: 12px;
        }
        .dept-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .dept-name {
            font-weight: 500;
            color: #333;
        }
        .dept-count {
            font-weight: 600;
            color: var(--primary-color);
        }
        .attendance-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .attendance-stat {
            text-align: center;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .attendance-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 4px;
        }
        .attendance-label {
            color: #666;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .main-content {
                padding: 16px;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .attendance-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    
    <div class="main-content">
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= number_format($total_employees) ?></div>
                <div class="stat-label">Total Employees</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-value"><?= number_format($total_departments) ?></div>
                <div class="stat-label">Departments</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-value"><?= number_format($total_teams) ?></div>
                <div class="stat-label">Teams</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value"><?= number_format($total_leaves) ?></div>
                <div class="stat-label">Pending Leaves</div>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-header">
                <div class="section-title">Today's Attendance</div>
                <a href="../attendances/" class="view-all">View All</a>
            </div>
            <div class="attendance-stats">
                <div class="attendance-stat">
                    <div class="attendance-value"><?= number_format($present_count) ?></div>
                    <div class="attendance-label">Present</div>
                </div>
                <div class="attendance-stat">
                    <div class="attendance-value"><?= number_format($absent_count) ?></div>
                    <div class="attendance-label">Absent</div>
                </div>
                <div class="attendance-stat">
                    <div class="attendance-value"><?= number_format($late_count) ?></div>
                    <div class="attendance-label">Late</div>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-header">
                <div class="section-title">Recent Leave Requests</div>
                <a href="../leavesApprovals/" class="view-all">View All</a>
            </div>
            <div class="recent-leaves">
                <?php foreach ($recent_leaves as $leave): ?>
                    <div class="leave-item">
                        <div class="leave-info">
                            <div class="leave-avatar">
                                <?= strtoupper(substr($leave['first_name'], 0, 1)) ?>
                            </div>
                            <div class="leave-details">
                                <div class="leave-name"><?= htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) ?></div>
                                <div class="leave-type"><?= htmlspecialchars($leave['leave_type']) ?> (<?= $leave['days'] ?> days)</div>
                            </div>
                        </div>
                        <div class="leave-status status-pending">Pending</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-header">
                <div class="section-title">Department Statistics</div>
                <a href="../departments/" class="view-all">View All</a>
            </div>
            <div class="dept-stats">
                <?php foreach ($dept_stats as $dept): ?>
                    <div class="dept-item">
                        <div class="dept-name"><?= htmlspecialchars($dept['name']) ?></div>
                        <div class="dept-count"><?= number_format($dept['count']) ?> employees</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html> 