<?php
// Assuming you have established a database connection

// Retrieve the user's borrowings from the database
$user_id = $_SESSION['user_id'];  // Assuming you have stored the user ID in the session
$query = "SELECT * FROM borrowing WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the borrowings as a JSON response
header('Content-Type: application/json');
echo json_encode($borrowings);
?>
