<?php
include 'include/db.php'; // Database connection

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare the delete query
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId); // Bind the user ID as an integer
    $stmt->execute();

    // Redirect to manage_users.php after deletion
    header("Location: manage_users.php");
    exit();
}
?>
