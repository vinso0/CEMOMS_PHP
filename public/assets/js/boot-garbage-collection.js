import { RouteDetailsView } from './components/RouteDetailsView.js';
import { EditTruckView } from './components/EditTruckView.js';
import { AddTruckView } from './components/AddTruckView.js';
import { Filters } from './components/Filters.js';

document.addEventListener('DOMContentLoaded', () => {
  const addView  = new AddTruckView('addTruckModal', 'addRouteMap');
  const editView = new EditTruckView('editTruckModal', 'editRouteMap');
  const details  = new RouteDetailsView('routeDetailsModal', 'route-map');
  new Filters().wire();

  // global handlers (optional)
  window.populateEditModal = (data) => editView.populate(data);
  window.populateRouteDetailsModal = (data) => details.open(data);
});