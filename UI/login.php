<?php
session_start();
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
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>
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

    <?php if (isset($error)): ?>
    <div class="error-message">
      <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <div class="signup-link">
      Don't have an account? <a href="signup.php">Sign Up</a>
    </div>
  </div>
</body>
</html>