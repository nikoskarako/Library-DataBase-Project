<?php
session_start();

// Assuming you have established a database connection already
// Replace DB_HOST, DB_USERNAME, DB_PASSWORD, and DB_NAME with your actual database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'library';
$db_port = 8889;

// Set initial response array
$response = array('success' => false);

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port";
    $conn = new PDO($dsn, $db_user, $db_password);

    // Retrieve the school ID of the logged-in user
    $username = $_SESSION['username'];

    // Retrieve the old password from the form data
    $oldPassword = $_POST['oldPassword'];

    // Retrieve the hashed password from the database
    $query = "SELECT password FROM user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username]);

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $user['password'];

        // Verify the old password
        if ($hashedPassword === $oldPassword) {
            $response['success'] = true;
        }
    }

    // Close the database connection
    $conn = null;
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = 'Connection failed: ' . $e->getMessage();
}

echo json_encode($response);
?>
