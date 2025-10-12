<?php

namespace Models;

use Core\App;
use Core\Database;

class GarbageCollection
{
    protected $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }


    public function getAll() {
        $query = "SELECT gc.*, r.route_name, v.vehicle_name 
                  FROM garbage_collections gc
                  JOIN routes r ON gc.route_id = r.id
                  JOIN vehicles v ON gc.vehicle_id = v.id";
        return $this->db->fetchAll($query);
    }

    public function find($id) {
        return $this->db->fetch("SELECT * FROM garbage_collections WHERE id = ?", [$id]);
    }

    public function create($data) {
        $query = "INSERT INTO garbage_collections (route_id, vehicle_id, collection_date, status) VALUES (?, ?, ?, ?)";
        return $this->db->execute($query, [
            $data['route_id'], $data['vehicle_id'], $data['collection_date'], $data['status']
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE garbage_collections 
                  SET route_id=?, vehicle_id=?, collection_date=?, status=? WHERE id=?";
        return $this->db->execute($query, [
            $data['route_id'], $data['vehicle_id'], $data['collection_date'], $data['status'], $id
        ]);
    }

    public function delete($id) {
        return $this->db->execute("DELETE FROM garbage_collections WHERE id = ?", [$id]);
    }
}
