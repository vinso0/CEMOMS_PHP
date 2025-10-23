<?php ?>
<table class="trucks-table">
    <thead>
        <tr>
            <th>Truck</th>
            <th>Assigned Foreman</th>
            <th>Route</th>
            <th>Schedule</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="trucks-tbody">
        <?php if (!empty($trucks)): ?>
            <?php foreach ($trucks as $truck): ?>
                <tr data-truck-id="<?= $truck['id'] ?>" 
                    data-foreman-id="<?= $truck['foreman_id'] ?>"
                    data-status="<?= $truck['status'] ?>">
                    <!-- Truck details -->
                    <td>
                        <strong><?= htmlspecialchars($truck['plate_number']) ?></strong>
                        <br>
                        <small class="text-muted"><?= htmlspecialchars($truck['body_number']) ?></small>
                    </td>
                    
                    <!-- Other columns... -->
                    
                    <td>
                        <div class="action-buttons">
                            <!-- Action buttons... -->
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No trucks found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>