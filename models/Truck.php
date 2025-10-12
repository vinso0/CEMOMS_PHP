<?php

namespace Models;

use Core\App;
use Core\Database;
use PDO;

class Truck
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    public function getAllTrucks()
    {
        $query = "SELECT * FROM truck";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTruck($truckType, $plateNumber)
    {
        $query = "INSERT INTO truck (truck_type, plate_number) VALUES (:truck_type, :plate_number)";
        $this->db->query($query, [
            ':truck_type' => $truckType,
            ':plate_number' => $plateNumber
        ]);
    }

    public function getTruckById($truckId)
    {
        $query = "SELECT * FROM truck WHERE truck_id = :truck_id";
        return $this->db->query($query, [':truck_id' => $truckId])->find();
    }
}