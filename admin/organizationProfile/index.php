<?php
require_once '../../config/config.php';
$current_page = 'organizationProfile';
$menu_path = '../';

$conn = getConnection();
// Fetch organization details (assuming only one row)
$org = $conn->query("SELECT * FROM organization_details LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Profile - ZoCrew HRMS</title>
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
        .profile-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.07);
            max-width: 700px;
            margin: 0 auto;
            padding: 32px 28px 24px 28px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .profile-title {
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
        .profile-logo {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: contain;
            background: #f5f6fa;
            border: 1px solid #eee;
            margin-bottom: 18px;
        }
        .profile-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 32px;
        }
        .profile-field {
            margin-bottom: 10px;
        }
        .profile-field label {
            font-weight: 500;
            color: #444;
            font-size: 0.97em;
        }
        .profile-field .value {
            display: block;
            color: #333;
            font-size: 1em;
            margin-top: 2px;
        }
        @media (max-width: 700px) {
            .main-content { padding: 15px 2vw 15px 2vw; }
            .profile-container { padding: 18px 6vw; }
            .profile-fields { grid-template-columns: 1fr; }
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
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-title"><i class="fas fa-building" style="color:var(--primary-color);margin-right:8px;"></i> Organization Profile</div>
                <button class="edit-btn" id="openModal"><i class="fas fa-edit"></i> Edit</button>
            </div>
            <div style="text-align:center;">
                <img src="<?= $org && $org['logo_url'] ? htmlspecialchars($org['logo_url']) : '../assets/images/company-default.png' ?>" alt="Company Logo" class="profile-logo">
            </div>
            <div class="profile-fields">
                <div class="profile-field"><label>Company Name</label><span class="value"><?= htmlspecialchars($org['company_name'] ?? '') ?></span></div>
                <div class="profile-field"><label>Company Code</label><span class="value"><?= htmlspecialchars($org['company_code'] ?? '') ?></span></div>
                <div class="profile-field"><label>Industry Type</label><span class="value"><?= htmlspecialchars($org['industry_type'] ?? '') ?></span></div>
                <div class="profile-field"><label>Contact Number</label><span class="value"><?= htmlspecialchars($org['contact_number'] ?? '') ?></span></div>
                <div class="profile-field"><label>Email</label><span class="value"><?= htmlspecialchars($org['email'] ?? '') ?></span></div>
                <div class="profile-field"><label>Website</label><span class="value"><?= htmlspecialchars($org['website_url'] ?? '') ?></span></div>
                <div class="profile-field"><label>Founding Year</label><span class="value"><?= htmlspecialchars($org['founding_year'] ?? '') ?></span></div>
                <div class="profile-field"><label>Total Employees</label><span class="value"><?= htmlspecialchars($org['total_employees'] ?? '') ?></span></div>
                <div class="profile-field"><label>Revenue</label><span class="value"><?= htmlspecialchars($org['revenue'] ?? '') ?></span></div>
                <div class="profile-field"><label>Registration Number</label><span class="value"><?= htmlspecialchars($org['business_registration_number'] ?? '') ?></span></div>
                <div class="profile-field"><label>Fiscal Year Start</label><span class="value"><?= htmlspecialchars($org['fiscal_year_start'] ?? '') ?></span></div>
                <div class="profile-field"><label>Fiscal Year End</label><span class="value"><?= htmlspecialchars($org['fiscal_year_end'] ?? '') ?></span></div>
                <div class="profile-field"><label>Address</label><span class="value"><?= htmlspecialchars($org['address'] ?? '') ?></span></div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-title">
                <i class="fas fa-edit" style="color:var(--primary-color);margin-right:8px;"></i>
                Edit Organization Profile
            </div>
            <form method="post" action="save.php" id="editForm" enctype="multipart/form-data">
                <input type="hidden" name="organization_id" value="<?= $org['organization_id'] ?? '' ?>">
                <label for="company_name">Company Name *</label>
                <input type="text" id="company_name" name="company_name" required maxlength="255" value="<?= htmlspecialchars($org['company_name'] ?? '') ?>">
                <label for="company_code">Company Code *</label>
                <input type="text" id="company_code" name="company_code" required maxlength="50" value="<?= htmlspecialchars($org['company_code'] ?? '') ?>">
                <label for="industry_type">Industry Type</label>
                <input type="text" id="industry_type" name="industry_type" maxlength="100" value="<?= htmlspecialchars($org['industry_type'] ?? '') ?>">
                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" maxlength="20" value="<?= htmlspecialchars($org['contact_number'] ?? '') ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="150" value="<?= htmlspecialchars($org['email'] ?? '') ?>">
                <label for="website_url">Website</label>
                <input type="text" id="website_url" name="website_url" maxlength="255" value="<?= htmlspecialchars($org['website_url'] ?? '') ?>">
                <label for="founding_year">Founding Year</label>
                <input type="number" id="founding_year" name="founding_year" min="1800" max="2100" value="<?= htmlspecialchars($org['founding_year'] ?? '') ?>">
                <label for="total_employees">Total Employees</label>
                <input type="number" id="total_employees" name="total_employees" min="0" value="<?= htmlspecialchars($org['total_employees'] ?? '') ?>">
                <label for="revenue">Revenue</label>
                <input type="number" id="revenue" name="revenue" min="0" step="0.01" value="<?= htmlspecialchars($org['revenue'] ?? '') ?>">
                <label for="business_registration_number">Registration Number</label>
                <input type="text" id="business_registration_number" name="business_registration_number" maxlength="100" value="<?= htmlspecialchars($org['business_registration_number'] ?? '') ?>">
                <label for="fiscal_year_start">Fiscal Year Start</label>
                <input type="date" id="fiscal_year_start" name="fiscal_year_start" value="<?= htmlspecialchars($org['fiscal_year_start'] ?? '') ?>">
                <label for="fiscal_year_end">Fiscal Year End</label>
                <input type="date" id="fiscal_year_end" name="fiscal_year_end" value="<?= htmlspecialchars($org['fiscal_year_end'] ?? '') ?>">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="2" maxlength="255"><?= htmlspecialchars($org['address'] ?? '') ?></textarea>
                <label for="logo_url">Company Logo</label>
                <input type="file" id="logo_url" name="logo_url" accept="image/*">
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