import { AddTruckView } from './components/AddTruckView.js';
import { EditTruckView } from './components/EditTruckView.js';
import { RouteDetailsView } from './components/RouteDetailsView.js';
import { Filters } from './components/Filters.js';

document.addEventListener('DOMContentLoaded', () => {
  new AddTruckView('addTruckModal', 'addRouteMap');
  new EditTruckView('editTruckModal', 'editRouteMap');
  new RouteDetailsView('routeDetailsModal', 'route-map');
  new Filters();
  
  console.log('🎯 Garbage Collection system initialized');
});
