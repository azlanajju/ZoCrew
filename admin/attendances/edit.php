<?php
require_once '../../config/config.php';
$current_page = 'attendances';
$menu_path = '../';

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_id = $_POST['attendance_id'];
    $punch_in = $_POST['punch_in'];
    $punch_out = $_POST['punch_out'];
    $log_status = $_POST['log_status'];
    $reason = $_POST['reason'];

    try {
        $stmt = $conn->prepare("UPDATE employee_attendance SET 
            punch_in = :punch_in,
            punch_out = :punch_out,
            log_status = :log_status,
            reason = :reason
            WHERE attendance_id = :attendance_id");
        
        $stmt->execute([
            ':punch_in' => $punch_in ? date('Y-m-d H:i:s', strtotime($punch_in)) : null,
            ':punch_out' => $punch_out ? date('Y-m-d H:i:s', strtotime($punch_out)) : null,
            ':log_status' => $log_status,
            ':reason' => $reason,
            ':attendance_id' => $attendance_id
        ]);

        header('Location: index.php?success=1');
        exit;
    } catch (PDOException $e) {
        $error = "Error updating attendance: " . $e->getMessage();
    }
}

// Get attendance record
$attendance_id = $_GET['id'] ?? null;
if (!$attendance_id) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT ea.*, e.first_name, e.last_name, e.department_name, e.designation_name 
                       FROM employee_attendance ea 
                       JOIN employees e ON ea.employee_id = e.employee_id 
                       WHERE ea.attendance_id = :id");
$stmt->execute([':id' => $attendance_id]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attendance) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance - ZoCrew HRMS</title>
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
        .back-btn {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95em;
        }
        .back-btn i {
            margin-right: 8px;
        }
        .edit-form {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.07);
            padding: 32px;
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            color: #444;
            margin-bottom: 8px;
            font-size: 0.95em;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.95em;
            background: #fafbfc;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border: 1.5px solid var(--primary-color);
            outline: none;
            background: #fff;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .employee-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .employee-info p {
            margin: 5px 0;
            color: #555;
            font-size: 0.95em;
        }
        .employee-info strong {
            color: #333;
            font-weight: 600;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }
        .btn {
            padding: 10px 24px;
            border-radius: 7px;
            font-weight: 600;
            font-size: 0.95em;
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
        }
        .error-message {
            background: #fff1f0;
            color: #f5222d;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        @media (max-width: 700px) {
            .main-content { padding: 15px 2vw 15px 2vw; }
            .edit-form { padding: 20px; }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    <div class="main-content">
        <div class="page-header">
            <h1>Edit Attendance</h1>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Attendance List
            </a>
        </div>

        <?php if (isset($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="edit-form">
            <div class="employee-info">
                <p><strong>Employee:</strong> <?= htmlspecialchars($attendance['first_name'] . ' ' . $attendance['last_name']) ?></p>
                <p><strong>Department:</strong> <?= htmlspecialchars($attendance['department_name']) ?></p>
                <p><strong>Designation:</strong> <?= htmlspecialchars($attendance['designation_name']) ?></p>
                <p><strong>Date:</strong> <?= date('F d, Y', strtotime($attendance['attendance_date'])) ?></p>
            </div>

            <form method="post" action="">
                <input type="hidden" name="attendance_id" value="<?= $attendance['attendance_id'] ?>">
                
                <div class="form-group">
                    <label for="punch_in">Punch In Time</label>
                    <input type="datetime-local" id="punch_in" name="punch_in" 
                           value="<?= $attendance['punch_in'] ? date('Y-m-d\TH:i', strtotime($attendance['punch_in'])) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="punch_out">Punch Out Time</label>
                    <input type="datetime-local" id="punch_out" name="punch_out"
                           value="<?= $attendance['punch_out'] ? date('Y-m-d\TH:i', strtotime($attendance['punch_out'])) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="log_status">Status</label>
                    <select id="log_status" name="log_status" required>
                        <option value="Present" <?= $attendance['log_status'] === 'Present' ? 'selected' : '' ?>>Present</option>
                        <option value="Absent" <?= $attendance['log_status'] === 'Absent' ? 'selected' : '' ?>>Absent</option>
                        <option value="Late" <?= $attendance['log_status'] === 'Late' ? 'selected' : '' ?>>Late</option>
                        <option value="Holiday" <?= $attendance['log_status'] === 'Holiday' ? 'selected' : '' ?>>Holiday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reason">Reason/Remarks</label>
                    <textarea id="reason" name="reason"><?= htmlspecialchars($attendance['reason'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 