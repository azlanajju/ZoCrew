<div class="sidebar collapsed" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-building"></i>
            <span>ZoCrew HRMS</span>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span class="link-text">Dashboard</span>
            </a>
        </li>
        <div class="section-divider"><span>Employee Management</span></div>
        <li class="<?php echo ($current_page == 'employees') ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>employees/">
                <i class="fas fa-users"></i>
                <span class="link-text">Employees</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'departments') ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>departments/">
                <i class="fas fa-building"></i>
                <span class="link-text">Departments</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'teams') ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>teams">
                <i class="fas fa-user-friends"></i>
                <span class="link-text">Teams</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'designations') ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>designations">
                <i class="fas fa-id-badge"></i>
                <span class="link-text">Designations</span>
            </a>
        </li>
        <div class="section-divider"><span>Attendance</span></div>
        <li class="<?php echo strpos($current_page, 'attendance/daily') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>attendances/">
                <i class="fas fa-calendar-check"></i>
                <span class="link-text">Daily Attendance</span>
            </a>
        </li>
        <li class="<?php echo strpos($current_page, 'attendance/reports') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>attendance/reports.php">
                <i class="fas fa-chart-bar"></i>
                <span class="link-text">Attendance Reports</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'timings') ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>timings">
                <i class="fas fa-clock"></i>
                <span class="link-text">Work Timings</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'holidays') ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>holidays">
                <i class="fas fa-calendar-day"></i>
                <span class="link-text">Holidays</span>
            </a>
        </li>
        <div class="section-divider"><span>Leave Management</span></div>
        <li class="<?php echo strpos($current_page, 'leavesBalances') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>admin/leavesBalances">
                <i class="fas fa-balance-scale"></i>
                <span class="link-text">Leave Balance</span>
            </a>
        </li>
        <li class="<?php echo strpos($current_page, 'leavesApprovals') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>leavesApprovals">
                <i class="fas fa-check-circle"></i>
                <span class="link-text">Leave Approvals</span>
            </a>
        </li>
        <div class="section-divider"><span>Organization</span></div>
        <li class="<?php echo strpos($current_page, 'organization/profile') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>organization/profile">
                <i class="fas fa-building"></i>
                <span class="link-text">Company Profile</span>
            </a>
        </li>
        <li class="<?php echo strpos($current_page, 'organization/branches') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>organization/branches">
                <i class="fas fa-map-marker-alt"></i>
                <span class="link-text">Branches</span>
            </a>
        </li>
        <div class="section-divider"><span>Settings</span></div>
        <li class="<?php echo strpos($current_page, 'systemSettings') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $menu_path; ?>systemSettings">
                <i class="fas fa-cogs"></i>
                <span class="link-text">System Settings</span>
            </a>
        </li>
    </ul>
</div>

<style>
:root {
    --sidebar-width: 250px;
    --sidebar-collapsed-width: 90px;
    --primary-color: #3a7bd5;
    --secondary-color: #5f4b8b;
    --text-color: #f0f0f0;
    --hover-color:rgb(166, 108, 224);
    --transition-speed: 0.3s;
}

.sidebar::-webkit-scrollbar {
    width: 3px;
    background: var(--secondary-color);
}
.sidebar::-webkit-scrollbar-thumb {
    background: var(--primary-color);
}
.sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    height: calc(100vh - 60px);
    width: var(--sidebar-width);
    background: linear-gradient(180deg, var(--secondary-color) 0%, var(--primary-color) 100%);
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.08);
    transition: all var(--transition-speed) ease;
    z-index: 100;
    overflow-y: auto;
    overflow-x: hidden;
    /* border-radius: 0 12px 12px 0; */
}
.sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
}
.sidebar.collapsed:hover {
    width: var(--sidebar-width);
}
.sidebar-header {
    padding: 20px 15px 10px 15px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.sidebar-logo {
    display: flex;
    align-items: center;
    color: var(--text-color);
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 1px;
}
.sidebar-logo i {
    margin-right: 12px;
    font-size: 22px;
    color: var(--hover-color);
}
.sidebar-logo span {
    opacity: 1;
    transition: opacity var(--transition-speed) ease;
}
.collapsed .sidebar-logo span {
    opacity: 0;
    width: 0;
    height: 0;
    overflow: hidden;
}
.sidebar-menu {
    padding: 10px 0;
    list-style: none;
    margin: 0;
}
.sidebar-menu li {
    position: relative;
    margin: 5px 10px;
    border-radius: 8px;
    transition: all var(--transition-speed) ease;
}
.sidebar-menu li:hover {
    background: rgba(255,255,255,0.08);
}
.sidebar-menu li.active {
    background: rgba(255,255,255,0.15);
    box-shadow: 0 2px 5px rgba(0,0,0,0.13);
}
.sidebar-menu li.active::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 0;
    height: 100%;
    width: 5px;
    background: var(--hover-color);
    border-radius: 0 5px 5px 0;
}
.sidebar-menu a {
    display: flex;
    align-items: center;
    color: var(--text-color);
    text-decoration: none;
    padding: 12px 15px;
    transition: all var(--transition-speed) ease;
    white-space: nowrap;
    overflow: hidden;
}
.sidebar-menu i {
    min-width: 30px;
    text-align: center;
    font-size: 18px;
    margin-right: 10px;
    transition: all var(--transition-speed) ease;
}
.sidebar-menu a:hover i {
    color: var(--hover-color);
    transform: translateX(5px);
}
.sidebar-menu .link-text {
    transition: opacity var(--transition-speed) ease;
    opacity: 1;
}
.collapsed .sidebar-menu .link-text {
    opacity: 0;
    width: 0;
    height: 0;
    overflow: hidden;
}
.sidebar.collapsed:hover .sidebar-logo span,
.sidebar.collapsed:hover .link-text,
.sidebar.collapsed:hover .section-divider span {
    opacity: 1;
    width: auto;
    height: auto;
    overflow: visible;
}
.section-divider {
    margin: 15px 15px 8px 15px;
    height: 1px;
    background: rgba(255,255,255,0.10);
    position: relative;
}
.section-divider span {
    position: absolute;
    left: 0;
    top: -8px;
    font-size: 12px;
    color: rgba(255,255,255,0.5);
    padding: 0 5px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: opacity var(--transition-speed) ease;
}
.collapsed .section-divider span {
    opacity: 0;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    // Sidebar is collapsed by default, expands on hover (no click needed)
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
});
</script> 