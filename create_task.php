<?php
// Include DB connection
include 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to create a task.";
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user is logged in and user_id is stored in session

// Check if the user is the room creator
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $due_date = $_POST['due_date'];
    
    // Check if required fields are filled
    if (empty($task_name) || empty($due_date)) {
        echo "Task name and due date are required.";
        exit;
    }

    // Check if the logged-in user is the creator of the room
    $check_creator = $conn->prepare("SELECT created_by FROM rooms WHERE id = ?");
    $check_creator->bind_param("i", $room_id);
    $check_creator->execute();
    $creator_result = $check_creator->get_result();
    
    if ($creator_result->num_rows > 0) {
        $room = $creator_result->fetch_assoc();
        if ($room['created_by'] != $user_id) {
            echo "You are not authorized to create tasks in this room.";
            exit;
        }

        // Handle file upload (if any)
        $task_file_name = null;
        if (isset($_FILES['task_file']) && $_FILES['task_file']['error'] == 0) {
            $upload_dir = 'uploads/';
            $task_file_name = time() . '-' . basename($_FILES['task_file']['name']);
            $upload_file = $upload_dir . $task_file_name;

            if (!move_uploaded_file($_FILES['task_file']['tmp_name'], $upload_file)) {
                echo "Error uploading file.";
                exit;
            }
        }

        // Insert task into the database
        $stmt = $conn->prepare("INSERT INTO tasks (room_id, task_name, task_description, due_date, task_file, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $room_id, $task_name, $task_description, $due_date, $task_file_name, $user_id);

        if ($stmt->execute()) {
            header("Location: room_tasks.php?id=$room_id");
            exit();
        } else {
            echo "Error creating task.";
        }
    } else {
        echo "Room not found.";
    }
}
?>

