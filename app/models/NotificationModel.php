<?php

class NotificationModel {
    private $table = 'notifications';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getUnreadNotifications($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC";
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getAllNotifications($user_id, $limit = 50) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        $this->db->bind('limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function addNotification($user_id, $title, $message) {
        $query = "INSERT INTO " . $this->table . " (user_id, title, message) VALUES (:user_id, :title, :message)";
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        $this->db->bind('title', $title);
        $this->db->bind('message', $message);
        
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function addNotificationToRole($role, $title, $message) {
        // Mendapatkan semua user_id dengan role tertentu
        $queryUser = "SELECT id FROM users WHERE role = :role";
        $this->db->query($queryUser);
        $this->db->bind('role', $role);
        $users = $this->db->resultSet();

        $count = 0;
        foreach($users as $user) {
            if ($this->addNotification($user['id'], $title, $message)) {
                $count++;
            }
        }
        return $count;
    }

    public function markAsRead($id, $user_id) {
        $query = "UPDATE " . $this->table . " SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('user_id', $user_id);
        
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function markAllAsRead($user_id) {
        $query = "UPDATE " . $this->table . " SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
