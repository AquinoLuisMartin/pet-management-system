<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <title>ANICARE - Veterinary Care System</title>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="logo-container">
                <i class="fas fa-hospital"></i>
            </div>
            <div class="brand-text">
                <h1>ANICARE</h1>
                <p>Veterinary Care System</p>
            </div>
        </div>
        
        <!-- Update these links to match your actual filenames -->
        <div class="sidebar-menu">
            <a href="index.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i>
                <span class="menu-text">Dashboard</span>
            </a>
            <a href="appointment.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'appointment.php' ? 'active' : ''; ?>">
                <i class="far fa-calendar"></i>
                <span class="menu-text">Appointment</span>
            </a>
            <a href="check_appointment.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'check_appointment.php' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-check"></i>
                <span class="menu-text">Check Appointment</span>
            </a>
            <a href="pets.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'pets.php' ? 'active' : ''; ?>">
                <i class="fas fa-paw"></i>
                <span class="menu-text">Pets</span>
            </a>
            <a href="owners.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'owners.php' ? 'active' : ''; ?>">
                <i class="fas fa-user"></i>
                <span class="menu-text">Owners</span>
            </a>
            <a href="veterinarians.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'veterinarians.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i>
                <span class="menu-text">Veterinarians</span>
            </a>
            <a href="payment.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i>
                <span class="menu-text">Payment</span>
            </a>
            <a href="settings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span class="menu-text">Settings</span>
            </a>

            <a href="logout.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-text">Logout</span>
            </a>
        </div>
        <div class="sidebar-menu">
            <?php if(isset($_SESSION['owner_id'])): ?>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-text">Logout</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="search-container">
                <input type="text" placeholder="Search Here">
                <i class="fas fa-search"></i>
            </div>
            <div class="notification-bell">
                <i class="far fa-bell"></i>
                <div class="notification-indicator"></div>
            </div>
        </div>

        <!-- Inside the user-dropdown div -->
        <div class="user-dropdown">
            <?php if(isset($_SESSION['owner_id'])): ?>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://via.placeholder.com/40" alt="User" class="user-avatar">
                    <span><?php echo $_SESSION['owner_name']; ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            <?php else: ?>
            <?php endif; ?>
        </div>