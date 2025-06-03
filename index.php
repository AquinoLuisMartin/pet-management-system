<?php
session_start(); 
include "includes/db_conn.php";


try {
    $stmt = $conn->prepare("CALL GetDashboardStats()");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $stats = mysqli_fetch_assoc($result)) {
        $pet_count = $stats['pet_count'];
        $owner_count = $stats['owner_count'];
        $vet_count = $stats['vet_count'];
        $appointment_count = $stats['appointment_count'];
    } else {
        
        $pet_count = 0;
        $owner_count = 0;
        $vet_count = 0;
        $appointment_count = 0;
    }
} catch (Exception $e) {
    
    $pet_count = 0;
    $owner_count = 0;
    $vet_count = 0;
    $appointment_count = 0;
    
    error_log("Dashboard stats error: " . $e->getMessage());
}


include "includes/header.php";


$owner_pets = array();
if(isset($_SESSION['owner_id'])) {
    $owner_id = $_SESSION['owner_id'];
    
    if (isset($result) && $result) {
        $result->close();
    }
    
    
    try {
        $stmt = $conn->prepare("CALL GetOwnerPets(?)");
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $pets_result = $stmt->get_result();
        
        if ($pets_result) {
            while($pet = mysqli_fetch_assoc($pets_result)) {
                $owner_pets[] = $pet;
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error fetching owner pets: " . $e->getMessage());
    }
}
?>


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