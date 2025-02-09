<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle "doctor"
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Configuration de la base de données
$host = 'localhost';
$dbname = 'webapp';
$user = 'webuser';
$password = 'BARA@@.bara2020';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Compter les messages non lus
    $unreadCount = $pdo->query("
        SELECT COUNT(*) 
        FROM Messages 
        WHERE receiver_role = 'doctor' 
          AND receiver_id = {$_SESSION['user_id']} 
          AND is_replied = 0
    ")->fetchColumn();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dc-interface.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Doctor</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="dc-interface.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="control_robot.php">
                    <i class="fas fa-fw fa-robot"></i>
                    <span>Control Robot</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="view_patient_data.php">
                    <i class="fas fa-fw fa-user-injured"></i>
                    <span>View Patient Data</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="view_logs.php">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>View Logs</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link" href="doctor_messages.php" role="button">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-danger badge-counter"><?= $unreadCount > 0 ? $unreadCount . '+' : '0' ?></span>
                            </a>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="modal" data-target="#logoutModal">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Doctor</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>
