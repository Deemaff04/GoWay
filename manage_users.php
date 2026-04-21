<?php 
include 'include/header.php'; 
// تأكد من أن المستخدم قد قام بتسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['rules'] !== 'admin') {
    // إذا كان المستخدم ليس مسجلاً دخول أو ليس "أدمن"
    header('Location: index.php'); // إعادة التوجيه إلى الصفحة الرئيسية
    exit(); // إيقاف تنفيذ السكربت بعد إعادة التوجيه
}

// Get search query if any
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the query based on search input
$query = "SELECT id, username, email, password, rules FROM users WHERE username LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($query);
$searchParam = "%" . $searchQuery . "%"; // Add % to match partial input
$stmt->bind_param('ss', $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="main-content">
    <h1 class="page-title">Manage Users</h1>

    <!-- Search Section -->
    <div class="search-container">
        <form method="GET" action="manage_users.php">
            <input type="text" id="search-user" name="search" placeholder="Search for a user..." value="<?= htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= str_repeat('•', strlen($row['password'])); ?></td> <!-- Password as dots -->
                            <td><?= htmlspecialchars($row['rules']); ?></td>
                            <td>
                            <button class="edit-btn">
                                <a href="edit_user.php?id=<?= $row['id']; ?>" style="color: white;">Edit</a>
                            </button>
                            <button class="delete-btn" onclick="confirmDelete(<?= $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Confirmation Modal for Deletion (Initially Hidden) -->
    <div id="confirmation-modal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this user?</h3>
            <div class="modal-actions">
                <button class="confirm-btn" id="confirm-delete">Yes, Delete</button>
                <button class="cancel-btn" id="cancel-delete">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Back Button Section -->
    <div class="back-btn-container">
        <button onclick="window.history.back()">Back</button>
    </div>
</main>

<script src="js/manage_users.js"></script>
<?php include 'include/footer.php'; ?>
