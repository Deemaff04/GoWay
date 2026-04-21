<?php
include 'include/header.php';
if (!isset($_SESSION['user_id'])) {
    // إذا لم يكن المستخدم مسجل دخوله، قم بإعادة توجيهه إلى صفحة تسجيل الدخول
    header('Location: login.php');
    exit(); // توقف تنفيذ السكربت بعد إعادة التوجيه
}
if ($_SESSION['rules'] !== 'admin') {
    // إذا كان المستخدم ليس مسجلاً دخول أو ليس "أدمن"
    header('Location: index.php'); // إعادة التوجيه إلى الصفحة الرئيسية
    exit(); // إيقاف تنفيذ السكربت بعد إعادة التوجيه
}

// التحقق من وجود معرف المستخدم في الـ URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    
    // جلب بيانات المستخدم من قاعدة البيانات
    $query = "SELECT id, username, email, password, rules FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // إذا لم يكن هناك مستخدم بهذا المعرف
        echo "User not found.";
        exit;
    }
} else {
    // إذا لم يتم العثور على معرف المستخدم
    echo "Invalid User ID.";
    exit;
}

// التحقق من وجود بيانات تم إرسالها عبر النموذج لتحديث المستخدم
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rules = $_POST['rules'];

    // التحقق إذا كان هناك مستخدم آخر بنفس اسم المستخدم
    $checkQuery = "SELECT id FROM users WHERE username = ? AND id != ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('si', $username, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // إذا كان هناك مستخدم آخر بنفس اسم المستخدم
        $error_message = "Username is already taken by another user.";
    } else {
        // إذا لم يكن هناك مستخدم بنفس اسم المستخدم
        $updateQuery = "UPDATE users SET username = ?, email = ?, password = ?, rules = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param('ssssi', $username, $email, $password, $rules, $userId);

        if ($updateStmt->execute()) {
            // إعادة التوجيه إلى صفحة إدارة المستخدمين بعد التحديث
            header('Location: manage_users.php');
            exit;
        } else {
            $error_message = "Error updating user details.";
        }
    }
}
?>

<main class="main-content">
    <h1 class="page-title">Edit User Details</h1>

    <form method="POST" action="edit_user.php?id=<?= htmlspecialchars($userId); ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" value="<?= htmlspecialchars($user['password']); ?>" required>
        </div>
        <div class="form-group">
            <label for="rules">Role</label>
            <select id="rules" name="rules" required>
                <option value="admin" <?= ($user['rules'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?= ($user['rules'] == 'user') ? 'selected' : ''; ?>>User</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit">Save Changes</button>
        </div>

        <!-- عرض رسالة الخطأ إذا كان اسم المستخدم مأخوذ -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

    </form>

    <div class="back-btn-container">
        <button onclick="window.history.back()">Back</button>
    </div>
</main>

<?php include 'include/footer.php'; ?>
