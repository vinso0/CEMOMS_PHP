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
                    <!-- Sweeper Information & Route Assignment -->
                    <div class="info-section p-3 bg-light border-bottom">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-broom me-1"></i>Sweeper Information
                                </h6>
                                <div class="info-item">
                                    <strong>Operation Name:</strong>
                                    <span id="details-operation-name">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Status:</strong>
                                    <span id="details-status">-</span>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <!-- Schedule Information -->
                            <div class="col-md-4">
                                <h6 class="text-info mb-2">
                                    <i class="fas fa-calendar-alt me-1"></i>Schedule Details
                                </h6>
                                <div class="info-item">
                                    <strong>Type:</strong>
                                    <span id="details-schedule-type">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Operation Time:</strong>
                                    <span id="details-operation-time">-</span>
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
                                <div id="route-map"></div>
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
