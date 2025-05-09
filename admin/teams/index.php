<?php
require_once '../../config/config.php';
$current_page = 'teams';
$menu_path = '../';

$conn = getConnection();
$teams = $conn->query("SELECT team_id, name, description FROM teams ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams - ZoCrew HRMS</title>
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
            min-width: 600px;
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
            max-width: 400px;
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
        .modal input, .modal textarea {
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
        .modal input:focus, .modal textarea:focus {
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
        @media (max-width: 700px) {
            .main-content { padding: 15px 2vw 15px 2vw; }
            .table-container { border-radius: 0; box-shadow: none; }
            .modal-content { padding: 18px 6vw; }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/topbar.php'; ?>
    <div class="main-content">
        <div class="page-header">
            <h1>Teams</h1>
            <button class="add-btn" id="openModal"><i class="fas fa-plus"></i> Add Team</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($teams): foreach ($teams as $team): ?>
                    <tr>
                        <td><?= htmlspecialchars($team['name']) ?></td>
                        <td><?= htmlspecialchars($team['description']) ?></td>
                        <td class="actions">
                            <a href="#" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="delete" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="3">No teams found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-title"><i class="fas fa-plus-circle" style="color:var(--primary-color);margin-right:8px;"></i>Add Team</div>
            <form method="post" action="add.php" autocomplete="off">
                <label for="team_name">Team Name *</label>
                <input type="text" id="team_name" name="team_name" required maxlength="100">
                <label for="team_desc">Description</label>
                <textarea id="team_desc" name="team_desc" rows="3" maxlength="255"></textarea>
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" id="cancelModal">Cancel</button>
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Modal open/close logic
    document.getElementById('openModal').onclick = function() {
        document.getElementById('addModal').style.display = 'block';
    };
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('addModal').style.display = 'none';
    };
    document.getElementById('cancelModal').onclick = function() {
        document.getElementById('addModal').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            document.getElementById('addModal').style.display = 'none';
        }
    };
    </script>
</body>
</html> 