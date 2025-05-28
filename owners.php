<?php
include "includes/db_conn.php";
include "includes/header.php";

// Get owners with pet counts
$sql = "SELECT o.*, COUNT(p.PetID) as PetCount 
        FROM owner o 
        LEFT JOIN pet p ON o.OwnerID = p.OwnerID 
        GROUP BY o.OwnerID
        ORDER BY o.LastName, o.FirstName";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-user"></i> Pet Owners</h1>
            <p class="text-muted">Manage owner information and records</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addOwnerModal">
                <i class="fas fa-plus"></i> Add Owner
            </button>
        </div>
    </div>

    <!-- Search/Filter Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-9">
                    <input type="text" id="ownerSearch" class="form-control" placeholder="Search owners by name, email, or phone...">
                </div>
                <div class="col-md-3">
                    <select id="sortOrder" class="form-control">
                        <option value="name">Sort by Name</option>
                        <option value="pets">Sort by Pet Count</option>
                        <option value="recent">Sort by Recently Added</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Owners Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Pets</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . $row['OwnerID'] . "</td>
                                    <td>" . $row['FirstName'] . " " . $row['LastName'] . "</td>
                                    <td>" . $row['Email'] . "</td>
                                    <td>" . $row['Phone'] . "</td>
                                    <td>" . $row['Address'] . "</td>
                                    <td><span class='badge bg-primary'>" . $row['PetCount'] . "</span></td>
                                    <td>
                                        <button class='btn btn-sm btn-outline-info view-btn' data-id='" . $row['OwnerID'] . "'><i class='fas fa-eye'></i></button>
                                        <button class='btn btn-sm btn-outline-primary edit-btn' data-id='" . $row['OwnerID'] . "'><i class='fas fa-edit'></i></button>
                                        <button class='btn btn-sm btn-outline-danger delete-btn' data-id='" . $row['OwnerID'] . "'><i class='fas fa-trash'></i></button>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No owners found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Owner Modal -->
<div class="modal fade" id="addOwnerModal" tabindex="-1" role="dialog" aria-labelledby="addOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOwnerModalLabel">Add New Owner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ownerForm" action="owner_process.php" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="ownerForm" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Search functionality
    $("#ownerSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // View owner
    $(".view-btn").on("click", function() {
        var ownerId = $(this).data("id");
        window.location.href = "view_owner.php?id=" + ownerId;
    });
    
    // Edit owner
    $(".edit-btn").on("click", function() {
        var ownerId = $(this).data("id");
        // Here you would normally load the owner data via AJAX
        $("#addOwnerModalLabel").text("Edit Owner");
        $("#addOwnerModal").modal("show");
    });
    
    // Delete owner
    $(".delete-btn").on("click", function() {
        var ownerId = $(this).data("id");
        if(confirm("Are you sure you want to delete this owner? This will also delete all associated pets.")) {
            // You could redirect to a delete script
            // window.location.href = "owner_process.php?action=delete&id=" + ownerId;
        }
    });
    
    // Sort functionality would go here
    $("#sortOrder").on("change", function() {
        // This would ideally reload the data with a new sort order
        // For now, we'll just show an alert
        alert("Sort order changed to: " + $(this).val());
    });
});
</script>

<?php include "includes/footer.php"; ?>