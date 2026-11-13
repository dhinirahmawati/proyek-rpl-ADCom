<?php

class ReviewModel {
    private $table = 'reviews';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function addReview($data) {
        $query = "INSERT INTO " . $this->table . " (order_id, rating, comment) VALUES (:order_id, :rating, :comment)";
        $this->db->query($query);
        $this->db->bind('order_id', $data['order_id']);
        $this->db->bind('rating', $data['rating']);
        $this->db->bind('comment', $data['comment']);

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getReviewByOrderId($order_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE order_id = :order_id";
        $this->db->query($query);
        $this->db->bind('order_id', $order_id);
        return $this->db->single();
    }

    public function getReviewsByServiceId($service_id) {
        $query = "SELECT r.*, u.name as customer_name, o.created_at as order_date
                  FROM " . $this->table . " r
                  JOIN orders o ON r.order_id = o.id
                  JOIN users u ON o.customer_id = u.id
                  WHERE o.service_id = :service_id
                  ORDER BY r.created_at DESC";
        $this->db->query($query);
        $this->db->bind('service_id', $service_id);
        return $this->db->resultSet();
    }

    public function getServiceRatingStats($service_id) {
        $query = "SELECT AVG(r.rating) as avg_rating, COUNT(r.id) as total_reviews
                  FROM " . $this->table . " r
                  JOIN orders o ON r.order_id = o.id
                  WHERE o.service_id = :service_id";
        $this->db->query($query);
        $this->db->bind('service_id', $service_id);
        return $this->db->single();
    }

    public function getAllReviews() {
        $query = "SELECT r.*, s.name as service_name, u.name as customer_name
                  FROM " . $this->table . " r
                  JOIN orders o ON r.order_id = o.id
                  JOIN services s ON o.service_id = s.id
                  JOIN users u ON o.customer_id = u.id
                  ORDER BY r.created_at DESC";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function getSatisfactionStats() {
        $stats = [
            'average_rating' => 0,
            'total_reviews' => 0,
            'distribution' => [
                5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
            ]
        ];

        // Get overall average and count
        $query = "SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM " . $this->table;
        $this->db->query($query);
        $overall = $this->db->single();

        if ($overall['total_reviews'] > 0) {
            $stats['average_rating'] = round($overall['avg_rating'], 1);
            $stats['total_reviews'] = $overall['total_reviews'];

            // Get distribution
            $queryDist = "SELECT rating, COUNT(id) as count FROM " . $this->table . " GROUP BY rating";
            $this->db->query($queryDist);
            $distribution = $this->db->resultSet();

            foreach ($distribution as $row) {
                $stats['distribution'][$row['rating']] = $row['count'];
            }
        }

        return $stats;
    }
}
