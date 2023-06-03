<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
  // The username is set and not empty
  $username = $_SESSION['username'];
} else {
  // Redirect to the login page if the user is not logged in
  header("Location: login.php");
  exit();
}

?>



<!DOCTYPE html>
<html>
<head>
  <title>Library Home</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      background-image: url('24manguel-superJumbo.jpg');
      background-repeat: no-repeat;
      background-size: cover;
    }
    .top-bar {
      background-color: #2a65a469;
      padding: 10px;
      text-align: center;
      backdrop-filter: blur(5px); /* Apply blur effect */
    }
    .top-bar a {
      color: #fff;
      text-decoration: none;
      margin: 0 20px; /* Increase spacing between buttons */
    }
    .container {
      max-width: 600px;
      width: 500px;
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
    }
    h2 {
      margin-bottom: 20px;
    }
    .button {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 3px;
      text-decoration: none;
      cursor: pointer;
    }
    .button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
 <div class="top-bar">
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
    <h2>Welcome Boss! </h2>
    <p>Get to work</p>
  </div>
</body>
</html>