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
    public function create($operationId, $routeId, $areaId, $truckId, $adminId, $foremanId, $scheduleType, $status, $operationTime = null)
    {
    $query = "INSERT INTO operation_schedule (operation_id, route_id, area_id, truck_id, admin_id, foreman_id, schedule_type, status, operation_time) 
                VALUES (:operation_id, :route_id, :area_id, :truck_id, :admin_id, :foreman_id, :schedule_type, :status, :operation_time)";

    $this->db->query($query, [
        ':operation_id' => $operationId,
        ':route_id' => $routeId,
        ':area_id' => $areaId,
        ':truck_id' => $truckId,
        ':admin_id' => $adminId,
        ':foreman_id' => $foremanId,
        ':schedule_type' => $scheduleType,
        ':status' => $status,
        ':operation_time' => $operationTime
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

        return true;
    }

    /**
     * Update schedule with foreman change
     */
    public function updateWithForeman($scheduleId, $routeId, $foremanId, $scheduleType, $status)
    {
        $sql = "UPDATE operation_schedule 
                SET route_id = :route_id,
                    foreman_id = :foreman_id,
                    schedule_type = :schedule_type,
                    status = :status
                WHERE schedule_id = :schedule_id";
        
        $this->db->query($sql, [
            ':route_id' => $routeId,
            ':foreman_id' => $foremanId,
            ':schedule_type' => $scheduleType,
            ':status' => $status,
            ':schedule_id' => $scheduleId
        ]);

        return true;
    }

    /**
     * Log dispatch time
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

        return true;
    }

    /**
     * Log return time
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

        return true;
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

    /**
     * Get schedule by ID
     */
    public function getById($scheduleId)
    {
        $sql = "SELECT * FROM operation_schedule WHERE schedule_id = :schedule_id";
        return $this->db->query($sql, [':schedule_id' => $scheduleId])->find();
    }

    /**
     * Delete schedule
     */
    public function delete($scheduleId)
    {
        $sql = "DELETE FROM operation_schedule WHERE schedule_id = :schedule_id";
        $this->db->query($sql, [':schedule_id' => $scheduleId]);
        return true;
    }

    /**
     * Delete schedules by truck ID
     */
    public function deleteByTruckId($truckId)
    {
        $sql = "DELETE FROM operation_schedule WHERE truck_id = :truck_id";
        $this->db->query($sql, [':truck_id' => $truckId]);
        return true;
    }

    /**
     * Check if truck has active schedules
     */
    public function hasActiveSchedules($truckId)
    {
        $sql = "SELECT COUNT(*) as count FROM operation_schedule 
                WHERE truck_id = :truck_id 
                AND status IN ('Scheduled', 'Dispatched')";
        
        $result = $this->db->query($sql, [':truck_id' => $truckId])->find();
        return $result['count'] > 0;
    }

    public function replaceWeeklyDays($scheduleId, array $days)
    {
        // remove existing
        $this->db->query("DELETE FROM schedule_days WHERE schedule_id = :sid", [':sid' => $scheduleId]);

        // normalize and insert new
        $stmt = $this->db->connection->prepare(
            "INSERT INTO schedule_days (schedule_id, day_of_week) VALUES (:sid, :day)"
        );
        foreach ($days as $d) {
            $day = trim($d);
            if (!$day) continue;
            $stmt->execute([':sid' => $scheduleId, ':day' => $day]);
        }
    }

    public function createWeeklyDays($scheduleId, array $days)
    {
        if (empty($days)) return;
        $stmt = $this->db->connection->prepare(
            "INSERT INTO schedule_days (schedule_id, day_of_week) VALUES (:sid, :day)"
        );
        foreach ($days as $d) {
            $day = trim($d);
            if (!$day) continue;
            $stmt->execute([':sid' => $scheduleId, ':day' => $day]);
        }
    }
}