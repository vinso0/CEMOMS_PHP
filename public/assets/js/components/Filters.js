
class Filters {
  constructor() {
    this.wire();
  }

  wire() {
    // Convert any existing onclick handlers to event listeners
    const filterButtons = document.querySelectorAll('[onclick*="applyFilters"], [onclick*="resetFilters"]');
    filterButtons.forEach(button => {
      const action = button.getAttribute('onclick');
      button.removeAttribute('onclick');
      
      if (action.includes('applyFilters')) {
        button.addEventListener('click', () => this.apply());
      } else if (action.includes('resetFilters')) {
        button.addEventListener('click', () => this.reset());
      }
    });

    // Expose global functions for legacy compatibility
    window.applyFilters = () => this.apply();
    window.resetFilters = () => this.reset();
  }

  apply() {
    const filters = {
      truck: document.getElementById('filter-truck')?.value || '',
      foreman: document.getElementById('filter-foreman')?.value || '',
      status: document.getElementById('filter-status')?.value || '',
      search: document.getElementById('filter-search')?.value || ''
    };
    
    const rows = document.querySelectorAll('#trucks-tbody tr[data-truck-id]');
    
    rows.forEach(row => {
      let showRow = true;
      
      // Filter by truck
      if (filters.truck && row.dataset.truckId !== filters.truck) {
        showRow = false;
      }
      
      // Filter by foreman
      if (filters.foreman && row.dataset.foremanId !== filters.foreman) {
        showRow = false;
      }
      
      // Filter by status
      if (filters.status && row.dataset.status !== filters.status) {
        showRow = false;
      }
      
      // Filter by search
      if (filters.search) {
        const searchText = row.textContent.toLowerCase();
        if (!searchText.includes(filters.search.toLowerCase())) {
          showRow = false;
        }
      }
      
      row.style.display = showRow ? '' : 'none';
    });
    
    this.updateResults();
  }
  
  reset() {
    const filterTruck = document.getElementById('filter-truck');
    const filterForeman = document.getElementById('filter-foreman');
    const filterStatus = document.getElementById('filter-status');
    const filterSearch = document.getElementById('filter-search');
    
    if (filterTruck) filterTruck.value = '';
    if (filterForeman) filterForeman.value = '';
    if (filterStatus) filterStatus.value = '';
    if (filterSearch) filterSearch.value = '';
    
    // Show all rows
    document.querySelectorAll('#trucks-tbody tr').forEach(row => {
      row.style.display = '';
    });
    
    this.updateResults();
  }
  
  updateResults() {
    const visibleRows = document.querySelectorAll('#trucks-tbody tr[data-truck-id][style=""], #trucks-tbody tr[data-truck-id]:not([style])');
    console.log(`Showing ${visibleRows.length} trucks`);
  }
}

window.Filters = Filters;