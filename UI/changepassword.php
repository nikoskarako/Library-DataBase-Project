<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have established a database connection already
    // Replace DB_HOST, DB_USERNAME, DB_PASSWORD, and DB_NAME with your actual database credentials
    $db_host = 'localhost';
    $db_user = 'root';
    $db_password = 'root';
    $db_name = 'library';
    $db_port = 8889;

    // Retrieve the logged-in user's username
    $username = $_SESSION['username'];

    // Retrieve the old and new passwords from the form data
    $oldPassword = $_POST['old-password'];
    $newPassword = $_POST['new-password'];

    // Validate the old password against the database
    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port";
        $conn = new PDO($dsn, $db_user, $db_password);

        // Retrieve the user's current password hash from the database
        $query = "SELECT password FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username]);

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $currentPasswordHash = $user['password'];

            // Verify the old password against the stored password hash
            if (password_verify($oldPassword, $currentPasswordHash)) {
                // Generate a new password hash
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the user's password in the database
                $query = "UPDATE user SET password = ? WHERE username = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$newPasswordHash, $username]);

                // Redirect the user to a success page or display a success message
                header("Location: passwordchanged.php");
                exit();
            } else {
                // Password verification failed
                $error = "Incorrect old password.";
            }
        } else {
            // User not found
            $error = "User not found.";
        }

        // Close the database connection
        $conn = null;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit();
    }
} else {
    // Redirect to the login page if the request is not a POST request
    header("Location: login.php");
    exit();
}
?>
