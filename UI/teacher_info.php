<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'library';
$db_user = 'root';
$db_pass = 'root';

try {
    // Establish the database connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// Database connection code here

// Assuming you have established a session and retrieved the username
session_start();

$error = '';
$success_message = '';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Retrieve the user's information based on the username stored in the session
$username = $_SESSION['username'];
$query = "SELECT * FROM `user` WHERE `username` = :username";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit;
}

// Update the session username if it's different from the user's current username
if ($user['username'] !== $username) {
    $_SESSION['username'] = $user['username'];
    $username = $user['username']; // Update the username variable
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = array();
    $newUsername = $_POST['username'];
    $password = $_POST['password'];
    $date_of_birth = $_POST['date_of_birth'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // Check if each field is set and not empty, then add it to the update data array
    if (isset($newUsername) && !empty($newUsername)) {
        $updateData['username'] = $newUsername;
    }
    if (isset($password) && !empty($password)) {
        $updateData['password'] = $password;
    }
    if (isset($date_of_birth) && !empty($date_of_birth)) {
        $updateData['date_of_birth'] = $date_of_birth;
    }
    if (isset($first_name) && !empty($first_name)) {
        $updateData['first_name'] = $first_name;
    }
    if (isset($last_name) && !empty($last_name)) {
        $updateData['last_name'] = $last_name;
    }

    if (empty($updateData)) {
        $error = 'Please provide at least one field to update.';
    } else {
        // Prepare the update query dynamically based on the fields to update
        $sql = "UPDATE `user` SET ";
        $values = array();
        foreach ($updateData as $field => $value) {
            $sql .= "`$field` = ?, ";
            $values[] = $value;
        }
        $sql = rtrim($sql, ', ');
        $sql .= " WHERE `user_id` = ?";
        $values[] = $user['user_id'];

        // Execute the update query
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($values)) {
            $success_message = 'Your information has been updated successfully.';
            // Refresh user information from the database
            $query = "SELECT * FROM `user` WHERE `user_id` = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':user_id', $user['user_id']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to update your information. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update Information</title>
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
            max-width: 600px;
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="top-bar">
        <a href="teacher_hompage.php">Home Page</a>
        <a href="CommunicationInfo.php">Communication Info</a>
        <a href="myaccount.php">My Account</a>
        <a href="search.php">Search</a>
        <a href="borrowing.php">Borrow</a>
        <a href="reservation.php">Make a Reservation</a>
        <a href="review.php">Review</a>
        <a href="teacher_info.php">My Info</a>
    </div>

    <div class="container">
        <h2>Update Information</h2>
        <?php if (!empty($error)): ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p>
                <?php echo $success_message; ?>
            </p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo $user['username']; ?>">

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" value="<?php echo $user['password']; ?>">

            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo $user['date_of_birth']; ?>">

            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" value="<?php echo $user['first_name']; ?>">

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" value="<?php echo $user['last_name']; ?>">

            <input type="submit" name="submit" value="Update">
        </form>
    </div>
</body>

</html>
