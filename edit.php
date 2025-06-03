<?php
include "includes/db_conn.php";
include "includes/header.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    
    $stmt = $conn->prepare("CALL GetPetById(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pet = mysqli_fetch_assoc($result);
    $stmt->close();
}

if (isset($_POST["submit"])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $species = mysqli_real_escape_string($conn, $_POST['species']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $dob = $_POST['dob'];
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $weight = $_POST['weight'];
    $conditions = mysqli_real_escape_string($conn, $_POST['conditions']);
    $owner_id = $_POST['owner_id'];

    
    $stmt = $conn->prepare("CALL UpdatePet(?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssdsi", $id, $name, $species, $breed, $dob, $gender, $weight, $conditions, $owner_id);
    $result = $stmt->execute();

    if ($result) {
        header("Location: pets.php?msg=Pet updated successfully");
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-edit"></i> Edit Pet</h1>
            <p class="text-muted">Update pet information</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="" method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Pet Name:</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $pet['Name']; ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Species:</label>
                        <input type="text" class="form-control" name="species" value="<?php echo $pet['Species']; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Breed:</label>
                        <input type="text" class="form-control" name="breed" value="<?php echo $pet['Breed']; ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Birth:</label>
                        <input type="date" class="form-control" name="dob" value="<?php echo $pet['DateOfBirth']; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Gender:</label>
                        <select class="form-control" name="gender" required>
                            <option value="Male" <?php if($pet['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if($pet['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Weight (kg):</label>
                        <input type="number" step="0.01" class="form-control" name="weight" value="<?php echo $pet['Weight']; ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Owner:</label>
                        <select class="form-control" name="owner_id" required>
                            <?php
                            
                            $stmt = $conn->prepare("CALL GetAllOwners()");
                            $stmt->execute();
                            $owners = $stmt->get_result();
                            
                            while($owner = mysqli_fetch_assoc($owners)) {
                                $selected = ($owner['OwnerID'] == $pet['OwnerID']) ? 'selected' : '';
                                echo "<option value='".$owner['OwnerID']."' $selected>".$owner['Name']."</option>";
                            }
                            $stmt->close();
                            ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Medical Conditions:</label>
                    <textarea class="form-control" name="conditions" rows="3"><?php echo $pet['MedicalConditions']; ?></textarea>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" name="submit">Update Pet</button>
                    <a href="pets.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>