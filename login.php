<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduVenture | Log In</title>
  <link rel="stylesheet" href="login.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
</head>
<body>
  <div class="split-screen">
    <div class="left">
      <header>EduVenture</header>
      <div class="login-form">
        <h2>Log In</h2>
        <form action="login.php" method="POST">
          <input type="email" name="email" placeholder="Email" required />
          <input type="password" name="password" placeholder="Password" required />
          <button type="submit">Log In</button>
          <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
      </div>
    </div>
    <div class="right">
      <div class="welcome-message">
        <h2>Welcome Back to EduVenture!</h2>
        <p>Your adventure in learning awaits!</p>
      </div>
    </div>
  </div>

  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      include 'db_connect.php';

      $email = trim($_POST["email"]);
      $password = $_POST["password"];

      // Check if email exists
      $check = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
      $check->bind_param("s", $email);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
          $check->bind_result($id, $name, $hashed_password);
          $check->fetch();

          // Verify the password
          if (password_verify($password, $hashed_password)) {
              session_start();
              $_SESSION["user_id"] = $id;
              $_SESSION["user_name"] = $name;
              echo "<script>alert('Login successful!'); window.location.href='dashboard.php';</script>";
          } else {
              echo "<script>alert('Incorrect password. Please try again.');</script>";
          }
      } else {
          echo "<script>alert('Email not found. Please sign up.');</script>";
      }

      $check->close();
      $conn->close();
  }
  ?>
</body>
</html>
