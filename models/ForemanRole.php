<?php

namespace Models;

use Core\App;
use Core\Database;

class ForemanRole
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Get all foreman roles
     */
    public function getAllRoles()
    {
        $sql = "SELECT foreman_role_id as id, role_name FROM foreman_role ORDER BY role_name";
        return $this->db->query($sql)->get();
    }

    /**
     * Get role by ID
     */
    public function getRoleById($id)
    {
        $sql = "SELECT foreman_role_id as id, role_name FROM foreman_role WHERE foreman_role_id = :id";
        return $this->db->query($sql, [':id' => $id])->find();
    }

    /**
     * Create new role (if needed for admin management)
     */
    public function create($roleName)
    {
        $sql = "INSERT INTO foreman_role (role_name) VALUES (:role_name)";
        $this->db->query($sql, [':role_name' => $roleName]);
    }
}