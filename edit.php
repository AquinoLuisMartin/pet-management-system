<?php
include "includes/db_conn.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM `pets` WHERE `id` = $id";
    $result = mysqli_query($conn, $sql);
    $pet = mysqli_fetch_assoc($result);
}

if (isset($_POST["submit"])) {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $owner_id = $_POST['owner_id'];

    $sql = "UPDATE `pets` SET `name`='$name', `species`='$species', `breed`='$breed', `age`='$age', `owner_id`='$owner_id' WHERE `id`=$id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: index.php?msg=Pet updated successfully");
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="assets/css/style.css">

    <title>Edit Pet - Pet Management System</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Edit Pet</h3>
            <p class="text-muted">Update the information below</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Pet Name:</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $pet['name']; ?>" required>
                    </div>

                    <div class="col">
                        <label class="form-label">Species:</label>
                        <input type="text" class="form-control" name="species" value="<?php echo $pet['species']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Breed:</label>
                    <input type="text" class="form-control" name="breed" value="<?php echo $pet['breed']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Age:</label>
                    <input type="number" class="form-control" name="age" value="<?php echo $pet['age']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Owner ID:</label>
                    <input type="number" class="form-control" name="owner_id" value="<?php echo $pet['owner_id']; ?>" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-success" name="submit">Update</button>
                    <a href="index.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>