<!-- View Truck Details Modal -->
<div class="modal fade" id="viewTruckModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-truck me-2"></i>
                    <span id="modalTruckTitle">Truck Details</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column - Truck Details -->
                    <div class="col-md-4">
                        <div class="truck-details-card">
                            <h6 class="details-header">
                                <i class="fas fa-truck me-2"></i>Truck Information
                            </h6>
                            
                            <div class="detail-item">
                                <label>Plate Number:</label>
                                <span id="detailPlateNumber" class="detail-value"></span>
                            </div>
                            
                            <div class="detail-item">
                                <label>Body Number:</label>
                                <span id="detailBodyNumber" class="detail-value"></span>
                            </div>
                            
                            <div class="detail-item">
                                <label>Status:</label>
                                <span id="detailStatus" class="detail-value"></span>
                            </div>
                        </div>

                        <div class="truck-details-card">
                            <h6 class="details-header">
                                <i class="fas fa-user-tie me-2"></i>Assignment
                            </h6>
                            
                            <div class="detail-item">
                                <label>Foreman:</label>
                                <span id="detailForeman" class="detail-value"></span>
                            </div>
                            
                            <div class="detail-item">
                                <label>Schedule:</label>
                                <span id="detailSchedule" class="detail-value"></span>
                            </div>
                        </div>

                        <div class="truck-details-card">
                            <h6 class="details-header">
                                <i class="fas fa-route me-2"></i>Route Details
                            </h6>
                            
                            <div class="detail-item">
                                <label>Route Name:</label>
                                <span id="detailRouteName" class="detail-value"></span>
                            </div>
                            
                            <div class="detail-item">
                                <label>Start Point:</label>
                                <span id="detailStartPoint" class="detail-value"></span>
                            </div>
                            
                            <div class="detail-item" id="detailMidPointContainer" style="display: none;">
                                <label>Mid Point:</label>
                                <span id="detailMidPoint" class="detail-value"></span>
                            </div>
                            
                            <div class="detail-item">
                                <label>End Point:</label>
                                <span id="detailEndPoint" class="detail-value"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Map -->
                    <div class="col-md-8">
                        <div class="map-container">
                            <h6 class="details-header">
                                <i class="fas fa-map me-2"></i>Route Map
                            </h6>
                            <div id="viewTruckMap" class="view-details-map"></div>
                            <div class="map-legend mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt text-success me-1"></i>Start Point
                                    <i class="fas fa-map-marker-alt text-warning mx-2"></i>Mid Point
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>End Point
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editTruck()">
                    <i class="fas fa-edit me-1"></i>Edit Truck
                </button>
            </div>
        </div>
    </div>
</div>
