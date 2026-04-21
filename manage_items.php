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
$query = "SELECT i.id, i.name, i.description, c.name AS category_name, i.logo
          FROM items i
          LEFT JOIN categories c ON i.category_id = c.id
          WHERE i.name LIKE ? OR i.description LIKE ? OR c.name LIKE ?";
$stmt = $conn->prepare($query);
$searchParam = "%" . $searchQuery . "%"; // Add % to match partial input
$stmt->bind_param('sss', $searchParam, $searchParam, $searchParam); // Bind the search parameter for name, description, and category
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="main-content">
    <h1 class="page-title">Manage Items</h1>

    <!-- Search Section -->
    <div class="search-container">
        <form method="GET" action="manage_items.php">
            <input type="text" id="search-item" name="search" placeholder="Search for an item or description..." value="<?= htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Item Description</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($row['logo']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" class="item-image"></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td><?= htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <button class="edit-btn">
                                    <a href="edit_item.php?id=<?= $row['id']; ?>" style="color: white;">Edit</a>
                                </button>
                                <button class="delete-btn" onclick="confirmDeleteItem(<?= $row['id']; ?>)">Delete</button>
                                </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Back Button and Add New Item Button -->
    <div class="back-btn-container">
        <button class="back-btn" onclick="window.history.back()">Back</button>
        <a href="add_item.php"><button class="add-btn">Add New Item</button></a> <!-- Add New Item button with blue color -->
    </div>

</main>

<div id="confirmation-Item-modal" class="modal">
    <div class="modal-content">
        <h3>Are you sure you want to delete this item?</h3>
        <div class="modal-actions">
            <button class="confirm-btn" id="confirm-delete-Item">Yes, Delete</button>
            <button class="cancel-btn" id="cancel-delete-Item">Cancel</button>
        </div>
    </div>
</div>


<script src="js/manage_items.js"></script>
<?php include 'include/footer.php'; ?>
