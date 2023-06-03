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
        // Get the form data
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $dob = $_POST['dob'];
        $school_number = $_POST['school_number'];
        $role = $_POST['role'];

        // Set the role value based on the selected option
        if ($role === 'student') {
            $role = 4;
        } elseif ($role === 'teacher') {
            $role = 3;
        }

        // Check if the username already exists in the database
        $query = "SELECT COUNT(*) FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            // Prepare and execute the query to insert the user into the database
            $query = "INSERT INTO user (first_name, last_name, username, password, date_of_birth, school_id, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$first_name, $last_name, $username, $password, $dob, $school_number, $role]);

            // Redirect to login.php after successful signup
            header("Location: login.php");
            exit();
        }
    }

    // Fetch the school numbers from the database
    $query = "SELECT school_id FROM school";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $school_numbers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Close the database connection
    $conn = null;
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Library Sign Up</title>
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
    .form-group input[type="password"],
    .form-group input[type="date"] {
      width: 100%;
      max-width: 300px; /* Adjust the max-width value to control the input field width */
      padding: 10px;
      border-radius: 3px;
      border: 1px solid #ccc;
    }
    .form-group input[type="number"] {
      width: 100%;
      max-width: 80px; /* Adjust the max-width value to control the input field width */
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
    .login-link {
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
    <h2>Sign Up</h2>
    <div class="error-message"><?php echo $error; ?></div>
    <form action="user_signup.php" method="post">
      <div class="form-group">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>
      </div>
      <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>
      </div>
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required>
      </div>
      <div class="form-group">
        <label for="school_number">School Number:</label>
        <select id="school_number" name="school_number" required>
          <?php foreach ($school_numbers as $number) : ?>
            <option value="<?php echo $number; ?>"><?php echo $number; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="role">Role:</label>
        <select id="role" name="role" required>
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
        </select>
      </div>
      <div class="form-group">
        <button type="submit">Sign Up</button>
      </div>
    </form>
    <div class="login-link">
      Already have an account? <a href="login.php">Back to Login</a>
    </div>
  </div>
</body>
</html>
