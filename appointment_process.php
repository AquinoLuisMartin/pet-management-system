<?php
<?php
include "includes/db_conn.php";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from form
    $petID = $_POST['petID'];
    $vetID = $_POST['vetID'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $status = $_POST['status'];

    // Check if appointment already exists
    $check_sql = "SELECT * FROM appointment 
                  WHERE PetID = $petID 
                  AND VetID = $vetID 
                  AND Date = '$date' 
                  AND Time = '$time'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Appointment already exists
        header("Location: appointment.php?error=Appointment already exists for this pet, veterinarian, date and time");
        exit();
    }

    // Insert new appointment
    $sql = "INSERT INTO appointment (PetID, VetID, Date, Time, Reason, Status)
            VALUES ($petID, $vetID, '$date', '$time', '$reason', '$status')";

    if (mysqli_query($conn, $sql)) {
        header("Location: appointment.php?success=Appointment added successfully");
        exit();
    } else {
        header("Location: appointment.php?error=" . urlencode(mysqli_error($conn)));
        exit();
    }
}

// Handle appointment edit
if (isset($_POST['edit'])) {
    // Get data from form
    $oldPetID = $_POST['oldPetID'];
    $oldVetID = $_POST['oldVetID'];
    $oldDate = $_POST['oldDate'];
    $oldTime = $_POST['oldTime'];
    
    $petID = $_POST['petID'];
    $vetID = $_POST['vetID'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $status = $_POST['status'];

    // Delete old appointment
    $delete_sql = "DELETE FROM appointment 
                   WHERE PetID = $oldPetID 
                   AND VetID = $oldVetID 
                   AND Date = '$oldDate' 
                   AND Time = '$oldTime'";
    mysqli_query($conn, $delete_sql);

    // Insert updated appointment
    $insert_sql = "INSERT INTO appointment (PetID, VetID, Date, Time, Reason, Status)
                   VALUES ($petID, $vetID, '$date', '$time', '$reason', '$status')";

    if (mysqli_query($conn, $insert_sql)) {
        header("Location: appointment.php?success=Appointment updated successfully");
        exit();
    } else {
        header("Location: appointment.php?error=" . urlencode(mysqli_error($conn)));
        exit();
    }
}

// Handle appointment delete
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $parts = explode('-', $id);
    
    if (count($parts) == 4) {
        $petID = $parts[0];
        $vetID = $parts[1];
        $date = $parts[2];
        $time = $parts[3];

        $sql = "DELETE FROM appointment 
                WHERE PetID = $petID 
                AND VetID = $vetID 
                AND Date = '$date' 
                AND Time = '$time'";

        if (mysqli_query($conn, $sql)) {
            header("Location: appointment.php?success=Appointment deleted successfully");
            exit();
        } else {
            header("Location: appointment.php?error=" . urlencode(mysqli_error($conn)));
            exit();
        }
    } else {
        header("Location: appointment.php?error=Invalid appointment ID format");
        exit();
    }
}

// Redirect back if no action was taken
header("Location: appointment.php");
exit();
?>