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

// Retrieve the username from the session
$username = $_SESSION['username'];

// Retrieve the user's school ID from the database
$query = "SELECT school_id FROM user WHERE username = '$username'";
$result = mysqli_query($connection, $query);

// Check if the query was successful and a row was returned
if ($result && mysqli_num_rows($result) > 0) {
  // Fetch the user's school ID from the result
  $row = mysqli_fetch_assoc($result);
  $school_id = $row['school_id'];

  // Query to fetch all borrowings by users with the same school ID
  $borrowingsQuery = "SELECT b.* 
                    FROM borrowing b
                    JOIN user u ON u.user_id = b.user_id
                    WHERE u.school_id = '$school_id'";
  $borrowingsResult = mysqli_query($connection, $borrowingsQuery);

  // Check if the query was successful and rows were returned
  if ($borrowingsResult && mysqli_num_rows($borrowingsResult) > 0) {
    // Fetch all the borrowings
    while ($row = mysqli_fetch_assoc($borrowingsResult)) {
      $borrowings[] = $row;
    }
  } else {
    // No borrowings found
    $error = "No borrowings found.";
  }

  // Close the borrowings result
  mysqli_free_result($borrowingsResult);

  // Close the result
  mysqli_free_result($result);
} else {
  // User not found or school ID not available
  // Handle the error or display an appropriate message
  echo "User not found or school ID not available.";
}

// Check if the form was submitted and the enableButton was clicked
if (isset($_POST['enableButton'])) {
  // Retrieve the borrowing ID, ISBN, and date of borrowing from the form submission
  $borrowingId = $_POST['borrowingId'];
  $isbn = $_POST['isbn'];
  $dateOfBorrowing = $_POST['dateOfBorrowing'];

  // Update the activation value of the specific borrowing
  $updateQuery = "UPDATE borrowing SET activation = 'enabled' WHERE user_id = '$borrowingId' AND isbn = '$isbn' AND date_of_borrowing = '$dateOfBorrowing'";
  $updateResult = mysqli_query($connection, $updateQuery);

  // Check if the update query was successful
  if ($updateResult) {
    $success = "Borrowing enabled successfully.";

    // Refresh the page after updating
    echo '<script type="text/javascript">
            window.location.href = "controller_borrowing.php";
          </script>';
  } else {
    $error = "Failed to enable borrowing.";
  }
}

// Close the database connection
mysqli_close($connection);
?>


<!DOCTYPE html>
<html>

<head>
  <title>Borrowings List</title>
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

    p.success-message {
      color: green;
      font-weight: bold;
      text-align: center;
    }

    .enable-button {
      background-color: #4287f5;
      color: #fff;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="top-bar">
    <a href="controller_homepage.php">Home Page</a>
    <a href="controller_CommunicationInfo.php">Communication Info</a>
    <a href="controller_myaccount.php">My Account</a>
    <a href="controller_search.php">Search</a>
    <a href="controller_borrowing.php">Borrow</a>
    <a href="controller_reservation.php">Make a Reservation</a>
    <a href="controller_review.php">Reviews</a>
    <a href="controller_check_expired.php">Expired Borrowings</a> 
    <a href="controller_add_book.php">Add Book</a> 
    <a href="controller_alter_book.php">Alter Book</a> 
    <a href="controller_user_status.php">User Status</a>
  </div>
  <div class="container">
    <h1>Borrowings List</h1>

    <?php if (isset($error)): ?>
      <p class="error-message"><?php echo $error; ?></p>
    <?php elseif (isset($borrowings)): ?>
      <?php if (isset($success)): ?>
        <p class="success-message"><?php echo $success; ?></p>
      <?php endif; ?>
      <table>
        <thead>
          <tr>
            <th>User ID</th>
            <th>Date of Borrowing</th>
            <th>ISBN</th>
            <th>Last Update</th>
            <th>Borrow Duration</th>
            <th>Status</th>
            <th>Activation</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($borrowings as $borrowing): ?>
            <tr>
              <td><?php echo $borrowing['user_id']; ?></td>
              <td><?php echo $borrowing['date_of_borrowing']; ?></td>
              <td><?php echo $borrowing['isbn']; ?></td>
              <td><?php echo $borrowing['last_update']; ?></td>
              <td><?php echo $borrowing['borrow_duration']; ?></td>
              <td><?php echo $borrowing['status']; ?></td>
              <td><?php echo $borrowing['activation']; ?></td>
              <td>
                <form method="post">
                  <input type="hidden" name="borrowingId" value="<?php echo $borrowing['user_id']; ?>">
                  <input type="hidden" name="isbn" value="<?php echo $borrowing['isbn']; ?>">
                  <input type="hidden" name="dateOfBorrowing" value="<?php echo $borrowing['date_of_borrowing']; ?>">
                  <button class="enable-button" type="submit" name="enableButton">Enable</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- JavaScript snippet to refresh the page after enabling borrowing -->
  <script type="text/javascript">
    if (typeof window.history.pushState == 'function') {
      window.history.pushState({}, "Hide", '<?php echo $_SERVER['PHP_SELF'];?>');
    }
  </script>
</body>

</html>