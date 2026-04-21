<?php
session_start();
include 'include/db.php'; // تضمين الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على البيانات من النموذج
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // التحقق من أن كلمة المرور وتأكيدها متطابقان
    if ($password !== $confirm_password) {
        $_SESSION['signup_error'] = "Passwords do not match.";
        header("Location: Signup.php");
        exit();
    }

    // التحقق من وجود المستخدم أو البريد الإلكتروني مسبقًا
    $check_user_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_user_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['signup_error'] = "Username or email is already taken.";
        header("Location: Signup.php");
        exit();
    }


    // إدخال المستخدم الجديد إلى قاعدة البيانات
    $insert_query = "INSERT INTO users (username, email, password, rules) VALUES (?, ?, ?, 'user')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['signup_error'] = "Failed to create an account. Please try again.";
        header("Location: Signup.php");
        exit();
    }
}

header("Location: Signup.php");
exit();
