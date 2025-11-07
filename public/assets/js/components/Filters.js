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

    // Add real-time filtering on input changes
    this.attachAutoFilters();

    // Expose global functions for legacy compatibility
    window.applyFilters = () => this.apply();
    window.resetFilters = () => this.reset();
    
    console.log('✅ Filters initialized');
  }

  /**
   * Attach event listeners for automatic filtering
   */
  attachAutoFilters() {
    const scheduleFilter = document.getElementById('filter-schedule');
    const foremanFilter = document.getElementById('filter-foreman');
    const statusFilter = document.getElementById('filter-status');
    const searchFilter = document.getElementById('filter-search');

    if (scheduleFilter) {
      scheduleFilter.addEventListener('change', () => this.apply());
    }

    if (foremanFilter) {
      foremanFilter.addEventListener('change', () => this.apply());
    }

    if (statusFilter) {
      statusFilter.addEventListener('change', () => this.apply());
    }

    if (searchFilter) {
      searchFilter.addEventListener('input', () => this.apply());
    }
  }

  /**
   * Apply all active filters to the trucks table
   */
  apply() {
    console.log('🔍 Applying filters...');
    
    const filters = {
      schedule: document.getElementById('filter-schedule')?.value.toLowerCase().trim() || '',
      foreman: document.getElementById('filter-foreman')?.value || '',
      status: document.getElementById('filter-status')?.value || '',
      search: document.getElementById('filter-search')?.value.toLowerCase().trim() || ''
    };
    
    console.log('Filter values:', filters);
    
    const rows = document.querySelectorAll('#trucks-tbody tr[data-truck-id]');
    let visibleCount = 0;
    
    rows.forEach(row => {
      let showRow = true;
      
      // Filter by schedule type (5th column - Schedule Type)
      if (filters.schedule) {
        const scheduleCell = row.querySelector('td:nth-child(5)');
        const scheduleType = scheduleCell?.textContent.toLowerCase().trim() || '';
        
        if (!scheduleType.includes(filters.schedule)) {
          showRow = false;
        }
      }
      
      // Filter by foreman (using data attribute)
      if (filters.foreman && row.dataset.foremanId !== filters.foreman) {
        showRow = false;
      }
      
      // Filter by status (using data attribute)
      if (filters.status && row.dataset.status !== filters.status) {
        showRow = false;
      }
      
      // Filter by search text (searches across all visible columns)
      if (filters.search) {
        const plateNumber = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
        const bodyNumber = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const routeName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
        const foremanName = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        const scheduleType = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
        
        const matchesSearch = 
          plateNumber.includes(filters.search) ||
          bodyNumber.includes(filters.search) ||
          routeName.includes(filters.search) ||
          foremanName.includes(filters.search) ||
          scheduleType.includes(filters.search);
        
        if (!matchesSearch) {
          showRow = false;
        }
      }
      
      // Show or hide row
      row.style.display = showRow ? '' : 'none';
      
      if (showRow) {
        visibleCount++;
      }
    });
    
    console.log(`✅ Showing ${visibleCount} of ${rows.length} trucks`);
    this.updateResults(visibleCount, rows.length);
  }
  
  /**
   * Reset all filters and show all trucks
   */
  reset() {
    console.log('🔄 Resetting filters...');

    const filterSchedule = document.getElementById('filter-schedule');
    const filterForeman = document.getElementById('filter-foreman');
    const filterStatus = document.getElementById('filter-status');
    const filterSearch = document.getElementById('filter-search');

    if (filterSchedule) filterSchedule.value = '';
    if (filterForeman) filterForeman.value = '';
    if (filterStatus) filterStatus.value = '';
    if (filterSearch) filterSearch.value = '';

    // Show all rows
    const rows = document.querySelectorAll('#trucks-tbody tr[data-truck-id]');
    rows.forEach(row => {
      row.style.display = '';
    });

    console.log('✅ Filters reset');
    this.updateResults(rows.length, rows.length);

    // Reset pagination to page 1 if there are pagination links
    const currentUrl = new URL(window.location);
    if (currentUrl.searchParams.has('page')) {
      currentUrl.searchParams.set('page', '1');
      window.location.href = currentUrl.toString();
    }
  }
  
  /**
   * Update results display and log
   * @param {number} visibleCount - Number of visible trucks
   * @param {number} totalCount - Total number of trucks
   */
  updateResults(visibleCount, totalCount) {
    // Update table info display
    const tableInfo = document.querySelector('.table-info');
    if (tableInfo) {
      tableInfo.textContent = `Showing ${visibleCount} of ${totalCount} trucks`;
    }

    // Remove any existing "no results" message
    const existingMessage = document.querySelector('#trucks-tbody .no-results-row');
    if (existingMessage) {
      existingMessage.remove();
    }

    // Show "no results" message if no trucks are visible
    if (visibleCount === 0) {
      this.showNoResultsMessage();
    }

    // Log results
    console.log(`📊 Filter results: ${visibleCount} of ${totalCount} trucks visible`);
  }
  
  /**
   * Display "no results found" message
   */
  showNoResultsMessage() {
    const tbody = document.getElementById('trucks-tbody');
    if (!tbody) return;
    
    const noResultsRow = document.createElement('tr');
    noResultsRow.className = 'no-results-row';
    noResultsRow.innerHTML = `
      <td colspan="7" class="text-center py-5">
        <div class="empty-state">
          <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
          <h5 class="text-muted">No Trucks Found</h5>
          <p class="text-muted mb-0">Try adjusting your filters or search terms</p>
        </div>
      </td>
    `;
    
    tbody.appendChild(noResultsRow);
  }
}

window.Filters = Filters;
