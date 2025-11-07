console.log('🔍 Debug: Script loading check');
console.log('L (Leaflet):', typeof L);
console.log('RouteMapSelector:', typeof window.RouteMapSelector);
console.log('AddSweeperView:', typeof window.AddSweeperView);
console.log('EditSweeperView:', typeof window.EditSweeperView);

document.addEventListener('DOMContentLoaded', () => {
  // Classes will be loaded as globals from individual script tags
  if (window.AddSweeperView) new AddSweeperView('addSweeperModal', 'addSweeperRouteMap');
  if (window.EditSweeperView) new EditSweeperView('editSweeperModal', 'editSweeperRouteMap');
  if (window.RouteDetailsView) new RouteDetailsView('routeDetailsModal', 'route-map');
  if (window.Filters) new Filters();

  console.log('🎯 Street Sweeping system initialized');
});