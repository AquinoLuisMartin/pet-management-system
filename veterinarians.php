<?php
include "includes/db_conn.php";
include "includes/header.php";

// Get all veterinarians - Using Stored Procedure
$sql = "CALL GetAllVeterinariansWithStats()";

$result = mysqli_query($conn, $sql) or die("Query failed: " . mysqli_error($conn));
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-user-md"></i> Veterinarians</h1>
            <p class="text-muted">Manage veterinary staff information</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addVetModal">
                <i class="fas fa-plus"></i> Add Veterinarian
            </button>
        </div>
    </div>

    <!-- Vets Grid -->
    <div class="row">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle mr-3">
                                    <span class="initials">' . substr($row['FirstName'], 0, 1) . substr($row['LastName'], 0, 1) . '</span>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Dr. ' . $row['FirstName'] . ' ' . $row['LastName'] . '</h5>
                                    <p class="text-muted mb-0">' . $row['Specialization'] . '</p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block">Email</small>
                                <a href="mailto:' . $row['Email'] . '" class="text-decoration-none">' . $row['Email'] . '</a>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block">Phone</small>
                                <a href="tel:' . $row['Phone'] . '" class="text-decoration-none">' . $row['Phone'] . '</a>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <h3 class="mb-0">' . $row['AppointmentCount'] . '</h3>
                                        <small class="text-muted">Appointments</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <h3 class="mb-0">' . $row['PetCount'] . '</h3>
                                        <small class="text-muted">Patients</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="vet_schedule.php?id=' . $row['VetID'] . '" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-calendar"></i> Schedule
                            </a>
                            <div>
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-id="' . $row['VetID'] . '">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $row['VetID'] . '">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info">No veterinarians found. Add a veterinarian to get started.</div></div>';
        }
        ?>
    </div>
</div>

<!-- Add Veterinarian Modal -->
<div class="modal fade" id="addVetModal" tabindex="-1" role="dialog" aria-labelledby="addVetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVetModalLabel">Add New Veterinarian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="vetForm" action="vet_process.php" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="specialization">Specialization</label>
                                <input type="text" class="form-control" id="specialization" name="specialization">
                            </div>
                            
                            <div class="form-group">
                                <label for="license">License Number</label>
                                <input type="text" class="form-control" id="license" name="license">
                            </div>
                            
                            <div class="form-group">
                                <label for="education">Education</label>
                                <input type="text" class="form-control" id="education" name="education">
                            </div>
                            
                            <div class="form-group">
                                <label for="hireDate">Hire Date</label>
                                <input type="date" class="form-control" id="hireDate" name="hireDate">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="vetForm" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Edit veterinarian
    $(".edit-btn").on("click", function() {
        var vetId = $(this).data("id");
        // Implement AJAX to get vet details
        $("#addVetModalLabel").text("Edit Veterinarian");
        $("#addVetModal").modal("show");
    });
    
    // Delete veterinarian
    $(".delete-btn").on("click", function() {
        var vetId = $(this).data("id");
        if(confirm("Are you sure you want to delete this veterinarian record?")) {
            // Implement deletion
        }
    });
});
</script>

<?php include "includes/footer.php"; ?>

