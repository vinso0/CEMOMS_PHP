// Garbage Collection Management - Modern Implementation
// Most functionality has been moved to modular components

class GarbageCollectionManager {
    constructor() {
        console.log('✅ GarbageCollectionManager loaded');
        this.initializeDeleteModal();
    }

    /**
     * Initialize delete modal event listeners using data attributes
     */
    initializeDeleteModal() {
        // Event delegation for delete buttons
        document.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-truck-btn');
            
            if (deleteBtn) {
                e.preventDefault(); // Prevent any default behavior
                
                // Get truck data from data attributes
                const truckId = deleteBtn.dataset.truckId;
                const plateNumber = deleteBtn.dataset.plateNumber;
                
                console.log('🗑️ Delete button clicked:', { truckId, plateNumber });
                
                // Validate data
                if (!truckId) {
                    console.error('❌ No truck ID found on delete button');
                    alert('Error: Unable to identify truck for deletion');
                    return;
                }
                
                // Populate the modal
                this.populateDeleteModal({
                    truck_id: truckId,
                    id: truckId,
                    plate_number: plateNumber || 'Unknown'
                });
            }
        });
        
        console.log('✅ Delete modal event listeners initialized');
    }

    /**
     * Populate delete modal with truck data
     * @param {Object} truck - Truck object with id and plate_number
     */
    populateDeleteModal(truck) {
        console.log('📝 Populating delete modal with:', truck);
        
        // Get the truck ID - check multiple possible keys
        const truckId = truck.truck_id || truck.id;
        const plateNumber = truck.plate_number || 'Unknown';
        
        // Validate truck ID
        if (!truckId) {
            console.error('❌ No valid truck ID found in:', truck);
            alert('Error: Invalid truck ID');
            return;
        }
        
        // Populate modal fields
        const deleteIdInput = document.getElementById('delete-truck-id');
        const deletePlateSpan = document.getElementById('delete-truck-plate');
        
        if (deleteIdInput) {
            deleteIdInput.value = truckId;
            console.log('✅ Set delete-truck-id to:', truckId);
        } else {
            console.error('❌ Element #delete-truck-id not found');
        }
        
        if (deletePlateSpan) {
            deletePlateSpan.textContent = plateNumber;
            console.log('✅ Set delete-truck-plate to:', plateNumber);
        } else {
            console.error('❌ Element #delete-truck-plate not found');
        }
    }
}

// Handle modal focus to prevent aria-hidden warnings
document.addEventListener('DOMContentLoaded', function() {
    // Modal cleanup on hide
    document.addEventListener('hide.bs.modal', function(event) {
        if (event.target.id === 'routeDetailsModal') {
            // Remove focus from any active button before hiding
            const activeElement = document.activeElement;
            if (activeElement && activeElement.tagName === 'BUTTON') {
                activeElement.blur();
            }
        }
    });

    // Initialize garbage collection manager
    window.garbageCollectionManager = new GarbageCollectionManager();
    
    console.log('✅ Garbage Collection scripts initialized');
});
