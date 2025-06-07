<?php
include "includes/db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    if (!isset($_POST['appointment_id'])) {
        header("Location: appointment.php?error=Invalid appointment data");
        exit();
    }
    
    $appointment_id = $_POST['appointment_id'];
    
    // Check if it's a valid integer (direct ID from database)
    if (is_numeric($appointment_id) && intval($appointment_id) > 0) {
        // For direct ID handling, query the appointment details 
        $stmt = $conn->prepare("SELECT pet_id, vet_id, appointment_date, appointment_time FROM appointments WHERE appointment_id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $pet_id = $row['pet_id'];
            $vet_id = $row['vet_id'];
            $date = $row['appointment_date'];
            $time = $row['appointment_time'];
            $stmt->close();
        } else {
            $stmt->close();
            header("Location: appointment.php?error=Appointment not found");
            exit();
        }
    } else {
        // Try the composite key format parsing
        $parts = explode('-', $appointment_id);
        
        // Check if all required parts exist
        if (count($parts) < 6) {
            header("Location: appointment.php?error=Invalid appointment format");
            exit();
        }
        
        $pet_id = intval($parts[0]);
        $vet_id = intval($parts[1]);
        $date = $parts[2] . '-' . $parts[3] . '-' . $parts[4];
        $time = $parts[5];
    }
    
    try {
        $stmt = $conn->prepare("CALL DeleteAppointment(?, ?, ?, ?)");
        $stmt->bind_param("iiss", $pet_id, $vet_id, $date, $time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
            $response = $result->fetch_assoc();
            $stmt->close();
            
            if ($response && isset($response['success']) && $response['success'] == 1) {
                header("Location: appointment.php?success=" . urlencode($response['message']));
            } else {
                $error_message = isset($response['message']) ? $response['message'] : "Failed to delete appointment";
                header("Location: appointment.php?error=" . urlencode($error_message));
            }
        } else {
            $stmt->close();
            header("Location: appointment.php?success=Appointment deleted successfully");
        }
    } catch (Exception $e) {
        header("Location: appointment.php?error=" . urlencode("Error: " . $e->getMessage()));
    }
    exit();
} else {
    header("Location: appointment.php");
    exit();
}
?>