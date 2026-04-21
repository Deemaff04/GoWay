let userIdToDelete = null;
function confirmDelete(userId) {
    document.getElementById('confirmation-modal').style.display = 'flex';
        document.getElementById('confirm-delete').onclick = function() {
        window.location.href = 'delete_user.php?id=' + userId;
    };
        document.getElementById('cancel-delete').onclick = function() {
        document.getElementById('confirmation-modal').style.display = 'none';
    };
}
document.getElementById('cancel-delete').onclick = function() {
    document.getElementById('confirmation-modal').style.display = 'none'; // Hide modal
};
document.getElementById('confirm-delete').onclick = function() {
    if (userIdToDelete) {
        // Perform the deletion by redirecting to delete_user.php
        window.location.href = 'delete_user.php?id=' + userIdToDelete;
    }
};
window.onload = function() {
    document.getElementById('confirmation-modal').style.display = 'none';
};
document.getElementById('cancel-edit').addEventListener('click', function() {
    window.history.back(); // Go back to the previous page
});
