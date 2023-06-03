<?php
// Start the session
session_start();

// Check if the user is logged in and the session contains the username
if (!isset($_SESSION['username'])) {
  // User not logged in
  // Redirect or display an appropriate message
  header("Location: login.php");
  exit();
}

// Assuming you have already established a database connection
$connection = mysqli_connect('localhost', 'root', 'root', 'library');

// Check if the form was submitted and a user ID was provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
  $user_id = $_POST['user_id'];

  // Update the status column to "disabled" for the specified user ID
  $updateQuery = "UPDATE user SET status = 'disabled' WHERE user_id = '$user_id'";
  mysqli_query($connection, $updateQuery);

  // Redirect to the same page to auto-refresh
  header("Location: admin_user_status.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id2'])) {
    $user_id = $_POST['user_id2'];
  
    // Update the status column to "disabled" for the specified user ID
    $updateQuery = "UPDATE user SET status = 'enabled' WHERE user_id = '$user_id'";
    mysqli_query($connection, $updateQuery);
  
    // Redirect to the same page to auto-refresh
    header("Location: admin_user_status.php");
    exit();
  }
// Retrieve the username from the session
$username = $_SESSION['username'];

  // Query to fetch all users with role IDs 3 or 4 and the same school ID
  $usersQuery = "SELECT * FROM user WHERE role_id = 2";
  $usersResult = mysqli_query($connection, $usersQuery);

  // Check if the query was successful and rows were returned
  if ($usersResult && mysqli_num_rows($usersResult) > 0) {
    // Fetch all the users
    while ($row = mysqli_fetch_assoc($usersResult)) {
      $users[] = $row;
    }
  } else {
    // No users found
    $error = "No users found.";
  }

  // Close the users result
  mysqli_free_result($usersResult);


if(!$username) {
  // User not found or school ID not available
  // Handle the error or display an appropriate message
  echo "User not found";
}

// Close the database connection
mysqli_close($connection);
?>


<!DOCTYPE html>
<html>

<head>
  <title>Users List</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url('24manguel-superJumbo.jpg');
      background-repeat: no-repeat;
      background-size: cover;
    }

    .top-bar {
      background-color: #2a65a469;
      padding: 10px;
      text-align: center;
      backdrop-filter: blur(5px);
    }

    .top-bar a {
      color: #fff;
      text-decoration: none;
      margin: 0 20px;
    }

    .container {
      max-width: 1000px;
      width: 90%;
      margin: 0 auto;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      overflow-y: auto; /* Add vertical scroll bar */
      max-height: 80vh; /* Set maximum height to 80% of viewport height */
    }

    h1 {
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }

    p.error-message {
      color: red;
      font-weight: bold;
      text-align: center;
    }

    .top-bar a {
      color: #fff;
      text-decoration: none;
      margin: 0 20px;
    }

    .action-buttons {
      display: flex;
      justify-content: center;
    }

    .enable-button,
    .disable-button {
      margin: 0 5px;
      padding: 5px 10px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
    }

    .enable-button {
      background-color: green;
      color: white;
    }

    .disable-button {
      background-color: red;
      color: white;
    }
  </style>
</head>

<body>
<div class="top-bar">
<a href="admin_homepage.php">Home Page</a>
 <a href="admin_borrowing_school.php">Borrowings per School</a>
 <a href="admin_per_field.php">Search per Field</a>
 <a href="admin_young_teachers.php">Young Teachers</a>
 <a href="admin_writer_not_borrowed.php">Unlucky Writers</a>
 <a href="admin_writer_5less.php">Writers with Fewer Books</a>
 <a href="admin_backup.php">Backup</a>
 <a href="admin_add_school.php">Add School</a>
 <a href="admin_user_status.php">Controller Status</a>
</div>

  <div class="container">
    <h1>Users List</h1>

    <?php if (isset($error)): ?>
      <p class="error-message"><?php echo $error; ?></p>
    <?php elseif (isset($users)): ?>
      <table>
        <thead>
          <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>School ID</th>
            <th>Role ID</th>
            <th>Status</th>
            <th>Disable User</th>
            <th>Enable User</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo $user['user_id']; ?></td>
              <td><?php echo $user['username']; ?></td>
              <td><?php echo $user['first_name']; ?></td>
              <td><?php echo $user['last_name']; ?></td>
              <td><?php echo $user['school_id']; ?></td>
              <td><?php echo $user['role_id']; ?></td>
              <td><?php echo $user['status']; ?></td>
              <td>
                <form method="POST" action="admin_user_status.php">
                  <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                  <button type="submit" class="disable-button">Disable</button>
                </form>
              </td>
              <td>
              <form method="POST" action="admin_user_status.php">
                  <input type="hidden" name="user_id2" value="<?php echo $user['user_id']; ?>">
                  <button type="submit" class="enable-button">Enable</button>
                </form>
          </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</body>

</html>
