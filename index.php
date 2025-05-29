<?php
include "includes/db_conn.php";

// Count data for dashboard stats using stored procedure
$stmt = $conn->prepare("CALL GetDashboardStats()");
$stmt->execute();
$result = $stmt->get_result();
$stats = mysqli_fetch_assoc($result);

$pet_count = $stats['pet_count'];
$owner_count = $stats['owner_count'];
$vet_count = $stats['vet_count'];
$today = date('Y-m-d');
$appointment_count = $stats['appointment_count'];

// Include header
include "includes/header.php";

// If owner is logged in, get their pets
$owner_pets = array();
if(isset($_SESSION['owner_id'])) {
    $owner_id = $_SESSION['owner_id'];
    $pets_query = mysqli_query($conn, "SELECT * FROM pet WHERE OwnerID = $owner_id");
    while($pet = mysqli_fetch_assoc($pets_query)) {
        $owner_pets[] = $pet;
    }
}
?>

<!-- Welcome message for new signups -->
<?php if(isset($_GET['welcome'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Welcome to PetCare!</strong> Your account has been created successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Hero Section -->
<div class="hero-section">
    <h1>Pet Services and Veterinary<br>Clinic</h1>
    <?php if(isset($_SESSION['owner_id'])): ?>
    <p>Welcome back, <?php echo $_SESSION['owner_name']; ?>!</p>
    
    
    <?php endif; ?>
    <div class="hero-buttons">
        <a href="pets.php" class="btn-white">Get Started</a>
    </div>
</div>

<!-- Features Section -->
<div class="section-header">
    <h2>Features</h2>
    <a href="#" class="view-all">
        <i class="fas fa-th"></i>
        View All
    </a>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-hospital"></i>
        </div>
        <h3>Clinic</h3>
        <p class="text-muted">Manage clinic operations</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-user-md"></i>
        </div>
        <h3>Doctor</h3>
        <p class="text-muted">Veterinarian profiles</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-pills"></i>
        </div>
        <h3>Pharmacy</h3>
        <p class="text-muted">Medication inventory</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="section-header">
    <h2>Clinic Statistics</h2>
</div>

<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-paw"></i>
        </div>
        <h3><?php echo $pet_count; ?></h3>
        <p class="text-muted">Total Pets</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-user"></i>
        </div>
        <h3><?php echo $owner_count; ?></h3>
        <p class="text-muted">Pet Owners</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <h3><?php echo $appointment_count; ?></h3>
        <p class="text-muted">Upcoming Appointments</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-user-md"></i>
        </div>
        <h3><?php echo $vet_count; ?></h3>
        <p class="text-muted">Veterinarians</p>
    </div>
</div>




<?php include "includes/footer.php"; ?>