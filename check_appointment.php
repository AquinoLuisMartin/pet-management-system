<?php
include "includes/db_conn.php";
include "includes/header.php";

$search_performed = false;
$appointment = null;

// Check if search was performed
if (isset($_POST['search'])) {
    $search_performed = true;
    $search_type = $_POST['search_type'];
    $search_value = mysqli_real_escape_string($conn, $_POST['search_value']);
    
    // Use stored procedures based on search type
    if ($search_type == 'appointment_id') {
        $stmt = $conn->prepare("CALL GetAppointmentById(?)");
        $stmt->bind_param("s", $search_value);
    } else if ($search_type == 'pet_name') {
        $stmt = $conn->prepare("CALL GetAppointmentsByPetName(?)");
        $stmt->bind_param("s", $search_value);
    } else if ($search_type == 'owner_name') {
        $stmt = $conn->prepare("CALL GetAppointmentsByOwnerName(?)");
        $stmt->bind_param("s", $search_value);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-search"></i> Check Appointment</h1>
            <p class="text-muted">Search and verify appointment details</p>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="post" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search_type" class="form-label">Search by</label>
                        <select class="form-control" id="search_type" name="search_type" required>
                            <option value="appointment_id">Appointment ID</option>
                            <option value="pet_name">Pet Name</option>
                            <option value="owner_name">Owner Name</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search_value" class="form-label">Search Value</label>
                        <input type="text" class="form-control" id="search_value" name="search_value" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="search" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <?php if ($search_performed): ?>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Search Results</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pet</th>
                                    <th>Owner</th>
                                    <th>Contact</th>
                                    <th>Date & Time</th>
                                    <th>Veterinarian</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): 
                                    $status_class = '';
                                    switch ($row['Status']) {
                                        case 'Completed':
                                            $status_class = 'badge bg-success';
                                            break;
                                        case 'Scheduled':
                                            $status_class = 'badge bg-primary';
                                            break;
                                        case 'Cancelled':
                                            $status_class = 'badge bg-danger';
                                            break;
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $row['AppointmentID']; ?></td>
                                        <td><?php echo $row['PetName']; ?><br>
                                            <small class="text-muted"><?php echo $row['Species'] . ' - ' . $row['Breed']; ?></small>
                                        </td>
                                        <td><?php echo $row['OwnerName']; ?></td>
                                        <td>
                                            <small>
                                                <i class="fas fa-envelope"></i> <?php echo $row['Email']; ?><br>
                                                <i class="fas fa-phone"></i> <?php echo $row['Phone']; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($row['Date'])); ?><br>
                                            <small class="text-muted"><?php echo date('h:i A', strtotime($row['Time'])); ?></small>
                                        </td>
                                        <td><?php echo $row['VetName']; ?></td>
                                        <td><?php echo $row['Reason']; ?></td>
                                        <td><span class="<?php echo $status_class; ?>"><?php echo $row['Status']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No appointments found matching your search criteria.
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center p-5">
            <i class="fas fa-calendar-check fa-4x text-muted mb-3"></i>
            <h4>Enter search criteria to check appointment details</h4>
            <p class="text-muted">You can search by appointment ID, pet name, or owner name</p>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>