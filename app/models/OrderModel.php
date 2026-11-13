<?php

class OrderModel {
    private $table = 'orders';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllOrders() {
        $query = "SELECT o.*, u.name as customer_name, s.name as service_name, t.name as technician_name 
                  FROM " . $this->table . " o 
                  LEFT JOIN users u ON o.customer_id = u.id 
                  LEFT JOIN services s ON o.service_id = s.id 
                  LEFT JOIN users t ON o.technician_id = t.id 
                  ORDER BY o.id DESC";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function getOrdersByTechnician($technician_id, $status = null) {
        $query = "SELECT o.*, u.name as customer_name, s.name as service_name 
                  FROM " . $this->table . " o 
                  LEFT JOIN users u ON o.customer_id = u.id 
                  LEFT JOIN services s ON o.service_id = s.id 
                  WHERE o.technician_id = :technician_id";
                  
        if ($status) {
            if (is_array($status)) {
                $statusList = "'" . implode("','", $status) . "'";
                $query .= " AND o.status IN ($statusList)";
            } else {
                $query .= " AND o.status = :status";
            }
        }
        
        $query .= " ORDER BY o.id DESC";
        
        $this->db->query($query);
        $this->db->bind('technician_id', $technician_id);
        
        if ($status && !is_array($status)) {
            $this->db->bind('status', $status);
        }
        
        return $this->db->resultSet();
    }

    public function getOrderById($id) {
        $query = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone_number as user_phone, s.name as service_name, t.name as technician_name, t.phone_number as technician_phone 
                  FROM " . $this->table . " o 
                  LEFT JOIN users u ON o.customer_id = u.id 
                  LEFT JOIN services s ON o.service_id = s.id 
                  LEFT JOIN users t ON o.technician_id = t.id 
                  WHERE o.id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function assignTechnician($order_id, $technician_id) {
        $query = "UPDATE " . $this->table . " SET technician_id = :technician_id, status = 'assigned' WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('technician_id', $technician_id);
        $this->db->bind('id', $order_id);
        
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function updateStatus($order_id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('status', $status);
        $this->db->bind('id', $order_id);
        
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getTechnicianStats($technician_id) {
        $stats = [
            'total' => 0,
            'active' => 0,
            'completed' => 0
        ];
        
        $query = "SELECT status, COUNT(*) as count FROM " . $this->table . " WHERE technician_id = :technician_id GROUP BY status";
        $this->db->query($query);
        $this->db->bind('technician_id', $technician_id);
        $results = $this->db->resultSet();
        
        foreach ($results as $row) {
            $stats['total'] += $row['count'];
            if (in_array($row['status'], ['assigned', 'processing'])) {
                $stats['active'] += $row['count'];
            } elseif ($row['status'] == 'completed') {
                $stats['completed'] += $row['count'];
            }
        }
        
        return $stats;
    }
    public function getOrdersByCustomer($customer_id) {
        $query = "SELECT o.*, s.name as service_name, t.name as technician_name 
                  FROM " . $this->table . " o 
                  LEFT JOIN services s ON o.service_id = s.id 
                  LEFT JOIN users t ON o.technician_id = t.id 
                  WHERE o.customer_id = :customer_id
                  ORDER BY o.id DESC";
        $this->db->query($query);
        $this->db->bind('customer_id', $customer_id);
        return $this->db->resultSet();
    }

    public function createOrder($data) {
        $query = "INSERT INTO " . $this->table . " (customer_id, service_id, status, problem_description, customer_address, customer_phone) 
                  VALUES (:customer_id, :service_id, 'pending', :problem_description, :customer_address, :customer_phone)";
        
        $this->db->query($query);
        $this->db->bind('customer_id', $data['customer_id']);
        $this->db->bind('service_id', $data['service_id']);
        $this->db->bind('problem_description', $data['problem_description']);
        $this->db->bind('customer_address', $data['customer_address'] ?? null);
        $this->db->bind('customer_phone', $data['customer_phone'] ?? null);
        
        try {
            $this->db->execute();
            // Return the ID of the newly created order
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function deleteOrder($id) {
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
