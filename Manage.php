<?php
include 'include/header.php';
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit(); // Stop the script after the redirection
}
if ($_SESSION['rules'] !== 'admin') {
    // إذا كان المستخدم ليس مسجلاً دخول أو ليس "أدمن"
    header('Location: index.php'); // إعادة التوجيه إلى الصفحة الرئيسية
    exit(); // إيقاف تنفيذ السكربت بعد إعادة التوجيه
}

// Database connection and query to get user counts
$userQuery = "SELECT COUNT(*) AS user_count FROM users WHERE rules = 'user'";
$adminQuery = "SELECT COUNT(*) AS admin_count FROM users WHERE rules = 'admin'";

// Get the count of users and admins
$userResult = $conn->query($userQuery);
$adminResult = $conn->query($adminQuery);

// Fetch the counts
$userCount = $userResult->fetch_assoc()['user_count'];
$adminCount = $adminResult->fetch_assoc()['admin_count'];

// Query to get category count
$categoryQuery = "SELECT COUNT(*) AS category_count FROM categories";
$categoryResult = $conn->query($categoryQuery);
$categoryCount = $categoryResult->fetch_assoc()['category_count'];

// Query to get item count
$itemQuery = "SELECT COUNT(*) AS item_count FROM items";
$itemResult = $conn->query($itemQuery);
$itemCount = $itemResult->fetch_assoc()['item_count'];
?>

<hr style="margin: 20px 0; border: none; border-top: 2px solid #ccc;">
<main>
    <div class="cards-container">
        <!-- Manage Users -->
        <a href="manage_users.php" class="card">
            <h2>Manage Users</h2>
            <p>Users count: <strong><?= $userCount ?></strong></p>
            <p>Admins count: <strong><?= $adminCount ?></strong></p>
        </a>
        <!-- Manage Categories -->
        <a href="manage_categories.php" class="card">
            <h2>Manage Categories</h2>
            <p>Categories count: <strong><?= $categoryCount ?></strong></p>
        </a>
        <!-- Manage Items -->
        <a href="manage_items.php" class="card">
            <h2>Manage Items</h2>
            <p>Items count: <strong><?= $itemCount ?></strong></p>
        </a>
    </div>
</main>

<?php include 'include/footer.php'; ?>
