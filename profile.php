<?php
// profile.php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$user_profile = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
if ($user_profile === false) {
    die('Error preparing statement: ' . $conn->error);
}

$user_profile->bind_param("i", $user_id);
$user_profile->execute();
$result = $user_profile->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

$user_profile->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link rel="stylesheet" href="profile.css">
</head>
<body>
  <header>
    <h1>My Profile</h1>
  </header>

  <main class="profile-container">
  <form action="update_profile.php" method="POST" class="profile-form">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

    <hr style="margin: 20px 0;">

    <h3>Change Password</h3>

    <label>Current Password:</label><br>
    <input type="password" name="current_password" required><br>

    <label>New Password:</label><br>
    <input type="password" name="new_password" required><br>

    <label>Confirm New Password:</label><br>
    <input type="password" name="confirm_new_password" required><br>

    <button type="submit" class="save-button">Save Changes</button>
  </form>

  <a href="dashboard.php" class="back-button">Back to Dashboard</a>
</main>

</body>
</html>
