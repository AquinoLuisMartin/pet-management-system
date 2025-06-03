<?php

include "includes/db_conn.php";
session_start();


if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: owners.php");
    exit();
}


if(!isset($_POST['ownerID']) || empty($_POST['ownerID']) ||
   !isset($_POST['firstName']) || empty($_POST['firstName']) ||
   !isset($_POST['lastName']) || empty($_POST['lastName']) ||
   !isset($_POST['email']) || empty($_POST['email']) ||
   !isset($_POST['phone']) || empty($_POST['phone'])) {
    header("Location: owners.php?msg=Missing required fields");
    exit();
}

$ownerID = $_POST['ownerID'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = isset($_POST['address']) ? $_POST['address'] : '';


$stmt = $conn->prepare("CALL UpdateOwner(?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $ownerID, $firstName, $lastName, $email, $phone, $address);
$stmt->execute();

if($stmt->affected_rows >= 0) {
    header("Location: owners.php?msg=Owner updated successfully");
} else {
    header("Location: owners.php?msg=Failed to update owner: " . mysqli_error($conn));
}
exit();
?>