<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to create a room.";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = trim($_POST['room_name']);
    $access_type = $_POST['access_type'];

   
    if (empty($room_name) || empty($access_type)) {
        echo "Please fill out all fields.";
        exit;
    }

    // Generate a random 4-digit room code
    $room_code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // Ensures a 4-digit code

    // Prepare the SQL statement
    $create_room = $conn->prepare("INSERT INTO rooms (room_name, room_code, access_type, created_by) VALUES (?, ?, ?, ?)");

    // Check if the prepare statement is successful
    if ($create_room === false) {
        echo "Error preparing the SQL statement: " . $conn->error;
        exit;
    }

    // Bind parameters
    $create_room->bind_param("sssi", $room_name, $room_code, $access_type, $user_id);

    // Execute the query
    if ($create_room->execute()) {
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Error creating room: " . $create_room->error;
    }
    
    // Close resources
    $create_room->close();
    $conn->close();
}
?>
