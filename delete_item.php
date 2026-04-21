<?php
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

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $item_id = $_GET['id'];

    // تنفيذ الاستعلام لحذف العنصر
    $query = "DELETE FROM items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();

    // إعادة توجيه إلى صفحة إدارة العناصر بعد الحذف
    header('Location: manage_items.php');
    exit();
} else {
    echo "Invalid item ID.";
    exit();
}

include 'include/footer.php';
?>
