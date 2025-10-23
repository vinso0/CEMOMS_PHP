<div class="modal fade" id="deleteTruckModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Delete Truck?</h5>
                <p class="text-muted mb-3">
                    Are you sure you want to delete truck <strong id="delete-truck-plate"></strong>? 
                    This action cannot be undone.
                </p>
                <form method="POST" action="/admin/operations/garbage_collection/delete">
                    <input type="hidden" id="delete-truck-id" name="id">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>