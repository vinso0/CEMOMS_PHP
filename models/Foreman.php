<?php

namespace Models;

use Core\App;
use Core\Database;

class Foreman
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }
    
    /**
     * Find foreman by email or username for login
     */
    public function findByLogin($loginInput)
    {
        $sql = "SELECT f.foreman_id as id, f.email, f.username, f.password, 
                       fr.role_name as role, f.foreman_role_id
                FROM foreman f
                JOIN foreman_role fr ON f.foreman_role_id = fr.foreman_role_id
                WHERE f.email = :login OR f.username = :login";

        return $this->db->query($sql, [':login' => $loginInput])->find();
    }

    /**
     * Find foreman by ID
     */
    public function findById($id)
    {
        $sql = "SELECT f.foreman_id as id, f.email, f.username, 
                       fr.role_name as role, f.foreman_role_id
                FROM foreman f
                JOIN foreman_role fr ON f.foreman_role_id = fr.foreman_role_id
                WHERE f.foreman_id = :id";

        return $this->db->query($sql, [':id' => $id])->find();
    }

    /**
     * Get foreman profile
     */
    public function getProfile($id)
    {
        return $this->db->query(
            "SELECT foreman_id as id, username, email FROM foreman WHERE foreman_id = :id",
            [':id' => $id]
        )->find();
    }

    /**
     * Get all foremen
     */
    public function findAll()
    {
        $sql = "SELECT f.foreman_id as id, f.username, f.email, 
                       fr.role_name as role, f.foreman_role_id
                FROM foreman f
                JOIN foreman_role fr ON f.foreman_role_id = fr.foreman_role_id
                ORDER BY f.username";

        return $this->db->query($sql)->get();
    }

    /**
     * Create new foreman
     */
    public function create($username, $email, $password, $foremanRoleId)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO foreman (username, email, password, foreman_role_id)
                VALUES (:username, :email, :password, :foreman_role_id)";

        $params = [
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':foreman_role_id' => $foremanRoleId,
        ];

        $this->db->query($sql, $params);
    }

    /**
     * Update foreman info
     */
    public function update($id, $username, $email, $foremanRoleId, $password = null)
    {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE foreman
                    SET username = :username, email = :email, 
                        foreman_role_id = :foreman_role_id, password = :password
                    WHERE foreman_id = :id";
            
            $params = [
                ':username' => $username,
                ':email' => $email,
                ':foreman_role_id' => $foremanRoleId,
                ':password' => $hashedPassword,
                ':id' => $id,
            ];
        } else {
            $sql = "UPDATE foreman
                    SET username = :username, email = :email, foreman_role_id = :foreman_role_id
                    WHERE foreman_id = :id";
            
            $params = [
                ':username' => $username,
                ':email' => $email,
                ':foreman_role_id' => $foremanRoleId,
                ':id' => $id,
            ];
        }

        $this->db->query($sql, $params);
    }

    /**
     * Delete foreman
     */
    public function delete($id)
    {
        $sql = "DELETE FROM foreman WHERE foreman_id = :id";
        return $this->db->query($sql, [':id' => $id]);
    }

    /**
     * Count active foremen
     */
    public function countActive()
    {
        $sql = "SELECT COUNT(*) as count FROM foreman";
        $result = $this->db->query($sql)->find();
        return $result['count'] ?? 0;
    }
}