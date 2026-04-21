<?php 
ob_start();
session_start();
include 'include/db.php'; 

if (!isset($_SESSION['rules'])) {
    $_SESSION['rules'] = 'user';
}
// الحصول على اسم الملف الحالي
$current_page = basename($_SERVER['PHP_SELF'], ".php");

// تحديد ملف الـ CSS بناءً على الصفحة المفتوحة
switch ($current_page) {
    case 'index':
        $css_file = 'index.css';
        break;
    case 'login':
        $css_file = 'login.css';
        break;
    case 'Manage':
        $css_file = 'Manage.css';
        break;
    case 'manage_users':
        $css_file = 'manage_users.css';
        break;
    case 'manage_categories':
        $css_file = 'manage_categories.css';
        break;
    case 'add_category':
        $css_file = 'add_category.css';
        break;
    case 'manage_items':
        $css_file = 'manage_items.css';
        break;
    case 'edit_category':
        $css_file = 'edit_category.css';
        break;
    case 'item_details':
        $css_file = 'item_details.css';
        break;
    case 'add_item':
        $css_file = 'add_item.css';
        break;
    case 'edit_item':
        $css_file = 'edit_item.css';
        break;
    case 'Signup':
        $css_file = 'Signup.css';
        break;
    case 'Welcome':
        $css_file = 'Welcome.css';
        break;
    case 'edit_user':
        $css_file = 'edit_user.css';
        break;
    case 'logout':
        $css_file = 'logout.css';
        break;
    default:
        $css_file = 'index.css'; // يمكن اختيار ملف افتراضي هنا
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Book your winter trip with GoWay - Stays, Flights, and Activities.">
    <meta name="keywords" content="GoWay, travel, winter trip, hotels, flights, activities">
    <meta name="author" content="GoWay Team">
    <title>GoWay</title>
    <link rel="stylesheet" href="css/<?php echo $css_file; ?>"> <!-- تحميل ملف الـ CSS بناءً على الصفحة -->
</head>
<body>
    <header class="header">
        <div class="container">
            <img class="logo" src="images/logo.svg" alt="GoWay Logo">
            <?php 
            if (isset($_SESSION['username'])) { 
                // إذا كان المستخدم قد سجل الدخول
                echo '<button class="login-btn" onclick="window.location.href=\'logout.php\';">Logout</button>';
            } else {
                // إذا لم يسجل المستخدم الدخول
                echo '<button class="login-btn" onclick="window.location.href=\'login.php\';">Sign in</button>';
            }
            ?>
        </div>
    </header>
        <section class="top-nav">
            <div class="container">
			<div class="left-nav" style="margin-right: auto;">
				<a href="index.php" class="nav-item" aria-label="Home">Home</a>
			</div>
			<?php if (isset($_SESSION['username'])): ?>
                <div class="right-nav">
                    <?php if ($_SESSION['rules'] == 'admin'): ?>
                        <a href="Manage.php" class="nav-item" aria-label="Manage">Manage</a>
                    <?php endif; ?>
                    <span class="welcome-message" style="color: gold;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
			<?php endif; ?>
			</div>
        </section>
