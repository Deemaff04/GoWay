<?php
// تضمين الملف
include 'include/header.php';

// التحقق من وجود المعرف
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid item ID.");
}

$item_id = intval($_GET['id']);

// جلب بيانات الصنف
$item_query = "SELECT id, category_id, name, logo, description FROM `items` WHERE id = ?";
$stmt = $conn->prepare($item_query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item_result = $stmt->get_result();
$item = $item_result->fetch_assoc();

if (!$item) {
    die("Item not found.");
}

// حساب متوسط التقييم
$rating_query = "SELECT AVG(rating) as average_rating FROM `reviews` WHERE item_id = ?";
$rating_stmt = $conn->prepare($rating_query);
$rating_stmt->bind_param("i", $item_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$average_rating = round($rating_result->fetch_assoc()['average_rating'] ?? 0, 1);

// جلب الريفيوهات مع بيانات المستخدمين
$reviews_query = "
    SELECT r.id as review_id, r.rating, r.body, r.user_id, u.username 
    FROM `reviews` r 
    JOIN `users` u ON r.user_id = u.id 
    WHERE r.item_id = ?
";
$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $item_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
$reviews = $reviews_result->fetch_all(MYSQLI_ASSOC);

// الحصول على دور المستخدم الحالي
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$current_user_role = isset($_SESSION['rules']) ? $_SESSION['rules'] : 'guest';

// Check if the user has already submitted a review for this item
$check_review_query = "SELECT id, rating, body FROM reviews WHERE item_id = ? AND user_id = ?";
$check_review_stmt = $conn->prepare($check_review_query);
$check_review_stmt->bind_param("ii", $item_id, $current_user_id);
$check_review_stmt->execute();
$check_review_result = $check_review_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    // التحقق من أن التقييم والمراجعة تم إدخالهما
    if (isset($_POST['rating']) && isset($_POST['review'])) {
        $rating = 6 - intval($_POST['rating']);
        $review = trim($_POST['review']);

        // التأكد من أن التقييم ضمن النطاق المسموح
        if ($rating < 1 || $rating > 5) {
            die("Invalid rating.");
        }

        if (empty($review)) {
            die("Review cannot be empty.");
        }

        // إذا كان المستخدم قد قام بمراجعة سابقة، يتم التحديث
        if ($check_review_result->num_rows > 0) {
            // التحديث
            $update_query = "UPDATE reviews SET rating = ?, body = ? WHERE item_id = ? AND user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("isii", $rating, $review, $item_id, $current_user_id);
            
            if ($update_stmt->execute()) {
                header("Location: item_details.php?id=" . $item_id);
                exit();
            } else {
                echo "Failed to update the review.";
            }
        } else {
            // إضافة تقييم جديد
            $insert_query = "INSERT INTO `reviews` (item_id, user_id, rating, body) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iiis", $item_id, $current_user_id, $rating, $review);
            
            if ($insert_stmt->execute()) {
                header("Location: item_details.php?id=" . $item_id);
                exit();
            } else {
                echo "Failed to submit the review.";
            }
        }
    }
}
?>

<main class="main-content">
    <div class="item-details-container">
        <!-- Left side: Item details -->
        <div class="Items-card">
            <img src="<?= htmlspecialchars($item['logo']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <div class="Items-info">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p><?= htmlspecialchars($item['description']) ?></p>
                <div class="rating-container">
                    <div class="Items-rating" data-rating="<?= round($average_rating) ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star<?= $i <= round($average_rating) ? ' filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side: Rating Section -->
        <div class="item-details-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="item_details.php?id=<?= $item_id ?>" method="POST">
                    <div class="rating-stars" id="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star" data-value="<?= $i ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>

                    <!-- Hidden input to store the rating value -->
                    <input type="hidden" name="rating" id="rating" value="">

                    <textarea id="review" name="review" placeholder="Write your comments here"></textarea>
                    <button type="submit" name="submit_rating" id="submit-btn">Submit Rating</button>
                    <button onclick="window.history.back()">Back</button>
                </form>
            <?php endif; ?>


            <h3>User Reviews</h3>
            <div class="ratingslist" id="ratings-list">
                <?php foreach ($reviews as $review): ?>
                    <div class="user-review" data-review-id="<?= $review['review_id'] ?>">
                        <p><?= htmlspecialchars($review['username']) ?></p>
                        <p>
                            <span class="review-stars" data-rating="<?= $review['rating'] ?>">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="review-star<?= $i <= $review['rating'] ? ' filled' : '' ?>"></span>
                                <?php endfor; ?>
                            </span>
                        </p>
                        <p><?= htmlspecialchars($review['body']) ?></p>
                        <?php if ( $current_user_id == $review['user_id']): ?>
                            <button class="edit-btn" onclick="editReview(<?= $review['review_id'] ?>)">Edit</button>
                        <?php endif; ?>
                        <?php if (($current_user_role === 'admin' || ($current_user_role === 'user' && $current_user_id == $review['user_id']))): ?>
                            <button class="delete-btn" onclick="deleteReview(<?= $review['review_id'] ?>)">Delete</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<script src="js/scripts.js"></script>
<?php include 'include/footer.php'; ?>
