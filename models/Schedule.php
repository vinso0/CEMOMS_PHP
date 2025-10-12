<?php

namespace Models;

use Core\App;
use Core\Database;
use PDO;

class Schedule
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    public function getAllSchedules()
    {
        $query = "SELECT os.*, t.truck_type, r.route_name, f.username AS foreman_name
                  FROM operation_schedule os
                  JOIN truck t ON os.truck_id = t.truck_id
                  JOIN route r ON os.route_id = r.route_id
                  JOIN foreman f ON os.foreman_id = f.foreman_id";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSchedule($operationId, $truckId, $routeId, $foremanId, $scheduleType, $timestamp, $status)
    {
        $query = "INSERT INTO operation_schedule (operation_id, truck_id, route_id, foreman_id, schedule_type, time_stamp, status)
                  VALUES (:operation_id, :truck_id, :route_id, :foreman_id, :schedule_type, :time_stamp, :status)";
        $this->db->query($query, [
            ':operation_id' => $operationId,
            ':truck_id' => $truckId,
            ':route_id' => $routeId,
            ':foreman_id' => $foremanId,
            ':schedule_type' => $scheduleType,
            ':time_stamp' => $timestamp,
            ':status' => $status
        ]);
    }

    public function getScheduleById($id)
    {
        $query = "SELECT os.*, t.truck_type, r.route_name
                  FROM operation_schedule os
                  JOIN truck t ON os.truck_id = t.truck_id
                  JOIN route r ON os.route_id = r.route_id
                  WHERE os.id = :id";
        return $this->db->query($query, [':id' => $id])->find();
    }

    public function updateSchedule($id, $truckId, $routeId, $collectionDate, $status)
    {
        $query = "UPDATE operation_schedule
                  SET truck_id = :truck_id, route_id = :route_id, time_stamp = :collection_date, status = :status
                  WHERE id = :id";
        $this->db->query($query, [
            ':truck_id' => $truckId,
            ':route_id' => $routeId,
            ':collection_date' => $collectionDate,
            ':status' => $status,
            ':id' => $id
        ]);
    }

    public function deleteSchedule($id)
    {
        $query = "DELETE FROM operation_schedule WHERE id = :id";
        $this->db->query($query, [':id' => $id]);
    }
}