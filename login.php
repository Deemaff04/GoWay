<?php include 'include/header.php'; ?>


<section class="login-section">
    <div class="login-container">
        <h2>Login to Your Account</h2>
        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Login</button>
            </div>
            <p>Don't have an account? <a href="Signup.php">Sign Up</a></p>
            <?php
                if (isset($_SESSION['login_error'])): ?>
                    <div class="error-message" style="color: #8B0000; margin-top: 10px; text-align: center;">
                        <?= htmlspecialchars($_SESSION['login_error']) ?>
                    </div>
                <?php 
                unset($_SESSION['login_error']);
                endif;
            ?>
        </form>
    </div>
</section>

<?php include 'include/footer.php'; ?>
