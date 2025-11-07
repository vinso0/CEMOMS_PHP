<?php
// models/StreetSweeping.php

namespace Models;

use Core\App;
use Core\Database;

class StreetSweeping
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Get all street sweeping operations
     */
    public function getAllStreetSweeping()
    {
        $sql = "SELECT 
                    os.schedule_id,
                    o.operation_name,
                    o.description,
                    r.route_name,
                    f.username as foreman_name,
                    os.schedule_type,
                    os.operation_time,
                    os.status
                FROM operation_schedule os
                JOIN operation o ON os.operation_id = o.operation_id
                JOIN route r ON os.route_id = r.route_id
                JOIN foreman f ON os.foreman_id = f.foreman_id
                WHERE o.operation_type_id = 2
                ORDER BY os.schedule_id DESC";

        return $this->db->query($sql)->get();
    }

    /**
     * Get street sweeping operation by schedule ID
     */
    public function getById($scheduleId)
    {
        $sql = "SELECT 
                    os.schedule_id,
                    os.operation_id,
                    os.route_id,
                    os.foreman_id,
                    os.schedule_type,
                    os.operation_time,
                    os.status,
                    o.operation_name,
                    o.description,
                    r.route_name,
                    r.start_point,
                    r.mid_point,
                    r.end_point
                FROM operation_schedule os
                JOIN operation o ON os.operation_id = o.operation_id
                JOIN route r ON os.route_id = r.route_id
                WHERE os.schedule_id = :schedule_id AND o.operation_type_id = 2";

        return $this->db->query($sql, [':schedule_id' => $scheduleId])->find();
    }

    /**
     * Get recent street sweeping schedules for dashboard
     */
    public function getRecentSchedules($limit = 10)
    {
        $sql = "SELECT 
                    os.schedule_id,
                    o.operation_name,
                    f.username as foreman_name,
                    r.route_name,
                    os.schedule_type,
                    os.operation_time,
                    os.status,
                    os.operation_id
                FROM operation_schedule os
                JOIN operation o ON os.operation_id = o.operation_id
                JOIN route r ON os.route_id = r.route_id
                JOIN foreman f ON os.foreman_id = f.foreman_id
                WHERE o.operation_type_id = 2
                ORDER BY os.schedule_id DESC
                LIMIT :limit";

        return $this->db->query($sql, [':limit' => $limit])->get();
    }
}
