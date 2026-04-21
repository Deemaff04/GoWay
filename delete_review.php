<?php
session_start();
include 'include/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $review_id = $input['review_id'] ?? null;
    $current_user_id = $_SESSION['user_id'] ?? null;
    $current_user_role = $_SESSION['rules'] ?? 'guest';

    if (!$review_id || !$current_user_id) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized request.']);
        exit;
    }

    // تحقق من أن المستخدم له الصلاحية
    $query = "SELECT user_id FROM reviews WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();

    if (!$review || ($current_user_role !== 'admin' && $current_user_id != $review['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this review.']);
        exit;
    }

    // حذف الريفيو
    $delete_query = "DELETE FROM reviews WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $review_id);
    if ($delete_stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete review.']);
    }
}
?>
