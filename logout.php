<?php 
 include 'include/header.php';

// التحقق إذا كان المستخدم مسجلاً دخوله
if (isset($_SESSION['username'])) {
    // حذف جميع الجلسات الحالية لتسجيل الخروج
    session_unset();
    session_destroy();
}
?>



<section class="logout-section">
    <div class="logout-container">
        <h1>You have successfully logged out, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!</h1>
                    </div>
</section>

<script>
    setTimeout(function() {
        window.location.href = 'index.php'; // Adjust the path to your homepage
    }, 3000);
</script>

<?php include 'include/footer.php'; ?>
