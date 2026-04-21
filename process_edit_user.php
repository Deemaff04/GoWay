<?php 
include 'include/header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استرجاع البيانات المدخلة من النموذج
    $userId = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rules = $_POST['rules'];

    // التحقق مما إذا كان قد تم إدخال كلمة مرور جديدة
    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT); // تجزئة كلمة المرور الجديدة
    } else {
        // إذا لم يتم تغيير كلمة المرور، نتركها كما هي
        $passwordHash = null;
    }

    // تجهيز الاستعلام لتحديث البيانات في قاعدة البيانات
    if ($passwordHash) {
        // إذا تم تغيير كلمة المرور
        $query = "UPDATE users SET username = ?, email = ?, password = ?, rules = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssi', $username, $email, $passwordHash, $rules, $userId);
    } else {
        // إذا لم يتم تغيير كلمة المرور
        $query = "UPDATE users SET username = ?, email = ?, rules = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssi', $username, $email, $rules, $userId);
    }

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        // بعد التعديل بنجاح، إعادة التوجيه إلى صفحة إدارة المستخدمين
        header("Location: manage_users.php");
        exit;
    } else {
        echo "Error updating user data.";
    }
}

include 'include/footer.php';
?>
