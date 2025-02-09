<?php 
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

require_once 'initialize_database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient = trim($_POST['patient']);
    $doctor = trim($_POST['doctor']);
    $operation_type = trim($_POST['operation_type']);
    $scheduled_date = trim($_POST['scheduled_date']);
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;

    if (empty($patient) || empty($doctor) || empty($operation_type) || empty($scheduled_date)) {
        header("Location: indexadmin.php?page=operations&success=0");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO Operations (patient_id, doctor_id, operation_type, scheduled_date, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$patient, $doctor, $operation_type, $scheduled_date, $comment]);

        // Redirect with a success parameter
        header("Location: indexadmin.php?page=operations&success=1");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

