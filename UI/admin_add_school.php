<?php
session_start();

// Database configuration
$host = 'localhost';
$dbName = 'library';
$username = 'root';
$password = 'root';

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to add a school to the database
function addSchool($name, $mail, $city, $phone, $email, $principal)
{
    global $pdo;

    // Prepare the SQL statement
    $stmt = $pdo->prepare("INSERT INTO school (school_name, mail_address, city, phone, email, principal_name) 
                           VALUES (?, ?, ?, ?, ?, ?)");

    // Bind the parameters
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $mail);
    $stmt->bindParam(3, $city);
    $stmt->bindParam(4, $phone);
    $stmt->bindParam(5, $email);
    $stmt->bindParam(6, $principal);

    // Execute the statement
    $stmt->execute();
}

// Initialize success message variable
$successMessage = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $mail = $_POST['mail'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $principal = $_POST['principal'];

    // Call the addSchool function
    addSchool($name, $mail, $city, $phone, $email, $principal);

    // Set the success message
    $successMessage = 'School added successfully!';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add School</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            background-image: url('24manguel-superJumbo.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
        }
        
        input[type="submit"]:hover {
            background-color: #0056b3;
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
        <h1>Add School</h1>
        <?php if (!empty($successMessage)) { ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php } ?>
        <form method="post" action="">
            <label for="name">School Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="mail">Mailing Address:</label>
            <input type="text" id="mail" name="mail" required>

            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>

            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="principal">Principal Name:</label>
            <input type="text" id="principal" name="principal" required>

            <input type="submit" value="Add School">
        </form>
    </div>
</body>
</html>
