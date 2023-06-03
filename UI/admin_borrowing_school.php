<?php
// Assuming you have already established a database connection
$connection = mysqli_connect('localhost', 'root', 'root', 'library');

// Function to generate the WHERE clause based on the desired year or month
function generateWhereClause($filterType, $filterValue) {
  $whereClause = '';

  if ($filterType === 'year') {
    $whereClause = "WHERE YEAR(borrowing.date_of_borrowing) = " . intval($filterValue);
  } elseif ($filterType === 'month') {
    $whereClause = "WHERE MONTH(borrowing.date_of_borrowing) = " . intval($filterValue);
  }

  return $whereClause;
}

// Get the filter type and value from the query parameters (e.g., ?filter_type=year&filter_value=2023)
$filterType = $_GET['filter_type'] ?? '';
$filterValue = $_GET['filter_value'] ?? '';

// Generate the WHERE clause based on the filter type and value
$whereClause = generateWhereClause($filterType, $filterValue);

// Query to retrieve the number of borrowings per school with the specified filter
$query = "SELECT school.school_name, COUNT(*) AS borrowing_count
          FROM school
          INNER JOIN user ON school.school_id = user.school_id
          INNER JOIN borrowing ON user.user_id = borrowing.user_id
          {$whereClause}
          GROUP BY school.school_id";

$result = mysqli_query($connection, $query);

// Check if the query was successful and at least one row was returned
if ($result && mysqli_num_rows($result) > 0) {
  $borrowingsPerSchool = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
  $borrowingsPerSchool = []; // Empty array if no data found
}

// Close the result
mysqli_free_result($result);

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Library - Borrowings per School</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-image: url('24manguel-superJumbo.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      margin: 0;
      padding: 0;
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
 <a href="admin_backup.php">Backup</a>
 <a href="admin_add_school.php">Add School</a>
 <a href="admin_user_status.php">Controller Status</a>
  </div>

  <div class="container">
    <h1>Borrowings per School</h1>

    <form method="get" action="">
      <label for="filter_type">Filter by:</label>
      <select name="filter_type" id="filter_type">
        <option value="year">Year</option>
        <option value="month">Month</option>
      </select>

      <input type="text" name="filter_value" id="filter_value" placeholder="Enter the year or month">

      <input type="submit" value="Apply Filter">
    </form>

    <?php if (empty($borrowingsPerSchool)) : ?>
      <p>No data available.</p>
    <?php else : ?>
      <table>
        <tr>
          <th>School Name</th>
          <th>Borrowing Count</th>
        </tr>
        <?php foreach ($borrowingsPerSchool as $borrowing) : ?>
          <tr>
            <td><?php echo $borrowing['school_name']; ?></td>
            <td><?php echo $borrowing['borrowing_count']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>
