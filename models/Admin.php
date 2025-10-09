<?php

namespace Models;

use Core\App;
use Core\Database;

class Admin
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }
    
    /**
     * Find admin by email for login
     */
    public function findByLogin($login)
    {
        $sql = "SELECT admin_id as id, email, username, password, 'admin' as role
                FROM admin
                WHERE email = :login OR username = :login";

        return $this->db->query($sql, [':login' => $login])->find();
    }

    /**
     * Get admin profile by ID
     */
    public function getProfile($id)
    {
        return $this->db->query(
            "SELECT admin_id as id, email FROM admin WHERE admin_id = :id",
            [':id' => $id]
        )->find();
    }

    /**
     * Update admin password
     */
    public function updatePassword($id, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE admin SET password = :password WHERE admin_id = :id";
        
        $this->db->query($sql, [
            ':password' => $hashedPassword,
            ':id' => $id
        ]);
    }
}