<?php include 'include/header.php'; ?>
<section class="signup-section">
    <div class="signup-container">
        <h2>Create Your Account</h2>
        <form action="process_signup.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Sign Up</button>
            </div>
            <p>Already have an account? <a href="login.php">Login</a></p>
            <!-- عرض رسالة الخطأ هنا -->
            <?php if (isset($_SESSION['signup_error'])): ?>
                <div class="error-message" style="color: red; margin-top: 10px;">
                    <?= htmlspecialchars($_SESSION['signup_error']); ?>
                </div>
                <?php unset($_SESSION['signup_error']); ?>
            <?php endif; ?>
        </form>
    </div>
</section>

<?php include 'include/footer.php'; ?>
