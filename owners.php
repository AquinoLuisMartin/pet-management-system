<?php
include "includes/db_conn.php";
include "includes/header.php";
session_start();


if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['success_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            ' . $_SESSION['error_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['error_message']);
}


$stmt = $conn->prepare("CALL GetAllOwnersWithPetCount()");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-user"></i> Pet Owners</h1>
            <p class="text-muted">Manage owner information and records</p>
        </div>
    </div>

     
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
                                        <a href='owner_view.php?id=" . $row['OwnerID'] . "' class='btn btn-sm btn-outline-info'><i class='fas fa-eye'></i></a>
                                        <a href='owner_update.php?id=" . $row['OwnerID'] . "' class='btn btn-sm btn-outline-primary'><i class='fas fa-edit'></i></a>
                                        <a href='owner_delete.php?id=" . $row['OwnerID'] . "' class='btn btn-sm btn-outline-danger delete-btn' onclick='return confirm(\"Are you sure you want to delete this owner?\")'><i class='fas fa-trash'></i></a>
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



<?php include "includes/footer.php"; ?>