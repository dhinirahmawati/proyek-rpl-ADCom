<?php

class Notification extends Controller {
    public function __construct() {
        // Ensure user is logged in before accessing any notification methods
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
    }

    public function getJson() {
        $user_id = $_SESSION['user_id'];
        $notifications = $this->model('NotificationModel')->getUnreadNotifications($user_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'count' => count($notifications),
            'data' => $notifications
        ]);
        exit;
    }

    public function markRead($id) {
        $user_id = $_SESSION['user_id'];
        $result = $this->model('NotificationModel')->markAsRead($id, $user_id);
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to mark as read']);
        }
        exit;
    }

    public function markAllRead() {
        $user_id = $_SESSION['user_id'];
        $result = $this->model('NotificationModel')->markAllAsRead($user_id);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'updated' => $result]);
        exit;
    }
}
