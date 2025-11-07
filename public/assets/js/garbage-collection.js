// Garbage Collection Management - Legacy Compatibility Layer
// Most functionality has been moved to modular components

// Minimal manager for any remaining functionality
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
            // Remove focus from any active button before hiding
            const activeElement = document.activeElement;
            if (activeElement && activeElement.tagName === 'BUTTON') {
                activeElement.blur();
            }
        }
    });

    // Initialize legacy manager for backward compatibility
    window.garbageCollectionManager = new GarbageCollectionManager();
});
