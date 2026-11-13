<?php

class ServiceModel {
    private $table = 'services';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllServices() {
        $this->db->query('SELECT s.*, c.name as category_name FROM ' . $this->table . ' s LEFT JOIN categories c ON s.category_id = c.id ORDER BY s.id DESC');
        return $this->db->resultSet();
    }

    public function getActiveServices($categoryId = null, $keyword = null) {
        $query = 'SELECT s.*, c.name as category_name FROM ' . $this->table . ' s LEFT JOIN categories c ON s.category_id = c.id WHERE s.is_active = 1';
        
        if ($categoryId) {
            $query .= ' AND s.category_id = :category_id';
        }
        
        if ($keyword) {
            $query .= ' AND s.name LIKE :keyword';
        }
        
        $query .= ' ORDER BY s.name ASC';
        
        $this->db->query($query);
        
        if ($categoryId) {
            $this->db->bind('category_id', $categoryId);
        }
        
        if ($keyword) {
            $this->db->bind('keyword', '%' . $keyword . '%');
        }
        
        return $this->db->resultSet();
    }

    public function getServiceById($id) {
        $this->db->query('SELECT s.*, c.name as category_name FROM ' . $this->table . ' s LEFT JOIN categories c ON s.category_id = c.id WHERE s.id = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function addService($data) {
        $query = "INSERT INTO " . $this->table . " (category_id, name, description, estimated_price, estimated_time, image, is_active) 
                  VALUES (:category_id, :name, :description, :estimated_price, :estimated_time, :image, :is_active)";
        
        $this->db->query($query);
        $this->db->bind('category_id', empty($data['category_id']) ? null : $data['category_id']);
        $this->db->bind('name', $data['name']);
        $this->db->bind('description', $data['description']);
        $this->db->bind('estimated_price', empty($data['estimated_price']) ? null : $data['estimated_price']);
        $this->db->bind('estimated_time', $data['estimated_time']);
        $this->db->bind('image', $data['image']);
        $this->db->bind('is_active', isset($data['is_active']) ? $data['is_active'] : 1);

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function updateService($data) {
        $query = "UPDATE " . $this->table . " 
                  SET category_id = :category_id, 
                      name = :name, 
                      description = :description, 
                      estimated_price = :estimated_price, 
                      estimated_time = :estimated_time,
                      is_active = :is_active";
                      
        if (isset($data['image']) && $data['image'] != '') {
            $query .= ", image = :image";
        }
        
        $query .= " WHERE id = :id";
        
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('category_id', empty($data['category_id']) ? null : $data['category_id']);
        $this->db->bind('name', $data['name']);
        $this->db->bind('description', $data['description']);
        $this->db->bind('estimated_price', empty($data['estimated_price']) ? null : $data['estimated_price']);
        $this->db->bind('estimated_time', $data['estimated_time']);
        $this->db->bind('is_active', isset($data['is_active']) ? (int)$data['is_active'] : 0);
        
        if (isset($data['image']) && $data['image'] != '') {
            $this->db->bind('image', $data['image']);
        }

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return -1;
        }
    }

    public function toggleActive($id, $status) {
        $query = "UPDATE " . $this->table . " SET is_active = :status WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('status', $status);
        
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function deleteService($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
