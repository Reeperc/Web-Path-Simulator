<?php
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

require_once 'initialize_database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        header("Location: indexadmin.php?page=register&success=0");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $role]);

        header("Location: indexadmin.php?page=register&success=1");
    } catch (PDOException $e) {
        header("Location: indexadmin.php?page=register&success=0");
    }
} else {
    header("Location: indexadmin.php?page=dashboard");
}
