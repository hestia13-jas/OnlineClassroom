<?php
// Include DB connection
include 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to edit a task.";
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user is logged in and user_id is stored in session

// Get task ID from URL
$task_id = $_GET['id'];

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $task = $result->fetch_assoc();
    // Check if the user is the room creator
    $room_id = $task['room_id'];
    $room_check_stmt = $conn->prepare("SELECT created_by FROM rooms WHERE id = ?");
    $room_check_stmt->bind_param("i", $room_id);
    $room_check_stmt->execute();
    $room_check_result = $room_check_stmt->get_result();
    $room = $room_check_result->fetch_assoc();

    if ($room['created_by'] != $user_id) {
        echo "You are not authorized to edit this task.";
        exit;
    }
} else {
    echo "Task not found.";
    exit;
}
?>

<!-- HTML form for editing task -->
<form method="POST" action="update_task.php">
    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
    <label for="task_name">Task Name:</label>
    <input type="text" name="task_name" value="<?php echo $task['task_name']; ?>" required><br>
    <label for="due_date">Due Date:</label>
    <input type="date" name="due_date" value="<?php echo $task['due_date']; ?>" required><br>
    <button type="submit">Update Task</button>
</form>
