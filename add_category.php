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

$error_message = ''; // Initialize the error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Check if the category name already exists
    $query = "SELECT id FROM categories WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If category name already exists, set the error message
        $error_message = "Category name already exists. Please choose another one.";
    } else {
        // If category name does not exist, proceed with insertion
        $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $name, $description);
        $stmt->execute();

        header('Location: manage_categories.php');
        exit();
    }
}
?>

<main class="main-content">
    <h1 class="page-title">Add New Category</h1>

    <form method="POST" action="add_category.php">
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Category Description</label>
            <textarea id="description" name="description" required></textarea>
        </div>

        <!-- Display error message if category name already exists -->
        <?php if ($error_message): ?>
            <p class="error-message"><?= $error_message; ?></p>
        <?php endif; ?>

        <button type="submit">Add Category</button>
    </form>

    <div class="back-btn-container">
        <button onclick="window.history.back()">Back</button>
    </div>
</main>

<?php include 'include/footer.php'; ?>
