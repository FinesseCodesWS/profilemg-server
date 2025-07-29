<?php
session_start();
require_once '../config/db.php';

class AuthController
{
    public function login($username, $password)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Invalid login']);
        }
    }

    public function logout()
    {
        session_destroy();
        echo json_encode(['status' => 'logged out']);
    }

    public function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }
}
