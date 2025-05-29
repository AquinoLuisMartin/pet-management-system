
<?php
session_start();
include "includes/db_conn.php";

// Check if user is already logged in
if(isset($_SESSION['owner_id'])) {
    header("Location: index.php");
    exit();
}

// Process login form
if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Check if user exists
    $sql = "SELECT * FROM owner WHERE Email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Verify password
        if(password_verify($password, $row['Password'])) {
            // Set session variables
            $_SESSION['owner_id'] = $row['OwnerID'];
            $_SESSION['owner_name'] = $row['FirstName'] . ' ' . $row['LastName'];
            $_SESSION['owner_email'] = $row['Email'];
            
            // Redirect to dashboard
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "No account found with that email. Please sign up.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Management System - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-area img {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo-area">
                <i class="fas fa-paw fa-4x text-primary"></i>
                <h2 class="mt-3">Pet Management System</h2>
                <p class="text-muted">Login to your account</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>