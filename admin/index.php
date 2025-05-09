<?php
$current_page = "index";
$menu_path="./";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZoCrew HRMS</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
        }

        .sidebar {
            /* Sidebar styles are in sidebar.php */
        }

        .topbar {
            /* Topbar styles are in topbar.php */
        }

        .main-content {
            margin-left: 270px; /* Sidebar width + left offset */
            margin-top: 60px;   /* Topbar height */
            padding: 30px 30px 30px 30px;
            min-height: calc(100vh - 60px);
            transition: margin-left 0.3s;
        }

        .main-content.expanded {
            margin-left: 80px; /* Collapsed sidebar width + left offset */
        }

        /* Sidebar Collapse Styles */
        .sidebar.collapsed {
            width: 60px !important;
        }
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .nav-section-title {
            display: none;
        }
        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px;
        }
        .sidebar.collapsed .nav-item i {
            margin: 0;
        }
        /* Topbar Expanded Styles */
        .topbar.expanded {
            left: 80px !important;
        }
        @media (max-width: 900px) {
            .main-content {
                margin-left: 0 !important;
            }
            .sidebar {
                left: 0 !important;
            }
            .topbar {
                left: 0 !important;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/sidebar.php'; ?>
    <?php include 'components/topbar.php'; ?>
    <div class="main-content">
        <!-- <h1>Welcome to ZoCrew HRMS</h1>
        <p>This is your dashboard. Start managing your HR operations from here.</p> -->
    </div>
</body>
</html> 