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

session_start();

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port";
    $conn = new PDO($dsn, $db_user, $db_password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['username'])) {
        // Username not found in the session
        $error = "Username not found in the session. Please try again!";
    } else {
        // Fetch the user's school ID from the user table based on the username stored in the session
        $username = $_SESSION['username'];
        $query = "SELECT school_id FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // User not found in the user table
            $error = "User not found. Please try again!";
        } else {
            $userSchoolID = $user['school_id'];

            // Fetch the books that belong to the user's school
            $query = "SELECT * FROM book WHERE school_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$userSchoolID]);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if the form is submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Get form data
                $isbn = $_POST['isbn'];

                // Prepare and execute the query to fetch the existing book data
                $query = "SELECT * FROM book WHERE isbn = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$isbn]);
                $existingBook = $stmt->fetch(PDO::FETCH_ASSOC);

                // Get the submitted form data
                $bookData = $_POST;

                // Remove unnecessary fields from the form data
                unset($bookData['isbn']);
                unset($bookData['submit']);

                // Iterate through each form field and check if it's empty
                // If empty, use the existing value from the database
                foreach ($bookData as $field => $value) {
                    if (empty($value)) {
                        $bookData[$field] = $existingBook[$field];
                    }
                }

                // Generate the SET clause for the update query
                $setClause = '';
                $values = [];
                foreach ($bookData as $field => $value) {
                    $setClause .= "$field = ?, ";
                    $values[] = $value;
                }
                $setClause = rtrim($setClause, ', ');

                // Prepare and execute the query to update the book
                $query = "UPDATE book SET $setClause WHERE isbn = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute(array_merge($values, [$isbn]));

                // Check if any book is successfully updated
                if ($stmt->rowCount() > 0) {
                    // Book updated successfully
                    $success_message = "Book updated successfully.";
                } else {
                    // No books found with the given ISBN
                    $error = "No books found with the given ISBN.";
                }
            }
        }
    }

    // Close the database connection
    $conn = null;
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Book</title>
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
            width: 80%;
            margin: 80px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        .contact-info {
            text-align: left;
            margin-bottom: 20px;
        }

        .contact-info p {
            margin: 5px 0;
        }

        .success-message {
            color: green;
            font-weight: bold;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-top: 10px;
            text-align: left;
            width: 100%;
            max-width: 300px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
        }

        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
        }

        input[type="submit"] {
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
    <h2>Update Book</h2>
    <?php if (!empty($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($books)): ?>
        <form method="POST" action="">
            <label for="isbn">Select ISBN:</label>
            <select name="isbn" id="isbn">
                <?php foreach ($books as $book): ?>
                    <option value="<?php echo $book['isbn']; ?>"><?php echo $book['isbn']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="page_no">Page No:</label>
            <input type="text" name="page_no" id="page_no">
            <label for="abstract">Abstract:</label>
            <textarea name="abstract" id="abstract"></textarea>
            <label for="available_copies">Available Copies:</label>
            <input type="text" name="available_copies" id="available_copies">
            <label for="language">Language:</label>
            <input type="text" name="language" id="language">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title">
            <label for="publisher">Publisher:</label>
            <input type="text" name="publisher" id="publisher">
            <label for="school_id">School ID:</label>
            <input type="text" name="school_id" id="school_id" value="<?php echo $userSchoolID; ?>" readonly>
            <input type="submit" name="submit" value="Update">
        </form>
    <?php endif; ?>
    <?php if (isset($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php endif; ?>
</div>
</body>
</html>
