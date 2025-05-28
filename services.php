<?php
include "includes/db_conn.php";
include "includes/header.php";

// Get services from database
$sql = "SELECT s.ServiceID, s.Name, s.Description, s.Cost, s.Duration, 
        COUNT(DISTINCT a.AppointmentID) as TimesBooked
        FROM services s
        LEFT JOIN appointment_service a_s ON s.ServiceID = a_s.ServiceID
        LEFT JOIN appointment a ON a_s.AppointmentID = a.AppointmentID
        GROUP BY s.ServiceID
        ORDER BY s.Name ASC";

$result = mysqli_query($conn, $sql);
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-stethoscope"></i> Services</h1>
            <p class="text-muted">Manage clinic services and treatments</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">
                <i class="fas fa-plus"></i> New Service
            </button>
        </div>
    </div>

    <!-- Services Cards -->
    <div class="row">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">' . $row['Name'] . '</h5>
                            <h6 class="card-subtitle mb-2 text-muted">$' . number_format($row['Cost'], 2) . ' - ' . $row['Duration'] . ' min</h6>
                            <p class="card-text">' . $row['Description'] . '</p>
                            <p class="text-muted small">Booked ' . $row['TimesBooked'] . ' times</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="' . $row['ServiceID'] . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $row['ServiceID'] . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info">No services found. Add a service to get started.</div></div>';
        }
        ?>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="serviceForm" action="service_process.php" method="post">
                    <div class="form-group">
                        <label for="serviceName">Service Name</label>
                        <input type="text" class="form-control" id="serviceName" name="serviceName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="serviceDescription">Description</label>
                        <textarea class="form-control" id="serviceDescription" name="serviceDescription" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="serviceCost">Cost ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="serviceCost" name="serviceCost" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="serviceDuration">Duration (minutes)</label>
                        <input type="number" min="1" class="form-control" id="serviceDuration" name="serviceDuration" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="serviceForm" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Edit service button click
    $(".edit-btn").on("click", function() {
        var serviceId = $(this).data("id");
        // You can implement AJAX to get service details and populate the form
        // For now, just show the modal
        $("#addServiceModalLabel").text("Edit Service");
        $("#addServiceModal").modal("show");
    });
    
    // Delete service button click
    $(".delete-btn").on("click", function() {
        var serviceId = $(this).data("id");
        if(confirm("Are you sure you want to delete this service?")) {
            // You can implement AJAX to delete the service
            // Example: window.location.href = "service_process.php?action=delete&id=" + serviceId;
        }
    });
});
</script>

<?php include "includes/footer.php"; ?>