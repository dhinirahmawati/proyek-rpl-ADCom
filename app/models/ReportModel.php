<?php

class ReportModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getOrdersSummary($start, $end) {
        $query = "SELECT 
                    COUNT(id) as total_orders,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as total_completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as total_cancelled,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as total_pending
                  FROM orders 
                  WHERE DATE(created_at) >= :start AND DATE(created_at) <= :end";
        
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        return $this->db->single();
    }
    
    public function getTotalRevenue($start, $end) {
        $query = "SELECT SUM(s.estimated_price) as total_revenue
                  FROM orders o
                  JOIN services s ON o.service_id = s.id
                  WHERE o.status = 'completed' 
                  AND DATE(o.created_at) >= :start AND DATE(o.created_at) <= :end";
        
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        $result = $this->db->single();
        return $result['total_revenue'] ? $result['total_revenue'] : 0;
    }

    public function getOrdersByStatus($start, $end) {
        $query = "SELECT status, COUNT(id) as count 
                  FROM orders 
                  WHERE DATE(created_at) >= :start AND DATE(created_at) <= :end
                  GROUP BY status";
        
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        return $this->db->resultSet();
    }

    public function getOrdersByService($start, $end) {
        $query = "SELECT s.name as service_name, COUNT(o.id) as count 
                  FROM orders o
                  JOIN services s ON o.service_id = s.id
                  WHERE DATE(o.created_at) >= :start AND DATE(o.created_at) <= :end
                  GROUP BY o.service_id
                  ORDER BY count DESC";
                  
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        return $this->db->resultSet();
    }

    public function getOrdersTrend($start, $end) {
        $query = "SELECT DATE(created_at) as date, COUNT(id) as count 
                  FROM orders 
                  WHERE DATE(created_at) >= :start AND DATE(created_at) <= :end
                  GROUP BY DATE(created_at)
                  ORDER BY date ASC";
                  
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        return $this->db->resultSet();
    }

    public function getRevenueTrend($start, $end) {
        $query = "SELECT DATE(o.created_at) as date, SUM(s.estimated_price) as revenue 
                  FROM orders o
                  JOIN services s ON o.service_id = s.id
                  WHERE o.status = 'completed'
                  AND DATE(o.created_at) >= :start AND DATE(o.created_at) <= :end
                  GROUP BY DATE(o.created_at)
                  ORDER BY date ASC";
                  
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        return $this->db->resultSet();
    }

    public function getTechnicianPerformance($start, $end) {
        $query = "SELECT u.name, 
                         COUNT(o.id) as total_assigned,
                         SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as total_completed
                  FROM users u
                  JOIN orders o ON u.id = o.technician_id
                  WHERE u.role = 'technician'
                  AND DATE(o.created_at) >= :start AND DATE(o.created_at) <= :end
                  GROUP BY u.id
                  ORDER BY total_completed DESC";
                  
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        return $this->db->resultSet();
    }

    public function getServiceRatings($start, $end) {
        $query = "SELECT s.name, AVG(r.rating) as avg_rating, COUNT(r.id) as total_reviews
                  FROM services s
                  JOIN orders o ON s.id = o.service_id
                  JOIN reviews r ON o.id = r.order_id
                  WHERE DATE(r.created_at) >= :start AND DATE(r.created_at) <= :end
                  GROUP BY s.id
                  ORDER BY avg_rating DESC, total_reviews DESC";
                  
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        
        return $this->db->resultSet();
    }

    public function getTopServices($start, $end, $limit = 5) {
        $query = "SELECT s.name, COUNT(o.id) as total_orders
                  FROM services s
                  JOIN orders o ON s.id = o.service_id
                  WHERE DATE(o.created_at) >= :start AND DATE(o.created_at) <= :end
                  GROUP BY s.id
                  ORDER BY total_orders DESC
                  LIMIT :limit";
                  
        $this->db->query($query);
        $this->db->bind('start', $start);
        $this->db->bind('end', $end);
        $this->db->bind('limit', $limit, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
}
