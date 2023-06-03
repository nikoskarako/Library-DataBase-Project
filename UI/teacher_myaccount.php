<?php
session_start();

// Database connection configuration
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'library';

// Create a database connection
$connection = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

// Retrieve the username from the session
$username = $_SESSION['username'];

// Query to fetch the user's ID based on the username
$query = "SELECT user_id FROM user WHERE username = '$username'";

// Execute the query
$result = $connection->query($query);

// Check if a user was found
if ($result->num_rows > 0) {
    // Fetch the user's ID
    $row = $result->fetch_assoc();
    $userID = $row['user_id'];

    // Store the user's ID in the session
    $_SESSION['user_id'] = $userID;

    // Query to fetch the user's borrowings
    $borrowingsQuery = "SELECT DISTINCT borrowing.*, book.title FROM borrowing
                        INNER JOIN book ON borrowing.isbn = book.isbn
                        WHERE borrowing.user_id = $userID";

    // Execute the borrowings query
    $borrowingsResult = $connection->query($borrowingsQuery);

    // Query to fetch the user's reservations
    $reservationsQuery = "SELECT DISTINCT reservation.*, book.title FROM reservation
                          INNER JOIN book ON reservation.isbn = book.isbn
                          WHERE reservation.user_id = $userID";

    // Execute the reservations query
    $reservationsResult = $connection->query($reservationsQuery);

} else {
    echo 'User not found.';
}

// Close the database connection
$connection->close();
?>




<!DOCTYPE html>
<html>
<head>
    <title>Library Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            background-image: url('24manguel-superJumbo.jpg');
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        .top-bar {
            background-color: #2a65a4;
            padding: 10px;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
        }
        
        .top-bar a {
            color: #fff;
            text-decoration: none;
            margin: 0 20px;
        }
        
        .container {
            max-width: 600px;
            width: 800px;
            margin: 80px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        h2 {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
        }
        
        input[type="password"] {
            padding: 5px;
            width: 100%;
            margin-bottom: 10px;
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
        
        #error-message {
            color: red;
        }
        
        #success-message {
            color: green;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f5f5f5;
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
    <h2>My Account</h2>
    <p>Username: <span id="username"><?php echo $_SESSION['username']; ?></span></p>

    <div class="middle-container">
        <div id="borrowings-section">
            <?php
            if ($borrowingsResult->num_rows > 0) {
                echo '<h3>Borrowings</h3>';
                echo '<table>';
                echo '<tr><th>ISBN</th><th>Title</th><th>Date of Borrowing</th><th>Borrow Duration</th><th>Status</th></tr>';

                // Loop through the borrowings and display them
                while ($row = $borrowingsResult->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['isbn'] . '</td>';
                    echo '<td>' . $row['title'] . '</td>';
                    echo '<td>' . $row['date_of_borrowing'] . '</td>';
                    echo '<td>' . $row['borrow_duration'] . '</td>';
                    echo '<td>' . $row['status'] . '</td>';
                    echo '</tr>';
                }

                echo '</table>';
            } else {
                echo '<h3>Borrowings</h3>';
                echo 'No borrowings found.';
            }
            ?>
        </div>

        <div id="reservations-section">
    <?php
    if ($reservationsResult->num_rows > 0) {
        echo '<h3>Reservations</h3>';
        echo '<table>';
        echo '<tr><th>ISBN</th><th>Title</th><th>Date of Reservation</th><th>Last Update</th><th>Expiration Date</th></tr>';

        // Loop through the reservations and display them
        while ($row = $reservationsResult->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['isbn'] . '</td>';
            echo '<td>' . $row['title'] . '</td>';
            echo '<td>' . $row['date_of_reservation'] . '</td>';
            echo '<td>' . $row['last_update'] . '</td>';
            echo '<td>' . $row['expire_date'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '<h3>Reservations</h3>';
        echo 'No reservations found.';
    }
    ?>
</div>


        <div id="password-change-section">
            <h3>Password Change</h3>
            <label for="old-password">Old Password:</label>
            <input type="password" id="old-password" name="old-password" required><br><br>
            <label for="new-password">New Password:</label>
            <input type="password" id="new-password" name="new-password" required><br><br>
            <button class="button" onclick="changePassword()">Change Password</button>
            <p id="error-message"></p>
            <p id="success-message"></p>
        </div>
    </div>
</div>

<script>
    function changePassword() {
        var oldPassword = document.getElementById("old-password").value;
        var newPassword = document.getElementById("new-password").value;

        // Make an AJAX request to the server to check the old password
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "check_password.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Old password matches, proceed with changing the password
                    changePasswordRequest(newPassword);
                } else {
                    // Old password does not match, display error message
                    var errorMessage = document.getElementById("error-message");
                    errorMessage.innerText = "Try again";
                    errorMessage.style.color = "red";
                }
            }
        };
        xhr.send("oldPassword=" + encodeURIComponent(oldPassword));

        function changePasswordRequest(newPassword) {
            // Make another AJAX request to the server to change the password
            var changePasswordXhr = new XMLHttpRequest();
            changePasswordXhr.open("POST", "update_password.php", true);
            changePasswordXhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            changePasswordXhr.onreadystatechange = function () {
                if (changePasswordXhr.readyState === 4 && changePasswordXhr.status === 200) {
                    var changePasswordResponse = JSON.parse(changePasswordXhr.responseText);
                    if (changePasswordResponse.success) {
                        // Password changed successfully, display success message
                        var successMessage = document.getElementById("success-message");
                        successMessage.innerText = "Password changed successfully";
                        successMessage.style.color = "green";
                    } else {
                        // Failed to change password, display error message
                        var errorMessage = document.getElementById("error-message");
                        errorMessage.innerText = "Failed to change password. Please try again.";
                        errorMessage.style.color = "red";
                    }
                }
            };
            changePasswordXhr.send("newPassword=" + encodeURIComponent(newPassword));
        }
    }
</script>
</body>
</html>