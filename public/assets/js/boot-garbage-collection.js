// Remove all import statements and use global classes
document.addEventListener('DOMContentLoaded', () => {
  console.log('🔄 Initializing Garbage Collection system...');
  
  // Check component availability
  const components = {
    AddTruckView: window.AddTruckView,
    EditTruckView: window.EditTruckView,
    RouteDetailsView: window.RouteDetailsView,
    Filters: window.Filters,
    RouteMapSelector: window.RouteMapSelector
  };

  // Log component status
  Object.entries(components).forEach(([name, component]) => {
    console.log(`${component ? '✅' : '❌'} ${name} is ${component ? 'available' : 'not available'}`);
  });

  try {
    // Initialize components
    if (components.AddTruckView) new AddTruckView('addTruckModal', 'addRouteMap');
    if (components.EditTruckView) new EditTruckView('editTruckModal', 'editRouteMap');
    if (components.RouteDetailsView) new RouteDetailsView('routeDetailsModal', 'route-map');
    if (components.Filters) new Filters();

    console.log('✅ Garbage Collection system initialized successfully');
  } catch (error) {
    console.error('❌ Error initializing components:', error);
  }
});
