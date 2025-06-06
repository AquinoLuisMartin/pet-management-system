<?php

include "includes/db_conn.php";
session_start();


if(!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid owner ID";
    header("Location: owners.php");
    exit();
}

$id = $_GET['id'];

try {
    
    $stmt = $conn->prepare("CALL DeleteOwner(?)");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    
    if($result) {
        $_SESSION['success_message'] = "Owner deleted successfully";
    } else {
        $_SESSION['error_message'] = "Failed to delete owner: " . $conn->error;
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
}

header("Location: owners.php");
exit();
?>