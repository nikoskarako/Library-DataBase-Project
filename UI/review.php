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

// Retrieve the user's ID from the database
$query = "SELECT user_id FROM user WHERE username = '$username'";
$result = mysqli_query($connection, $query);

// Check if the query was successful and a row was returned
if ($result && mysqli_num_rows($result) > 0) {
  // Fetch the user's ID from the result
  $row = mysqli_fetch_assoc($result);
  $user_id = $row['user_id'];

  // Check if the form was submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'], $_POST['isbn'], $_POST['likert'])) {
    // Retrieve the form data
    $review_text = $_POST['review_text'];
    $isbn = $_POST['isbn'];
    $likert = $_POST['likert'];

    // Prepare the insert statement
    $insertReviewQuery = "INSERT INTO review (user_id, review_text, isbn, likert) VALUES (?, ?, ?, ?)";
    $statement = mysqli_prepare($connection, $insertReviewQuery);

    // Bind the parameters and execute the statement
    mysqli_stmt_bind_param($statement, "issi", $user_id, $review_text, $isbn, $likert);
    mysqli_stmt_execute($statement);

    // Check if the insertion was successful
    if (mysqli_stmt_affected_rows($statement) > 0) {
      $success = "Review sent!";
    } else {
      $error = "Failed to send review.";
    }

    // Close the statement
    mysqli_stmt_close($statement);
  }

  // Close the result
  mysqli_free_result($result);
} else {
  // User not found
  // Handle the error or display an appropriate message
  echo "User not found.";
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Library</title>
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

    h1 {
      color: #2a65a4;
      margin-bottom: 30px;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
      resize: vertical;
    }

    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
      appearance: none;
      background: url('arrow-down.png') no-repeat right center / 15px;
    }

    .btn {
      background-color: #2a65a4;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #1d4f7e;
    }

    .success {
      color: green;
      margin-bottom: 20px;
    }

    .error {
      color: red;
      margin-bottom: 20px;
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
    <h1>Write a Review</h1>

    <?php if (isset($success)) : ?>
      <p class="success"><?php echo $success; ?></p>
    <?php elseif (isset($error)) : ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
      <textarea name="review_text" placeholder="Write your review" required></textarea>
      <input type="text" name="isbn" placeholder="Book ISBN" required>
      <select name="likert" required>
        <option value="">Select your rating</option>
        <option value="1">1 - Poor</option>
        <option value="2">2 - Fair</option>
        <option value="3">3 - Good</option>
        <option value="4">4 - Very Good</option>
        <option value="5">5 - Excellent</option>
      </select>
      <button class="btn" type="submit" name="submit">Submit</button>
    </form>
  </div>
</body>

</html>
