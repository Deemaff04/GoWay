<?php
// تضمين الملف
include 'include/header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['rules'] !== 'admin') {
    // إذا كان المستخدم ليس مسجلاً دخول أو ليس "أدمن"
    header('Location: index.php'); // إعادة التوجيه إلى الصفحة الرئيسية
    exit(); // إيقاف تنفيذ السكربت بعد إعادة التوجيه
}

// التحقق من صلاحيات المستخدم (اختياري)
if (!isset($_SESSION['user_id']) || $_SESSION['rules'] !== 'admin') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من المدخلات
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $logo = $_FILES['logo'] ?? null;

    if (empty($name) || empty($description) || $category_id <= 0 || !$logo) {
        $error = "All fields are required.";
    } else {
        // رفع الصورة
        $upload_dir = 'images/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_name = basename($logo['name']);
        $file_type = mime_content_type($logo['tmp_name']);
        $file_path = $upload_dir . $file_name;

        if (in_array($file_type, $allowed_types) && move_uploaded_file($logo['tmp_name'], $file_path)) {
            // إدخال البيانات في قاعدة البيانات
            $insert_query = "INSERT INTO `items` (category_id, name, logo, description) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isss", $category_id, $name, $file_path, $description);

            if ($stmt->execute()) {
                header("Location: manage_items.php");
                exit();
            } else {
                $error = "Failed to add the item.";
            }
        } else {
            $error = "Failed to upload the image.";
        }
    }
}
?>

<main class="main-content">
    <div class="item-details-container">
        <!-- Left side: Image Preview -->
        <div class="image-preview-container">
            <div id="image-preview" class="image-preview">
                <img id="image-preview-img" src="#" alt="Image Preview" />
            </div>
        </div>

        <!-- Right side: Add Item Form -->
        <div class="item-details-right">
            <h2>Add New Item</h2>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="add_item.php" method="POST" enctype="multipart/form-data">
                <label for="name">Item Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>

                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    // جلب الفئات من قاعدة البيانات
                    $categories_query = "SELECT id, name FROM `categories`";
                    $categories_result = $conn->query($categories_query);

                    while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="logo">Upload Image:</label>
                <input type="file" id="logo" name="logo" accept="image/*" required onchange="previewImage(event)">
                
                <button type="submit">Add Item</button>
            </form>
        </div>
    </div>
</main>

<script src="js/add_item.js"></script>

<?php include 'include/footer.php'; ?>
