<?php
// dashboard.php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to view your rooms.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Initialize the result variables to avoid undefined variable warnings
$created_rooms_result = null;
$joined_rooms_result = null;

// Fetch rooms created by the user
$created_rooms = $conn->prepare("SELECT id, room_name, room_code FROM rooms WHERE created_by = ?");
if ($created_rooms === false) {
    die('Error preparing statement: ' . $conn->error);
}
$created_rooms->bind_param("i", $user_id);
$created_rooms->execute();
$created_rooms_result = $created_rooms->get_result();

// Fetch rooms the user has joined
$joined_rooms = $conn->prepare("SELECT r.id, r.room_name, r.room_code FROM rooms r 
                                JOIN room_members rm ON r.id = rm.room_id 
                                WHERE rm.user_id = ?");
if ($joined_rooms === false) {
    die('Error preparing statement: ' . $conn->error);
}
$joined_rooms->bind_param("i", $user_id);
$joined_rooms->execute();
$joined_rooms_result = $joined_rooms->get_result();
?>

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
    <div class="create-room-div">
      <button class="create" onclick="openModal('createModal')">Create Room</button>
      <button class="join" onclick="openModal('joinModal')">Join Room</button>
    </div>

    <div class="available-rooms-div">
      <h2>My Rooms</h2>

      <div class="room-category created-rooms">
        <h4>Created Rooms</h4>
        <?php if ($created_rooms_result && $created_rooms_result->num_rows > 0): ?>
          <ul>
            <?php while ($room = $created_rooms_result->fetch_assoc()): ?>
              <li class="document-card">
                <a href="room_tasks.php?id=<?php echo $room['id']; ?>"> 
                  <?php echo htmlspecialchars($room['room_name']); ?>, Code: <?php echo htmlspecialchars($room['room_code']); ?>
                </a>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else: ?>
          <p>You haven't created any rooms yet.</p>
        <?php endif; ?>
      </div>

      <div class="room-category joined-rooms">
        <h4>Joined Rooms</h4>
        <?php if ($joined_rooms_result && $joined_rooms_result->num_rows > 0): ?>
          <ul>
            <?php while ($room = $joined_rooms_result->fetch_assoc()): ?>
              <li class="document-card">
                <a href="room_tasks.php?id=<?php echo $room['id']; ?>"> 
                  <?php echo htmlspecialchars($room['room_name']); ?>, Code: <?php echo htmlspecialchars($room['room_code']); ?>
                </a>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php else: ?>
          <p>You haven't joined any rooms yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <!-- Create Room Modal -->
  <div class="modal" id="createModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('createModal')">&times;</span>
      <h2>Create a Room</h2>
      <form action="create_room.php" method="POST">
        <input type="text" name="room_name" placeholder="Room Name" required><br>
        <select name="access_type" required>
          <option value="public">Public</option>
          <option value="cvsu.edu.ph">CVSU Only</option>
        </select><br>
        <button class="submit" type="submit">Create</button>
      </form>
    </div>
  </div>

  <!-- Join Room Modal -->
  <div class="modal" id="joinModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('joinModal')">&times;</span>
      <h2>Join a Room</h2>
      <form action="join_room.php" method="POST">
        <input type="text" name="room_code" placeholder="Enter Room Code" required>
        <button class="submit" type="submit">Join</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(id) {
      document.getElementById(id).style.display = "block";
    }

    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }

    window.onclick = function(event) {
      const createModal = document.getElementById('createModal');
      const joinModal = document.getElementById('joinModal');
      if (event.target === createModal) createModal.style.display = "none";
      if (event.target === joinModal) joinModal.style.display = "none";
    }

   // JavaScript for dropdown functionality
document.querySelector('.profile-icon').addEventListener('click', function () {
    const profileWrapper = document.querySelector('.profile-wrapper');
    profileWrapper.classList.toggle('active'); // Toggle the 'active' class
});

document.addEventListener('click', function (event) {
    if (!event.target.closest('.profile-wrapper')) {
        const profileWrapper = document.querySelector('.profile-wrapper');
        profileWrapper.classList.remove('active');
    }
});


  </script>
</body>
</html>

<?php
$created_rooms->close();
$joined_rooms->close();
$conn->close();
?>
