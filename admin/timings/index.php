<?php
require_once '../../config/config.php';
$current_page = 'timings';
$menu_path = '../';

$conn = getConnection();

// Get all work timings
$timings = $conn->query("SELECT * FROM company_timings ORDER BY shift_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Timings - ZoCrew HRMS</title>
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
        .status-active {
            background: #e3fcef;
            color: #00a854;
        }
        .status-inactive {
            background: #fff1f0;
            color: #f5222d;
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
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            overflow: auto;
            background: rgba(44, 75, 139, 0.18);
        }
        .modal-content {
            background: #fff;
            margin: 60px auto;
            border-radius: 12px;
            padding: 32px 28px 24px 28px;
            max-width: 500px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.13);
            position: relative;
        }
        .modal-title {
            font-size: 1.1em;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 18px;
        }
        .close {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 1.2em;
            color: #aaa;
            cursor: pointer;
            transition: color 0.2s;
        }
        .close:hover {
            color: var(--primary-color);
        }
        .modal label {
            font-weight: 500;
            color: #444;
            margin-bottom: 6px;
            display: block;
        }
        .modal input, .modal select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1em;
            margin-bottom: 18px;
            background: #fafbfc;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        .modal input:focus, .modal select:focus {
            border: 1.5px solid var(--primary-color);
            outline: none;
            background: #fff;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .modal .btn {
            padding: 9px 22px;
            border-radius: 7px;
            font-weight: 600;
            font-size: 1em;
            border: none;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .modal .btn-cancel {
            background: #f5f6fa;
            color: #5f4b8b;
        }
        .modal .btn-cancel:hover {
            background: #ede7f6;
            color: var(--primary-color);
        }
        .modal .btn-save {
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: var(--text-color);
        }
        .modal .btn-save:hover {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
        }
        .time-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        @media (max-width: 700px) {
            .main-content { padding: 15px 2vw 15px 2vw; }
            .table-container { border-radius: 0; box-shadow: none; }
            .modal-content { padding: 18px 6vw; }
            .time-inputs { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    <div class="main-content">
        <div class="page-header">
            <h1>Work Timings</h1>
            <button class="add-btn" id="openModal"><i class="fas fa-plus"></i> Add Shift</button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Shift Name</th>
                        <th>Work Hours</th>
                        <th>Break Time</th>
                        <th>Grace Period</th>
                        <th>Required Hours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($timings): foreach ($timings as $timing): ?>
                    <tr>
                        <td><?= htmlspecialchars($timing['shift_name']) ?></td>
                        <td>
                            <?= date('h:i A', strtotime($timing['work_start'])) ?> - 
                            <?= date('h:i A', strtotime($timing['work_end'])) ?>
                        </td>
                        <td>
                            <?php if ($timing['break_start'] && $timing['break_end']): ?>
                                <?= date('h:i A', strtotime($timing['break_start'])) ?> - 
                                <?= date('h:i A', strtotime($timing['break_end'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= $timing['grace_period_minutes'] ?> minutes</td>
                        <td>
                            <?= $timing['effective_hours_required'] ?> (Effective)<br>
                            <?= $timing['gross_hours_required'] ?> (Gross)
                        </td>
                        <td>
                            <span class="status-badge status-<?= $timing['is_active'] ? 'active' : 'inactive' ?>">
                                <?= $timing['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="#" title="Edit" onclick="editTiming(<?= htmlspecialchars(json_encode($timing)) ?>)">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="delete" title="Delete" onclick="deleteTiming(<?= $timing['timing_id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7">No work timings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="timingModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-title">
                <i class="fas fa-clock" style="color:var(--primary-color);margin-right:8px;"></i>
                <span id="modalTitle">Add Shift</span>
            </div>
            <form method="post" action="save.php" id="timingForm">
                <input type="hidden" name="timing_id" id="timing_id">
                
                <label for="shift_name">Shift Name *</label>
                <input type="text" id="shift_name" name="shift_name" required maxlength="100">
                
                <div class="time-inputs">
                    <div>
                        <label for="work_start">Work Start Time *</label>
                        <input type="time" id="work_start" name="work_start" required>
                    </div>
                    <div>
                        <label for="work_end">Work End Time *</label>
                        <input type="time" id="work_end" name="work_end" required>
                    </div>
                </div>

                <div class="time-inputs">
                    <div>
                        <label for="break_start">Break Start Time</label>
                        <input type="time" id="break_start" name="break_start">
                    </div>
                    <div>
                        <label for="break_end">Break End Time</label>
                        <input type="time" id="break_end" name="break_end">
                    </div>
                </div>

                <div class="time-inputs">
                    <div>
                        <label for="grace_period">Grace Period (minutes)</label>
                        <input type="number" id="grace_period" name="grace_period_minutes" min="0" max="60" value="0">
                    </div>
                    <div>
                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="time-inputs">
                    <div>
                        <label for="effective_hours">Required Effective Hours *</label>
                        <input type="time" id="effective_hours" name="effective_hours_required" required>
                    </div>
                    <div>
                        <label for="gross_hours">Required Gross Hours *</label>
                        <input type="time" id="gross_hours" name="gross_hours_required" required>
                    </div>
                </div>

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
        document.getElementById('modalTitle').textContent = 'Add Shift';
        document.getElementById('timingForm').reset();
        document.getElementById('timing_id').value = '';
        document.getElementById('timingModal').style.display = 'block';
    };

    document.getElementById('closeModal').onclick = function() {
        document.getElementById('timingModal').style.display = 'none';
    };

    document.getElementById('cancelModal').onclick = function() {
        document.getElementById('timingModal').style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target == document.getElementById('timingModal')) {
            document.getElementById('timingModal').style.display = 'none';
        }
    };

    // Edit timing
    function editTiming(timing) {
        document.getElementById('modalTitle').textContent = 'Edit Shift';
        document.getElementById('timing_id').value = timing.timing_id;
        document.getElementById('shift_name').value = timing.shift_name;
        document.getElementById('work_start').value = timing.work_start;
        document.getElementById('work_end').value = timing.work_end;
        document.getElementById('break_start').value = timing.break_start || '';
        document.getElementById('break_end').value = timing.break_end || '';
        document.getElementById('grace_period').value = timing.grace_period_minutes;
        document.getElementById('is_active').value = timing.is_active;
        document.getElementById('effective_hours').value = timing.effective_hours_required;
        document.getElementById('gross_hours').value = timing.gross_hours_required;
        
        document.getElementById('timingModal').style.display = 'block';
    }

    // Delete timing
    function deleteTiming(timingId) {
        if (confirm('Are you sure you want to delete this shift?')) {
            window.location.href = `delete.php?id=${timingId}`;
        }
    }
    </script>
</body>
</html> 