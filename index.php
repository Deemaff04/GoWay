<?php
include 'include/header.php';

// جلب التصنيفات من قاعدة البيانات
$categories_query = "SELECT id, name, description FROM `categories`";
$categories_result = $conn->query($categories_query);
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

?>


<?php foreach ($categories as $category): ?>
    <section class="Items-section">
        <div class="Items-section-cards">
            <div class="container">
                <div class="Items-text">
                    <h2>Top international <?= htmlspecialchars($category['name']) ?></h2>
                    <p><?= htmlspecialchars($category['description']) ?></p>
                </div>
            </div>
        </div>
        <div class="Items-cards">
            <div class="container">
                <?php
                // جلب العناصر المرتبطة بهذا التصنيف
                $items_query = "SELECT id, name, logo, description 
                FROM `items` 
                WHERE category_id = ? 
                ORDER BY id DESC 
                LIMIT 4";
                $stmt = $conn->prepare($items_query);
                $stmt->bind_param("i", $category['id']);
                $stmt->execute();
                $items_result = $stmt->get_result();
                $items = $items_result->fetch_all(MYSQLI_ASSOC);

                foreach ($items as $item):
                    // حساب متوسط التقييم لكل عنصر
                    $reviews_query = "SELECT AVG(rating) as average_rating FROM `reviews` WHERE item_id = ?";
                    $reviews_stmt = $conn->prepare($reviews_query);
                    $reviews_stmt->bind_param("i", $item['id']);
                    $reviews_stmt->execute();
                    $reviews_result = $reviews_stmt->get_result();
                    $average_rating = $reviews_result->fetch_assoc()['average_rating'] ?? 0;
                ?>
                    <div class="Items-card">
                        <a href="item_details.php?id=<?= htmlspecialchars($item['id']) ?>" class="item-link">
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
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<script src="js/scripts.js"></script>
<?php include 'include/footer.php'; ?>
