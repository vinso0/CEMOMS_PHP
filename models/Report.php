<?php
// models/Report.php

namespace Models;

use Core\App;
use Core\Database;

class Report
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Get all reports with related data
     */
    public function getAllReports()
    {
        $sql = "SELECT
                    r.report_id as id,
                    r.schedule_id,
                    r.foreman_id,
                    r.area_id,
                    r.time_stamp,
                    r.before_image,
                    r.after_image,
                    r.remarks,
                    r.approved_by_admin_id,
                    r.status,
                    f.username as foreman_name,
                    a.area_name as location,
                    ot.type_name as operation_type,
                    DATE(r.time_stamp) as date
                FROM report r
                JOIN foreman f ON r.foreman_id = f.foreman_id
                JOIN area a ON r.area_id = a.area_id
                JOIN operation_schedule os ON r.schedule_id = os.schedule_id
                JOIN operation o ON os.operation_id = o.operation_id
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                ORDER BY r.time_stamp DESC";

        return $this->db->query($sql)->get();
    }

    /**
     * Get report by ID
     */
    public function getById($reportId)
    {
        $sql = "SELECT
                    r.report_id as id,
                    r.schedule_id,
                    r.foreman_id,
                    r.area_id,
                    r.time_stamp,
                    r.before_image,
                    r.after_image,
                    r.remarks,
                    r.approved_by_admin_id,
                    r.status,
                    f.username as foreman_name,
                    a.area_name as location,
                    ot.type_name as operation_type,
                    o.operation_name,
                    r.route_name
                FROM report r
                JOIN foreman f ON r.foreman_id = f.foreman_id
                JOIN area a ON r.area_id = a.area_id
                JOIN operation_schedule os ON r.schedule_id = os.schedule_id
                JOIN operation o ON os.operation_id = o.operation_id
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                WHERE r.report_id = :report_id";

        return $this->db->query($sql, [':report_id' => $reportId])->find();
    }

    /**
     * Get reports by operation type
     */
    public function getReportsByOperationType($operationTypeId)
    {
        $sql = "SELECT
                    r.report_id as id,
                    r.schedule_id,
                    r.foreman_id,
                    r.area_id,
                    r.time_stamp,
                    r.before_image,
                    r.after_image,
                    r.remarks,
                    r.approved_by_admin_id,
                    r.status,
                    f.username as foreman_name,
                    a.area_name as location,
                    ot.type_name as operation_type,
                    DATE(r.time_stamp) as date
                FROM report r
                JOIN foreman f ON r.foreman_id = f.foreman_id
                JOIN area a ON r.area_id = a.area_id
                JOIN operation_schedule os ON r.schedule_id = os.schedule_id
                JOIN operation o ON os.operation_id = o.operation_id
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                WHERE o.operation_type_id = :operation_type_id
                ORDER BY r.time_stamp DESC";

        return $this->db->query($sql, [':operation_type_id' => $operationTypeId])->get();
    }

    /**
     * Get reports by date range
     */
    public function getReportsByDateRange($startDate, $endDate)
    {
        $sql = "SELECT
                    r.report_id as id,
                    r.schedule_id,
                    r.foreman_id,
                    r.area_id,
                    r.time_stamp,
                    r.before_image,
                    r.after_image,
                    r.remarks,
                    r.approved_by_admin_id,
                    r.status,
                    f.username as foreman_name,
                    a.area_name as location,
                    ot.type_name as operation_type,
                    DATE(r.time_stamp) as date
                FROM report r
                JOIN foreman f ON r.foreman_id = f.foreman_id
                JOIN area a ON r.area_id = a.area_id
                JOIN operation_schedule os ON r.schedule_id = os.schedule_id
                JOIN operation o ON os.operation_id = o.operation_id
                JOIN operation_type ot ON o.operation_type_id = ot.operation_type_id
                WHERE DATE(r.time_stamp) BETWEEN :start_date AND :end_date
                ORDER BY r.time_stamp DESC";

        return $this->db->query($sql, [
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ])->get();
    }

    /**
     * Get reports statistics
     */
    public function getReportsStats()
    {
        $sql = "SELECT
                    COUNT(*) as total_reports,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_reports,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reports,
                    SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) as declined_reports
                FROM report";

        return $this->db->query($sql)->find();
    }

    /**
     * Get operation type statistics
     */
    public function getOperationTypeStats()
    {
        $sql = "SELECT
                    ot.type_name,
                    COUNT(r.report_id) as report_count
                FROM operation_type ot
                LEFT JOIN operation o ON ot.operation_type_id = o.operation_type_id
                LEFT JOIN operation_schedule os ON o.operation_id = os.operation_id
                LEFT JOIN report r ON os.schedule_id = r.schedule_id
                GROUP BY ot.operation_type_id, ot.type_name
                ORDER BY ot.type_name";

        return $this->db->query($sql)->get();
    }
}