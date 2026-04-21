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

// تحقق من وجود معلمة id في الرابط
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // تحقق من أن المعرف هو رقم صالح
    if (is_numeric($category_id)) {
        // استعلام لجلب بيانات التصنيف بناءً على المعرف
        $query = "SELECT name, description FROM categories WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // إذا تم العثور على التصنيف
        if ($result->num_rows > 0) {
            $category = $result->fetch_assoc();
        } else {
            echo "Category not found.";
            exit();
        }
    } else {
        // إذا كان المعرف غير صالح
        echo "Invalid category ID.";
        exit();
    }
} else {
    // إذا لم يكن هناك معلمة id في الرابط
    echo "Category ID is missing.";
    exit();
}

$error_message = ''; // متغير لرسالة الخطأ

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // إذا كان المستخدم قد أرسل النموذج، نقوم بتحديث البيانات
    $name = $_POST['name'];
    $description = $_POST['description'];

    // تحقق إذا كان الاسم الجديد موجود بالفعل في قاعدة البيانات
    $check_query = "SELECT id FROM categories WHERE name = ? AND id != ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('si', $name, $category_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // إذا كان الاسم موجودًا بالفعل
    if ($check_result->num_rows > 0) {
        $error_message = 'Category name already exists. Please choose a different name.';
    } else {
        // استعلام لتحديث بيانات التصنيف
        $update_query = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('ssi', $name, $description, $category_id);
        $stmt->execute();

        // بعد التحديث، إعادة توجيه المستخدم إلى صفحة إدارة التصنيفات
        header('Location: manage_categories.php');
        exit();
    }
}

?>

<main class="main-content">
    <h1 class="page-title">Edit Category</h1>

    <form method="POST" action="edit_category.php?id=<?= $category_id; ?>">
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Category Description</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($category['description']); ?></textarea>
        </div>
        <button type="submit">Save Changes</button>
    </form>

    <!-- عرض رسالة الخطأ بشكل مركزي تحت زر "Save Changes" -->
    <?php if ($error_message): ?>
        <div style="color: red; margin-top: 10px; text-align: center;">
            <?= htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="back-btn-container">
        <button onclick="window.history.back()">Back</button>
    </div>
</main>

<?php include 'include/footer.php'; ?>
