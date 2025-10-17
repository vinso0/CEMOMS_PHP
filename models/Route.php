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
     * Create a new route with start, midpoint (optional), and end
     */
    public function createRoute($routeName, $startPoint, $midPoint, $endPoint)
    {
        // For now, we'll use a default area_id = 1
        // You can modify this later when implementing proper area management
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
        
        return $this->db->connection->lastInsertId();
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

    public function deleteRoute($routeId)
    {
        $query = "DELETE FROM route WHERE route_id = :route_id";
        $this->db->query($query, [':route_id' => $routeId]);
    }
}