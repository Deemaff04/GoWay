function confirmDelete(categoryId) {
    const modal = document.getElementById("confirmation-category-modal");
    const confirmBtn = document.getElementById("confirm-delete-category");
    const cancelBtn = document.getElementById("cancel-delete-category");
    modal.style.display = "block";
    confirmBtn.onclick = function () {
        window.location.href = "delete_category.php?id=" + categoryId;
    };
    cancelBtn.onclick = function () {
        modal.style.display = "none";
    };
}

function searchCategory() {
    const searchInput = document.getElementById('search-category').value;
    window.location.href = `manage_categories.php?search=${searchInput}`;
}
