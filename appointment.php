<?php
include "includes/db_conn.php";
include "includes/header.php";


$stmt = $conn->prepare("CALL GetAllAppointments()");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


$conn->next_result(); 
$stmt = $conn->prepare("CALL GetPetsWithOwners()");
$stmt->execute();
$pets_result = $stmt->get_result();
$stmt->close();


$conn->next_result(); 
$stmt = $conn->prepare("CALL GetAllVetsForDropdown()");
$stmt->execute();
$vets_result = $stmt->get_result();
$stmt->close();


$conn->next_result(); 
$stmt = $conn->prepare("CALL GetOwnersForDropdown()");
$stmt->execute();
$owners_result = $stmt->get_result();
$stmt->close();


$owner_id = $_SESSION['owner_id'] ?? null;
$current_owner_name = '';

if ($owner_id) {
    $conn->next_result(); 
    $stmt = $conn->prepare("CALL GetOwnerById(?)");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $owner_result = $stmt->get_result();
    if ($owner_data = mysqli_fetch_assoc($owner_result)) {
        $current_owner_name = $owner_data['FirstName'] . ' ' . $owner_data['LastName'];
    }
    $stmt->close();
    
    
    $conn->next_result(); 
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
                                        <form action='delete_appointment.php' method='post' style='display:inline;'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='appointment_id' value='" . $row['AppointmentID'] . "'>
                                            <button type='submit' class='btn btn-danger btn-sm'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </form>
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
                        
                        <div class="form-group mb-3">
                            <label for="petID" class="form-label">Select Your Pet</label>
                            <select class="form-control" id="petID" name="petID" required>
                                <?php 
                                while ($pet = mysqli_fetch_assoc($owner_pets_result)):
                                ?>
                                <option value="<?= $pet['PetID'] ?>"><?= htmlspecialchars($pet['PetName']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        
                        <input type="hidden" name="ownerID" value="<?= $owner_id ?>">
                        
                        
                        <div class="form-group mb-3">
                            <label for="ownerDisplay" class="form-label">Owner</label>
                            <input type="text" class="form-control" id="ownerDisplay" value="<?= htmlspecialchars($current_owner_name) ?>" readonly>
                        </div>
                    <?php else: ?>
                        
                        <div class="form-group mb-3">
                            
                            <select class="form-control" id="petID" name="petID" required>
                                <option value="">-- Select a pet --</option>
                                <?php 
                                while ($pet = mysqli_fetch_assoc($pets_result)):
                                ?>
                                <option value="<?= $pet['PetID'] ?>" data-owner="<?= htmlspecialchars($pet['OwnerName']) ?>">
                                    <?= htmlspecialchars($pet['PetName']) ?> (<?= htmlspecialchars($pet['OwnerName']) ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
        
                            <input type="text" class="form-control" id="ownerDisplay" readonly>
                        </div>
                    <?php endif; ?>
                    
                    
                    <div class="form-group mb-3">
                        
                        <select class="form-control" id="vetID" name="vetID" required>
                            <option value="">-- Select a veterinarian --</option>
                            <?php 
                            mysqli_data_seek($vets_result, 0);
                            while ($vet = mysqli_fetch_assoc($vets_result)):
                            ?>
                            <option value="<?= $vet['VetID'] ?>">
                                Dr. <?= htmlspecialchars($vet['VetName']) ?> (<?= htmlspecialchars($vet['Specialization']) ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    
                    <div class="form-group mb-3">
                        
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        
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


<div class="modal fade" id="editAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="editAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAppointmentModalLabel">Edit Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAppointmentForm" action="appointment_process.php" method="post">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="appointmentID" id="edit_appointmentID">
                    
                    
                    <div class="form-group mb-3">
                        <label for="edit_petID">Pet</label>
                        <select class="form-control" id="edit_petID" name="petID" required>
                            <option value="">Select a pet</option>
                            <?php 
                            
                            mysqli_data_seek($pets_result, 0);
                            while ($pet = mysqli_fetch_assoc($pets_result)):
                            ?>
                            <option value="<?= $pet['PetID'] ?>" data-owner="<?= htmlspecialchars($pet['OwnerName']) ?>">
                                <?= htmlspecialchars($pet['PetName']) ?> (<?= htmlspecialchars($pet['OwnerName']) ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    
                    <div class="form-group mb-3">
                        <label>Owner Name</label>
                        <input type="text" class="form-control" id="edit_ownerDisplay" readonly>
                    </div>
                    
                    
                    <div class="form-group mb-3">
                        <label for="edit_vetID">Veterinarian</label>
                        <select class="form-control" id="edit_vetID" name="vetID" required>
                            <option value="">Select a veterinarian</option>
                            <?php 
                            
                            mysqli_data_seek($vets_result, 0);
                            while ($vet = mysqli_fetch_assoc($vets_result)):
                            ?>
                            <option value="<?= $vet['VetID'] ?>">
                                Dr. <?= htmlspecialchars($vet['VetName']) ?> (<?= htmlspecialchars($vet['Specialization']) ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    
                    <div class="form-group mb-3">
                        <label for="edit_date">Date</label>
                        <input type="date" class="form-control" id="edit_date" name="date" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_time">Time</label>
                        <input type="time" class="form-control" id="edit_time" name="time" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_reason">Reason</label>
                        <textarea class="form-control" id="edit_reason" name="reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_notes">Additional Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_status">Status</label>
                        <select class="form-control" id="edit_status" name="status">
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="editAppointmentForm" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteAppointmentModal" tabindex="-1" aria-labelledby="deleteAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAppointmentModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this appointment?</p>
                <p><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteAppointmentForm" action="appointment_process.php" method="post">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="appointmentID" id="delete_appointmentID">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
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
    
    
    $("#searchFilter").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    
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

<script>
$(document).ready(function() {
    $(".delete-btn").on("click", function() {
        var appointmentId = $(this).data("id");
        $("#delete_appointmentID").val(appointmentId);
        $("#deleteAppointmentModal").modal("show");
    });
});
</script>

<?php include "includes/footer.php"; ?>