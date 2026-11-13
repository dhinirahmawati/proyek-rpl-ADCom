<?php

class OrderUpdateModel {
    private $table = 'order_updates';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getUpdatesByOrderId($order_id) {
        $query = "SELECT ou.*, u.name as updater_name, u.role as updater_role 
                  FROM " . $this->table . " ou 
                  JOIN users u ON ou.updated_by = u.id 
                  WHERE ou.order_id = :order_id 
                  ORDER BY ou.created_at ASC";
        $this->db->query($query);
        $this->db->bind('order_id', $order_id);
        return $this->db->resultSet();
    }

    public function addUpdate($data) {
        $query = "INSERT INTO " . $this->table . " (order_id, updated_by, update_text, image) 
                  VALUES (:order_id, :updated_by, :update_text, :image)";
        $this->db->query($query);
        $this->db->bind('order_id', $data['order_id']);
        $this->db->bind('updated_by', $data['updated_by']);
        $this->db->bind('update_text', $data['update_text']);
        $this->db->bind('image', empty($data['image']) ? null : $data['image']);
        
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
