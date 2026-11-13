<?php

class AIModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getConversations($user_id) {
        $this->db->query('SELECT * FROM ai_conversations WHERE user_id = :user_id ORDER BY updated_at DESC');
        $this->db->bind('user_id', $user_id);
        return $this->db->resultSet();
    }
    
    public function getConversationById($id, $user_id) {
        $this->db->query('SELECT * FROM ai_conversations WHERE id = :id AND user_id = :user_id');
        $this->db->bind('id', $id);
        $this->db->bind('user_id', $user_id);
        return $this->db->single();
    }

    public function getConversation($id) {
        $this->db->query('SELECT c.*, u.name as user_name, u.email as user_email, u.role as user_role 
                          FROM ai_conversations c 
                          LEFT JOIN users u ON c.user_id = u.id 
                          WHERE c.id = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function getConversationWithDetails($id) {
        $this->db->query("SELECT c.*, 
            u.name as user_name, 
            u.email as user_email, 
            u.role as user_role,
            (SELECT COUNT(*) FROM ai_messages WHERE conversation_id = c.id) as message_count
            FROM ai_conversations c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public static function detectComplaint($text) {
        if (empty($text)) return 0;
        $keywords = [
            'rusak', 'mati', 'error', 'tidak bisa', 'gak bisa', 'gabisa', 'blank', 
            'lambat', 'lemot', 'bunyi', 'hang', 'restart', 'bluescreen', 'blue screen', 
            'konslet', 'terbakar', 'kena air', 'pecah', 'bocor', 'putus', 'kabel putus',
            'gangguan', 'masalah', 'keluhan', 'komplain', 'tidak jalan', 'sinyal hilang',
            'no signal', 'bad sector', 'virus', 'ransomware', 'panas', 'overheat',
            'tidak bunyi', 'buram', 'gelap', 'bintik', 'patah', 'kecewa', 'tolong bantu',
            'kendala', 'bermasalah', 'ngehang', 'drop', 'putus-putus', 'mati total'
        ];
        $lower = strtolower($text);
        foreach ($keywords as $kw) {
            if (strpos($lower, $kw) !== false) {
                return 1;
            }
        }
        return 0;
    }

    public function createAdvancedConversation($user_id, $title, $guest_name = null, $guest_phone = null, $guest_session = null, $is_complaint = 0) {
        $query = "INSERT INTO ai_conversations (user_id, guest_name, guest_phone, guest_session, title, is_complaint, status) 
                  VALUES (:user_id, :guest_name, :guest_phone, :guest_session, :title, :is_complaint, 'new')";
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        $this->db->bind('guest_name', $guest_name);
        $this->db->bind('guest_phone', $guest_phone);
        $this->db->bind('guest_session', $guest_session);
        $this->db->bind('title', $title);
        $this->db->bind('is_complaint', $is_complaint);
        
        try {
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function markAsComplaint($id) {
        $this->db->query("UPDATE ai_conversations SET is_complaint = 1 WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    public function getAllConversations($filterType = 'all', $status = 'all', $dateFilter = 'all', $keyword = null) {
        $sql = "SELECT c.*, 
                u.name as user_name, 
                u.email as user_email, 
                u.role as user_role,
                (SELECT message FROM ai_messages WHERE conversation_id = c.id AND sender = 'user' ORDER BY created_at ASC LIMIT 1) as first_question,
                (SELECT message FROM ai_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT sender FROM ai_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_sender,
                (SELECT COUNT(*) FROM ai_messages WHERE conversation_id = c.id) as message_count
                FROM ai_conversations c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if ($filterType === 'complaints') {
            $sql .= " AND c.is_complaint = 1";
        } elseif ($filterType === 'general') {
            $sql .= " AND (c.is_complaint = 0 OR c.is_complaint IS NULL)";
        }
        
        if ($status !== 'all' && in_array($status, ['new', 'followed_up', 'closed'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $status;
        }
        
        if ($dateFilter === 'today') {
            $sql .= " AND DATE(c.updated_at) = CURDATE()";
        } elseif ($dateFilter === 'week') {
            $sql .= " AND YEARWEEK(c.updated_at, 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($dateFilter === 'month') {
            $sql .= " AND YEAR(c.updated_at) = YEAR(CURDATE()) AND MONTH(c.updated_at) = MONTH(CURDATE())";
        }
        
        if (!empty($keyword)) {
            $sql .= " AND (c.title LIKE :kw OR u.name LIKE :kw OR c.guest_name LIKE :kw OR EXISTS (SELECT 1 FROM ai_messages WHERE conversation_id = c.id AND message LIKE :kw))";
            $params['kw'] = '%' . $keyword . '%';
        }
        
        $sql .= " ORDER BY c.updated_at DESC";
        
        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        return $this->db->resultSet();
    }

    public function getConversationStats() {
        $stats = [
            'total' => 0,
            'complaints' => 0,
            'today' => 0,
            'followed_up' => 0
        ];
        
        $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN is_complaint = 1 THEN 1 ELSE 0 END) as complaints,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN status = 'followed_up' THEN 1 ELSE 0 END) as followed_up
            FROM ai_conversations");
        $row = $this->db->single();
        if ($row) {
            $stats['total'] = (int)($row['total'] ?? 0);
            $stats['complaints'] = (int)($row['complaints'] ?? 0);
            $stats['today'] = (int)($row['today'] ?? 0);
            $stats['followed_up'] = (int)($row['followed_up'] ?? 0);
        }
        return $stats;
    }

    public function updateConversationStatus($id, $status) {
        if (!in_array($status, ['new', 'followed_up', 'closed'])) return false;
        $this->db->query("UPDATE ai_conversations SET status = :status WHERE id = :id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    public function deleteConversation($id) {
        $this->db->query("DELETE FROM ai_conversations WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    public function getMessages($conversation_id) {
        $this->db->query('SELECT * FROM ai_messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC');
        $this->db->bind('conversation_id', $conversation_id);
        return $this->db->resultSet();
    }

    public function createConversation($user_id, $title) {
        $query = "INSERT INTO ai_conversations (user_id, title) VALUES (:user_id, :title)";
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        $this->db->bind('title', $title);
        
        try {
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function addMessage($conversation_id, $sender, $message) {
        $query = "INSERT INTO ai_messages (conversation_id, sender, message) VALUES (:conversation_id, :sender, :message)";
        $this->db->query($query);
        $this->db->bind('conversation_id', $conversation_id);
        $this->db->bind('sender', $sender);
        $this->db->bind('message', $message);
        
        try {
            $this->db->execute();
            
            // Update conversation updated_at
            $this->db->query("UPDATE ai_conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = :conversation_id");
            $this->db->bind('conversation_id', $conversation_id);
            $this->db->execute();
            
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function checkRateLimit($user_id, $limit = 10) {
        // Count how many messages the user has sent today across all conversations
        $query = "SELECT COUNT(*) as msg_count 
                  FROM ai_messages m 
                  JOIN ai_conversations c ON m.conversation_id = c.id 
                  WHERE c.user_id = :user_id 
                  AND m.sender = 'user' 
                  AND DATE(m.created_at) = CURDATE()";
                  
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        $result = $this->db->single();
        
        if ($result && $result['msg_count'] >= $limit) {
            return false; // Limit reached
        }
        
        return true; // Limit not reached
    }

    public function getDatabaseStats() {
        $stats = [];
        
        // Total seluruh pengguna di sistem
        $this->db->query("SELECT COUNT(*) as count FROM users");
        $stats['total_all_users'] = $this->db->single()['count'] ?? 0;

        // Pelanggan (Customer)
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
        $stats['total_customers'] = $this->db->single()['count'] ?? 0;
        
        $this->db->query("SELECT DISTINCT name FROM users WHERE role = 'customer' ORDER BY name ASC");
        $customers = $this->db->resultSet() ?: [];
        $custNames = [];
        foreach ($customers as $c) {
            if (!empty($c['name'])) {
                $custNames[] = ucwords(trim($c['name']));
            }
        }
        $stats['customer_names'] = implode(', ', $custNames);
        
        // Teknisi (Technician)
        $this->db->query("SELECT id, name, email, is_active FROM users WHERE role = 'technician' ORDER BY name ASC");
        $allTechs = $this->db->resultSet() ?: [];
        $stats['total_technicians'] = count($allTechs);
        
        $activeTechNames = [];
        $inactiveTechNames = [];
        $techWithStatus = [];
        
        foreach ($allTechs as $t) {
            $tName = ucwords(trim($t['name']));
            $isActive = (isset($t['is_active']) && $t['is_active'] == 1);
            if ($isActive) {
                $activeTechNames[] = $tName;
                $techWithStatus[] = "{$tName} (Aktif)";
            } else {
                $inactiveTechNames[] = $tName;
                $techWithStatus[] = "{$tName} (Non-Aktif)";
            }
        }
        
        $stats['total_technicians_active'] = count($activeTechNames);
        $stats['technicians_active_names'] = implode(', ', $activeTechNames);
        
        $stats['total_technicians_inactive'] = count($inactiveTechNames);
        $stats['technicians_inactive_names'] = implode(', ', $inactiveTechNames);
        
        $stats['technician_names'] = implode(', ', array_map(function($t){ return ucwords(trim($t['name'])); }, $allTechs));
        $stats['technicians_with_status'] = implode(', ', $techWithStatus);
        $stats['technicians_list'] = $allTechs;
        
        // Operator
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'operator'");
        $stats['total_operators'] = $this->db->single()['count'] ?? 0;
        
        $this->db->query("SELECT DISTINCT name FROM users WHERE role = 'operator' ORDER BY name ASC");
        $operators = $this->db->resultSet() ?: [];
        $opNames = [];
        foreach ($operators as $o) {
            if (!empty($o['name'])) {
                $opNames[] = ucwords(trim($o['name']));
            }
        }
        $stats['operator_names'] = implode(', ', $opNames);

        // Admin
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $stats['total_admins'] = $this->db->single()['count'] ?? 0;
        
        $this->db->query("SELECT DISTINCT name FROM users WHERE role = 'admin' ORDER BY name ASC");
        $admins = $this->db->resultSet() ?: [];
        $admNames = [];
        foreach ($admins as $a) {
            if (!empty($a['name'])) {
                $admNames[] = ucwords(trim($a['name']));
            }
        }
        $stats['admin_names'] = implode(', ', $admNames);

        // Pesanan (Orders) Lengkap
        $this->db->query("SELECT o.id, o.status, o.problem_description, s.name as service_name, 
                                 u.name as customer_name, t.name as technician_name, o.created_at
                          FROM orders o 
                          LEFT JOIN services s ON o.service_id = s.id 
                          LEFT JOIN users u ON o.customer_id = u.id 
                          LEFT JOIN users t ON o.technician_id = t.id 
                          ORDER BY o.id DESC");
        $allOrders = $this->db->resultSet() ?: [];
        $stats['orders_list'] = $allOrders;
        $stats['total_orders'] = count($allOrders);

        $processingOrders = [];
        $assignedOrders = [];
        $pendingOrders = [];
        $completedOrders = [];
        $cancelledOrders = [];

        foreach ($allOrders as $ord) {
            $st = strtolower($ord['status']);
            if ($st === 'processing') {
                $processingOrders[] = $ord;
            } elseif ($st === 'assigned') {
                $assignedOrders[] = $ord;
            } elseif ($st === 'pending') {
                $pendingOrders[] = $ord;
            } elseif ($st === 'completed') {
                $completedOrders[] = $ord;
            } elseif ($st === 'cancelled') {
                $cancelledOrders[] = $ord;
            }
        }

        $stats['orders_processing'] = $processingOrders;
        $stats['orders_assigned'] = $assignedOrders;
        $stats['orders_pending'] = $pendingOrders;
        $stats['orders_completed'] = $completedOrders;
        $stats['orders_cancelled'] = $cancelledOrders;

        $stats['count_processing'] = count($processingOrders);
        $stats['count_assigned'] = count($assignedOrders);
        $stats['count_pending'] = count($pendingOrders);
        $stats['count_completed'] = count($completedOrders);
        $stats['count_cancelled'] = count($cancelledOrders);
        
        $this->db->query("SELECT COUNT(*) as count FROM services");
        $stats['total_services'] = $this->db->single()['count'] ?? 0;

        // Ulasan & Penilaian (Reviews)
        $this->db->query("SELECT r.*, u.name as customer_name, s.name as service_name, t.name as technician_name 
                          FROM reviews r 
                          JOIN orders o ON r.order_id = o.id 
                          JOIN users u ON o.customer_id = u.id 
                          JOIN services s ON o.service_id = s.id 
                          LEFT JOIN users t ON o.technician_id = t.id 
                          ORDER BY r.id DESC");
        $reviewsList = $this->db->resultSet() ?: [];
        $stats['reviews_list'] = $reviewsList;
        $stats['total_reviews'] = count($reviewsList);
        
        $reviewers = [];
        foreach ($reviewsList as $rev) {
            $reviewers[] = $rev['customer_name'];
        }
        $stats['reviewers_names'] = implode(', ', array_unique($reviewers));

        return $stats;
    }

    /**
     * Get a single AI setting
     */
    public function getSetting($key, $default = null) {
        $this->db->query("SELECT setting_value FROM ai_settings WHERE setting_key = :k LIMIT 1");
        $this->db->bind('k', $key);
        $row = $this->db->single();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Get all AI settings as associative array
     */
    public function getAllSettings(): array {
        $this->db->query("SELECT setting_key, setting_value, description, updated_at FROM ai_settings ORDER BY id ASC");
        $rows = $this->db->resultSet() ?: [];
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = [
                'value' => $r['setting_value'],
                'description' => $r['description'],
                'updated_at' => $r['updated_at']
            ];
        }
        return $settings;
    }

    /**
     * Update settings in ai_settings
     */
    public function updateSettings(array $settings): bool {
        try {
            foreach ($settings as $key => $val) {
                $this->db->query("UPDATE ai_settings SET setting_value = :val WHERE setting_key = :key");
                $this->db->bind('val', is_null($val) ? null : (string)$val);
                $this->db->bind('key', (string)$key);
                $this->db->execute();
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update Gemini connection test result
     */
    public function updateTestStatus($status, $latencyMs = null, $message = null): bool {
        try {
            $this->updateSettings([
                'last_test_at' => date('Y-m-d H:i:s'),
                'last_test_status' => $status,
                'last_test_latency' => $latencyMs !== null ? (string)$latencyMs : null,
                'last_test_message' => $message
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Reset AI settings to default values
     */
    public function resetSettingsToDefault(): bool {
        $defaults = [
            'ai_enabled' => '1',
            'ai_provider' => 'gemini',
            'ai_model' => 'gemini-flash-latest',
            'ai_temperature' => '0.7',
            'ai_max_tokens' => '800',
            'ai_timeout' => '15',
            'ai_fallback_faq' => '1',
            'bot_name' => 'ADCom Virtual Assistant',
            'welcome_message' => 'Halo! Saya Asisten Virtual ADCom Jasa. Ada yang bisa saya bantu seputar layanan servis komputer, laptop, printer, atau instalasi jaringan kami hari ini?',
            'response_language' => 'id',
            'bot_tone' => 'friendly_professional',
            'system_instruction' => "Anda adalah Chatbot Asisten AI untuk 'ADCom Jasa TKJ'. Tugas Anda adalah menjawab pertanyaan pengguna, menjelaskan paket layanan jasa, membantu keluhan customer, alur pemesanan, dan simulasi harga secara akurat dan ramah. Gunakan bahasa Indonesia yang baik dan sopan."
        ];

        return $this->updateSettings($defaults);
    }
}

