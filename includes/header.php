<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <title>ANICARE - Veterinary Care System</title>
</head>
<body>
    
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
    </div>
    
    
    <div class="main-content">
       
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

       