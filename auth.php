<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'helpers.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . base_url('login'));
        exit();
    }
}

function requireRole($requiredRole) {
    requireAuth();
    
    $userRole = getUserRole();
    if ($userRole !== $requiredRole) {
        header('Location: ' . base_url('unauthorized'));
        exit();
    }
}

function login($email, $password) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_role'] = strtolower($user['CorporateRight']);
        $_SESSION['user_name'] = $user['FirstName'] . ' ' . $user['Surname'];
        return $_SESSION['user_role'];
    }
    
    return false;
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: ' . base_url('login'));
    exit();
}