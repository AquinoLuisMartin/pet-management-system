<?php
include "includes/db_conn.php";
include "includes/header.php";

// Handle messages
if (isset($_GET["msg"])) {
    $msg = $_GET["msg"];
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    ' . $msg . '
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>

<div class="section-header">
    <h2>Pet Management</h2>
    <a href="add_pet.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Pet
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Species</th>
                    <th>Breed</th>
                    <th>Gender</th>
                    <th>Owner</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT p.*, CONCAT(o.FirstName, ' ', o.LastName) as OwnerName 
                        FROM pet p 
                        LEFT JOIN owner o ON p.OwnerID = o.OwnerID
                        ORDER BY p.PetID DESC";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo $row["PetID"]; ?></td>
                            <td><?php echo $row["Name"]; ?></td>
                            <td><?php echo $row["Species"]; ?></td>
                            <td><?php echo $row["Breed"]; ?></td>
                            <td><?php echo $row["Gender"]; ?></td>
                            <td><?php echo $row["OwnerName"]; ?></td>
                            <td>
                                <a href="view_pet.php?id=<?php echo $row["PetID"]; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $row['PetID']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?php echo $row['PetID']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No pets found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "includes/footer.php"; ?>