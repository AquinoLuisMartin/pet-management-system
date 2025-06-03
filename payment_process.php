<?php
include "includes/db_conn.php";
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $action = isset($_POST['action']) ? $_POST['action'] : 'add';
    $appointmentID = isset($_POST['appointmentID']) ? $_POST['appointmentID'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
    $paymentDate = isset($_POST['paymentDate']) ? $_POST['paymentDate'] : '';
    $paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $paymentID = isset($_POST['payment_id']) ? $_POST['payment_id'] : '';

    
    if ($action == 'add') {
        
        $petID = $appointmentID; 
        
        
        $stmt = $conn->prepare("CALL GetPetOwnerById(?)");
        $stmt->bind_param("i", $petID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $ownerID = $row['OwnerID'];
            $stmt->close();
            $conn->next_result(); 
            
            
            $stmt = $conn->prepare("CALL CreatePayment(?, ?, ?, ?)");
            $stmt->bind_param("iids", $petID, $ownerID, $amount, $paymentMethod);
            
            if ($stmt->execute()) {
                
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $newPaymentID = $row['InvoiceID'];
                    $stmt->close();
                    $conn->next_result(); 
                    
                    
                    $stmt = $conn->prepare("CALL UpdatePaymentDetails(?, ?, ?, ?)");
                    $stmt->bind_param("isss", $newPaymentID, $paymentDate, $status, $notes);
                    $stmt->execute();
                    $stmt->close();
                }
                
                $_SESSION['success_message'] = "Payment added successfully!";
            } else {
                $_SESSION['error_message'] = "Error adding payment: " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "Pet not found.";
            $stmt->close();
        }
    }
    
    else if ($action == 'edit') {
        
        $stmt = $conn->prepare("CALL UpdatePayment(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idssis", $paymentID, $amount, $paymentDate, $paymentMethod, $status, $notes);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Payment updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating payment: " . $conn->error;
        }
        $stmt->close();
    }
    
    
    header("Location: payment.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $paymentID = $_GET['id'];
    
    
    $stmt = $conn->prepare("CALL DeletePayment(?)");
    $stmt->bind_param("i", $paymentID);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Payment deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting payment: " . $conn->error;
    }
    $stmt->close();
    
    
    header("Location: payment.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'get_payment' && isset($_GET['id'])) {
    $paymentID = $_GET['id'];
    
    
    $stmt = $conn->prepare("CALL GetPaymentById(?)");
    $stmt->bind_param("i", $paymentID);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();
    $stmt->close();
    
    
    header('Content-Type: application/json');
    echo json_encode($payment);
    exit();
}


header("Location: payment.php");
exit();
?>