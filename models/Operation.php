<?php
/**
 * Operation Model
 * File: models/Operation.php
 * 
 * Handles all database operations for the 'operation' table
 * This table stores the main operation records that link to operation_schedule
 */

namespace Models;

use Core\App;
use Core\Database;

class Operation
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Create a new operation
     * 
     * @param string $operationName Name of the operation (e.g., "Garbage Collection - ABC 1234")
     * @param int $operationTypeId Type of operation (1=Garbage Collection, 2=Street Sweeping, etc.)
     * @param int $adminId ID of the admin creating the operation
     * @param string $description Optional description or notes about the operation
     * @return int The ID of the newly created operation
     */
    public function create($operationName, $operationTypeId, $adminId, $description = '')
    {
        // We're NOT using truck_id in the INSERT because it's causing issues
        // The truck is linked through operation_schedule table instead
        $sql = "INSERT INTO operation (operation_name, operation_type_id, admin_id, description, is_truck_based)
                VALUES (:operation_name, :operation_type_id, :admin_id, :description, :is_truck_based)";
        
        $this->db->query($sql, [
            ':operation_name' => $operationName,
            ':operation_type_id' => $operationTypeId,
            ':admin_id' => $adminId,
            ':description' => $description,
            ':is_truck_based' => 1 // 1 = truck-based operation (like garbage collection)
        ]);
        
        // Return the ID of the newly created operation
        return $this->db->connection->lastInsertId();
    }

    /**
     * Get operation by ID
     * 
     * @param int $operationId
     * @return array|false Operation details with type name
     */
    public function getById($operationId)
    {
        $sql = "SELECT o.operation_id, 
                       o.operation_name, 
                       o.operation_type_id, 
                       o.admin_id, 
                       o.is_truck_based, 
                       o.description,
                       ot.type_name
                FROM operation o
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                WHERE o.operation_id = :operation_id";
        
        return $this->db->query($sql, [':operation_id' => $operationId])->find();
    }

    /**
     * Get all operations
     * 
     * @return array List of all operations with their type names
     */
    public function getAll()
    {
        $sql = "SELECT o.operation_id, 
                       o.operation_name, 
                       o.operation_type_id, 
                       o.admin_id, 
                       o.is_truck_based, 
                       o.description,
                       ot.type_name,
                       a.username as admin_name
                FROM operation o
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                LEFT JOIN admin a ON o.admin_id = a.admin_id
                ORDER BY o.operation_id DESC";
        
        return $this->db->query($sql)->get();
    }

    /**
     * Get operations by type
     * 
     * @param int $operationTypeId Type ID (1=Garbage Collection, 2=Street Sweeping, etc.)
     * @return array List of operations of specified type
     */
    public function getByType($operationTypeId)
    {
        $sql = "SELECT o.operation_id, 
                       o.operation_name, 
                       o.operation_type_id, 
                       o.admin_id, 
                       o.is_truck_based, 
                       o.description,
                       ot.type_name
                FROM operation o
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                WHERE o.operation_type_id = :operation_type_id
                ORDER BY o.operation_id DESC";
        
        return $this->db->query($sql, [':operation_type_id' => $operationTypeId])->get();
    }

    /**
     * Get operations by admin
     * 
     * @param int $adminId
     * @return array List of operations created by specified admin
     */
    public function getByAdmin($adminId)
    {
        $sql = "SELECT o.operation_id, 
                       o.operation_name, 
                       o.operation_type_id, 
                       o.admin_id, 
                       o.is_truck_based, 
                       o.description,
                       ot.type_name
                FROM operation o
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                WHERE o.admin_id = :admin_id
                ORDER BY o.operation_id DESC";
        
        return $this->db->query($sql, [':admin_id' => $adminId])->get();
    }

    /**
     * Update operation
     * 
     * @param int $operationId
     * @param string $operationName
     * @param string $description
     * @return bool Success status
     */
    public function update($operationId, $operationName, $description = '')
    {
        $sql = "UPDATE operation 
                SET operation_name = :operation_name,
                    description = :description
                WHERE operation_id = :operation_id";
        
        $this->db->query($sql, [
            ':operation_name' => $operationName,
            ':description' => $description,
            ':operation_id' => $operationId
        ]);

        return true;
    }

    /**
     * Update operation with type change
     * 
     * @param int $operationId
     * @param string $operationName
     * @param int $operationTypeId
     * @param string $description
     * @return bool Success status
     */
    public function updateWithType($operationId, $operationName, $operationTypeId, $description = '')
    {
        $sql = "UPDATE operation 
                SET operation_name = :operation_name,
                    operation_type_id = :operation_type_id,
                    description = :description
                WHERE operation_id = :operation_id";
        
        $this->db->query($sql, [
            ':operation_name' => $operationName,
            ':operation_type_id' => $operationTypeId,
            ':description' => $description,
            ':operation_id' => $operationId
        ]);

        return true;
    }

    /**
     * Delete operation
     * Note: This should check for dependencies first (schedules, etc.)
     * 
     * @param int $operationId
     * @return bool Success status
     */
    public function delete($operationId)
    {
        // Check if operation has active schedules
        $checkSql = "SELECT COUNT(*) as count 
                     FROM operation_schedule 
                     WHERE operation_id = :operation_id 
                     AND status IN ('Scheduled', 'Dispatched')";
        
        $result = $this->db->query($checkSql, [':operation_id' => $operationId])->find();
        
        if ($result['count'] > 0) {
            throw new \Exception('Cannot delete operation with active schedules.');
        }
        
        // Delete the operation
        $sql = "DELETE FROM operation WHERE operation_id = :operation_id";
        $this->db->query($sql, [':operation_id' => $operationId]);
        
        return true;
    }

    /**
     * Get operation with full details including schedule info
     * 
     * @param int $operationId
     * @return array Operation with schedule, route, and truck details
     */
    public function getFullDetails($operationId)
    {
        $sql = "SELECT o.operation_id,
                       o.operation_name,
                       o.operation_type_id,
                       o.description,
                       ot.type_name,
                       os.schedule_id,
                       os.schedule_type,
                       os.status,
                       os.dispatch_time,
                       os.return_time,
                       r.route_name,
                       r.start_point,
                       r.mid_point,
                       r.end_point,
                       t.plate_number,
                       t.body_number,
                       f.username as foreman_name,
                       a.area_name
                FROM operation o
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                LEFT JOIN operation_schedule os ON o.operation_id = os.operation_id
                LEFT JOIN route r ON os.route_id = r.route_id
                LEFT JOIN truck t ON os.truck_id = t.truck_id
                LEFT JOIN foreman f ON os.foreman_id = f.foreman_id
                LEFT JOIN area a ON os.area_id = a.area_id
                WHERE o.operation_id = :operation_id";
        
        return $this->db->query($sql, [':operation_id' => $operationId])->find();
    }

    /**
     * Count operations by type
     * 
     * @param int|null $operationTypeId Optional type filter
     * @return int Count of operations
     */
    public function countByType($operationTypeId = null)
    {
        if ($operationTypeId) {
            $sql = "SELECT COUNT(*) as count 
                    FROM operation 
                    WHERE operation_type_id = :operation_type_id";
            $result = $this->db->query($sql, [':operation_type_id' => $operationTypeId])->find();
        } else {
            $sql = "SELECT COUNT(*) as count FROM operation";
            $result = $this->db->query($sql)->find();
        }
        
        return $result['count'] ?? 0;
    }

    /**
     * Get operations with their schedule status
     * 
     * @param string|null $status Optional status filter ('Scheduled', 'Dispatched', 'Completed', 'Parked')
     * @return array List of operations with schedule status
     */
    public function getWithScheduleStatus($status = null)
    {
        $sql = "SELECT o.operation_id,
                       o.operation_name,
                       o.operation_type_id,
                       ot.type_name,
                       os.status,
                       os.dispatch_time,
                       os.return_time,
                       t.plate_number,
                       f.username as foreman_name
                FROM operation o
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                LEFT JOIN operation_schedule os ON o.operation_id = os.operation_id
                LEFT JOIN truck t ON os.truck_id = t.truck_id
                LEFT JOIN foreman f ON os.foreman_id = f.foreman_id";
        
        if ($status) {
            $sql .= " WHERE os.status = :status";
            return $this->db->query($sql, [':status' => $status])->get();
        }
        
        $sql .= " ORDER BY o.operation_id DESC";
        return $this->db->query($sql)->get();
    }

    /**
     * Check if operation exists
     * 
     * @param int $operationId
     * @return bool
     */
    public function exists($operationId)
    {
        $sql = "SELECT COUNT(*) as count FROM operation WHERE operation_id = :operation_id";
        $result = $this->db->query($sql, [':operation_id' => $operationId])->find();
        return $result['count'] > 0;
    }

    /**
     * Get operation statistics
     * 
     * @return array Statistics about operations
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total_operations,
                    SUM(CASE WHEN ot.operation_type_id = 1 THEN 1 ELSE 0 END) as garbage_collection,
                    SUM(CASE WHEN ot.operation_type_id = 2 THEN 1 ELSE 0 END) as street_sweeping,
                    SUM(CASE WHEN ot.operation_type_id = 3 THEN 1 ELSE 0 END) as flushing,
                    SUM(CASE WHEN ot.operation_type_id = 4 THEN 1 ELSE 0 END) as de_clogging,
                    SUM(CASE WHEN ot.operation_type_id = 5 THEN 1 ELSE 0 END) as cleanup_drives
                FROM operation o
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id";
        
        return $this->db->query($sql)->find();
    }
}