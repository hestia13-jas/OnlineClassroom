<?php
// room_tasks.php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to view your tasks.";
    exit;
}

$user_id = $_SESSION['user_id'];
$room_id = isset($_GET['id']) ? $_GET['id'] : null; // Ensure that room_id is set

if (!$room_id) {
    echo "Room ID is missing.";
    exit;
}

// Fetch room details to check if the user is the creator
$room_query = $conn->prepare("SELECT id, room_name, created_by FROM rooms WHERE id = ?");
$room_query->bind_param("i", $room_id);
$room_query->execute();
$room_result = $room_query->get_result();
$room = $room_result->fetch_assoc();

// Check if the current user is the creator of the room
if ($room['created_by'] != $user_id) {
    echo "You are not authorized to add tasks to this room.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Task creation form processing
    $task_name = $_POST['task_name'] ?? '';
    $task_description = $_POST['task_description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $task_file = isset($_FILES['task_file']) ? $_FILES['task_file']['name'] : '';

    // Check if all required fields are filled
    if (empty($task_name) || empty($task_description) || empty($due_date)) {
        echo "All required fields must be filled.";
        exit;
    }

    // Process file upload if a file is uploaded
    if ($task_file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($task_file);
        
        // Check if file is uploaded and move it
        if (!move_uploaded_file($_FILES['task_file']['tmp_name'], $target_file)) {
            echo "There was an error uploading the file.";
            exit;
        }
    }

    // Insert task into the database
    $create_task = $conn->prepare("INSERT INTO tasks (room_id, task_name, task_description, due_date, task_file, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $create_task->bind_param("issssi", $room_id, $task_name, $task_description, $due_date, $task_file, $user_id);
    
    if ($create_task->execute()) {
        echo "Task created successfully!";
        header("Location: room_tasks.php?id=" . $room_id); // Redirect to avoid re-posting the form on refresh
        exit();
    } else {
        echo "There was an error creating the task.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Task Management for <?php echo htmlspecialchars($room['room_name']); ?></title>
  <link rel="stylesheet" href="room_tasks.css"/>
</head>
<body>
  <header>
    <h1>Add Task to Room: <?php echo htmlspecialchars($room['room_name']); ?></h1>
  </header>

  <main>
    <form action="room_tasks.php?id=<?php echo $room_id; ?>" method="POST" enctype="multipart/form-data">
      <label for="task_name">Task Name</label>
      <input type="text" name="task_name" id="task_name" required><br>

      <label for="task_description">Task Description</label>
      <textarea name="task_description" id="task_description" required></textarea><br>

      <label for="due_date">Due Date</label>
      <input type="date" name="due_date" id="due_date" required><br>

      <label for="task_file">Upload File</label>
      <input type="file" name="task_file" id="task_file"><br>

      <button type="submit">Add Task</button>
    </form>
  </main>
</body>
</html>

<?php
$room_query->close();
$conn->close();
?>
