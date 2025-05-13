<?php
// update_profile.php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in.";
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];

$update = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
if ($update === false) {
    die('Error preparing statement: ' . $conn->error);
}

$update->bind_param("ssi", $name, $email, $user_id);
if ($update->execute()) {
    header("Location: profile.php");
    exit;
} else {
    echo "Failed to update profile.";
}

$update->close();
$conn->close();
?>
