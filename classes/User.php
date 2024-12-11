<?php
require_once 'db.php';

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAllUsers() {
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($data) {
        $query = "INSERT INTO users (name, phone, email, address, picture) VALUES (:name, :phone, :email, :address, :picture)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function updateUser($data) {
        // বিদ্যমান রেকর্ড আপডেট করা
        $query = "UPDATE users 
                  SET name = :name, 
                      phone = :phone, 
                      email = :email, 
                      address = :address, 
                      picture = :picture 
                  WHERE id = :id";
        
        // Prepare Statement
        $stmt = $this->conn->prepare($query);
    
        // Execute with Bind Parameters
        return $stmt->execute([
            'id' => $data['id'],                // User ID
            'name' => $data['name'],            // Name
            'phone' => $data['phone'],          // Phone
            'email' => $data['email'],          // Email
            'address' => $data['address'],      // Address
            'picture' => $data['picture']       // Picture File Name
        ]);
    }
    
    
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}
?>
