<?php
session_start();
include "includes/db_conn.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['petID']) && !empty($_POST['petID'])) {
        
        $petID = (int)$_POST['petID'];
        $vetID = (int)$_POST['vetID'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $status = $_POST['status'];
        $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
        
        
        $stmt = $conn->prepare("CALL CreateAppointment(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $petID, $vetID, $date, $time, $reason, $notes, $status);
    } 
    else if (isset($_POST['petName']) && isset($_POST['ownerName'])) {
        
        $petName = mysqli_real_escape_string($conn, $_POST['petName']);
        $ownerName = mysqli_real_escape_string($conn, $_POST['ownerName']);
        $vetID = $_POST['vetID'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $status = $_POST['status'];
        $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
        
        
        $owner_parts = explode(' ', $ownerName);
        $lastName = end($owner_parts);
        $firstName = reset($owner_parts);
        
        $stmt = $conn->prepare("CALL FindPetByNameAndOwner(?, ?, ?)");
        $stmt->bind_param("sss", $petName, $firstName, $lastName);
        $stmt->execute();
        $pet_result = $stmt->get_result();
        $stmt->close();
        
        if ($pet_result->num_rows > 0) {
           
            $petID = $pet_result->fetch_assoc()['PetID'];
        } else {
            
            $conn->next_result();
            $hashedPassword = password_hash(uniqid(), PASSWORD_DEFAULT); 
            $email = strtolower($firstName . '.' . $lastName . '@example.com'); 
            $phone = ''; 
            $address = ''; 
            
            $stmt = $conn->prepare("CALL RegisterOwner(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $firstName, $lastName, $phone, $email, $address, $hashedPassword);
            $stmt->execute();
            $result = $stmt->get_result();
            $owner = $result->fetch_assoc();
            $ownerID = $owner['OwnerID'];
            $stmt->close();
            
            
            $conn->next_result();
            $species = ''; 
            $breed = '';
            $dob = date('Y-m-d'); 
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
        
       
        $conn->next_result();
        $stmt = $conn->prepare("CALL CreateAppointment(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $petID, $vetID, $date, $time, $reason, $notes, $status);
    } 
    else {
        
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


if (isset($_POST['edit'])) {
    
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


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    
    
    $stmt = $conn->prepare("CALL GetAppointmentById(?)");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        
        header('Content-Type: application/json');
        echo json_encode($row);
    } else {
       
        http_response_code(404);
        echo json_encode(['error' => 'Appointment not found']);
    }
    
    $stmt->close();
    $conn->close();
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    
    if ($_POST['action'] === 'create') {
        $pet_id = intval($_POST['petID']);
        $vet_id = intval($_POST['vetID']);
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = $_POST['reason'];
        $notes = $_POST['notes'] ?? '';
        $status = $_POST['status'];
        
        
        $stmt = $conn->prepare("CALL CreateAppointment(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $pet_id, $vet_id, $date, $time, $reason, $notes, $status);
        
        if ($stmt->execute()) {
            header("Location: appointment.php?success=Appointment created successfully");
        } else {
            header("Location: appointment.php?error=Failed to create appointment: " . $conn->error);
        }
        
        $stmt->close();
    }
    
    
    else if ($_POST['action'] === 'update' && isset($_POST['appointmentID'])) {
        $appointment_id = intval($_POST['appointmentID']);
        $pet_id = intval($_POST['petID']);
        $vet_id = intval($_POST['vetID']);
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = $_POST['reason'];
        $notes = $_POST['notes'] ?? '';
        $status = $_POST['status'];
        
        
        $stmt = $conn->prepare("CALL UpdateAppointment(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisssss", $appointment_id, $pet_id, $vet_id, $date, $time, $reason, $notes, $status);
        
        if ($stmt->execute()) {
            header("Location: appointment.php?success=Appointment updated successfully");
        } else {
            header("Location: appointment.php?error=Failed to update appointment: " . $conn->error);
        }
        
        $stmt->close();
    }
    
    
    else if ($_POST['action'] === 'delete' && isset($_POST['appointmentID'])) {
        $appointment_id = intval($_POST['appointmentID']);
        
        
        $stmt = $conn->prepare("CALL DeleteAppointment(?)");
        $stmt->bind_param("i", $appointment_id);
        
        if ($stmt->execute()) {
            header("Location: appointment.php?success=Appointment deleted successfully");
        } else {
            header("Location: appointment.php?error=Failed to delete appointment: " . $conn->error);
        }
        
        $stmt->close();
    }
    
    $conn->close();
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $petID = (int)$_POST['petID'];
    $vetID = (int)$_POST['vetID'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $_POST['status'];
    
    
    $stmt = $conn->prepare("CALL UpdateAppointmentStatus(?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $petID, $vetID, $date, $time, $status);
    
    if ($stmt->execute()) {
        
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&success=Status updated successfully");
    } else {
        
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=Failed to update status: " . $conn->error);
    }
    
    $stmt->close();
    exit;
}


header("Location: appointment.php");
exit();
?>