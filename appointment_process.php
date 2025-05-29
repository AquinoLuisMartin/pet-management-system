<?php
include "includes/db_conn.php";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from form
    $petName = mysqli_real_escape_string($conn, $_POST['petName']);
    $ownerName = mysqli_real_escape_string($conn, $_POST['ownerName']);
    $vetID = $_POST['vetID'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $status = $_POST['status'];
    
    // First check if we can find this pet by name and owner
    $owner_parts = explode(' ', $ownerName);
    $lastName = end($owner_parts);
    $firstName = reset($owner_parts);
    
    $find_pet_sql = "SELECT p.PetID 
                    FROM pet p 
                    JOIN owner o ON p.OwnerID = o.OwnerID 
                    WHERE p.Name = '$petName' 
                    AND (o.FirstName LIKE '%$firstName%' AND o.LastName LIKE '%$lastName%')";
    
    $pet_result = mysqli_query($conn, $find_pet_sql);
    
    if (mysqli_num_rows($pet_result) > 0) {
        // Found the pet, use its ID
        $petID = mysqli_fetch_assoc($pet_result)['PetID'];
    } else {
        // Pet not found, create new pet and owner records
        // First create the owner
        $create_owner = "INSERT INTO owner (FirstName, LastName) VALUES ('$firstName', '$lastName')";
        mysqli_query($conn, $create_owner);
        $ownerID = mysqli_insert_id($conn);
        
        // Then create the pet
        $create_pet = "INSERT INTO pet (Name, OwnerID) VALUES ('$petName', $ownerID)";
        mysqli_query($conn, $create_pet);
        $petID = mysqli_insert_id($conn);
    }
    
    // Now create the appointment using the pet ID
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