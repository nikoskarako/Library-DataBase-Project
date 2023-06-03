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

  // Check if the form was submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchQuery'])) {
    // Retrieve the search query
    $searchQuery = $_POST['searchQuery'];

    // Perform the search query
    $searchQuery = mysqli_real_escape_string($connection, $searchQuery);
    $searchQuery = '%' . $searchQuery . '%'; // Add wildcards for partial matches
    $searchBooksQuery = "SELECT * FROM book WHERE title LIKE '$searchQuery' AND school_id = '$school_id'";
    $searchResult = mysqli_query($connection, $searchBooksQuery);

    // Check if the query was successful and rows were returned
    if ($searchResult && mysqli_num_rows($searchResult) > 0) {
      // Fetch all the search results
      while ($row = mysqli_fetch_assoc($searchResult)) {
        $searchResults[] = $row;
      }
    } else {
      // No books found
      $error = "No books found.";
    }

    // Close the search result
    mysqli_free_result($searchResult);
  }

  // Check if the book borrow form was submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrowBookTitle']) && isset($_POST['isbn'])) {
    // Retrieve the book details from the form
    $borrowBookTitle = $_POST['borrowBookTitle'];
    $isbn = $_POST['isbn'];

    // Retrieve the user_id from the database
    $userQuery = "SELECT user_id FROM user WHERE username = '$username'";
    $userResult = mysqli_query($connection, $userQuery);

    if ($userResult && mysqli_num_rows($userResult) > 0) {
      // Fetch the user_id
      $userRow = mysqli_fetch_assoc($userResult);
      $user_id = $userRow['user_id'];

      // Get the current date
      $today = date("Y-m-d");

      try {
        // Perform the borrowing action
        $borrowQuery = "CALL InsertBorrowing($user_id, '$today', '$isbn')";
        $borrowResult = mysqli_query($connection, $borrowQuery);

        // Check if the borrowing procedure was successful
        if ($borrowResult) {
          // Borrowing successful
          $success = "Book borrowed successfully!";
        } else {
          // Borrowing failed
          throw new Exception("Borrowing failed. Error: " . mysqli_error($connection));
        }
      } catch (Exception $e) {
        // Display the error message
        $error = "Error: " . $e->getMessage();
      }
    }

    // Close the user result
    mysqli_free_result($userResult);
  }
} else {
  // User not found in the database
  // Redirect or display an appropriate message
  header("Location: login.php");
  exit();
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Book Borrowing</title>
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
      backdrop-filter: blur(5px);
    }

    .top-bar a {
      color: #fff;
      text-decoration: none;
      margin: 0 20px;
    }

    .container {
      max-width: 600px;
      width: 500px;
      height: 400px;
      margin: 0 auto;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      overflow-y: scroll; /* Add scroll bar for vertical overflow */
    }

    h1 {
      text-align: center;
    }

    .search-form {
      margin-bottom: 20px;
    }

    .search-form input[type="text"] {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid #ccc;
      outline: none;
    }

    .search-form input[type="submit"] {
      display: none;
    }

    .search-results {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    .search-results li {
      background-color: #fff;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .search-results li:last-child {
      margin-bottom: 0;
    }

    .borrow-form {
      margin-top: 20px;
    }

    .borrow-form input[type="text"],
    .borrow-form input[type="submit"] {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid #ccc;
      outline: none;
    }

    .borrow-form .error-message {
      color: red;
      margin-bottom: 10px;
    }

    .borrow-form .success-message {
      color: green;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
<div class="top-bar">
    <a href="homepage.php">Home Page</a>
    <a href="CommunicationInfo.php">Communication Info</a>
    <a href="myaccount.php">My Account</a>
    <a href="search.php">Search</a>
    <a href="borrowing.php">Borrow</a>
    <a href="reservation.php">Make a Reservation</a>
    <a href="review.php">Review</a>
  </div>

  <div class="container">
    <h1>Book Borrowing</h1>

    <!-- Display success message if applicable -->
    <?php if (isset($success)) : ?>
      <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Display error message if applicable -->
    <?php if (isset($error)) : ?>
      <div class="error-message" style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Book Search Form -->
    <form class="search-form" method="POST">
      <input type="text" name="searchQuery" placeholder="Search for a book">
      <button type="submit">Search</button>
    </form>

    <!-- Display search results -->
    <?php if (isset($searchResults)) : ?>
      <h2>Search Results:</h2>
      <ul class="search-results">
        <?php foreach ($searchResults as $result) : ?>
          <li>
            <?php echo $result['title']; ?>
            <form class="borrow-form" method="POST">
              <input type="hidden" name="borrowBookTitle" value="<?php echo $result['title']; ?>">
              <input type="hidden" name="isbn" value="<?php echo $result['isbn']; ?>">
              <button type="submit">Borrow</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

</body>
</html>
