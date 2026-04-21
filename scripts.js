document.addEventListener('DOMContentLoaded', () => {
    const ratings = document.querySelectorAll('.Items-rating');
    ratings.forEach(rating => {
        const ratingValue = parseFloat(rating.getAttribute('data-rating')); 
        const stars = rating.querySelectorAll('.star');
        stars.forEach((star, index) => {
            if (index < Math.floor(ratingValue)) {
                star.classList.add('filled');
            } else if (index < ratingValue) {
                star.classList.add('half-filled');
            }
        });
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.rating-stars .star');

    stars.forEach((star, index) => {
        // عند التمرير بالماوس
        star.addEventListener('mouseenter', () => {
            stars.forEach((s, i) => {
                if (i >= index) { // عكس الاتجاه
                    s.classList.add('hover');
                } else {
                    s.classList.remove('hover');
                }
            });
        });

        // عند إزالة الماوس
        star.addEventListener('mouseleave', () => {
            stars.forEach(s => s.classList.remove('hover'));
        });

        // عند الضغط على نجمة
        star.addEventListener('click', () => {
            stars.forEach(s => s.classList.remove('selected'));
            for (let i = index; i < stars.length; i++) { // عكس الاتجاه
                stars[i].classList.add('selected');
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    // الحصول على كل عنصر يحتوي على التقييم (review-stars)
    const reviews = document.querySelectorAll('.review-stars');

    reviews.forEach(review => {
        const ratingValue = parseFloat(review.getAttribute('data-rating'));  // الحصول على قيمة التقييم من data-rating
        const stars = review.querySelectorAll('.review-star');  // الحصول على كل النجوم داخل العنصر

        stars.forEach((star, index) => {
            if (index < ratingValue) {
                star.classList.add('filled');  // تلوين النجمة إذا كان التقييم أكبر أو يساوي الفهرس
            } else {
                star.classList.remove('filled');  // إزالة التلوين إذا لم يكن التقييم أكبر من الفهرس
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('#rating-stars .star');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            // تحديث قيمة hidden input
            ratingInput.value = star.getAttribute('data-value');

            // تغيير مظهر النجوم بناءً على التقييم المختار
            stars.forEach(s => s.classList.remove('filled'));  // إزالة اللون من كل النجوم
            for (let i = 0; i < star.getAttribute('data-value'); i++) {
                stars[i].classList.add('filled');  // تلوين النجوم حتى النجمة المختارة
            }
        });
    });
});

function editReview(button) {
    const reviewElement = button.parentElement;
    const ratingValue = reviewElement.querySelector('p').textContent.split(':')[1].trim().split(' ')[0];
    const reviewText = reviewElement.querySelector('p + p').textContent.split(':')[1].trim();

    const stars = document.querySelectorAll('.rating-stars .star');
    stars.forEach(star => {
        const starValue = parseInt(star.getAttribute('data-value'), 10);
        if (starValue <= parseInt(ratingValue, 10)) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });

    document.getElementById('review').value = reviewText;

    reviewElement.remove();
}
function deleteReview(reviewId) {
    if (!confirm("Are you sure you want to delete this review?")) return;

    fetch('delete_review.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ review_id: reviewId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Review deleted successfully.");
            location.reload(); // إعادة تحميل الصفحة
        } else {
            alert(data.error || "An error occurred while deleting the review.");
        }
    })
    .catch(err => console.error("Error:", err));
}
function editReview(reviewId) {
    // البحث عن الريفيو المطلوب
    const reviewElement = document.querySelector(`.user-review[data-review-id="${reviewId}"]`);
    if (!reviewElement) {
        alert("Review not found.");
        return;
    }

    // الحصول على التقييم الموجود في الريفيو
    const ratingValue = parseInt(reviewElement.querySelector('.review-stars').getAttribute('data-rating'), 10);

    // تحديد النجوم بناءً على التقييم
    const stars = document.querySelectorAll('.rating-stars .star');
    
    stars.forEach((star, index) => {
        // إعادة تعيين جميع النجوم
        star.classList.remove('selected', 'hover');

        // إضافة النجوم المحددة بناءً على التقييم، ولكن عكس التحديد
        if (index >= 5 - ratingValue) {
            star.classList.add('selected');
        }
    });
    document.getElementById('rating').value = 6 - ratingValue;
    // نقل نص الريفيو إلى حقل التعليقات
    const comment = reviewElement.querySelector('p:nth-child(3)').textContent.trim();
    document.getElementById('review').value = comment;

    // تغيير النص داخل الزر إلى "Update Review"
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.textContent = "Update Review";

    // تغيير وظيفة الزر لتحديث الريفيو عند الضغط
    submitBtn.onclick = function () {
        updateReview(reviewId);
    };
}

function updateReview(reviewId) {
    // الحصول على التقييم من النجوم المحددة
    const selectedStars = document.querySelectorAll('#rating-stars .star.selected');
    const rating = selectedStars.length; // عدد النجوم المحددة
    const comment = document.getElementById('review').value;

    if (!rating) {
        alert("Please provide a rating.");
        return;
    }
    if (!comment.trim()) {
        alert("Please provide a comment.");
        return;
    }

    // إرسال البيانات لتحديث الريفيو
    fetch('update_review.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ review_id: reviewId, body: comment, rating: rating })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Review updated successfully.");
            location.reload(); // إعادة تحميل الصفحة لتحديث الريفيو
        } else {
            alert(data.error || "An error occurred while updating the review.");
        }
    })
    .catch(err => console.error("Error:", err));
}
