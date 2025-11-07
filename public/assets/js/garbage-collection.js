class GarbageCollectionManager {
    constructor() {
        // Keep only if you have other non-modal functionality
        console.log('Legacy GarbageCollectionManager loaded - most functionality moved to components');
    }
}

// Handle modal focus to prevent aria-hidden warnings (keep if still needed)
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('hide.bs.modal', function(event) {
        if (event.target.id === 'routeDetailsModal') {
            const activeElement = document.activeElement;
            if (activeElement && activeElement.tagName === 'BUTTON') {
                activeElement.blur();
            }
        }
    });

    window.garbageCollectionManager = new GarbageCollectionManager();
});
