<?php
// Assuming you have already established a database connection
$connection = mysqli_connect('localhost', 'root', 'root', 'library');

// Function to retrieve writers of a specific field
function getWritersByField($field, $connection) {
  $field = mysqli_real_escape_string($connection, $field);

  $query = "SELECT w.last_name, w.first_name
            FROM writer w
            INNER JOIN field f ON w.isbn = f.isbn
            WHERE f.field_name = '$field'";

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

// Function to retrieve users with borrowed books from a specific field
function getUsersWithBorrowedBooksByField($field, $connection) {
    $field = mysqli_real_escape_string($connection, $field);
  
    $query = "SELECT DISTINCT u.username
              FROM user u
              INNER JOIN borrowing b ON u.user_id = b.user_id
              INNER JOIN book d ON b.isbn = d.isbn
              INNER JOIN field f ON f.isbn = d.isbn
              WHERE f.field_name = '$field' AND u.role_id = 3";
  
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
  
  
  
  

// Retrieve available field names from the field table
$query2 = "SELECT DISTINCT field_name FROM field";
$result2 = mysqli_query($connection, $query2);
$fieldNames = mysqli_fetch_all($result2, MYSQLI_ASSOC);

// Close the result
mysqli_free_result($result2);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Library - Writers by Field</title>
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

    form {
      margin-bottom: 20px;
      text-align: center;
    }

    label {
      font-weight: bold;
      margin-right: 10px;
    }

    select,
    input[type="text"],
    input[type="submit"] {
      padding: 5px;
      border-radius: 3px;
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
  <a href="admin_backup.php">Writers with Fewer Books</a>
  <a href="admin_user_status.php">Controller Status</a>
</div>

<div class="container">
  <h1>Writers by Field</h1>

  <form method="get" action="">
    <label for="field">Field:</label>
    <select name="field" id="field">
      <?php foreach ($fieldNames as $fieldName): ?>
        <option value="<?php echo $fieldName['field_name']; ?>"><?php echo $fieldName['field_name']; ?></option>
      <?php endforeach; ?>
    </select>
    <input type="submit" value="Search">
  </form>

  <?php
  if (isset($_GET['field'])) {
    $field = $_GET['field'];
    $writers = getWritersByField($field, $connection);

    if (empty($writers)) {
      echo "<p>No writers found for the selected field.</p>";
    } else {
      echo "<table>";
      echo "<tr><th>Last Name</th><th>First Name</th></tr>";
      foreach ($writers as $writer) {
        echo "<tr>";
        echo "<td>" . $writer['last_name'] . "</td>";
        echo "<td>" . $writer['first_name'] . "</td>";
        echo "</tr>";
      }
      echo "</table>";
    }
  }
  ?>
</div>

<div class="container">
  <h1>Users with Borrowed Books from a Field</h1>

  <form method="get" action="">
    <label for="field2">Field:</label>
    <select name="field2" id="field2">
      <?php foreach ($fieldNames as $fieldName): ?>
        <option value="<?php echo $fieldName['field_name']; ?>"><?php echo $fieldName['field_name']; ?></option>
      <?php endforeach; ?>
    </select>
    <input type="submit" value="Search">
  </form>

  <?php
  if (isset($_GET['field2'])) {
    $field = $_GET['field2'];
    $users = getUsersWithBorrowedBooksByField($field, $connection);

    if (empty($users)) {
      echo "<p>No users found with borrowed books from the selected field.</p>";
    } else {
      echo "<table>";
      echo "<tr><th>Username</th></tr>";
      foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['username'] . "</td>";
        echo "</tr>";
      }
      echo "</table>";
    }
  }
  ?>
</div>
</body>

</html>