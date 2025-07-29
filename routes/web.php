<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/PersonnelController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$controller = new PersonnelController();
$auth = new AuthController();

// ROUTES

if ($requestUri === '/' && $requestMethod === 'GET') {
    echo json_encode(['message' => 'Welcome to Personnel Profile Management API']);
}

// GET all personnel
elseif ($requestUri === '/api/personnel' && $requestMethod === 'GET') {
    $controller->index();
}

// GET one personnel by ID
elseif (preg_match('#^/api/personnel/([a-zA-Z0-9]+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $controller->show($matches[1]);
}

// CREATE personnel
elseif ($requestUri === '/api/personnel/create' && $requestMethod === 'POST') {
    $controller->create();
}

// UPDATE personnel
elseif ($requestUri === '/api/personnel/update' && $requestMethod === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['personnel_id'])) {
        echo json_encode(['error' => 'personnel_id is required']);
        exit;
    }
    $controller->update($data['personnel_id']);
}

// DELETE personnel
if (preg_match('#^/api/personnel/delete/([a-zA-Z0-9]+)$#', $requestUri, $matches) && $requestMethod === 'DELETE') {
    $personnel_id = $matches[1];
    $controller->delete($personnel_id);
    exit;
}

// LOGIN
elseif ($requestUri === '/api/login' && $requestMethod === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $auth->login($data['username'], $data['password']);
}

// LOGOUT
elseif ($requestUri === '/api/logout' && $requestMethod === 'POST') {
    $auth->logout();
}

// 404 fallback
else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
