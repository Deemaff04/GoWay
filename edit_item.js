function previewImage(event) {
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewImg = document.getElementById('image-preview-img');

    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview.style.display = 'block';  // إظهار الـ div عند تحميل الصورة
            imagePreviewImg.src = e.target.result;  // تعيين الصورة المعروضة في العنصر
        };

        reader.readAsDataURL(file);  // قراءة الصورة وتحويلها إلى URL قابل للعرض
    } else {
        imagePreview.style.display = 'none';  // إخفاء الـ div إذا لم يتم اختيار صورة
    }
}
