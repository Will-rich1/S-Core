<?php

// Set headers untuk CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);

// Router
if ($method === 'POST' && $path === '/login') {
    handleLogin();
} elseif ($method === 'GET' && $path === '/test') {
    handleTest();
} else {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Endpoint not found'
    ]);
}

// Login handler
function handleLogin() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    // Validasi
    if (empty($email) || empty($password)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Email and password are required'
        ]);
        return;
    }
    
    // Test credentials (nanti bisa diganti dengan database)
    $testEmail = 'admin@itbss.ac.id';
    $testPassword = 'password';
    
    if ($email === $testEmail && $password === $testPassword) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => 1,
                    'name' => 'Manda Aprikasari',
                    'email' => $email,
                    'role' => 'student'
                ],
                'token' => 'dummy-token-' . time()
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Incorrect email address or password'
        ]);
    }
}

// Test handler
function handleTest() {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
