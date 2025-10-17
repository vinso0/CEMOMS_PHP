<?php

namespace Models;

use Core\App;
use Core\Database;

class Route
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    public function getAllRoutes()
    {
        $query = "SELECT route_id, route_name, area_id, start_point, mid_point, end_point, date_created 
                  FROM route 
                  ORDER BY route_name";
        return $this->db->query($query)->get();
    }

    /**
     * Create a new route with coordinates
     */
    public function createRoute($routeName, $startPoint, $midPoint, $endPoint)
    {
        $areaId = 1;
        
        $query = "INSERT INTO route (route_name, area_id, start_point, mid_point, end_point, date_created) 
                  VALUES (:route_name, :area_id, :start_point, :mid_point, :end_point, NOW())";
        
        $this->db->query($query, [
            ':route_name' => $routeName,
            ':area_id' => $areaId,
            ':start_point' => $startPoint,
            ':mid_point' => $midPoint ?: null,
            ':end_point' => $endPoint
        ]);
        
        $routeId = $this->db->connection->lastInsertId();
        
        return $routeId;
    }

    /**
     * Create route with coordinates stored in route_points table
     */
    public function createRouteWithCoordinates($routeName, $startPoint, $startLat, $startLon, 
                                                $midPoint = null, $midLat = null, $midLon = null,
                                                $endPoint, $endLat, $endLon)
    {
        $areaId = 1;
        
        // Insert main route
        $query = "INSERT INTO route (route_name, area_id, start_point, mid_point, end_point, date_created) 
                  VALUES (:route_name, :area_id, :start_point, :mid_point, :end_point, NOW())";
        
        $this->db->query($query, [
            ':route_name' => $routeName,
            ':area_id' => $areaId,
            ':start_point' => $startPoint,
            ':mid_point' => $midPoint,
            ':end_point' => $endPoint
        ]);
        
        $routeId = $this->db->connection->lastInsertId();
        
        // Insert route points
        $this->insertRoutePoint($routeId, $startLat, $startLon, 1, 'Start');
        
        if ($midPoint && $midLat && $midLon) {
            $this->insertRoutePoint($routeId, $midLat, $midLon, 2, 'Midpoint');
            $this->insertRoutePoint($routeId, $endLat, $endLon, 3, 'End');
        } else {
            $this->insertRoutePoint($routeId, $endLat, $endLon, 2, 'End');
        }
        
        return $routeId;
    }

    /**
     * Insert a route point
     */
    private function insertRoutePoint($routeId, $lat, $lon, $order, $label)
    {
        $query = "INSERT INTO route_points (route_id, latitude, longtitude, point_order, label) 
                  VALUES (:route_id, :latitude, :longtitude, :point_order, :label)";
        
        $this->db->query($query, [
            ':route_id' => $routeId,
            ':latitude' => $lat,
            ':longtitude' => $lon,
            ':point_order' => $order,
            ':label' => $label
        ]);
    }

    /**
     * Get route points for a route
     */
    public function getRoutePoints($routeId)
    {
        $query = "SELECT * FROM route_points 
                  WHERE route_id = :route_id 
                  ORDER BY point_order";
        return $this->db->query($query, [':route_id' => $routeId])->get();
    }

    public function addRoute($routeName, $areaId, $startPoint, $endPoint)
    {
        $query = "INSERT INTO route (route_name, area_id, start_point, end_point, date_created) 
                  VALUES (:route_name, :area_id, :start_point, :end_point, NOW())";
        $this->db->query($query, [
            ':route_name' => $routeName,
            ':area_id' => $areaId,
            ':start_point' => $startPoint,
            ':end_point' => $endPoint
        ]);
    }

    public function getRouteById($routeId)
    {
        $query = "SELECT route_id, route_name, area_id, start_point, mid_point, end_point, date_created 
                  FROM route 
                  WHERE route_id = :route_id";
        return $this->db->query($query, [':route_id' => $routeId])->find();
    }

    public function updateRoute($routeId, $routeName, $startPoint, $midPoint, $endPoint)
    {
        $query = "UPDATE route 
                  SET route_name = :route_name,
                      start_point = :start_point,
                      mid_point = :mid_point,
                      end_point = :end_point
                  WHERE route_id = :route_id";
        $this->db->query($query, [
            ':route_name' => $routeName,
            ':start_point' => $startPoint,
            ':mid_point' => $midPoint ?: null,
            ':end_point' => $endPoint,
            ':route_id' => $routeId
        ]);
    }

    /**
     * Update route with coordinates
     */
    public function updateRouteWithCoordinates($routeId, $routeName, 
                                                $startPoint, $startLat, $startLon,
                                                $midPoint = null, $midLat = null, $midLon = null,
                                                $endPoint, $endLat, $endLon)
    {
        // Update main route
        $query = "UPDATE route 
                  SET route_name = :route_name,
                      start_point = :start_point,
                      mid_point = :mid_point,
                      end_point = :end_point
                  WHERE route_id = :route_id";
        
        $this->db->query($query, [
            ':route_name' => $routeName,
            ':start_point' => $startPoint,
            ':mid_point' => $midPoint,
            ':end_point' => $endPoint,
            ':route_id' => $routeId
        ]);
        
        // Delete existing route points
        $this->deleteRoutePoints($routeId);
        
        // Insert new route points
        $this->insertRoutePoint($routeId, $startLat, $startLon, 1, 'Start');
        
        if ($midPoint && $midLat && $midLon) {
            $this->insertRoutePoint($routeId, $midLat, $midLon, 2, 'Midpoint');
            $this->insertRoutePoint($routeId, $endLat, $endLon, 3, 'End');
        } else {
            $this->insertRoutePoint($routeId, $endLat, $endLon, 2, 'End');
        }
    }

    /**
     * Delete route points for a route
     */
    private function deleteRoutePoints($routeId)
    {
        $query = "DELETE FROM route_points WHERE route_id = :route_id";
        $this->db->query($query, [':route_id' => $routeId]);
    }

    public function deleteRoute($routeId)
    {
        // Delete route points first
        $this->deleteRoutePoints($routeId);
        
        // Delete route
        $query = "DELETE FROM route WHERE route_id = :route_id";
        $this->db->query($query, [':route_id' => $routeId]);
    }
}