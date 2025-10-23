<?php ?>
<table class="logs-table">
    <thead>
        <tr>
            <th>Date/Time</th>
            <th>Truck</th>
            <th>Foreman</th>
            <th>Action</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <!-- Log details... -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No logs found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>