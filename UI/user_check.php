<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        // Get the username and password from the form
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare and execute the query
        $query = "SELECT * FROM user WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username, $password]);

        // Check if a matching user is found
        if ($stmt->rowCount() === 1) {
            // Username and password match
            // Find the role_id and status
            $query2 = "SELECT role_id, status FROM user WHERE username = ? AND password = ?";
            $stmt2 = $conn->prepare($query2);
            $stmt2->execute([$username, $password]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);

            $role_id = $row['role_id'];
            $status = $row['status'];

            if ($status === 'enabled') {
                // Redirect to the appropriate homepage based on role_id
                session_start();
                $_SESSION['username'] = $username;

                if ($role_id == 4) {
                    header("Location: homepage.php");
                    exit();
                } elseif($role_id == 3){
                  header("Location: teacher_homepage.php");
                } elseif ($role_id == 2) {
                    header("Location: controller_homepage.php");
                    exit();
                } elseif ($role_id == 1) {
                    header("Location: admin_homepage.php");
                    exit();
                }
            } else {
                // User's status is not enabled
                $error = "Your account is not enabled. Please contact the controller.";
            }
        } else {
            // Invalid username or password
            $error = "Invalid username or password. Try again!";
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
  <title>Library Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      background-image: url('24manguel-superJumbo.jpg');
      background-repeat: no-repeat;
      background-size: cover;
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
    }
    h2 {
      text-align: center;
    }
    .form-group {
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .form-group input[type="text"],
    .form-group input[type="password"] {
      width: 100%;
      padding: 10px;
      border-radius: 3px;
      border: 1px solid #ccc;
    }
    .form-group button {
      width: 100%;
      padding: 10px;
      border-radius: 3px;
      border: none;
      background-color: #007bff;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
    }
    .form-group button:hover {
      background-color: #0056b3;
    }
    .signup-link {
      text-align: center;
      margin-top: 10px;
    }
    .error-message {
      text-align: center;
      color: red;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <div class="error-message"><?php echo $error; ?></div>
    <form method="POST" action="user_check.php">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <button type="submit">Login</button>
      </div>
    </form>
    <div class="signup-link">
      Don't have an account? <a href="signup.php">Sign Up</a>
    </div>
  </div>
</body>
</html>
