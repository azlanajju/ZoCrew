<?php
require_once '../../config/config.php';
$current_page = 'holidays';
$menu_path = '../';

$conn = getConnection();

// Fetch holidays (optionally filter by year)
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$holidays = $conn->prepare("SELECT * FROM company_holidays WHERE financial_year = :year ORDER BY holiday_date ASC");
$holidays->execute([':year' => $year]);
$holidays = $holidays->fetchAll(PDO::FETCH_ASSOC);

// Get all years for filter dropdown
$years = $conn->query("SELECT DISTINCT financial_year FROM company_holidays ORDER BY financial_year DESC")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holidays - ZoCrew HRMS</title>
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
        .badge-normal { background: #e3fcef; color: #00a854; }
        .badge-floater { background: #e6e6fa; color: #5f4b8b; }
        .badge-federal { background: #fff1f0; color: #f5222d; }
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
            <h1>Holidays</h1>
            <button class="add-btn" id="openModal"><i class="fas fa-plus"></i> Add Holiday</button>
        </div>
        <div class="filter-bar">
            <form method="get" style="display:inline-flex;align-items:center;gap:10px;">
                <label for="year">Financial Year:</label>
                <select name="year" id="year" onchange="this.form.submit()">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= htmlspecialchars($y) ?>" <?= $y == $year ? 'selected' : '' ?>><?= htmlspecialchars($y) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Federal</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($holidays): foreach ($holidays as $holiday): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($holiday['holiday_date'])) ?></td>
                        <td><?= htmlspecialchars($holiday['holiday_name']) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($holiday['holiday_type']) ?>">
                                <?= htmlspecialchars($holiday['holiday_type']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($holiday['is_federal'])): ?>
                                <span class="badge badge-federal">Yes</span>
                            <?php else: ?>
                                <span class="badge">No</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($holiday['description']) ?></td>
                        <td class="actions">
                            <a href="#" title="Edit" onclick='editHoliday(<?= json_encode($holiday) ?>)'><i class="fas fa-edit"></i></a>
                            <a href="#" class="delete" title="Delete" onclick="deleteHoliday(<?= $holiday['holiday_id'] ?>)"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6">No holidays found for this year.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="holidayModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-title">
                <i class="fas fa-calendar-day" style="color:var(--primary-color);margin-right:8px;"></i>
                <span id="modalTitle">Add Holiday</span>
            </div>
            <form method="post" action="save.php" id="holidayForm">
                <input type="hidden" name="holiday_id" id="holiday_id">
                <label for="holiday_name">Holiday Name *</label>
                <input type="text" id="holiday_name" name="holiday_name" required maxlength="255">
                <label for="holiday_date">Date *</label>
                <input type="date" id="holiday_date" name="holiday_date" required>
                <label for="holiday_type">Type *</label>
                <select id="holiday_type" name="holiday_type" required>
                    <option value="Normal">Normal</option>
                    <option value="Floater">Floater</option>
                </select>
                <label for="is_federal">Federal Holiday?</label>
                <select id="is_federal" name="is_federal">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
                <label for="financial_year">Financial Year *</label>
                <input type="text" id="financial_year" name="financial_year" required placeholder="e.g. 2024-2025" maxlength="9">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="2" maxlength="255"></textarea>
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
        document.getElementById('modalTitle').textContent = 'Add Holiday';
        document.getElementById('holidayForm').reset();
        document.getElementById('holiday_id').value = '';
        document.getElementById('holidayModal').style.display = 'block';
    };
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('holidayModal').style.display = 'none';
    };
    document.getElementById('cancelModal').onclick = function() {
        document.getElementById('holidayModal').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('holidayModal')) {
            document.getElementById('holidayModal').style.display = 'none';
        }
    };
    // Edit holiday
    function editHoliday(holiday) {
        document.getElementById('modalTitle').textContent = 'Edit Holiday';
        document.getElementById('holiday_id').value = holiday.holiday_id;
        document.getElementById('holiday_name').value = holiday.holiday_name;
        document.getElementById('holiday_date').value = holiday.holiday_date;
        document.getElementById('holiday_type').value = holiday.holiday_type;
        document.getElementById('is_federal').value = holiday.is_federal;
        document.getElementById('financial_year').value = holiday.financial_year;
        document.getElementById('description').value = holiday.description || '';
        document.getElementById('holidayModal').style.display = 'block';
    }
    // Delete holiday
    function deleteHoliday(holidayId) {
        if (confirm('Are you sure you want to delete this holiday?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete.php';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'holiday_id';
            input.value = holidayId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html> 