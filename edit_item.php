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

// التحقق من وجود المعرف (id) في الرابط
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    // جلب بيانات العنصر من قاعدة البيانات
    $item_query = "SELECT id, name, description, category_id, logo FROM items WHERE id = ?";
    $stmt = $conn->prepare($item_query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item_result = $stmt->get_result();

    if ($item_result->num_rows > 0) {
        $item = $item_result->fetch_assoc();
    } else {
        die("Item not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من المدخلات
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $logo = $_FILES['logo'] ?? null;

    if (empty($name) || empty($description) || $category_id <= 0) {
        $error = "All fields are required.";
    } else {
        // إذا تم رفع صورة جديدة
        if ($logo && $logo['error'] == 0) {
            // رفع الصورة
            $upload_dir = 'images/';
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_name = basename($logo['name']);
            $file_type = mime_content_type($logo['tmp_name']);
            $file_path = $upload_dir . $file_name;

            if (in_array($file_type, $allowed_types) && move_uploaded_file($logo['tmp_name'], $file_path)) {
                $logo_path = $file_path;
            } else {
                $error = "Failed to upload the image.";
            }
        } else {
            // إذا لم يتم رفع صورة جديدة، الاحتفاظ بالصورة القديمة
            $logo_path = $item['logo'];
        }

        if (!isset($error)) {
            // تحديث البيانات في قاعدة البيانات
            $update_query = "UPDATE items SET name = ?, description = ?, category_id = ?, logo = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssisi", $name, $description, $category_id, $logo_path, $item_id);

            if ($stmt->execute()) {
                header("Location: manage_items.php");
                exit();
            } else {
                $error = "Failed to update the item.";
            }
        }
    }
}
?>

<main class="main-content">
    <div class="item-details-container">
        <!-- Left side: Image Preview -->
        <div class="image-preview-container">
            <div id="image-preview" class="image-preview">
                <img id="image-preview-img" src="<?= $item['logo'] ?>" alt="Image Preview" />
            </div>
        </div>

        <!-- Right side: Edit Item Form -->
        <div class="item-details-right">
            <h2>Edit Item</h2>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="edit_item.php?id=<?= $item['id'] ?>" method="POST" enctype="multipart/form-data">
                <label for="name">Item Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($item['description']) ?></textarea>

                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    // جلب الفئات من قاعدة البيانات
                    $categories_query = "SELECT id, name FROM `categories`";
                    $categories_result = $conn->query($categories_query);

                    while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $item['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="logo">Upload New Image (optional):</label>
                <input type="file" id="logo" name="logo" accept="image/*" onchange="previewImage(event)">

                <button type="submit">Update Item</button>
            </form>
        </div>
    </div>
</main>

<script src="js/edit_item.js"></script>
<?php include 'include/footer.php'; ?>
