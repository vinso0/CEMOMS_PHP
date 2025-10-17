<?php

namespace Models;

use Core\App;
use Core\Database;

class Truck
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Get all trucks with their assignments
     */
    public function getAllTrucks()
    {
        $sql = "SELECT 
                    t.truck_id as id,
                    t.plate_number,
                    t.body_number,
                    f.username as foreman_name,
                    f.foreman_id,
                    r.route_name,
                    r.route_id,
                    r.start_point,
                    r.end_point,
                    os.schedule_type as schedule,
                    os.status
                FROM truck t
                LEFT JOIN foreman f ON t.foreman_id = f.foreman_id
                LEFT JOIN operation_schedule os ON t.truck_id = os.truck_id
                LEFT JOIN route r ON os.route_id = r.route_id
                ORDER BY t.plate_number";
        
        return $this->db->query($sql)->get();
    }

    /**
     * Get truck by ID
     */
    public function getTruckById($id)
    {
        $sql = "SELECT 
                    t.truck_id as id,
                    t.plate_number,
                    t.body_number,
                    t.foreman_id,
                    f.username as foreman_name,
                    r.route_id,
                    r.route_name,
                    r.start_point,
                    r.end_point,
                    os.schedule_type as schedule,
                    os.status
                FROM truck t
                LEFT JOIN foreman f ON t.foreman_id = f.foreman_id
                LEFT JOIN operation_schedule os ON t.truck_id = os.truck_id
                LEFT JOIN route r ON os.route_id = r.route_id
                WHERE t.truck_id = :id";
        
        return $this->db->query($sql, [':id' => $id])->find();
    }

    /**
     * Create new truck
     */
    public function create($plateNumber, $bodyNumber, $foremanId)
    {
        $sql = "INSERT INTO truck (plate_number, body_number, foreman_id)
                VALUES (:plate_number, :body_number, :foreman_id)";
        
        $this->db->query($sql, [
            ':plate_number' => $plateNumber,
            ':body_number' => $bodyNumber,
            ':foreman_id' => $foremanId
        ]);
        
        // Get the last inserted ID
        return $this->db->connection->lastInsertId();
    }

    /**
     * Update truck
     */
    public function update($id, $plateNumber, $bodyNumber, $foremanId)
    {
        $sql = "UPDATE truck 
                SET plate_number = :plate_number,
                    body_number = :body_number,
                    foreman_id = :foreman_id
                WHERE truck_id = :id";
        
        $this->db->query($sql, [
            ':plate_number' => $plateNumber,
            ':body_number' => $bodyNumber,
            ':foreman_id' => $foremanId,
            ':id' => $id
        ]);
    }

    /**
     * Delete truck
     */
    public function delete($id)
    {
        $sql = "DELETE FROM truck WHERE truck_id = :id";
        $this->db->query($sql, [':id' => $id]);
    }

    /**
     * Get dispatch logs
     */
    public function getDispatchLogs($limit = 10)
    {
        $sql = "SELECT 
                    os.schedule_id as id,
                    DATE(os.dispatch_time) as date,
                    t.plate_number,
                    r.route_name,
                    f.username as foreman_name,
                    TIME_FORMAT(os.dispatch_time, '%h:%i %p') as dispatch_time,
                    TIME_FORMAT(os.return_time, '%h:%i %p') as return_time,
                    os.status
                FROM operation_schedule os
                JOIN truck t ON os.truck_id = t.truck_id
                JOIN route r ON os.route_id = r.route_id
                JOIN foreman f ON os.foreman_id = f.foreman_id
                WHERE os.dispatch_time IS NOT NULL
                ORDER BY os.dispatch_time DESC
                LIMIT :limit";
        
        $stmt = $this->db->connection->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}