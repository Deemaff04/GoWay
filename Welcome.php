<?php include 'include/header.php'; ?>


<section class="welcome-section">
    <div class="welcome-container">
        <h1>Welcome to GoWay, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            </div>
</section>

<script>
    setTimeout(function() {
        window.location.href = 'index.php'; // Adjust the path to your homepage
    }, 1500);
</script>

<?php include 'include/footer.php'; ?>
