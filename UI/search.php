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

    if (isset($_POST['search_title'])) {
      $search_title = $_POST['search_title'];

      // Retrieve book titles based on the school ID and search title
      $query = "SELECT title FROM book WHERE school_id = ? AND title LIKE ?";
      $stmt = $conn->prepare($query);
      $stmt->execute([$school_id, "%$search_title%"]);

      $book_titles = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if (empty($book_titles)) {
        $error = "Unfortunately, we don't have that book.";
      }
    }

    if (isset($_POST['search_writer'])) {
      $search_writer = $_POST['search_writer'];

      // Retrieve book titles based on the school ID and search writer
      $query2 = "SELECT DISTINCT b.title, w.last_name
      FROM book b
      JOIN writer w ON b.isbn = w.isbn
      WHERE school_id = ? AND w.last_name LIKE ?";
      
      $stmt2 = $conn->prepare($query2);
      $stmt2->execute([$school_id, "%$search_writer%"]);

      $book_titles2 = $stmt2->fetchAll(PDO::FETCH_COLUMN);

      if (empty($book_titles2)) {
        $error2 = "Unfortunately, we don't have books of this writer.";
      }
    }

    if (isset($_POST['search_field'])) {
      $search_field = $_POST['search_field'];

      // Retrieve book titles based on the school ID and search field
      $query3 = "SELECT DISTINCT b.title
      FROM book b
      JOIN field w ON b.isbn = w.isbn
      WHERE school_id = ? AND w.field_name LIKE ?";
      $stmt3 = $conn->prepare($query3);
      $stmt3->execute([$school_id, "%$search_field%"]);

      $book_titles3 = $stmt3->fetchAll(PDO::FETCH_COLUMN);

      if (empty($book_titles3)) {
        $error3 = "Unfortunately, we don't have books in this field.";
      }
    }

    if (isset($_POST['search_copies'])) {
      $search_copies = $_POST['search_copies'];

      // Retrieve book titles based on the school ID and search copies
      $query4 = "SELECT title FROM book WHERE school_id = ? AND available_copies = ?";
      $stmt4 = $conn->prepare($query4);
      $stmt4->execute([$school_id, $search_copies]);

      $book_titles4 = $stmt4->fetchAll(PDO::FETCH_COLUMN);

      if (empty($book_titles4)) {
        $error4 = "Unfortunately, we don't have books with this number of copies.";
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
    <a href="homepage.php">Home Page</a>
    <a href="CommunicationInfo.php">Communication Info</a>
    <a href="myaccount.php">My Account</a>
    <a href="search.php">Search</a>
    <a href="borrowing.php">Borrow</a>
    <a href="reservation.php">Make a Reservation</a>
    <a href="review.php">Review</a>
  </div>

  <div class="container">
    <h2>Search Books by Title</h2>
    <form method="POST" action="search.php">
      <input type="text" name="search_title" class="search-box" placeholder="Enter a book title">
      <button type="submit" class="search-button">Search</button>
    </form>
    <?php if (!empty($error)): ?>
      <p class="error-message">
        <?php echo $error; ?>
      </p>
    <?php else: ?>
      <div class="book-list-container">
        <h3>Title</h3>
        <ul class="book-list">
          <?php foreach ($book_titles as $title): ?>
            <li>
              <?php echo $title; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>

  <div class="container">
    <h2>Search Books by Writer</h2>
    <form method="POST" action="search.php">
      <input type="text" name="search_writer" class="search-box" placeholder="Enter a book writer">
      <button type="submit" class="search-button">Search</button>
    </form>
    <?php if (!empty($error2)): ?>
      <p class="error-message">
        <?php echo $error2; ?>
      </p>
    <?php else: ?>
      <div class="book-list-container">
        <h3>Writer</h3>
        <ul class="book-list">
          <?php foreach ($book_titles2 as $title): ?>
            <li>
              <?php echo $title; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>

  <div class="container">
    <h2>Search Books by Field</h2>
    <form method="POST" action="search.php">
      <input type="text" name="search_field" class="search-box" placeholder="Enter a book field">
      <button type="submit" class="search-button">Search</button>
    </form>
    <?php if (!empty($error3)): ?>
      <p class="error-message">
        <?php echo $error3; ?>
      </p>
    <?php else: ?>
      <div class="book-list-container">
        <h3>Field</h3>
        <ul class="book-list">
          <?php foreach ($book_titles3 as $title): ?>
            <li>
              <?php echo $title; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>

  <div class="container">
    <h2>Search Books by Copies</h2>
    <form method="POST" action="search.php">
      <input type="text" name="search_copies" class="search-box" placeholder="Enter number of copies">
      <button type="submit" class="search-button">Search</button>
    </form>
    <?php if (!empty($error4)): ?>
      <p class="error-message">
        <?php echo $error4; ?>
      </p>
    <?php else: ?>
      <div class="book-list-container">
        <h3>Copies</h3>
        <ul class="book-list">
          <?php foreach ($book_titles4 as $title): ?>
            <li>
              <?php echo $title; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>