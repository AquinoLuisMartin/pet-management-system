<?php
include "includes/db_conn.php";
include "includes/header.php";

// Check if user is logged in (if you have authentication)
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Get appointment data from database
$sql = "SELECT CONCAT(a.PetID, '-', a.VetID, '-', a.Date, '-', a.Time) AS AppointmentID, 
        a.PetID, a.VetID, a.Date, a.Time, a.Status, a.Reason, 
        p.Name as PetName, 
        CONCAT(o.FirstName, ' ', o.LastName) as OwnerName,
        CONCAT(v.FirstName, ' ', v.LastName) as VetName
        FROM appointment a
        JOIN pet p ON a.PetID = p.PetID
        JOIN owner o ON p.OwnerID = o.OwnerID
        JOIN veterinarian v ON a.VetID = v.VetID
        ORDER BY a.Date DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-calendar-alt"></i> Appointments</h1>
            <p class="text-muted">Manage pet appointments and schedules</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                <i class="fas fa-plus"></i> New Appointment
            </button>
        </div>
    </div>

    <!-- Display success/error messages --
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
                    <!-- Pet information as text inputs -->
                    <div class="form-group">
                        <label for="petName">Pet Name</label>
                        <input type="text" class="form-control" id="petName" name="petName" placeholder="Enter pet name" required>
                    </div>

                    <div class="form-group">
                        <label for="ownerName">Owner Name</label>
                        <input type="text" class="form-control" id="ownerName" name="ownerName" placeholder="Enter owner name" required>
                    </div>
                    
                    
                    
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
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
// Add your JavaScript for handling form submissions, filtering, etc.
$(document).ready(function() {
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