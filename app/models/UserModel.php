<?php

class UserModel {
    private $table = 'users';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getUserByEmail($email) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE email = :email');
        $this->db->bind('email', $email);
        return $this->db->single();
    }

    public function getUserByPhone($phone) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE phone_number = :phone');
        $this->db->bind('phone', $phone);
        return $this->db->single();
    }

    public function getUserByIdentifier($identifier) {
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$identifier);
        $normPhone = '';
        if (!empty($cleanPhone)) {
            if (strpos($cleanPhone, '08') === 0) {
                $normPhone = '62' . substr($cleanPhone, 1);
            } elseif (strpos($cleanPhone, '8') === 0) {
                $normPhone = '62' . $cleanPhone;
            } else {
                $normPhone = $cleanPhone;
            }
        }

        $query = 'SELECT * FROM ' . $this->table . ' WHERE LOWER(email) = LOWER(:email)';
        if (!empty($cleanPhone)) {
            $query .= ' OR phone_number = :raw_phone OR phone_number = :norm_phone';
        }
        $query .= ' LIMIT 1';

        $this->db->query($query);
        $this->db->bind('email', trim($identifier));
        if (!empty($cleanPhone)) {
            $this->db->bind('raw_phone', $cleanPhone);
            $this->db->bind('norm_phone', $normPhone);
        }
        return $this->db->single();
    }

    public function registerCustomer($data) {
        $query = "INSERT INTO users (name, email, phone_number, phone_verified_at, password, role) 
                  VALUES (:name, :email, :phone_number, :phone_verified_at, :password, 'customer')";
        
        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('phone_number', !empty($data['phone_number']) ? $data['phone_number'] : null);
        $this->db->bind('phone_verified_at', !empty($data['phone_verified_at']) ? $data['phone_verified_at'] : null);
        
        // Check if password already hashed
        $password = $data['password'];
        $hash = (strpos($password, '$2y$') === 0 || strpos($password, '$2a$') === 0) 
            ? $password 
            : password_hash($password, PASSWORD_BCRYPT);
        $this->db->bind('password', $hash);

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function isPhoneTaken($phone, $excludeId = null) {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE phone_number = :phone';
        if ($excludeId !== null) {
            $query .= ' AND id != :excludeId';
        }
        $this->db->query($query);
        $this->db->bind('phone', trim($phone));
        if ($excludeId !== null) {
            $this->db->bind('excludeId', $excludeId);
        }
        $res = $this->db->single();
        return !empty($res);
    }

    public function updatePassword($userId, $newPassword) {
        $query = 'UPDATE ' . $this->table . ' SET password = :password WHERE id = :id';
        $this->db->query($query);
        $this->db->bind('password', password_hash($newPassword, PASSWORD_BCRYPT));
        $this->db->bind('id', $userId);
        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }
    public function getTechnicians() {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE role = "technician" ORDER BY name ASC');
        return $this->db->resultSet();
    }

    public function getTechnicianById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id AND role = "technician"');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function addTechnician($data) {
        $query = "INSERT INTO users (name, email, password, role, is_active) VALUES (:name, :email, :password, 'technician', :is_active)";
        
        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('password', password_hash($data['password'], PASSWORD_BCRYPT));
        $this->db->bind('is_active', isset($data['is_active']) ? $data['is_active'] : 1);

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function updateTechnician($data) {
        $query = "UPDATE users SET name = :name, email = :email, is_active = :is_active";
        
        if (!empty($data['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id AND role = 'technician'";
        
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('name', $data['name']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('is_active', isset($data['is_active']) ? $data['is_active'] : 1);

        if (!empty($data['password'])) {
            $this->db->bind('password', password_hash($data['password'], PASSWORD_BCRYPT));
        }

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
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

    public function getTechnicianJobCount($id) {
        $this->db->query('SELECT COUNT(*) as active_jobs FROM orders WHERE technician_id = :id AND status IN ("assigned", "processing")');
        $this->db->bind('id', $id);
        $result = $this->db->single();
        return $result ? $result['active_jobs'] : 0;
    }

    // ==========================================
    // MANAJEMEN PENGGUNA UNTUK ADMIN
    // ==========================================

    public function getAllUsers($role = null, $keyword = null) {
        $query = 'SELECT id, name, email, phone_number, phone_verified_at, role, is_active, created_at, updated_at FROM ' . $this->table . ' WHERE 1=1';
        $params = [];

        if ($role === 'inactive') {
            $query .= ' AND is_active = 0';
        } elseif (!empty($role) && in_array($role, ['admin', 'operator', 'technician', 'customer'])) {
            $query .= ' AND role = :role';
            $params['role'] = $role;
        }

        if (!empty($keyword)) {
            $query .= ' AND (name LIKE :kw OR email LIKE :kw OR phone_number LIKE :kw)';
            $params['kw'] = '%' . $keyword . '%';
        }

        $query .= ' ORDER BY FIELD(role, "admin", "operator", "technician", "customer"), id ASC';

        $this->db->query($query);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }

        return $this->db->resultSet();
    }

    public function getUserById($id) {
        $this->db->query('SELECT id, name, email, phone_number, phone_verified_at, role, is_active, created_at, updated_at FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function getUserStats() {
        $this->db->query('SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN role = "admin" THEN 1 ELSE 0 END) as total_admin,
            SUM(CASE WHEN role = "operator" THEN 1 ELSE 0 END) as total_operator,
            SUM(CASE WHEN role = "technician" THEN 1 ELSE 0 END) as total_technician,
            SUM(CASE WHEN role = "customer" THEN 1 ELSE 0 END) as total_customer,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as total_active,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as total_inactive
            FROM ' . $this->table);
        $result = $this->db->single();
        return [
            'total' => (int)($result['total'] ?? 0),
            'admin' => (int)($result['total_admin'] ?? 0),
            'operator' => (int)($result['total_operator'] ?? 0),
            'technician' => (int)($result['total_technician'] ?? 0),
            'customer' => (int)($result['total_customer'] ?? 0),
            'active' => (int)($result['total_active'] ?? 0),
            'inactive' => (int)($result['total_inactive'] ?? 0)
        ];
    }

    public function isEmailTaken($email, $excludeId = null) {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE LOWER(email) = LOWER(:email)';
        if ($excludeId !== null) {
            $query .= ' AND id != :excludeId';
        }
        $this->db->query($query);
        $this->db->bind('email', trim($email));
        if ($excludeId !== null) {
            $this->db->bind('excludeId', $excludeId);
        }
        $res = $this->db->single();
        return !empty($res);
    }

    public function addUser($data) {
        $query = "INSERT INTO " . $this->table . " (name, email, password, role, is_active) VALUES (:name, :email, :password, :role, :is_active)";
        
        $this->db->query($query);
        $this->db->bind('name', trim($data['name']));
        $this->db->bind('email', trim(strtolower($data['email'])));
        $this->db->bind('password', password_hash($data['password'], PASSWORD_BCRYPT));
        $this->db->bind('role', $data['role']);
        $this->db->bind('is_active', isset($data['is_active']) ? (int)$data['is_active'] : 1);

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function updateUser($data) {
        $query = "UPDATE " . $this->table . " SET name = :name, email = :email, role = :role, is_active = :is_active";
        
        if (!empty($data['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('name', trim($data['name']));
        $this->db->bind('email', trim(strtolower($data['email'])));
        $this->db->bind('role', $data['role']);
        $this->db->bind('is_active', isset($data['is_active']) ? (int)$data['is_active'] : 1);

        if (!empty($data['password'])) {
            $this->db->bind('password', password_hash($data['password'], PASSWORD_BCRYPT));
        }

        try {
            $this->db->execute();
            return $this->db->rowCount();
        } catch (PDOException $e) {
            return -1;
        }
    }

    public function deleteUser($id) {
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

    public function getUserRelatedInfo($id) {
        $this->db->query('SELECT COUNT(*) as order_count FROM orders WHERE customer_id = :id OR technician_id = :id');
        $this->db->bind('id', $id);
        $res = $this->db->single();
        return [
            'order_count' => (int)($res['order_count'] ?? 0)
        ];
    }
}

