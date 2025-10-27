<?php
// Route Details Modal
?>
<div class="modal fade" id="routeDetailsModal" tabindex="-1" aria-labelledby="routeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routeDetailsModalLabel">Route Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="route-details-container">
                    <div class="route-info-grid">
                        <div class="route-info-item">
                            <div class="route-info-label">Plate Number</div>
                            <div class="route-info-value" id="details-plate-number">-</div>
                        </div>
                        <div class="route-info-item">
                            <div class="route-info-label">Body Number</div>
                            <div class="route-info-value" id="details-body-number">-</div>
                        </div>
                        <div class="route-info-item">
                            <div class="route-info-label">Route Name</div>
                            <div class="route-info-value" id="details-route-name">-</div>
                        </div>
                        <div class="route-info-item">
                            <div class="route-info-label">Foreman</div>
                            <div class="route-info-value" id="details-foreman">-</div>
                        </div>
                        <div class="route-info-item">
                            <div class="route-info-label">Schedule Type</div>
                            <div class="route-info-value" id="details-schedule">-</div>
                        </div>
                        <div class="route-info-item">
                            <div class="route-info-label">Status</div>
                            <div class="route-info-value" id="details-status">-</div>
                        </div>
                    </div>

                    <div class="route-points-display">
                        <h6 class="mb-3">Route Points</h6>
                        <div id="route-points-list">
                            <!-- Route points will be populated here -->
                        </div>
                    </div>

                    <div id="routeDetailsMap"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>