<?php
include "includes/db_conn.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "DELETE FROM `pets` WHERE `id` = '$id'";
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