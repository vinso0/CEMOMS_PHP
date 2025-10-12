<?php

namespace Models;

use Core\App;
use Core\Database;
use PDO;

class Route
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    public function getAllRoutes()
    {
        $query = "SELECT * FROM route";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRoute($routeName, $description)
    {
        $query = "INSERT INTO route (route_name, description) VALUES (:route_name, :description)";
        $this->db->query($query, [
            ':route_name' => $routeName,
            ':description' => $description
        ]);
    }

    public function getRouteById($routeId)
    {
        $query = "SELECT * FROM route WHERE route_id = :route_id";
        return $this->db->query($query, [':route_id' => $routeId])->find();
    }
}