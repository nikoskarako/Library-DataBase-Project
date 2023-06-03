<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect the user to the login page
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

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port";
    $conn = new PDO($dsn, $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve the school information based on the user's school_id
    // Join the user and school tables based on the school_id foreign key
    $query = "SELECT * FROM school WHERE school_id = (SELECT school_id FROM user WHERE username = :username)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $_SESSION['username']);
    $stmt->execute();
    $schoolInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Close the database connection
    $conn = null;
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>School Communication</title>
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
    </style>
</head>
<body>
<div class="top-bar">
    <a href="teacher_homepage.php">Home Page</a> 
    <a href="teacher_CommunicationInfo.php">Communication Info</a>
    <a href="teacher_myaccount.php">My Account</a>
    <a href="teacher_search.php">Search</a>
    <a href="teacher_borrowing.php">Borrow</a>
    <a href="teacher_reservation.php">Make a Reservation</a>
    <a href="teacher_review.php">Review</a>
    <a href="teacher_info.php">My Info</a>
  </div>

    <div class="container">
        <h2>School Information</h2>
        <div class="contact-info">
            <?php
            if ($schoolInfo) {
                // Display the school information
                echo "<p><strong>Email:</strong> " . $schoolInfo['email'] . "</p>";
                echo "<p><strong>School Name:</strong> " . $schoolInfo['school_name'] . "</p>";
                echo "<p><strong>Mail Address:</strong> " . $schoolInfo['mail_address'] . "</p>";
                echo "<p><strong>City:</strong> " . $schoolInfo['city'] . "</p>";
                echo "<p><strong>Phone:</strong> " . $schoolInfo['phone'] . "</p>";
                echo "<p><strong>Principal Name:</strong> " . $schoolInfo['principal_name'] . "</p>";
            } else {
                echo "<p>No school information found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>