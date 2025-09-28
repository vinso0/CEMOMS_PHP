<?php

namespace Models;

use Core\App;
use Core\Database;

class User
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    /**
     * Find user by username/email and include role name
     */
    public function findByLogin($loginInput)
    {
        $sql = "SELECT u.*, r.role_name AS role
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.username = :login OR u.email = :login";

        return $this->db->query($sql, [':login' => $loginInput])->find();
    }

    /**
     * Find user by ID, optionally restricted to allowed roles
     */
    public function findById($id, array $allowedRoles = [])
    {
        $sql = "SELECT u.id, u.username, u.email, r.role_name AS role
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id";

        $params = [':id' => $id];

        if (!empty($allowedRoles)) {
            $rolePlaceholders = [];
            foreach ($allowedRoles as $i => $role) {
                $ph = ":role{$i}";
                $rolePlaceholders[] = $ph;
                $params[$ph] = $role;
            }
            $sql .= " AND r.role_name IN (" . implode(',', $rolePlaceholders) . ")";
        }

        return $this->db->query($sql, $params)->find();
    }

    /**
     * Get all users, with optional exclusion of roles
     */
    public function findAll(array $excludedRoles = [])
    {
        $sql = "SELECT u.id, u.username, u.email, r.role_name AS role
                FROM users u
                JOIN roles r ON u.role_id = r.id";

        $params = [];

        if (!empty($excludedRoles)) {
            $rolePlaceholders = [];
            foreach ($excludedRoles as $i => $role) {
                $ph = ":excluded{$i}";
                $rolePlaceholders[] = $ph;
                $params[$ph] = $role;
            }
            $sql .= " WHERE r.role_name NOT IN (" . implode(',', $rolePlaceholders) . ")";
        }

        return $this->db->query($sql, $params)->get();
    }

    /**
     * Update user info
     */
    public function update($id, $username, $email, $roleId)
    {
        $sql = "UPDATE users
                SET username = :username, email = :email, role_id = :role_id
                WHERE id = :id";

        $params = [
            ':username' => $username,
            ':email' => $email,
            ':role_id' => $roleId,
            ':id' => $id,
        ];

        $this->db->query($sql, $params);
    }

    /**
     * Create new user
     */
    public function create($username, $email, $password, $roleId)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, role_id)
                VALUES (:username, :email, :password, :role_id)";

        $params = [
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role_id' => $roleId,
        ];

        $this->db->query($sql, $params);
    }

    /**
     * Delete user, optionally restricted by allowed roles
     */
    public function delete($id, array $allowedRoles = [])
    {
        $sql = "DELETE FROM users
                WHERE id = :id";

        $params = [':id' => $id];

        if (!empty($allowedRoles)) {
            $rolePlaceholders = [];
            foreach ($allowedRoles as $i => $role) {
                $ph = ":role{$i}";
                $rolePlaceholders[] = $ph;
                $params[$ph] = $role;
            }
            $sql .= " AND role_id IN (
                        SELECT id FROM roles WHERE role_name IN (" . implode(',', $rolePlaceholders) . ")
                     )";
        }

        return $this->db->query($sql, $params);
    }
}
