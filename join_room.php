<?php
// Include DB connection
include 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to join a room.";
    exit;
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Initialize $check_member_stmt to null to avoid errors during closure
$check_member_stmt = null;

// Check if room code is provided
if (isset($_POST['room_code'])) {
    $room_code = $_POST['room_code']; // Get room code from POST

    // Validate room code
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE room_code = ?");
    $stmt->bind_param("s", $room_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Join the room by adding to room_members table
        $room = $result->fetch_assoc();
        $room_id = $room['id'];

        // Check if the user is already a member of the room
        $check_member_stmt = $conn->prepare("SELECT * FROM room_members WHERE room_id = ? AND user_id = ?");
        $check_member_stmt->bind_param("ii", $room_id, $user_id);
        $check_member_stmt->execute();
        $check_result = $check_member_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Insert the user into room_members table
            $stmt = $conn->prepare("INSERT INTO room_members (room_id, user_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $room_id, $user_id);
            $stmt->execute();

            // Redirect to the room's details page
            header("Location: rooms.php?id=$room_id");
            exit();
        } else {
            echo "You are already a member of this room.";
        }

    } else {
        echo "Invalid room code.";
    }

    // Close the check_member_stmt only if it is initialized
    if ($check_member_stmt) {
        $check_member_stmt->close();
    }

} else {
    echo "Room code is required.";
}

// Close the statements and connection only if they are initialized
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
