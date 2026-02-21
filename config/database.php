<?php
session_start();

class Database {
    private $host;
    private $db;
    private $user;
    private $pass;
    private $port;
    private $charset;
    private $pdo;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db = getenv('DB_NAME') ?: 'securecode';
        $this->user = getenv('DB_USER') ?: 'carlos';
        $this->pass = getenv('DB_PASSWORD') ?: '';
        $this->port = getenv('DB_PORT') ?: '5432';
        $this->charset = 'utf8';
    }

    public function getConnection() {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db};options='--client_encoding={$this->charset}'";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new \Exception("Database connection failed");
        }

        return $this->pdo;
    }

    public function query($sql, $params = []) {
        $pdo = $this->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function escapeHtml($data) {
    if (is_array($data)) {
        return array_map('escapeHtml', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateString($data, $maxLength = 1000) {
    if (is_array($data)) {
        return array_map(fn($item) => validateString($item, $maxLength), $data);
    }
    $data = trim($data);
    if (strlen($data) > $maxLength) {
        return false;
    }
    return preg_match('/^[\p{L}\p{N}\s\-_.,!?@]+$/u', $data) === 1 || empty($data);
}

function sanitizeForDatabase($data) {
    if (is_array($data)) {
        return array_map('sanitizeForDatabase', $data);
    }
    return preg_replace('/[^\p{L}\p{N}\s\-_.,!?@]/u', '', $data);
}

function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    }
    
    if (strlen($password) > 128) {
        $errors[] = 'Password must be less than 128 characters';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }
    
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = 'Password must contain at least one special character';
    }
    
    return $errors;
}

function generateToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
