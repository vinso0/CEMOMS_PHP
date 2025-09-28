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

    // Find user by username/email and include role name
    public function findByLogin($loginInput)
    {
        return $this->db->query(
            "SELECT users.*, roles.name as role
             FROM users
             JOIN roles ON users.role_id = roles.id
             WHERE username = :login OR email = :login",
            [':login' => $loginInput]
        )->find();
    }

    public function findById($id, $allowedRoles = [])
    {
        $sql = "SELECT u.id, u.username, u.email, r.name as role 
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id";

        if (!empty($allowedRoles)) {
            $placeholders = implode(',', array_fill(0, count($allowedRoles), '?'));
            $sql .= " AND r.name IN ($placeholders)";
            return $this->db->query($sql, array_merge([$id], $allowedRoles))->find();
        }

        return $this->db->query($sql, [':id' => $id])->find();
    }

    public function findAll($excludedRoles = [])
    {
        $sql = "SELECT u.id, u.username, u.email, r.name as role
                FROM users u
                JOIN roles r ON u.role_id = r.id";

        $params = [];

        // If excluded roles are provided
        if (!empty($excludedRoles)) {
            $placeholders = implode(',', array_fill(0, count($excludedRoles), '?'));
            $sql .= " WHERE r.name NOT IN ($placeholders)";
            $params = $excludedRoles;
        }

        return $this->db->query($sql, $params)->get();
    }

    public function update($id, $username, $email, $roleId)
    {
        $this->db->query(
            "UPDATE users SET username = :username, email = :email, role_id = :role_id WHERE id = :id",
            [
                ':username' => $username,
                ':email' => $email,
                ':role_id' => $roleId,
                ':id' => $id,
            ]
        );
    }

    public function create($username, $email, $password, $roleId)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->db->query(
            "INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)",
            [
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role_id' => $roleId,
            ]
        );
    }

    public function delete($id, array $allowedRoles = [])
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $params = [':id' => $id];

        if (!empty($allowedRoles)) {
            // Restrict which roles can be deleted
            $rolePlaceholders = implode(',', array_fill(0, count($allowedRoles), '?'));
            $sql .= " AND role_id IN (
                SELECT id FROM roles WHERE name IN ($rolePlaceholders)
            )";
            $params = array_merge([$id], $allowedRoles);
        }

        return $this->db->query($sql, $params);
    }
}
