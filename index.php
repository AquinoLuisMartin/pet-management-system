<?php
include "includes/db_conn.php";

// Count data for dashboard stats
$pet_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pet"))['count'];
$owner_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM owner"))['count'];
$vet_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM veterinarian"))['count'];
$today = date('Y-m-d');
$appointment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointment WHERE Date >= '$today'"))['count'];

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
        <a href="services.php" class="btn-outline-white">Learn More</a>
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
            <i class="fas fa-stethoscope"></i>
        </div>
        <h3>Services</h3>
        <p class="text-muted">Medical treatments</p>
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

<!-- Recent Activity Section -->
<div class="section-header">
    <h2>Recent Activities</h2>
</div>

<table class="table table-hover bg-white rounded shadow-sm">
    <thead class="bg-light">
        <tr>
            <th>ID</th>
            <th>Pet Name</th>
            <th>Owner</th>
            <th>Date</th>
            <th>Service</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT a.Date, a.Status, a.Reason, 
                p.Name as PetName, p.PetID,
                CONCAT(o.FirstName, ' ', o.LastName) as OwnerName
                FROM appointment a
                JOIN pet p ON a.PetID = p.PetID
                JOIN owner o ON p.OwnerID = o.OwnerID
                ORDER BY a.Date DESC LIMIT 5";
        
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $status_class = '';
                switch ($row['Status']) {
                    case 'Completed':
                        $status_class = 'text-success';
                        break;
                    case 'Scheduled':
                        $status_class = 'text-primary';
                        break;
                    case 'Cancelled':
                        $status_class = 'text-danger';
                        break;
                }
                
                echo "<tr>
                    <td>" . $row['PetID'] . "</td>
                    <td>" . $row['PetName'] . "</td>
                    <td>" . $row['OwnerName'] . "</td>
                    <td>" . $row['Date'] . "</td>
                    <td>" . $row['Reason'] . "</td>
                    <td class='" . $status_class . "'>" . $row['Status'] . "</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No recent appointments found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php include "includes/footer.php"; ?>