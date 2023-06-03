<?php
// Assuming you have already established a database connection
$connection = mysqli_connect('localhost', 'root', 'root', 'library');

// Query to retrieve the average review rate for each user who has at least one review
$userQuery = "SELECT user.username, AVG(review.likert) AS avg_likert
          FROM user
          INNER JOIN review ON user.user_id = review.user_id
          GROUP BY user.user_id";

$userResult = mysqli_query($connection, $userQuery);

// Check if the query was successful and at least one row was returned
if ($userResult && mysqli_num_rows($userResult) > 0) {
  $averageRates = mysqli_fetch_all($userResult, MYSQLI_ASSOC);
} else {
  $averageRates = []; // Empty array if no data found
}

// Query to retrieve the average review rate per field
$fieldQuery = "SELECT field.field_name, AVG(review.likert) AS avg_likert
          FROM field
          INNER JOIN review ON field.isbn = review.isbn
          GROUP BY field.field_name";

$fieldResult = mysqli_query($connection, $fieldQuery);

// Check if the query was successful and at least one row was returned
if ($fieldResult && mysqli_num_rows($fieldResult) > 0) {
  $averageFieldRates = mysqli_fetch_all($fieldResult, MYSQLI_ASSOC);
} else {
  $averageFieldRates = []; // Empty array if no data found
}

// Close the result
mysqli_free_result($userResult);
mysqli_free_result($fieldResult);

// Close the database connection
mysqli_close($connection);
?>


<!DOCTYPE html>
<html>

<head>
  <title>Library</title>
  <style>
    /* Styles for the containers and search inputs */
    .container {
  max-width: 600px;
  width: 500px;
  margin: 20px auto;
  padding: 20px;
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  text-align: center;
  overflow-y: auto;
  max-height: 400px; /* Set a maximum height for the container */
}

.container:first-child {
  margin-top: 0; /* Remove top margin for the first container */
}

.container:not(:first-child) {
  margin-top: 20px; /* Add top margin for subsequent containers */
}


    .search-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .search-input {
      width: 70%;
      padding: 8px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }

    .search-button {
      width: 25%;
      padding: 8px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    /* Other styles */
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
    }

    .top-bar a {
      color: #fff;
      text-decoration: none;
      margin: 0 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 8px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <div class="top-bar">
    <a href="controller_homepage.php">Home Page</a>
    <a href="controller_CommunicationInfo.php">Communication Info</a>
    <a href="controller_myaccount.php">My Account</a>
    <a href="controller_search.php">Search</a>
    <a href="controller_borrowing.php">Borrow</a>
    <a href="controller_reservation.php">Make a Reservation</a>
    <a href="controller_review.php">Reviews</a>
    <a href="controller_check_expired.php">Expired Borrowings</a> 
    <a href="controller_add_book.php">Add Book</a> 
    <a href="controller_alter_book.php">Alter Book</a> 
    <a href="controller_user_status.php">User Status</a>
  </div>

  <div class="container">
    <h1>Average Review Rates per User</h1>

    <div class="search-container">
      <input type="text" id="userSearchInput" class="search-input" placeholder="Search by Username">
      <button id="userSearchButton" class="search-button">Search</button>
    </div>

    <table id="userReviewTable">
      <tr>
        <th>Username</th>
        <th>Average Review Rate</th>
      </tr>
      <!-- PHP code to populate the table -->
      <?php foreach ($averageRates as $averageRate) : ?>
        <tr>
          <td><?php echo $averageRate['username']; ?></td>
          <td><?php echo number_format($averageRate['avg_likert'], 2); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="container">
    <h1>Average Review Rates per Field</h1>

    <div class="search-container">
      <input type="text" id="fieldSearchInput" class="search-input" placeholder="Search by Field Name">
      <button id="fieldSearchButton" class="search-button">Search</button>
    </div>

    <table id="fieldReviewTable">
      <tr>
        <th>Field Name</th>
        <th>Average Review Rate</th>
      </tr>
      <!-- PHP code to populate the table -->
      <?php foreach ($averageFieldRates as $averageFieldRate) : ?>
        <tr>
          <td><?php echo $averageFieldRate['field_name']; ?></td>
          <td><?php echo number_format($averageFieldRate['avg_likert'], 2); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <script>
    // Function to filter the user review table based on search input
    function filterUserReviews() {
      var input = document.getElementById('userSearchInput').value.toUpperCase();
      var table = document.getElementById('userReviewTable');
      var rows = table.getElementsByTagName('tr');

      for (var i = 0; i < rows.length; i++) {
        var usernameColumn = rows[i].getElementsByTagName('td')[0];
        if (usernameColumn) {
          var username = usernameColumn.textContent || usernameColumn.innerText;
          if (username.toUpperCase().indexOf(input) > -1) {
            rows[i].style.display = '';
          } else {
            rows[i].style.display = 'none';
          }
        }
      }
    }

    // Function to filter the field review table based on search input
    function filterFieldReviews() {
      var input = document.getElementById('fieldSearchInput').value.toUpperCase();
      var table = document.getElementById('fieldReviewTable');
      var rows = table.getElementsByTagName('tr');

      for (var i = 0; i < rows.length; i++) {
        var fieldNameColumn = rows[i].getElementsByTagName('td')[0];
        if (fieldNameColumn) {
          var fieldName = fieldNameColumn.textContent || fieldNameColumn.innerText;
          if (fieldName.toUpperCase().indexOf(input) > -1) {
            rows[i].style.display = '';
          } else {
            rows[i].style.display = 'none';
          }
        }
      }
    }

    // Add event listeners to search buttons
    document.getElementById('userSearchButton').addEventListener('click', filterUserReviews);
    document.getElementById('fieldSearchButton').addEventListener('click', filterFieldReviews);
  </script>
</body>

</html>
