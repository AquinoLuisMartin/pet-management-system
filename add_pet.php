<?php
include "includes/db_conn.php";

if (isset($_POST["submit"])) {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $conditions = $_POST['conditions'];
    $ownerID = $_POST['ownerID'];

    // Use stored procedure instead of direct SQL
    $stmt = $conn->prepare("CALL AddNewPet(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdsi", $name, $species, $breed, $dob, $gender, $weight, $conditions, $ownerID);
    $result = $stmt->execute();

    if ($result) {
        header("Location: pets.php?msg=New pet added successfully");
        exit;
    } else {
        $error = "Failed: " . mysqli_error($conn);
    }
}

include "includes/header.php";
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-paw"></i> Add New Pet</h1>
            <p class="text-muted">Register a new pet in the system</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="" method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Pet Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ownerID" class="form-label">Owner</label>
                            <select class="form-control" id="ownerID" name="ownerID" required>
                                <option value="">Select Owner</option>
                                <?php
                                $owners = mysqli_query($conn, "SELECT OwnerID, CONCAT(FirstName, ' ', LastName) as Name FROM owner ORDER BY Name");
                                while ($owner = mysqli_fetch_assoc($owners)) {
                                    echo "<option value='" . $owner['OwnerID'] . "'>" . $owner['Name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="species" class="form-label">Species</label>
                            <input type="text" class="form-control" id="species" name="species" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="breed" class="form-label">Breed</label>
                            <input type="text" class="form-control" id="breed" name="breed" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="weight" name="weight" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="conditions" class="form-label">Medical Conditions</label>
                    <textarea class="form-control" id="conditions" name="conditions" rows="3"></textarea>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="pets.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="submit" class="btn btn-primary">Save Pet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>