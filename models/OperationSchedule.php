<?php

namespace Models;

use Core\App;
use Core\Database;

class OperationSchedule
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Create operation schedule
     */
    public function create($operationId, $routeId, $areaId, $truckId, $adminId, $foremanId, $scheduleType, $status)
    {
        $sql = "INSERT INTO operation_schedule 
                (operation_id, route_id, area_id, truck_id, admin_id, foreman_id, schedule_type, status)
                VALUES (:operation_id, :route_id, :area_id, :truck_id, :admin_id, :foreman_id, :schedule_type, :status)";
        
        $this->db->query($sql, [
            ':operation_id' => $operationId,
            ':route_id' => $routeId,
            ':area_id' => $areaId,
            ':truck_id' => $truckId,
            ':admin_id' => $adminId,
            ':foreman_id' => $foremanId,
            ':schedule_type' => $scheduleType,
            ':status' => $status
        ]);
        
        return $this->db->connection->lastInsertId();
    }

    /**
     * Update operation schedule
     */
    public function update($scheduleId, $routeId, $scheduleType, $status)
    {
        $sql = "UPDATE operation_schedule 
                SET route_id = :route_id,
                    schedule_type = :schedule_type,
                    status = :status
                WHERE schedule_id = :schedule_id";
        
        $this->db->query($sql, [
            ':route_id' => $routeId,
            ':schedule_type' => $scheduleType,
            ':status' => $status,
            ':schedule_id' => $scheduleId
        ]);
    }

    /**
     * Log dispatch
     */
    public function logDispatch($scheduleId, $dispatchTime)
    {
        $sql = "UPDATE operation_schedule 
                SET dispatch_time = :dispatch_time,
                    status = 'Dispatched'
                WHERE schedule_id = :schedule_id";
        
        $this->db->query($sql, [
            ':dispatch_time' => $dispatchTime,
            ':schedule_id' => $scheduleId
        ]);
    }

    /**
     * Log return
     */
    public function logReturn($scheduleId, $returnTime)
    {
        $sql = "UPDATE operation_schedule 
                SET return_time = :return_time,
                    status = 'Parked'
                WHERE schedule_id = :schedule_id";
        
        $this->db->query($sql, [
            ':return_time' => $returnTime,
            ':schedule_id' => $scheduleId
        ]);
    }

    /**
     * Get schedule by truck ID
     */
    public function getByTruckId($truckId)
    {
        $sql = "SELECT * FROM operation_schedule 
                WHERE truck_id = :truck_id 
                ORDER BY schedule_id DESC 
                LIMIT 1";
        
        return $this->db->query($sql, [':truck_id' => $truckId])->find();
    }
}