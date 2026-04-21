let itemIdTodelete = null;

// Function to show the confirmation modal
function confirmDeleteItem(itemId) {
    itemIdTodelete = itemId;  // Save the ID of the item to be deleted
    document.getElementById('confirmation-Item-modal').style.display = 'flex';  
}

// Event listener for confirming deletion
document.getElementById('confirm-delete-Item')?.addEventListener('click', function() {
    if (itemIdTodelete !== null) {
        // Redirect to the delete script with the item ID
        window.location.href = 'delete_item.php?id=' + itemIdTodelete;  
    }
});

// Event listener for cancelling deletion
document.getElementById('cancel-delete-Item')?.addEventListener('click', function() {
    document.getElementById('confirmation-Item-modal').style.display = 'none';  
});
