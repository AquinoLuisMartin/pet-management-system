<?php
include "includes/db_conn.php";
include "includes/header.php";

// Get appointment data using stored procedure
$stmt = $conn->prepare("CALL GetAllAppointments()");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Get all pets for dropdown using stored procedure
$conn->next_result(); // Clear previous result set
$stmt = $conn->prepare("CALL GetPetsWithOwners()");
$stmt->execute();
$pets_result = $stmt->get_result();
$stmt->close();

// Get vets for dropdown using stored procedure
$conn->next_result(); // Clear previous result set
$stmt = $conn->prepare("CALL GetOwnersForDropdown()");
$stmt->execute();
$vets_result = $stmt->get_result();
$stmt->close();

// Get owners for dropdown using stored procedure
$conn->next_result(); // Clear previous result set
$stmt = $conn->prepare("CALL GetOwnersForDropdown()");
$stmt->execute();
$owners_result = $stmt->get_result();
$stmt->close();

// Get current logged in owner ID if available
$owner_id = $_SESSION['owner_id'] ?? null;
$current_owner_name = '';

if ($owner_id) {
    $conn->next_result(); // Clear previous result set
    $stmt = $conn->prepare("CALL GetOwnerById(?)");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $owner_result = $stmt->get_result();
    if ($owner_data = mysqli_fetch_assoc($owner_result)) {
        $current_owner_name = $owner_data['FirstName'] . ' ' . $owner_data['LastName'];
    }
    $stmt->close();
    
    // Get pets for this owner only
    $conn->next_result(); // Clear previous result set
    $stmt = $conn->prepare("CALL GetPetsByOwnerID(?)");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $owner_pets_result = $stmt->get_result();
    $stmt->close();
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-calendar-alt"></i> Appointments</h1>
            <p class="text-muted">Manage pet appointments and schedules</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal" 
                <?php if($current_owner_name): ?>
                    data-owner="<?php echo htmlspecialchars($current_owner_name); ?>"
                <?php endif; ?>>
                <i class="fas fa-plus"></i> New Appointment
            </button>
        </div>
    </div>

    <!-- Display success/error messages -->
    <?php
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . $_GET['success'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . $_GET['error'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
    ?>

    <!-- Filter/Search Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-4">
                    <label for="dateFilter" class="form-label">Date</label>
                    <input type="date" class="form-control" id="dateFilter">
                </div>
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-control" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchFilter" class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchFilter" placeholder="Search pet or owner...">
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pet</th>
                            <th>Owner</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Veterinarian</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
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
                                
                                echo "<tr>
                                    <td>" . $row['AppointmentID'] . "</td>
                                    <td>" . $row['PetName'] . "</td>
                                    <td>" . $row['OwnerName'] . "</td>
                                    <td>" . $row['Date'] . "</td>
                                    <td>" . $row['Time'] . "</td>
                                    <td>" . $row['VetName'] . "</td>
                                    <td>" . $row['Reason'] . "</td>
                                    <td><span class='" . $status_class . "'>" . $row['Status'] . "</span></td>
                                    <td>
                                        <button class='btn btn-sm btn-outline-primary edit-btn' data-id='" . $row['AppointmentID'] . "'><i class='fas fa-edit'></i></button>
                                        <button class='btn btn-sm btn-outline-danger delete-btn' data-id='" . $row['AppointmentID'] . "'><i class='fas fa-trash'></i></button>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center'>No appointments found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Appointment Modal -->
<div class="modal fade" id="addAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAppointmentModalLabel">Add New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm" action="appointment_process.php" method="post">
                    <input type="hidden" name="action" value="create">
                    <?php if ($owner_id): ?>
                        <!-- If owner is logged in, only show their pets -->
                        <div class="form-group mb-3">
                            <label for="petID">Select Your Pet</label>
                            <select class="form-control" id="petID" name="petID" required>
                                <option value="">Select a pet</option>
                                <?php 
                                while ($pet = mysqli_fetch_assoc($owner_pets_result)):
                                ?>
                                <option value="<?= $pet['PetID'] ?>"><?= htmlspecialchars($pet['PetName']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Hidden field for owner -->
                        <input type="hidden" name="ownerID" value="<?= $owner_id ?>">
                        
                        <!-- Display owner name as non-editable -->
                        <div class="form-group mb-3">
                            <label>Owner</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($current_owner_name) ?>" readonly>
                        </div>
                    <?php else: ?>
                        <!-- If not logged in as owner, show selection form -->
                        <div class="form-group mb-3">
                            <label for="petID">Select Pet</label>
                            <select class="form-control" id="petID" name="petID" required>
                                <option value="">Select a pet</option>
                                <?php 
                                while ($pet = mysqli_fetch_assoc($pets_result)):
                                ?>
                                <option value="<?= $pet['PetID'] ?>" data-owner="<?= htmlspecialchars($pet['OwnerName']) ?>">
                                    <?= htmlspecialchars($pet['PetName']) ?> (<?= htmlspecialchars($pet['OwnerName']) ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <!-- Display calculated owner name -->
                        <div class="form-group mb-3">
                            <label>Owner Name</label>
                            <input type="text" class="form-control" id="ownerDisplay" readonly>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Veterinarian Selection -->
                    <div class="form-group mb-3">
                        <label for="vetID">Veterinarian</label>
                        <select class="form-control" id="vetID" name="vetID" required>
                            <option value="">Select a veterinarian</option>
                            <?php 
                            while ($vet = mysqli_fetch_assoc($vets_result)):
                            ?>
                            <option value="<?= $vet['VetID'] ?>">
                                Dr. <?= htmlspecialchars($vet['VetName']) ?> (<?= htmlspecialchars($vet['Specialization']) ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <!-- Date and Time -->
                    <div class="form-group mb-3">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="time">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="reason">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="notes">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="appointmentForm" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate owner name when pet is selected
    const petSelect = document.getElementById('petID');
    if (petSelect) {
        petSelect.addEventListener('change', function() {
            const ownerDisplay = document.getElementById('ownerDisplay');
            if (ownerDisplay) {
                const selectedOption = this.options[this.selectedIndex];
                const ownerName = selectedOption.getAttribute('data-owner');
                if (ownerName) {
                    ownerDisplay.value = ownerName;
                } else {
                    ownerDisplay.value = '';
                }
            }
        });
    }
    
    // Search and filter functionality
    $("#searchFilter").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Status filter
    $("#statusFilter").on("change", function() {
        var value = $(this).val().toLowerCase();
        if (value === "") {
            $("table tbody tr").show();
        } else {
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).find("td:nth-child(8)").text().toLowerCase().indexOf(value) > -1)
            });
        }
    });
    
    // Date filter
    $("#dateFilter").on("change", function() {
        var value = $(this).val();
        if (value === "") {
            $("table tbody tr").show();
        } else {
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).find("td:nth-child(4)").text() === value)
            });
        }
    });
});
</script>

<?php include "includes/footer.php"; ?>