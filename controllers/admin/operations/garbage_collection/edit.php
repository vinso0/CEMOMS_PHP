<?php
adminAuth();

use Models\OperationSchedule;
use Models\Route;
use Models\Truck;

require_once base_path('models/Schedule.php');
require_once base_path('models/Route.php');
require_once base_path('models/Truck.php');

$scheduleModel = new OperationSchedule();
$routeModel = new Route();
$truckModel = new Truck();

$id = $_GET['id'];
$collection = $scheduleModel->getByTruckId($id); // You may need to implement this method
$routes = $routeModel->getAllRoutes();
$trucks = $truckModel->getAllTrucks();

require base_path('views/admin/operations/garbage_collection/edit.php');