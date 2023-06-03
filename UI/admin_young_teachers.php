<?php
// Assuming you have already established a database connection
$connection = mysqli_connect('localhost', 'root', 'root', 'library');

// Function to retrieve users with role_id=3 who are younger than 40 and their borrowed book count
function getUsersYoungerThan40($connection) {
  $query = "SELECT user.username, COUNT(borrowing.isbn) AS borrowed_books
            FROM user
            INNER JOIN borrowing ON user.user_id = borrowing.user_id
            WHERE user.role_id = 3 AND TIMESTAMPDIFF(YEAR, user.date_of_birth, CURDATE()) < 40
            GROUP BY user.user_id
            ORDER BY borrowed_books DESC";

  $result = mysqli_query($connection, $query);

  // Check if the query was successful and at least one row was returned
  if ($result && mysqli_num_rows($result) > 0) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  } else {
    $users = []; // Empty array if no data found
  }

  // Close the result
  mysqli_free_result($result);

  return $users;
}

// Retrieve users younger than 40 with role_id=3 and their borrowed book count
$users = getUsersYoungerThan40($connection);

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Library - Users Younger than 40</title>
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
      width: 100%;
      margin: 20px auto;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
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
  <h1>Users Younger than 40</h1>

  <?php if (empty($users)) : ?>
    <p>No users found.</p>
  <?php else : ?>
    <table>
      <tr>
        <th>Username</th>
        <th>Borrowed Books</th>
      </tr>
      <?php foreach ($users as $user) : ?>
        <tr>
          <td><?php echo $user['username']; ?></td>
          <td><?php echo $user['borrowed_books']; ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>
</body>

</html>
