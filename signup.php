<?php
session_start();
include "includes/db_conn.php";


if(isset($_SESSION['owner_id'])) {
    header("Location: index.php");
    exit();
}


if(isset($_POST['signup'])) {
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    
    if($password != $confirmPassword) {
        $error = "Passwords do not match. Please try again.";
    } else {
        
        $stmt = $conn->prepare("CALL CheckEmailExists(?, @email_exists)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->execute();
        $stmt->close();
        
        
        $select_result = $conn->query("SELECT @email_exists AS email_exists");
        $result = $select_result;
        $row = $result->fetch_assoc();

        if ($row['email_exists']) {
            
            $error = "Email already registered. Please use a different email.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            
            $stmt = $conn->prepare("CALL RegisterOwner(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $firstName, $lastName, $phone, $email, $address, $hashed_password);
            $stmt->execute();
            $result = $stmt->get_result();
            $owner_data = mysqli_fetch_assoc($result);

            if ($owner_data) {
                
                $owner_id = $owner_data['OwnerID'];
                $_SESSION['owner_id'] = $owner_id;
                $_SESSION['owner_name'] = $firstName . ' ' . $lastName;
                $_SESSION['owner_email'] = $email;
                
                header("Location: index.php?welcome=1");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Management System - Sign Up</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .signup-container {
            max-width: 700px;
            margin: 50px auto;
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
        <div class="signup-container">
            <div class="logo-area">
                <i class="fas fa-paw fa-4x text-primary"></i>
                <h2 class="mt-3">Pet Management System</h2>
                <p class="text-muted">Create a new account</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="" method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required value="<?php echo isset($_POST['firstName']) ? $_POST['firstName'] : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required value="<?php echo isset($_POST['lastName']) ? $_POST['lastName'] : ''; ?>">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        
                    </div>
                    <div class="col-md-6">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" name="signup" class="btn btn-primary">Create Account</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>