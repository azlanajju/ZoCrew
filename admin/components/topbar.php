<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

?>

<div class="topbar">
    <div class="topbar-left">
        <button id="sidebar-toggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search...">
        </div>
    </div>
    
    <div class="topbar-right">
        <div class="notifications">
            <button class="notification-btn">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
        </div>
        
        <div class="messages">
            <button class="message-btn">
                <i class="fas fa-envelope"></i>
                <span class="message-badge">5</span>
            </button>
        </div>
        
        <div class="user-profile">
            <img src="assets/images/default-avatar.png" alt="User Avatar" class="avatar">
            <div class="user-info">
                <span class="user-name">John Doe</span>
                <span class="user-role">Administrator</span>
            </div>
            <div class="dropdown-menu">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<style>
.topbar {
    height: 60px;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.sidebar-toggle {
    background: none;
    border: none;
    font-size: 1.2em;
    cursor: pointer;
    color: #2c3e50;
}

.search-box {
    display: flex;
    align-items: center;
    background-color: #f5f6fa;
    border-radius: 20px;
    padding: 5px 15px;
}

.search-box input {
    border: none;
    background: none;
    outline: none;
    padding: 5px;
    width: 200px;
}

.search-box i {
    color: #7f8c8d;
    margin-right: 5px;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.notification-btn, .message-btn {
    background: none;
    border: none;
    font-size: 1.2em;
    color: #2c3e50;
    cursor: pointer;
    position: relative;
}

.notification-badge, .message-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #e74c3c;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.7em;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    cursor: pointer;
}

.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    font-size: 0.9em;
    color: #2c3e50;
}

.user-role {
    font-size: 0.8em;
    color: #7f8c8d;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 4px;
    padding: 10px 0;
    display: none;
    min-width: 150px;
}

.user-profile:hover .dropdown-menu {
    display: block;
}

.dropdown-menu a {
    display: flex;
    align-items: center;
    padding: 8px 15px;
    color: #2c3e50;
    text-decoration: none;
    transition: background-color 0.3s;
}

.dropdown-menu a:hover {
    background-color: #f5f6fa;
}

.dropdown-menu i {
    margin-right: 10px;
    width: 15px;
}
</style>

<script>
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.topbar').classList.toggle('expanded');
    document.querySelector('.main-content').classList.toggle('expanded');
});
</script> 