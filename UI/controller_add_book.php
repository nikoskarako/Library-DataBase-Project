<?php
// Assuming you have established a database connection already
// Replace DB_HOST, DB_USERNAME, DB_PASSWORD, and DB_NAME with your actual database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'library';
$db_port = 8889;

// Set initial error message to empty
$error = '';

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port";
    $conn = new PDO($dsn, $db_user, $db_password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $isbn = $_POST['isbn'];
        $page_no = $_POST['page_no'];
        $abstract = $_POST['abstract'];
        $available_copies = $_POST['available_copies'];
        $language = $_POST['language'];
        $title = $_POST['title'];
        $publisher = $_POST['publisher'];
        $school_id = $_POST['school_id'];

        // Prepare and execute the query
        $query = "INSERT INTO book (isbn, page_no, abstract, available_copies, language, title, publisher, school_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$isbn, $page_no, $abstract, $available_copies, $language, $title, $publisher, $school_id]);

        // Check if the book is successfully inserted
        if ($stmt->rowCount() > 0) {
            // Book inserted successfully
            // Redirect to a success page or perform any other desired action
            header("Location: controller_add_book.php");
            exit();
        } else {
            // Failed to insert the book
            $error = "Failed to insert the book. Please try again!";
        }
    }

    // Fetch available schools
    $schoolQuery = "SELECT * FROM school";
    $schoolStmt = $conn->query($schoolQuery);
    $schools = $schoolStmt->fetchAll(PDO::FETCH_ASSOC);

    // Close the database connection
    $conn = null;
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <style>
        body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      background-image: url('24manguel-superJumbo.jpg');
      background-repeat: no-repeat;
      background-size: cover;
    }

        .top-bar {
            background-color: #2a65a4;
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
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        input[type="file"] {
            padding: 5px;
        }

        button[type="submit"] {
            padding: 10px;
            border-radius: 3px;
            border: none;
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            text-align: center;
            color: red;
            margin-top: 10px;
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
    <h2>Add Book</h2>

    <div class="error-message"><?php echo $error; ?></div>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" required>
        </div>

        <div class="form-group">
            <label for="page_no">Page Number:</label>
            <input type="number" id="page_no" name="page_no" min="1" max="5000" required>
        </div>

        <div class="form-group">
            <label for="abstract">Abstract:</label>
            <textarea id="abstract" name="abstract"></textarea>
        </div>

        <div class="form-group">
            <label for="available_copies">Available Copies:</label>
            <input type="number" id="available_copies" name="available_copies">
        </div>

        <div class="form-group">
            <label for="language">Language:</label>
            <input type="text" id="language" name="language" required>
        </div>

        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="publisher">Publisher:</label>
            <input type="text" id="publisher" name="publisher" required>
        </div>

        <div class="form-group">
            <label for="school_id">School:</label>
            <select id="school_id" name="school_id">
                <?php foreach ($schools as $school): ?>
                    <option value="<?php echo $school['school_id']; ?>"><?php echo $school['school_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" id="image" name="image">
        </div>

        <div class="form-group">
            <button type="submit">Add Book</button>
        </div>
    </form>
</div>
</body>
</html>
