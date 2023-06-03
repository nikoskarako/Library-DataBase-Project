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

// Establish database connection
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'library';
$db_port = 8889;

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port";
    $conn = new PDO($dsn, $db_user, $db_password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the user's ID from the database
    $query = "SELECT user_id FROM user WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_id = $user['user_id'];

    // Get the book's ISBN from the request
    $isbn = $_POST['isbn'];

    // Call the InsertBorrowing stored procedure
    $query = "CALL InsertBorrowing(:user_id, :isbn)";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
    $stmt->execute();

    // If the borrowing was successful, return a success message
    $response = [
        'status' => 'success',
        'message' => 'Book borrowed successfully!'
    ];
    echo json_encode($response);
} catch (PDOException $e) {
    // If an error occurred, return an error message
    $response = [
        'status' => 'error',
        'message' => 'An error occurred while borrowing the book.'
    ];
    echo json_encode($response);
}
