<?php
include "includes/db_conn.php";
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        
        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
        $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
        $contactNumber = mysqli_real_escape_string($conn, $_POST['contactNumber']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        

        $stmt = $conn->prepare("CALL AddVeterinarian(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstName, $lastName, $specialization, $contactNumber, $email);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $newVetID = $data['VetID'];
            
            
            $_SESSION['success_message'] = "Veterinarian added successfully";
            $_SESSION['new_vet_id'] = $newVetID;
            
            header("Location: veterinarians.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error adding veterinarian: " . $stmt->error;
            header("Location: veterinarians.php");
            exit();
        }
        $stmt->close();
    } 
    else if (isset($_POST['action']) && $_POST['action'] == 'update') {
        
        $vetID = (int)$_POST['vetID'];
        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
        $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
        $contactNumber = mysqli_real_escape_string($conn, $_POST['contactNumber']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);


        
        $stmt = $conn->prepare("CALL UpdateVeterinarian(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $vetID, $firstName, $lastName, $specialization, $contactNumber, $email);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Veterinarian updated successfully";
            header("Location: veterinarians.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating veterinarian: " . $stmt->error;
            header("Location: veterinarians.php");
            exit();
        }
        $stmt->close();
    }
}


if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $vetID = (int)$_GET['id'];
    
    
    $stmt = $conn->prepare("CALL DeleteVeterinarian(?)");
    $stmt->bind_param("i", $vetID);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Veterinarian deleted successfully";
    } else {
        $_SESSION['error_message'] = "Error deleting veterinarian: " . $stmt->error;
    }
    
    header("Location: veterinarians.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'getVet' && isset($_GET['id'])) {
    $vetID = (int)$_GET['id'];
    
    $stmt = $conn->prepare("CALL GetVeterinarianByID(?)");
    $stmt->bind_param("i", $vetID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        
        header('Content-Type: application/json');
        echo json_encode($row);
    } else {
        
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Veterinarian not found']);
    }
    
    exit();
}


header("Location: veterinarians.php");
exit();
?>