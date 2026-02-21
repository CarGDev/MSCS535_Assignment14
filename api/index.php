<?php
require_once __DIR__ . '/../config/database.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    $actions = [
        'csrf_token' => fn() => print json_encode(['token' => generateToken()]),
        'create' => fn() => handleRegistration($database),
        'login' => fn() => handleLogin($database),
        'submit_data' => fn() => handleDataSubmission($database),
        'get_data' => fn() => handleGetData($database),
        'delete_data' => fn() => handleDeleteData($database),
    ];

    if (isset($actions[$action])) {
        $actions[$action]();
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function handleRegistration($database) {
    $token = $_POST['csrf_token'] ?? '';
    
    if (!verifyToken($token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        return;
    }

    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        return;
    }

    if (!validateEmail($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }

    if (!validateString($username, 50)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid username format']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }

    $passwordErrors = validatePassword($password);
    if (!empty($passwordErrors)) {
        http_response_code(400);
        echo json_encode(['error' => $passwordErrors[0]]);
        return;
    }

    try {
        $checkSql = "SELECT id FROM users WHERE email = ? OR username = ?";
        $stmt = $database->query($checkSql, [$email, $username]);
        
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'User already exists']);
            return;
        }

        $hashedPassword = hashPassword($password);
        
        $insertSql = "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())";
        $database->query($insertSql, [sanitizeInput($username), $email, $hashedPassword]);

        echo json_encode(['success' => true, 'message' => 'User created successfully']);
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create user']);
    }
}

function handleLogin($database) {
    $token = $_POST['csrf_token'] ?? '';
    
    if (!verifyToken($token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        return;
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }

    try {
        $sql = "SELECT id, username, email, password FROM users WHERE email = ?";
        $stmt = $database->query($sql, [$email]);
        $user = $stmt->fetch();

        if (!$user || !verifyPassword($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Login failed']);
    }
}

function handleDataSubmission($database) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        return;
    }

    $token = $_POST['csrf_token'] ?? '';
    
    if (!verifyToken($token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        return;
    }

    $data = sanitizeInput($_POST['data'] ?? '');

    if (empty($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Data is required']);
        return;
    }

    if (!validateString($data, 5000)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data format']);
        return;
    }

    try {
        $sql = "INSERT INTO user_data (user_id, data, created_at) VALUES (?, ?, NOW())";
        $database->query($sql, [$_SESSION['user_id'], $data]);

        echo json_encode(['success' => true, 'message' => 'Data saved successfully']);
    } catch (Exception $e) {
        error_log("Data submission error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save data']);
    }
}

function handleGetData($database) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        return;
    }

    try {
        $sql = "SELECT id, data, created_at FROM user_data WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $database->query($sql, [$_SESSION['user_id']]);
        $data = $stmt->fetchAll();

        $sanitizedData = array_map(function($item) {
            return [
                'id' => $item['id'],
                'data' => escapeHtml($item['data']),
                'created_at' => $item['created_at']
            ];
        }, $data);

        echo json_encode(['success' => true, 'data' => $sanitizedData]);
    } catch (Exception $e) {
        error_log("Get data error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to retrieve data']);
    }
}

function handleDeleteData($database) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        return;
    }

    $token = $_POST['csrf_token'] ?? '';
    
    if (!verifyToken($token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        return;
    }

    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
        return;
    }

    try {
        $sql = "DELETE FROM user_data WHERE id = ? AND user_id = ?";
        $database->query($sql, [$id, $_SESSION['user_id']]);

        echo json_encode(['success' => true, 'message' => 'Data deleted successfully']);
    } catch (Exception $e) {
        error_log("Delete data error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete data']);
    }
}
