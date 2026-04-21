<?php
session_start();
include 'include/db.php'; // تضمين ملف الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // الحصول على اسم المستخدم وكلمة المرور من النموذج
    $username = $_POST['username'];
    $password = $_POST['password'];

    // استعلام للتحقق من وجود المستخدم في قاعدة البيانات
    $query = "SELECT id, username, email, password, rules FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username); // الربط بين المتغير واسم المستخدم في الاستعلام
    $stmt->execute();
    $result = $stmt->get_result();

    // التحقق من أن المستخدم موجود
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // التحقق من كلمة المرور
		if ($password == $user['password']) {
            // تخزين بيانات المستخدم في الجلسة
            $_SESSION['username'] = $user['username'];
            $_SESSION['rules'] = $user['rules']; // تخزين نوع المستخدم (admin أو user)
            $_SESSION['user_id'] = $user['id'];

            // إعادة التوجيه إلى الصفحة الرئيسية أو الصفحة المخصصة
            header("Location: Welcome.php");
            exit();
        } else {
            // إذا كانت كلمة المرور غير صحيحة
            $error_message = "Incorrect password. Please try again.";
            if (isset($error_message)) {
                $_SESSION['login_error'] = $error_message;
                header("Location: login.php");
                exit();
            }
            }
    } else {
        // إذا لم يتم العثور على المستخدم
        $error_message = "No account found with that username.";
    }

    // إغلاق الاتصال
    $stmt->close();
}
?>

<?php if (isset($error_message)): ?>
    <div class="error-message">
        <p><?php echo $error_message; ?></p>
    </div>
<?php endif; ?>
