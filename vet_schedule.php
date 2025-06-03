<?php
include "includes/db_conn.php";
include "includes/header.php";
session_start();

// Get the vet ID from URL
$vet_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$vet_id) {
    // Redirect if no valid vet ID
    header("Location: veterinarians.php");
    exit;
}

// Fetch veterinarian information using stored procedure
$stmt = $conn->prepare("CALL GetVeterinarianById(?)");
$stmt->bind_param("i", $vet_id);
$stmt->execute();
$vet_result = $stmt->get_result();

if ($vet_result->num_rows === 0) {
    // Redirect if vet not found
    header("Location: veterinarians.php");
    exit;
}

$vet = $vet_result->fetch_assoc();
$stmt->close();
$conn->next_result(); // Clear the result set

// Get today's date for default filter
$today = date('Y-m-d');

// Get filtered date (default to today)
$filter_date = isset($_GET['date']) ? $_GET['date'] : $today;

// Get all appointments for this vet
$stmt = $conn->prepare("CALL GetAllAppointments()");
$stmt->execute();
$all_appointments = $stmt->get_result();
$stmt->close();
$conn->next_result();

// Filter appointments for this specific vet
$appointments = array();
while ($row = $all_appointments->fetch_assoc()) {
    if ($row['VetID'] == $vet_id) {
        $appointments[] = $row;
    }
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1>
                <i class="fas fa-calendar-alt"></i> 
                Schedule for Dr. <?= htmlspecialchars($vet['FirstName'] . ' ' . $vet['LastName']) ?>
            </h1>
            <p class="text-muted"><?= htmlspecialchars($vet['Specialization']) ?></p>
        </div>
        <div class="col-auto">
            <a href="veterinarians.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Veterinarians
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-center">
                <input type="hidden" name="id" value="<?= $vet_id ?>">
                <div class="col-auto">
                    <label for="date" class="form-label">Filter by Date:</label>
                </div>
                <div class="col-auto">
                    <input type="date" id="date" name="date" class="form-control" value="<?= $filter_date ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
                <div class="col-auto">
                    <a href="vet_schedule.php?id=<?= $vet_id ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Appointments</h5>
            <span class="badge bg-primary rounded-pill"><?= count($appointments) ?> Total</span>
        </div>
        <div class="card-body">
            <?php if (count($appointments) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Pet</th>
                                <th>Owner</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr class="<?= ($appointment['Date'] == $filter_date) ? 'table-active' : '' ?>">
                                    <td>
                                        <?= date('M d, Y', strtotime($appointment['Date'])) ?><br>
                                        <span class="text-muted"><?= date('h:i A', strtotime($appointment['Time'])) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($appointment['PetName']) ?></td>
                                    <td><?= htmlspecialchars($appointment['OwnerName']) ?></td>
                                    <td><?= htmlspecialchars($appointment['Reason']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusBadgeClass($appointment['Status']) ?>">
                                            <?= htmlspecialchars($appointment['Status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary update-status" 
                                                data-pet-id="<?= $appointment['PetID'] ?>" 
                                                data-vet-id="<?= $appointment['VetID'] ?>" 
                                                data-date="<?= $appointment['Date'] ?>" 
                                                data-time="<?= $appointment['Time'] ?>"
                                                data-status="<?= $appointment['Status'] ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#statusModal">
                                                <i class="fas fa-exchange-alt"></i> Status
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No appointments found for this veterinarian.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Appointment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm" action="appointment_process.php" method="post">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="petID" id="status_petID">
                    <input type="hidden" name="vetID" id="status_vetID">
                    <input type="hidden" name="date" id="status_date">
                    <input type="hidden" name="time" id="status_time">
                    
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
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
                <button type="submit" form="statusForm" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status update button handler
    document.querySelectorAll('.update-status').forEach(button => {
        button.addEventListener('click', function() {
            const petId = this.getAttribute('data-pet-id');
            const vetId = this.getAttribute('data-vet-id');
            const date = this.getAttribute('data-date');
            const time = this.getAttribute('data-time');
            const currentStatus = this.getAttribute('data-status');
            
            document.getElementById('status_petID').value = petId;
            document.getElementById('status_vetID').value = vetId;
            document.getElementById('status_date').value = date;
            document.getElementById('status_time').value = time;
            document.getElementById('status').value = currentStatus;
        });
    });
});

// Function to determine badge color based on status
function getStatusBadgeClass(status) {
    switch(status) {
        case 'Scheduled':
            return 'primary';
        case 'Completed':
            return 'success';
        case 'Cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
</script>

<?php
// Helper function to get badge class
function getStatusBadgeClass($status) {
    switch($status) {
        case 'Scheduled':
            return 'primary';
        case 'Completed':
            return 'success';
        case 'Cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

include "includes/footer.php"; 
?>