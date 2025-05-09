<?php
require_once '../../config/config.php';
$current_page = 'systemSettings';
$menu_path = '../';

$conn = getConnection();
// Fetch system settings (assuming only one row)
$settings = $conn->query("SELECT * FROM system_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - ZoCrew HRMS</title>
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
        .settings-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.07);
            max-width: 700px;
            margin: 0 auto;
            padding: 32px 28px 24px 28px;
        }
        .settings-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .settings-title {
            font-size: 1.2em;
            font-weight: 700;
            color: var(--secondary-color);
            letter-spacing: 0.5px;
        }
        .edit-btn {
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: var(--text-color);
            padding: 8px 20px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.98em;
            box-shadow: 0 2px 8px rgba(90, 123, 213, 0.10);
            border: none;
            outline: none;
            letter-spacing: 0.2px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .edit-btn i { margin-right: 7px; color: var(--hover-color); }
        .edit-btn:hover { background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: #fff; }
        .edit-btn:hover i { color: #fff; }
        .settings-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 32px;
        }
        .settings-field {
            margin-bottom: 10px;
        }
        .settings-field label {
            font-weight: 500;
            color: #444;
            font-size: 0.97em;
        }
        .settings-field .value {
            display: block;
            color: #333;
            font-size: 1em;
            margin-top: 2px;
        }
        @media (max-width: 700px) {
            .main-content { padding: 15px 2vw 15px 2vw; }
            .settings-container { padding: 18px 6vw; }
            .settings-fields { grid-template-columns: 1fr; }
        }
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100vw; height: 100vh; overflow: auto; background: rgba(44, 75, 139, 0.18); }
        .modal-content { background: #fff; margin: 60px auto; border-radius: 12px; padding: 32px 28px 24px 28px; max-width: 600px; box-shadow: 0 8px 32px rgba(44,62,80,0.13); position: relative; }
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
        <div class="settings-container">
            <div class="settings-header">
                <div class="settings-title"><i class="fas fa-cogs" style="color:var(--primary-color);margin-right:8px;"></i> System Settings</div>
                <button class="edit-btn" id="openModal"><i class="fas fa-edit"></i> Edit</button>
            </div>
            <div class="settings-fields">
                <div class="settings-field"><label>Default Timezone</label><span class="value"><?= htmlspecialchars($settings['timezone'] ?? '') ?></span></div>
                <div class="settings-field"><label>Default Working Days</label><span class="value"><?= htmlspecialchars($settings['working_days'] ?? '') ?></span></div>
                <div class="settings-field"><label>Default Work Start</label><span class="value"><?= htmlspecialchars($settings['work_start'] ?? '') ?></span></div>
                <div class="settings-field"><label>Default Work End</label><span class="value"><?= htmlspecialchars($settings['work_end'] ?? '') ?></span></div>
                <div class="settings-field"><label>Default Leave Policy</label><span class="value"><?= htmlspecialchars($settings['leave_policy'] ?? '') ?></span></div>
                <div class="settings-field"><label>Attendance Grace (min)</label><span class="value"><?= htmlspecialchars($settings['attendance_grace'] ?? '') ?></span></div>
                <div class="settings-field"><label>Payroll Cycle</label><span class="value"><?= htmlspecialchars($settings['payroll_cycle'] ?? '') ?></span></div>
                <div class="settings-field"><label>Notification Email</label><span class="value"><?= htmlspecialchars($settings['notification_email'] ?? '') ?></span></div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-title">
                <i class="fas fa-edit" style="color:var(--primary-color);margin-right:8px;"></i>
                Edit System Settings
            </div>
            <form method="post" action="save.php" id="editForm">
                <input type="hidden" name="settings_id" value="<?= $settings['settings_id'] ?? '' ?>">
                <label for="timezone">Default Timezone</label>
                <input type="text" id="timezone" name="timezone" maxlength="100" value="<?= htmlspecialchars($settings['timezone'] ?? '') ?>">
                <label for="working_days">Default Working Days</label>
                <input type="text" id="working_days" name="working_days" maxlength="50" value="<?= htmlspecialchars($settings['working_days'] ?? '') ?>">
                <label for="work_start">Default Work Start</label>
                <input type="time" id="work_start" name="work_start" value="<?= htmlspecialchars($settings['work_start'] ?? '') ?>">
                <label for="work_end">Default Work End</label>
                <input type="time" id="work_end" name="work_end" value="<?= htmlspecialchars($settings['work_end'] ?? '') ?>">
                <label for="leave_policy">Default Leave Policy</label>
                <textarea id="leave_policy" name="leave_policy" rows="2" maxlength="255"><?= htmlspecialchars($settings['leave_policy'] ?? '') ?></textarea>
                <label for="attendance_grace">Attendance Grace (min)</label>
                <input type="number" id="attendance_grace" name="attendance_grace" min="0" max="120" value="<?= htmlspecialchars($settings['attendance_grace'] ?? '') ?>">
                <label for="payroll_cycle">Payroll Cycle</label>
                <input type="text" id="payroll_cycle" name="payroll_cycle" maxlength="50" value="<?= htmlspecialchars($settings['payroll_cycle'] ?? '') ?>">
                <label for="notification_email">Notification Email</label>
                <input type="email" id="notification_email" name="notification_email" maxlength="150" value="<?= htmlspecialchars($settings['notification_email'] ?? '') ?>">
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
        document.getElementById('editModal').style.display = 'block';
    };
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('editModal').style.display = 'none';
    };
    document.getElementById('cancelModal').onclick = function() {
        document.getElementById('editModal').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('editModal')) {
            document.getElementById('editModal').style.display = 'none';
        }
    };
    </script>
</body>
</html> 