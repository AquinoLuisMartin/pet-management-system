<?php
// filepath: c:\xampp\htdocs\pet-management-system\owner_delete.php
include "includes/db_conn.php";
session_start();

// Check if the owner ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: owners.php?msg=Invalid owner ID");
    exit();
}

$id = $_GET['id'];

// Use stored procedure to delete the owner
$stmt = $conn->prepare("CALL DeleteOwner(?)");
$stmt->bind_param("i", $id);
$result = $stmt->execute();

if($result) {
    header("Location: owners.php?msg=Owner deleted successfully");
} else {
    header("Location: owners.php?msg=Failed to delete owner: " . mysqli_error($conn));
}
exit();
?>