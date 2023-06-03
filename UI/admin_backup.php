<?php
// Configuration
$backupDir = '/Users/nikoskarakostas/Documents/Coding_Projects/HTML'; // Replace with the desired backup directory path
$dbHost = 'localhost'; // Replace with your database host
$dbUser = 'root'; // Replace with your database username
$dbPass = 'root'; // Replace with your root user's password
$dbName = 'library'; // Replace with your database name

// Function to execute the backup command
function createDatabaseBackup($backupDir, $dbHost, $dbUser, $dbPass, $dbName)
{
    // Create a unique filename for the backup
    $backupFile = $backupDir . '/backup_db3.sql';

    // Construct the command to create the backup
    $command = "mysqldump -h $dbHost -u $dbUser -p$dbPass $dbName > $backupFile";

    // Execute the command
    exec($command, $output, $returnValue);

    // Check if the backup was created successfully
    if ($returnValue === 0) {
        return $backupFile;
    } else {
        return false;
    }
}

// Check if the backup button was clicked
if (isset($_POST['backup'])) {
    // Call the createDatabaseBackup function
    $backupFile = createDatabaseBackup($backupDir, $dbHost, $dbUser, $dbPass, $dbName);

    if ($backupFile) {
        // Backup created successfully
        $message = 'Database backup created successfully: ' . $backupFile;
    } else {
        // Failed to create backup
        $message = 'Failed to create database backup.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
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
    <title>Create Database Backup</title>
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
    <h2>Create Database Backup</h2>
    <?php if (isset($message)) { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>
    <form method="post">
        <button type="submit" name="backup">Create Backup</button>
    </form>
    </div>
</body>
</html>
