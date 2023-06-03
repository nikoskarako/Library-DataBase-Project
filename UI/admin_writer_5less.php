<?php
// Assuming you have already established a database connection
$connection = mysqli_connect('localhost', 'root', 'root', 'library');

// Function to retrieve the writer with the most books
function findWriterWithMostBooks($connection) {
  $query = "SELECT COUNT(*) AS max_books
            FROM writer AS w
            JOIN book AS b ON w.isbn = b.isbn
            GROUP BY w.last_name, w.first_name
            ORDER BY max_books DESC
            LIMIT 1";

  $result = mysqli_query($connection, $query);

  // Check if the query was successful and at least one row was returned
  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $maxBooks = $row['max_books'];
  } else {
    $maxBooks = 0; // Default value if no data found
  }

  // Close the result
  mysqli_free_result($result);

  return $maxBooks;
}

// Retrieve the writer with the most books
$maxBooks = findWriterWithMostBooks($connection);

// Function to retrieve writers who have written at least 5 fewer books than the writer with the most books
function getWritersWithFewerBooks($connection, $maxBooks) {
  $query = "SELECT w.last_name, w.first_name, COUNT(*) AS num_books
            FROM writer AS w
            JOIN book AS b ON w.isbn = b.isbn
            GROUP BY w.last_name, w.first_name
            HAVING COUNT(*) <= ($maxBooks - 5) AND COUNT(*) > 0
            ORDER BY w.last_name, w.first_name";

  $result = mysqli_query($connection, $query);

  // Check if the query was successful and at least one row was returned
  if ($result && mysqli_num_rows($result) > 0) {
    $writers = mysqli_fetch_all($result, MYSQLI_ASSOC);
  } else {
    $writers = []; // Empty array if no data found
  }

  // Close the result
  mysqli_free_result($result);

  return $writers;
}

// Retrieve writers who have written at least 5 fewer books than the writer with the most books
$writers = getWritersWithFewerBooks($connection, $maxBooks);

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Library - Writers with Fewer Books</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      background-image: url('24manguel-superJumbo.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
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
    <h1>Writers with Fewer Books</h1>

    <?php if (empty($writers)) : ?>
      <p>No writers found.</p>
    <?php else : ?>
      <table>
        <tr>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Number of Books</th>
        </tr>
        <?php foreach ($writers as $writer) : ?>
          <tr>
            <td><?php echo $writer['last_name']; ?></td>
            <td><?php echo $writer['first_name']; ?></td>
            <td><?php echo $writer['num_books']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>
