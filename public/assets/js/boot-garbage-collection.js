// Remove all import statements and use global classes
document.addEventListener('DOMContentLoaded', () => {
  // Classes will be loaded as globals from individual script tags
  if (window.AddTruckView) new AddTruckView('addTruckModal', 'addRouteMap');
  if (window.EditTruckView) new EditTruckView('editTruckModal', 'editRouteMap');  
  if (window.RouteDetailsView) new RouteDetailsView('routeDetailsModal', 'route-map');
  if (window.Filters) new Filters();
  
  console.log('🎯 Garbage Collection system initialized');
});