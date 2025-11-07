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
     * Get only garbage collection trucks (operation_type_id = 1) with pagination
     */
    public function getGarbageCollectionTrucks($limit = null, $offset = 0)
    {
        $query = "SELECT
                    t.truck_id,
                    t.plate_number,
                    t.body_number,
                    t.foreman_id,

                    -- ROUTE COLUMNS
                    os.route_id,
                    r.route_name,
                    r.start_point,
                    r.mid_point,
                    r.end_point,

                    -- FOREMAN COLUMNS
                    f.username as foreman_name,

                    -- SCHEDULE COLUMNS
                    os.schedule_type as schedule,
                    os.operation_time,
                    os.status,

                    -- WEEKLY DAYS (if needed)
                    GROUP_CONCAT(sd.day_of_week ORDER BY
                        FIELD(sd.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
                        SEPARATOR ','
                    ) as weekly_days_str

                FROM truck t
                LEFT JOIN operation_schedule os ON t.truck_id = os.truck_id
                LEFT JOIN route r ON os.route_id = r.route_id
                LEFT JOIN foreman f ON t.foreman_id = f.foreman_id
                LEFT JOIN schedule_days sd ON os.schedule_id = sd.schedule_id
                GROUP BY t.truck_id, t.plate_number, t.body_number, t.foreman_id, os.route_id, r.route_name, f.username, os.schedule_type, os.operation_time, os.status
                ORDER BY t.truck_id DESC";

        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->connection->prepare($query);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $trucks = $stmt->fetchAll();
        } else {
            $trucks = $this->db->query($query)->get();
        }

        // Process Weekly days for each truck
        foreach ($trucks as &$truck) {
            if (!empty($truck['weekly_days_str'])) {
                $truck['weekly_days'] = explode(',', $truck['weekly_days_str']);
            } else {
                $truck['weekly_days'] = [];
            }
            // Remove the temporary string field
            unset($truck['weekly_days_str']);
        }

        return $trucks;
    }

    /**
     * Get all trucks with their assignments and schedules
     */
    public function getAllTrucks()
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
                    os.schedule_id,
                    os.schedule_type as schedule,
                    os.status,
                    os.dispatch_time,
                    os.return_time
                FROM truck t
                LEFT JOIN foreman f ON t.foreman_id = f.foreman_id
                LEFT JOIN operation_schedule os ON t.truck_id = os.truck_id
                LEFT JOIN route r ON os.route_id = r.route_id
                ORDER BY t.plate_number";
        
        return $this->db->query($sql)->get();
    }

    /**
     * Get truck by ID with full details
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
                    r.mid_point,
                    r.end_point,
                    os.schedule_id,
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
        try {
            $sql = "INSERT INTO truck (plate_number, body_number, foreman_id)
                    VALUES (:plate_number, :body_number, :foreman_id)";
            
            $this->db->query($sql, [
                ':plate_number' => $plateNumber,
                ':body_number' => $bodyNumber,
                ':foreman_id' => $foremanId
            ]);
            
            $lastId = $this->db->connection->lastInsertId();
            
            if ($lastId == 0) {
                throw new \Exception('Failed to get auto-increment ID. Please check database configuration.');
            }
            
            return $lastId;
            
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new \Exception('Duplicate entry or constraint violation: ' . $e->getMessage());
            }
            throw new \Exception('Database error creating truck: ' . $e->getMessage());
        }
    }


    /**
     * Update truck details
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

        return true;
    }

    /**
     * Delete truck (checks for dependencies)
     */
    public function delete($id)
    {
        // First check if truck has active schedules
        $checkSql = "SELECT COUNT(*) as count 
                     FROM operation_schedule 
                     WHERE truck_id = :id 
                     AND status IN ('Dispatched')";
        
        $result = $this->db->query($checkSql, [':id' => $id])->find();
        
        if ($result['count'] > 0) {
            throw new \Exception('Cannot delete truck with active dispatches. Please park the truck first.');
        }

        // Delete the truck
        $sql = "DELETE FROM truck WHERE truck_id = :id";
        $this->db->query($sql, [':id' => $id]);

        return true;
    }

    /**
     * Check if plate number already exists
     */
    public function plateNumberExists($plateNumber, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM truck 
                    WHERE plate_number = :plate_number AND truck_id != :id";
            $result = $this->db->query($sql, [
                ':plate_number' => $plateNumber,
                ':id' => $excludeId
            ])->find();
        } else {
            $sql = "SELECT COUNT(*) as count FROM truck WHERE plate_number = :plate_number";
            $result = $this->db->query($sql, [':plate_number' => $plateNumber])->find();
        }
        
        return $result['count'] > 0;
    }

    /**
     * Get garbage collection dispatch logs with pagination
     */
    public function getGarbageCollectionDispatchLogs($limit = 10, $offset = 0)
    {
        $sql = "SELECT 
                    os.schedule_id as id,
                    DATE(os.dispatch_time) as date,
                    t.truck_id,
                    t.plate_number,
                    t.body_number,
                    r.route_name,
                    f.username as foreman_name,
                    TIME_FORMAT(os.dispatch_time, '%h:%i %p') as dispatch_time,
                    TIME_FORMAT(os.return_time, '%h:%i %p') as return_time,
                    os.status
                FROM operation_schedule os
                INNER JOIN truck t ON os.truck_id = t.truck_id
                INNER JOIN route r ON os.route_id = r.route_id
                INNER JOIN foreman f ON os.foreman_id = f.foreman_id
                INNER JOIN operation o ON os.operation_id = o.operation_id
                WHERE os.dispatch_time IS NOT NULL
                AND o.operation_type_id = 1
                ORDER BY os.dispatch_time DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->connection->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get dispatch logs with pagination
     */
    public function getDispatchLogs($limit = 10, $offset = 0)
    {
        $sql = "SELECT 
                    os.schedule_id as id,
                    DATE(os.dispatch_time) as date,
                    t.truck_id,
                    t.plate_number,
                    t.body_number,
                    r.route_name,
                    f.username as foreman_name,
                    TIME_FORMAT(os.dispatch_time, '%h:%i %p') as dispatch_time,
                    TIME_FORMAT(os.return_time, '%h:%i %p') as return_time,
                    os.status
                FROM operation_schedule os
                INNER JOIN truck t ON os.truck_id = t.truck_id
                INNER JOIN route r ON os.route_id = r.route_id
                INNER JOIN foreman f ON os.foreman_id = f.foreman_id
                WHERE os.dispatch_time IS NOT NULL
                ORDER BY os.dispatch_time DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->connection->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get garbage collection trucks count
     */
    public function getGarbageCollectionTrucksCount()
    {
        $sql = "SELECT COUNT(DISTINCT t.truck_id) as count
                FROM truck t
                LEFT JOIN operation_schedule os ON t.truck_id = os.truck_id
                LEFT JOIN operation o ON os.operation_id = o.operation_id
                WHERE o.operation_type_id = 1 OR o.operation_type_id IS NULL";
        $result = $this->db->query($sql)->find();
        return $result['count'] ?? 0;
    }

    /**
     * Get trucks count
     */
    public function getTrucksCount()
    {
        $sql = "SELECT COUNT(*) as count FROM truck";
        $result = $this->db->query($sql)->find();
        return $result['count'] ?? 0;
    }

    /**
     * Get active trucks (with dispatched status)
     */
    public function getActiveTrucks()
    {
        $sql = "SELECT 
                    t.truck_id as id,
                    t.plate_number,
                    t.body_number,
                    os.status
                FROM truck t
                INNER JOIN operation_schedule os ON t.truck_id = os.truck_id
                WHERE os.status = 'Dispatched'
                ORDER BY t.plate_number";
        
        return $this->db->query($sql)->get();
    }
}