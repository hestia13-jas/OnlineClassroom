<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Room Management</title>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>
<header>
    <h1>EduVenture</h1>
    <div class="profile-wrapper">
    <div class="profile-icon">
        <img src="profile.jpg" alt="Profile">
    </div>
    <div class="dropdown-menu">
        <a href="profile.php">Settings</a>
        <a href="login.php">Logout</a>
    </div>
</div>
  </header>

  <div class="sidebar">
    <nav>
      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
      </ul>
    </nav>
  </div>


    <main>
        <h2>Sticky Notes</h2>
        <div class="sticky-notes-container">
            <div class="sticky-note">
                <h3>Reminder 1</h3>
                <textarea placeholder="Write your reminder..."></textarea>
            </div>
            <div class="sticky-note">
                <h3>Reminder 2</h3>
                <textarea placeholder="Write your reminder..."></textarea>
            </div>
        </div>

        <h2>Schedule Tracker</h2>
        <div class="schedule-tracker">
            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Task</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Monday</td>
                        <td>Math Assignment</td>
                        <td>10:00 AM</td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td>Project Meeting</td>
                        <td>2:00 PM</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
