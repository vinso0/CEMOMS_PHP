<?php
namespace Models;

use Core\App;
use Core\Database;

class Roles
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Get roles, optionally excluding some by name
     */
    public function getRoles(array $excludedRoles = [])
    {
        $sql = "SELECT id, role_name FROM roles";
        $params = [];

        if (!empty($excludedRoles)) {
            $placeholders = [];
            foreach ($excludedRoles as $i => $role) {
                $ph = ":excluded{$i}";
                $placeholders[] = $ph;
                $params[$ph] = $role;
            }
            $sql .= " WHERE role_name NOT IN (" . implode(',', $placeholders) . ")";
        }

        return $this->db->query($sql, $params)->get();
    }
}

