<?php
// Start the session
session_start();

// Check if the user is logged in and has the doctor role
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'webapp';
$user = 'webuser';
$password = 'BARA@@.bara2020';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all logs
    $stmt = $pdo->prepare("
        SELECT l.log_id, l.timestamp, l.action, l.details, 
               (SELECT name FROM Users WHERE id = l.admin_id) AS admin_name,
               (SELECT name FROM Doctors WHERE doctor_id = l.doctor_id) AS doctor_name
        FROM Logs l
        ORDER BY l.timestamp DESC
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error connecting to the database: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .table-container {
            margin: 20px auto;
            max-width: 1200px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table-container h2 {
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .no-data {
            text-align: center;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>

<body>
    <?php include 'headerDoctor.php'; // Include the header ?>

    <div class="container-fluid">
        <div class="table-container">
            <h2>System Logs</h2>

            <?php if (count($logs) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>Admin</th>
                            <th>Doctor</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['log_id']) ?></td>
                                <td><?= htmlspecialchars($log['admin_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($log['doctor_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= htmlspecialchars($log['details']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No logs found in the system.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footerDoctor.php'; // Include the footer ?>
</body>

</html>
