<?php
include "includes/db_conn.php";

// Check if ID was provided
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    
    // Use stored procedure to delete the pet
    $stmt = $conn->prepare("CALL DeletePet(?)");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    
    if($result) {
        header("Location: pets.php?msg=Pet deleted successfully");
    } else {
        header("Location: pets.php?msg=Failed to delete pet: " . mysqli_error($conn));
    }
} else {
    header("Location: pets.php?msg=No pet ID provided");
}
exit();
?>