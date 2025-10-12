<h2>Add Garbage Collection Schedule</h2>

<form action="/admin/operations/garbage_collection/store" method="POST">
    <table cellpadding="8">
        <tr>
            <td><label for="route_id">Route:</label></td>
            <td>
                <select name="route_id" id="route_id" required>
                    <option value="">-- Select Route --</option>
                    <?php foreach ($routes as $route): ?>
                        <option value="<?= htmlspecialchars($route['route_id']) ?>">
                            <?= htmlspecialchars($route['route_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td><label for="truck_id">Truck:</label></td>
            <td>
                <select name="truck_id" id="truck_id" required>
                    <option value="">-- Select Truck --</option>
                    <?php foreach ($trucks as $truck): ?>
                        <option value="<?= htmlspecialchars($truck['truck_id']) ?>">
                            <?= htmlspecialchars($truck['truck_type']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td><label for="collection_date">Collection Date:</label></td>
            <td><input type="date" name="collection_date" id="collection_date" required></td>
        </tr>

        <tr>
            <td><label for="status">Status:</label></td>
            <td>
                <select name="status" id="status" required>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </td>
        </tr>

        <tr>
            <td colspan="2" align="right">
                <button type="submit">Save</button>
                <a href="/admin/operations/garbage_collection">Cancel</a>
            </td>
        </tr>
    </table>
</form>