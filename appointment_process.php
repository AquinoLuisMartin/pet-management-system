<?php
include "includes/db_conn.php";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which form fields were provided
    if (isset($_POST['petID']) && !empty($_POST['petID'])) {
        // Use the directly provided petID (from dropdown)
        $petID = (int)$_POST['petID'];
        $vetID = (int)$_POST['vetID'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $status = $_POST['status'];
        $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
        
        // Now create the appointment using the CreateAppointment procedure
        $stmt = $conn->prepare("CALL CreateAppointment(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $petID, $vetID, $date, $time, $reason, $notes, $status);
    } 
    else if (isset($_POST['petName']) && isset($_POST['ownerName'])) {
        // The existing flow for creating new pets when scheduled from elsewhere
        $petName = mysqli_real_escape_string($conn, $_POST['petName']);
        $ownerName = mysqli_real_escape_string($conn, $_POST['ownerName']);
        $vetID = $_POST['vetID'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $status = $_POST['status'];
        $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
        
        // First parse owner name
        $owner_parts = explode(' ', $ownerName);
        $lastName = end($owner_parts);
        $firstName = reset($owner_parts);
        
        $stmt = $conn->prepare("CALL FindPetByNameAndOwner(?, ?, ?)");
        $stmt->bind_param("sss", $petName, $firstName, $lastName);
        $stmt->execute();
        $pet_result = $stmt->get_result();
        $stmt->close();
        
        if ($pet_result->num_rows > 0) {
            // Found the pet, use its ID
            $petID = $pet_result->fetch_assoc()['PetID'];
        } else {
            // Pet not found, create new owner first using existing RegisterOwner procedure
            $conn->next_result();
            $hashedPassword = password_hash(uniqid(), PASSWORD_DEFAULT); // Generate random password
            $email = strtolower($firstName . '.' . $lastName . '@example.com'); // Generate email
            $phone = ''; // Empty phone
            $address = ''; // Empty address
            
            $stmt = $conn->prepare("CALL RegisterOwner(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $firstName, $lastName, $phone, $email, $address, $hashedPassword);
            $stmt->execute();
            $result = $stmt->get_result();
            $owner = $result->fetch_assoc();
            $ownerID = $owner['OwnerID'];
            $stmt->close();
            
            // Then create pet using CreatePet procedure
            $conn->next_result();
            $species = ''; // Default empty values
            $breed = '';
            $dob = date('Y-m-d'); // Today's date as default
            $gender = '';
            $weight = 0.0;
            $medicalConditions = '';
            
            $stmt = $conn->prepare("CALL CreatePet(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssds", $ownerID, $petName, $species, $breed, $dob, $gender, $weight, $medicalConditions);
            $stmt->execute();
            $result = $stmt->get_result();
            $pet = $result->fetch_assoc();
            $petID = $pet['PetID'];
            $stmt->close();
        }
        
        // Now create the appointment using the CreateAppointment procedure
        $conn->next_result();
        $stmt = $conn->prepare("CALL CreateAppointment(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $petID, $vetID, $date, $time, $reason, $notes, $status);
    } 
    else {
        // No valid pet identification provided
        header("Location: appointment.php?error=No pet selected");
        exit();
    }

    if ($stmt->execute()) {
        header("Location: appointment.php?success=Appointment added successfully");
        exit();
    } else {
        header("Location: appointment.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
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
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    

    
    $stmt = $conn->prepare("CALL UpdateAppointment(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissiissss", $oldPetID, $oldVetID, $oldDate, $oldTime, 
                     $petID, $vetID, $date, $time, $reason, $notes, $status);
    
    if ($stmt->execute()) {
        header("Location: appointment.php?success=Appointment updated successfully");
        exit();
    } else {
        header("Location: appointment.php?error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
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
        
        $stmt = $conn->prepare("CALL DeleteAppointment(?, ?, ?, ?)");
        $stmt->bind_param("iiss", $petID, $vetID, $date, $time);
        
        if ($stmt->execute()) {
            header("Location: appointment.php?success=Appointment deleted successfully");
            exit();
        } else {
            header("Location: appointment.php?error=" . urlencode($stmt->error));
            exit();
        }
        $stmt->close();
    } else {
        header("Location: appointment.php?error=Invalid appointment ID format");
        exit();
    }
}

// Redirect back if no action was taken
header("Location: appointment.php");
exit();
?>