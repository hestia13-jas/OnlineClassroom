<?php
// rooms.php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in first.";
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    echo "Room ID is missing.";
    exit;
}

$room_id = $_GET['id'];

// Check if the user is a member of the room
$check_membership = $conn->prepare("SELECT * FROM room_members WHERE room_id = ? AND user_id = ?");
$check_membership->bind_param("ii", $room_id, $user_id);
$check_membership->execute();
$result = $check_membership->get_result();

if ($result->num_rows == 0) {
    echo "You are not a member of this room.";
    exit;
}

// Fetch room details
$room_query = $conn->prepare("SELECT room_name FROM rooms WHERE id = ?");
$room_query->bind_param("i", $room_id);
$room_query->execute();
$room_result = $room_query->get_result();
$room = $room_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($room['room_name']); ?> | Room Details</title>
    <link rel="stylesheet" href="rooms.css"> <!-- optional if you want to style it -->
</head>
<body>
    <header>
        <h1>Welcome to <?php echo htmlspecialchars($room['room_name']); ?> Room!</h1>
    </header>

    <main>
        <p>You have successfully joined this room.</p>

        <!-- Add more content here like Tasks, Members, Announcements, etc. -->

        <a href="dashboard.php">Go back to Dashboard</a>
    </main>
</body>
</html>

<?php
$check_membership->close();
$room_query->close();
$conn->close();
?>
