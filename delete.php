<?php
include "includes/db_conn.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    // Using a prepared statement for deletion via stored procedure
    $stmt = mysqli_prepare($conn, "CALL delete_pet(?)");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // Check if the stored procedure was successful
    if(mysqli_affected_rows($conn) > 0) {
        header("Location: index.php?msg=Pet deleted successfully");
        exit();
    } else {
        echo "Failed to delete pet: " . mysqli_error($conn);
    }
    // Remove the unnecessary extra SQL query
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: index.php?msg=Pet deleted successfully");
    } else {
        echo "Failed to delete pet: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php?msg=Invalid request");
}
?>