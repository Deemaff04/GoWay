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


// Get search query if any
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the query based on search input
$query = "SELECT id, name, description FROM categories WHERE name LIKE ? OR description LIKE ?";



$stmt = $conn->prepare($query);
$searchParam = "%" . $searchQuery . "%"; // Add % to match partial input
$stmt->bind_param('ss', $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="main-content">
    <h1 class="page-title">Manage Categories</h1>

    <!-- Search Section -->
    <div class="search-container">
        <form method="GET" action="manage_categories.php">
            <input type="text" id="search-category" name="search" placeholder="Search for a category..." value="<?= htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Category Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="category-table-body">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['description']); ?></td>
                <td>
                    <!-- Edit Button with consistent style -->
                    <a href="edit_category.php?id=<?= $row['id']; ?>" class="edit-btn">Edit</a>
                    <!-- Delete Button with consistent style -->
                    <button class="delete-btn" onclick="confirmDelete(<?= $row['id']; ?>)">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No categories found.</td>
        </tr>
    <?php endif; ?>
</tbody>

        </table>
    </div>

    <!-- Confirmation Modal for Deletion -->
    <div id="confirmation-category-modal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this category?</h3>
            <div class="modal-actions">
                <button class="confirm-btn" id="confirm-delete-category">Yes, Delete</button>
                <button class="cancel-btn" id="cancel-delete-category">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Back Button Section -->
    <div class="back-btn-container">
        <button onclick="window.history.back()">Back</button>
        <!-- Add New Category Button -->
        <a href="add_category.php" class="add-category-btn">Add New Category</a>
    </div>

</main>

<script src="js/manage_categories.js"></script>

<?php include 'include/footer.php'; ?>
