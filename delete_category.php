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
        // استعلام لحذف التصنيف من قاعدة البيانات
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $category_id);
        $stmt->execute();

        // إعادة توجيه المستخدم إلى صفحة إدارة التصنيفات بعد الحذف
        header('Location: manage_categories.php');
        exit();
    } else {
        // إذا كان المعرف غير صالح، يمكن إظهار رسالة خطأ
        echo "Invalid category ID.";
        exit();
    }
} else {
    // إذا لم يكن هناك معلمة id في الرابط
    echo "Category ID is missing.";
    exit();
}

include 'include/footer.php';
?>
