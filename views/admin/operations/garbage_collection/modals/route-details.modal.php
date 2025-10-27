<?php
// Simplified Route Details Modal - HTML only (JavaScript handled by garbage-collection.js)
?>
<div class="modal fade" id="routeDetailsModal" tabindex="-1" aria-labelledby="routeDetailsModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routeDetailsModalLabel">
                    <i class="fas fa-route me-2"></i>Route Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="route-details-container">
                    <!-- Truck Information & Route Assignment -->
                    <div class="info-section p-3 bg-light border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-truck me-1"></i>Truck Information
                                </h6>
                                <div class="info-item">
                                    <strong>Plate Number:</strong> 
                                    <span id="details-plate-number">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Body Number:</strong> 
                                    <span id="details-body-number">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success mb-2">
                                    <i class="fas fa-route me-1"></i>Route Assignment
                                </h6>
                                <div class="info-item">
                                    <strong>Route:</strong> 
                                    <span id="details-route-name">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Foreman:</strong> 
                                    <span id="details-foreman">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Route Points & Map -->
                    <div class="row g-0">
                        <!-- Route Points List -->
                        <div class="col-md-4 border-end">
                            <div class="points-section">
                                <div class="points-header p-2 bg-secondary text-white">
                                    <h6 class="mb-0">Route Points</h6>
                                </div>
                                <div class="points-list" id="route-points-list">
                                    <div class="loading-points text-center p-4">
                                        <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Loading route points...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map -->
                        <div class="col-md-8">
                            <div class="map-section">
                                <div class="map-header p-2 bg-dark text-white">
                                    <small>Interactive Route Map</small>
                                </div>
                                <div id="route-map" style="height: 360px; background: #f0f0f0;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Keep the CSS styles -->
<style>
/* Styles for route details modal */
.route-details-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.info-section .info-item {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.points-section {
    height: 400px;
    display: flex;
    flex-direction: column;
}

.points-list {
    flex: 1;
    overflow-y: auto;
    background: #f8f9fa;
}

.point-item {
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
    background: white;
    margin: 0.25rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.point-item:hover {
    background: #e3f2fd;
}

.point-item.active {
    background: #bbdefb;
    border-left: 4px solid #2196f3;
}

.point-number {
    display: inline-block;
    width: 20px;
    height: 20px;
    background: #007bff;
    color: white;
    text-align: center;
    border-radius: 50%;
    font-size: 0.75rem;
    font-weight: bold;
    line-height: 20px;
    margin-right: 0.5rem;
}

.point-number.start { background: #28a745; }
.point-number.end { background: #dc3545; }

.point-name {
    font-weight: 600;
    color: #212529;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.point-address {
    color: #6c757d;
    font-size: 0.75rem;
    line-height: 1.2;
}

.loading-points, .empty-points {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
}

/* Add this to your route-details.modal.php styles or main CSS */
.leaflet-container {
    height: 100%;
    width: 100%;
    background: #f0f0f0;
}

.leaflet-tile {
    filter: none !important;
}

#route-map .leaflet-container {
    min-height: 360px;
}


/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-dialog.modal-lg {
        margin: 0.5rem;
        max-width: none;
    }
    
    .row.g-0 > .col-md-4,
    .row.g-0 > .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .points-section {
        height: 200px;
    }
    
    #route-map {
        height: 300px !important;
    }
}
</style>
