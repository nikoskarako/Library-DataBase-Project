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

// Assuming you have established a database connection already
// Replace DB_HOST, DB_USERNAME, DB_PASSWORD, and DB_NAME with your actual database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'library';
$db_port = 8889;

// Set initial error and book titles variables
$error = '';
$book_titles = [];
$book_titles2 = [];
$book_titles3 = [];
$book_titles4 = [];

try {
  $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port";
  $conn = new PDO($dsn, $db_user, $db_password);

  // Retrieve the school ID of the logged-in user
  $query = "SELECT school_id FROM user WHERE username = ?";
  $stmt = $conn->prepare($query);
  $stmt->execute([$username]);

  if ($stmt->rowCount() === 1) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $school_id = $user['school_id'];

    if (isset($_POST['search_days_passed'])) {
      $search_days_passed = $_POST['search_days_passed'];
  
      // Retrieve borrowings that have been placed that many days before
      $query3 = "SELECT b.user_id, u.first_name, u.last_name, b.isbn, b.date_of_borrowing
                 FROM borrowing b
                 JOIN user u ON u.user_id = b.user_id
                 WHERE b.borrow_duration = -1
                       AND u.school_id = $school_id
                       AND DATEDIFF(CURDATE(), b.date_of_borrowing) = 7 + ?";
      $stmt3 = $conn->prepare($query3);
      $stmt3->bindParam(':search_days_passed', $search_days_passed, PDO::PARAM_INT);
      $stmt3->execute([$search_days_passed]);
  
      $late_borrowings_by_days = $stmt3->fetchAll(PDO::FETCH_ASSOC);
  
      if (empty($late_borrowings_by_days)) {
          $error3 = "Unfortunately, there are no borrowings matching your search.";
      } else {
          // Handle the fetched data
      }
  }

    if (isset($_POST['search_first_name'])) {
        $search_first_name = $_POST['search_first_name'];
      
        // Retrieve user_id based on the first_name
        $user_query = "SELECT user_id FROM user WHERE first_name = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->execute([$search_first_name]);
        $user_id = $user_stmt->fetchColumn();
      
        if ($user_id) {
          // Retrieve borrowings with borrow_duration = -1 for the specific user_id
          $query = "SELECT b.user_id, u.first_name, b.isbn, b.date_of_borrowing
                    FROM borrowing b
                    JOIN user u ON b.user_id = u.user_id
                    WHERE b.user_id = ? AND u.school_id = $school_id AND b.borrow_duration = -1 AND b.status = 'returned'";
          $stmt = $conn->prepare($query);
          $stmt->execute([$user_id]);
      
          $late_borrowings_by_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
          if (empty($late_borrowings_by_name)) {
            $error = "Unfortunately, there are no borrowings matching your search.";
          }
        } else {
          $error = "User not found.";
        }
      }
      

      if (isset($_POST['search_last_name'])) {
        $search_last_name = $_POST['search_last_name'];
      
        // Retrieve user_id based on the last_name
        $user_query2 = "SELECT user_id FROM user WHERE last_name = ?";
        $user_stmt2 = $conn->prepare($user_query2);
        $user_stmt2->execute([$search_last_name]);
        $user_id2 = $user_stmt2->fetchColumn();
      
        if ($user_id2) {
          // Retrieve borrowings with borrow_duration = -1 for the specific user_id
          $query2 = "SELECT b.user_id, u.first_name, u.last_name, b.isbn, b.date_of_borrowing
                    FROM borrowing b
                    JOIN user u ON b.user_id = u.user_id
                    WHERE u.user_id = ? AND u.school_id = $school_id 
                    AND b.borrow_duration = -1 AND b.status != 'returned'";
          $stmt2 = $conn->prepare($query2);
          $stmt2->execute([$user_id2]);
      
          $late_borrowings_by_surname = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      
          if (empty($late_borrowings_by_surname)) {
            $error2 = "Unfortunately, there are no borrowings matching your search.";
          }
        } else {
          $error2 = "User not found.";
        }
      }

      
    

    // Close the database connection
    $conn = null;
  } else {
    $error = "User not found.";
  }
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  exit();
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Library Search</title>
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
      /* Apply blur effect */
    }

    .top-bar a {
      color: #fff;
      text-decoration: none;
      margin: 0 20px;
      /* Increase spacing between buttons */
    }

    .container {
      width: 45%;
      margin: 0 auto;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      text-align: center;
      overflow: hidden;
      /* Add overflow property to contain book list */
      display: inline-block;
      vertical-align: top;
      margin-bottom: 20px;
    }

    .container:nth-child(even) {
      margin-left: 20px;
      margin-right: 0;
    }

    .container:nth-child(odd) {
      margin-right: 20px;
      margin-left: 0;
    }

    .container h2 {
      margin-bottom: 20px;
    }

    .search-box {
      width: 100%;
      margin-bottom: 20px;
      padding: 10px;
      border-radius: 3px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    .search-button {
      background-color: #2a65a4;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 3px;
      cursor: pointer;
      font-size: 16px;
    }

    .search-button:hover {
      background-color: #184166;
    }

    .book-list-container {
      max-height: 200px;
      overflow-y: auto;
      text-align: left;
      margin-top: 20px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      background-color: #fff;
    }

    .book-list {
      counter-reset: book-counter;
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    .book-list li {
      position: relative;
      margin-bottom: 10px;
      padding-left: 25px;
    }

    .book-list li::before {
      content: counter(book-counter);
      counter-increment: book-counter;
      position: absolute;
      left: 0;
      top: 0;
      color: #2a65a4;
      font-weight: bold;
    }

    .book-list li:not(:last-child) {
      border-bottom: 1px solid #ccc;
    }

    .error-message {
      color: red;
      margin-bottom: 10px;
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
    <a href="controller_check_expired.php">Expired Borrowings</a> 
    <a href="controller_add_book.php">Add Book</a> 
    <a href="controller_alter_book.php">Alter Book</a> 
    <a href="controller_user_status.php">User Status</a>
  </div>


  <div class="container">
  <h2>Expired Borrowings By Days Expired</h2>
  <form method="POST" action="controller_check_expired.php">
    <input type="number" name="search_days_passed" class="search-box" placeholder="Enter days of expiration">
    <button type="submit" class="search-button">Search</button>
  </form>
  <?php if (!empty($error3)): ?>
    <p class="error-message">
      <?php echo $error3; ?>
    </p>
  <?php else: ?>
    <div class="book-list-container">
      <h3>Expired Borrowings</h3>
      <ul class="book-list">
        <?php foreach ($late_borrowings_by_days as $borrowing3): ?>
          <li>
            User ID: <?php echo $borrowing3['user_id']; ?>,
            Name: <?php echo $borrowing3['first_name']; ?>,
            Surname: <?php echo $borrowing3['last_name']; ?>
            ISBN: <?php echo $borrowing3['isbn']; ?>,
            Date of Borrowing: <?php echo $borrowing3['date_of_borrowing']; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>

<div class="container">
  <h2>Expired Borrowing by Name</h2>
  <form method="POST" action="controller_check_expired.php">
    <input type="text" name="search_first_name" class="search-box" placeholder="Enter Name of User">
    <button type="submit" class="search-button">Search</button>
  </form>
  <?php if (!empty($error)): ?>
    <p class="error-message">
      <?php echo $error; ?>
    </p>
  <?php elseif (!empty($late_borrowings_by_name)): ?>
    <div class="book-list-container">
      <h3>Expired Borrowings</h3>
      <ul class="book-list">
        <?php foreach ($late_borrowings_by_name as $borrowing): ?>
          <li>
            User ID: <?php echo $borrowing['user_id']; ?>,
            Name: <?php echo $borrowing['first_name']; ?>,
            ISBN: <?php echo $borrowing['isbn']; ?>,
            Date of Borrowing: <?php echo $borrowing['date_of_borrowing']; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>

<div class="container">
  <h2>Expired Borrowing by Surname</h2>
  <form method="POST" action="controller_check_expired.php">
    <input type="text" name="search_last_name" class="search-box" placeholder="Enter Surname of User">
    <button type="submit" class="search-button">Search</button>
  </form>
  <?php if (!empty($error2)): ?>
    <p class="error-message">
      <?php echo $error2; ?>
    </p>
  <?php elseif (!empty($late_borrowings_by_surname)): ?>
    <div class="book-list-container">
      <h3>Expired Borrowings</h3>
      <ul class="book-list">
        <?php foreach ($late_borrowings_by_surname as $borrowing2): ?>
          <li>
            User ID: <?php echo $borrowing2['user_id']; ?>,
            Surname: <?php echo $borrowing2['last_name']; ?>,
            ISBN: <?php echo $borrowing2['isbn']; ?>,
            Date of Borrowing: <?php echo $borrowing2['date_of_borrowing']; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>
        </body>
      </html>