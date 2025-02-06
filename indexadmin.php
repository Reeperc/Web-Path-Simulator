<?php
// Start the session
session_start();

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header("Location: login.html");
    exit();
}

// Determine the current page from the URL (to toggle content visibility)
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Check for success parameter to display success modal
$success = isset($_GET['success']) ? $_GET['success'] : false;

// Inclure le script qui gère l'AJAX (ping/iperf) si POST
include 'trucainclure.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .green {
            background-color: #d4edda;
            color: #155724; /* Vert */
        }
        .orange {
            background-color: #fff3cd;
            color: #856404; /* Orange */
        }
        .red {
            background-color: #f8d7da;
            color: #721c24; /* Rouge */
        }
        .gray {
            background-color: #e0e0e0;
            color: #6c757d; /* Gris pour offline */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .latency-green {
            background-color: #d4edda; /* vert clair */
            color: #155724;           /* texte vert foncé */
        }
        .latency-orange {
            background-color: #fff3cd; /* orange clair */
            color: #856404;            /* texte orange foncé */
        }
        .latency-red {
            background-color: #f8d7da; /* rouge clair */
            color: #721c24;            /* texte rouge foncé */
        }
        .te0{
            color:rgb(0, 120, 201);
        }

    </style>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Responsive Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Admin - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,
        300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="indexadmin.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-laugh-wink"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Admin</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <a class="nav-link" href="indexadmin.php?page=dashboard">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Register User -->
        <li class="nav-item <?= $currentPage === 'register' ? 'active' : '' ?>">
            <a class="nav-link" href="indexadmin.php?page=register">
                <i class="fas fa-user-plus"></i>
                <span>Register User</span>
            </a>
        </li>

        

        <!-- Network Metrics -->
        <li class="nav-item <?= $currentPage === 'network-metrics' ? 'active' : '' ?>">
            <a class="nav-link" href="indexadmin.php?page=network-metrics">
                <i class="fas fa-tachometer-alt"></i>
                <span>Network Metrics</span>
            </a>
        </li>

        <!-- Notifications / Surgeries -->
        <li class="nav-item <?= $currentPage === 'operations' ? 'active' : '' ?>">
            <a class="nav-link" href="indexadmin.php?page=operations">
                <i class="fas fa-procedures"></i>
                <span>Manage Surgeries</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">
    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Messages -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link" href="messages.php" role="button">
                            <i class="fas fa-envelope fa-fw"></i>
                            <span class="badge badge-danger badge-counter">3+</span>
                        </a>
                    </li>

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- User Information / Logout -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle"
                           href="#"
                           id="userDropdown"
                           role="button"
                           data-toggle="modal"
                           data-target="#logoutModal">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Container -->
            <div class="container-fluid">

                <!-- DASHBOARD PAGE -->
                <?php if ($currentPage === 'dashboard'): ?>
                    <?php
                    // Example: fetch some operations for the calendar
                    require_once 'initialize_database.php';
                    $stmt = $pdo->prepare("
                        SELECT o.id, o.operation_type, o.scheduled_date,
                               p.name AS patient_name,
                               u.name AS doctor_name
                        FROM Operations o
                        JOIN Patients p ON o.patient_id = p.patient_id
                        JOIN Users u ON o.doctor_id = u.id
                        ORDER BY o.scheduled_date ASC
                    ");
                    $stmt->execute();
                    $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
                    <p>Welcome to the admin dashboard.</p>
                    <div id="calendar"></div>
                    <style>
                        #calendar {
                            max-width: 90%;
                            margin: 0 auto;
                            padding: 20px;
                        }
                    </style>

                <!-- REGISTER PAGE -->
                <?php elseif ($currentPage === 'register'): ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Register User</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="process_register.php">
                                        <div class="form-group">
                                            <label for="name">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role" name="role" required>
                                                <option value="doctor">Doctor</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="specialty">Specialty (For Doctors)</label>
                                            <select class="form-control" id="specialty" name="specialty" disabled>
                                                <option value="">Select Specialty</option>
                                                <option value="General Surgery">General Surgery</option>
                                                <option value="Cardiology">Cardiology</option>
                                                <option value="Neurology">Neurology</option>
                                                <option value="Orthopedics">Orthopedics</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Register</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- NETWORK METRICS -->
<!-- NETWORK METRICS -->
<?php elseif ($currentPage === 'network-metrics'): ?>
<h1 class="h3 mb-4 te0 text-center">Network Metrics</h1>

<!-- Styles additionnels pour embellir la page -->
<style>
    /* ------------------------ BODY / GLOBAL ------------------------ */
    body {
        background: linear-gradient(135deg, #2c3e50 0%, #4CA1AF 100%);
        font-family: Arial, sans-serif;
        color: #fff;
    }

    .network-metrics-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 10px;
    }

    h1.h3 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 30px;
        /* text-shadow: 2px 2px 4px rgba(0,0,0,0.3); */
    }

    /* ------------------------ DIAGRAMMES DE ROUTES ------------------------ */
    .route-container {
        margin-bottom: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .route-diagram {
        margin: 20px 0;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        backdrop-filter: blur(5px);
        width: 100%;
        max-width: 900px;
        overflow-x: auto;
    }
    .route-container h2 {
        margin-top: 10px;
        font-size: 1.5rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    svg {
        width: 100%;
        height: auto;
        overflow: visible;
    }

    /* Styles des "serveurs" (étapes géographiques) */
    .server {
        fill: #25bb74;
        stroke: #3498db;
        stroke-width: 2;
        filter: drop-shadow(3px 3px 5px rgba(0,0,0,0.5));
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    .server2 {
  fill: #ff40ab;
  stroke: #fbfdff;
  stroke-width: 2;
  filter: drop-shadow(3px 3px 5px rgba(0,0,0,0.5));
  transition: transform 0.3s ease;
  cursor: pointer; }

    .server:hover {
        transform: scale(1.02);
        stroke: #2ecc71;
    }
    .server-text {
        fill: #ecf0f1;
        font-size: 14px;
        font-weight: bold;
        pointer-events: none;
    }

    .server2:hover {
        transform: scale(1.02);
        stroke: #2ecc71;
    }
    

    /* Animation de pulsation subtile */
    .pulse {
        transform-origin: center;
        transform-box: fill-box;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%   { transform: scale(1);   opacity: 0.95; }
        50%  { transform: scale(1.02); opacity: 1;    }
        100% { transform: scale(1);   opacity: 0.95; }
    }

    /* Animation et style des lignes de connexion */
    .line {
        fill: none;
        stroke: #e74c3c;
        stroke-width: 4;
        stroke-dasharray: 10;
        stroke-dashoffset: 0;
        animation: dash 2s linear infinite;
    }
    @keyframes dash {
        to { stroke-dashoffset: -5; }
    }

    /* ------------------------ BOUTONS START/STOP ------------------------ */
    /* Boostrap + style perso */
    .btn-custom {
        font-size: 1.2rem;
        padding: 0.8rem 2rem;
        margin: 0.5rem;
        text-transform: uppercase;
        border: none;
        border-radius: 30px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    }
    /* .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgb(196, 241, 160);
    } */
    /* Exemple de gradient pour Start */
    .btn-start {
        background: linear-gradient(to right, #207ee2, #37c4a8);
        color: #fff;
    }
    /* .btn-start:hover {
        background: linear-gradient(to right,rgb(26, 123, 188), #2ecc71);
    } */
    /* Exemple de gradient pour Stop */
    .btn-stop {
        background: linear-gradient(to right, #c0392b, #e74c3c);
        color: #fff;
    }
    /* .btn-stop:hover {
        background: linear-gradient(to right, #e74c3c, #e67e22);
    } */

    /* ------------------------ GRAPHE ------------------------ */
    /* On force une hauteur plus importante et on laisse la largeur prendre la place dispo */
    #chartContainer {
        width: 100%;
        max-width: 900px; /* Ajuste si tu veux plus grand ou plus petit */
        margin: 30px auto;
    }
    #latencyChart {
        width: 100%;     /* Chart.js respectera la taille du parent pour la largeur */
        height: 400px;   /* Hauteur fixe pour avoir de la place */
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 5px 10px rgba(0,0,0,0.3);
    }

    /* ------------------------ TABLEAU ------------------------ */
    .table {
        margin-top: 20px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    }
    .table thead {
        background-color: #3498db;
        border: none;
    }
    .table thead th {
        border: none !important;
    }
    .table tbody tr {
        transition: background-color 0.3s;
    }
    .table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.15);
    }

    /* Couleurs de fond pour les latences */
    .latency-green {
        background-color: rgba(46, 204, 113, 0.2) !important;
    }
    .latency-orange {
        background-color: rgba(241, 196, 15, 0.2) !important;
    }
    .latency-red {
        background-color: rgba(231, 76, 60, 0.2) !important;
    }
    .txtr{
        color:rgb(0, 104, 202)
    }
</style>

<div class="network-metrics-container">

    <!-- DIAGRAMMES DES ROUTES -->
    <div class="route-container">
        <!-- Route 1 : US - Italy - Korea - UK -->
        <h2 class="txtr" >Route 1 : US - Italy - Korea - UK</h2>
        <div class="route-diagram">
            <svg width="900" height="200">
                <defs>
                    <!-- Dégradé radial pour l'effet lumineux -->
                    <radialGradient id="gradient" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#1abc9c"/>
                        <stop offset="100%" stop-color="#16a085"/>
                    </radialGradient>
                </defs>
                <!-- US -->
                <g class="pulse">
                    <rect class="server" x="50" y="100" width="120" height="60" rx="10" ry="10"/>
                    <text x="110" y="135" text-anchor="middle" class="server-text">US</text>
                </g>
                <!-- Italy -->
                <g class="pulse">
                    <rect class="server" x="230" y="50" width="120" height="60" rx="10" ry="10"/>
                    <text x="290" y="85" text-anchor="middle" class="server-text">Italy</text>
                </g>
                <!-- Korea -->
                <g class="pulse">
                    <rect class="server" x="410" y="100" width="120" height="60" rx="10" ry="10"/>
                    <text x="470" y="135" text-anchor="middle" class="server-text">Korea</text>
                </g>
                <!-- UK -->
                <g class="pulse">
                    <rect class="server" x="590" y="50" width="120" height="60" rx="10" ry="10"/>
                    <text x="650" y="85" text-anchor="middle" class="server-text">UK</text>
                </g>
                <!-- Liaisons -->
                <path class="line" d="M170,130 C200,80 250,80 230,80"/>
                <path class="line" d="M350,80 C380,130 410,130 410,130"/>
                <path class="line" d="M530,130 C560,80 600,80 590,80"/>
            </svg>
        </div>

        <!-- Route 2 : US - Poland - Portugal - UK -->
        <h2 class="txtr" >Route 2 : US - Poland - Portugal - UK</h2>
        <div class="route-diagram">
            <svg width="900" height="200">
                <defs>
                    <!-- Même dégradé utilisé pour les serveurs -->
                    <radialGradient id="gradient" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#1abc9c"/>
                        <stop offset="100%" stop-color="#16a085"/>
                    </radialGradient>
                </defs>
                <!-- US -->
                <g class="pulse">
                    <rect class="server2" x="50" y="100" width="120" height="60" rx="10" ry="10"/>
                    <text x="110" y="135" text-anchor="middle" class="server-text">US</text>
                </g>
                <!-- Poland -->
                <g class="pulse">
                    <rect class="server2" x="230" y="50" width="120" height="60" rx="10" ry="10"/>
                    <text x="290" y="85" text-anchor="middle" class="server-text">Poland</text>
                </g>
                <!-- Portugal -->
                <g class="pulse">
                    <rect class="server2" x="410" y="100" width="120" height="60" rx="10" ry="10"/>
                    <text x="470" y="135" text-anchor="middle" class="server-text">Portugal</text>
                </g>
                <!-- UK -->
                <g class="pulse">
                    <rect class="server2" x="590" y="50" width="120" height="60" rx="10" ry="10"/>
                    <text x="650" y="85" text-anchor="middle" class="server-text">UK</text>
                </g>
                <!-- Liaisons -->
                <path class="line" d="M170,130 C200,80 250,80 230,80"/>
                <path class="line" d="M350,80 C380,130 410,130 410,130"/>
                <path class="line" d="M530,130 C560,80 600,80 590,80"/>
            </svg>
        </div>
    </div>

    <!-- BOUTONS START/STOP -->
    <div class="text-center">
        <button id="startBtn" class="btn btn-custom btn-start">Start</button>
        <button id="stopBtn" class="btn btn-custom btn-stop">Stop</button>
    </div>

    <!-- GRAPHE (responsive et plus grand) -->
    <div id="chartContainer">
        <canvas id="latencyChart"></canvas>
    </div>

    <!-- TABLEAU -->
    <table class="table table-bordered table-hover text-center">
        <thead>
            <tr>
                <th>Robot ID</th>
                <th>Latency (ms)</th>
                <th>Bandwidth (Mbits/s)</th>
                <th>Connection Status</th>
            </tr>
        </thead>
        <tbody id="metricsTableBody">
            <!-- Dynamique -->
        </tbody>
    </table>
</div>

<!-- Chart.js (via CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // -------------------------------------------------------------------------
    // CHART
    // -------------------------------------------------------------------------
    const ctx = document.getElementById('latencyChart').getContext('2d');
    const chartData = {
        labels: [],
        datasets: [
            {
                label: 'Latency Route 1 (ms)',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                fill: false,
            },
            {
                label: 'Latency Route 2 (ms)',
                data: [],
                borderColor: 'rgb(255, 99, 132)',
                fill: false,
            },
        ]
    };

    const latencyChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,         // Rend le chart responsive
            maintainAspectRatio: false, // Permet de remplir le container en hauteur
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Latency (ms)'
                    }
                }
            }
        }
    });

    // -------------------------------------------------------------------------
    // BOUTONS START/STOP
    // -------------------------------------------------------------------------
    let pollingInterval = null;

    document.getElementById('startBtn').addEventListener('click', () => {
        fetch('ajax_network.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'start' })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            startPolling();
        })
        .catch(err => console.error(err));
    });

    document.getElementById('stopBtn').addEventListener('click', () => {
        fetch('ajax_network.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'stop' })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            stopPolling();
        })
        .catch(err => console.error(err));
    });

    function startPolling() {
        if (!pollingInterval) {
            pollingInterval = setInterval(fetchMetrics, 2000); // ex: toutes les 2s
        }
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    function getLatencyClass(latency) {
        if (latency <= 400) {
            return 'latency-green';
        } else if (latency > 400 && latency <= 600) {
            return 'latency-orange';
        } else {
            return 'latency-red';
        }
    }

    // -------------------------------------------------------------------------
    // FETCH METRICS POUR LE GRAPHE ET LE TABLEAU
    // -------------------------------------------------------------------------
    function fetchMetrics() {
        fetch('ajax_network.php?action=get_data')
            .then(res => res.json())
            .then(data => {
                // data = { timestamp, robots: [ { id, latency, bandwidth, status }, ... ] }

                const timeLabel = new Date(data.timestamp * 1000).toLocaleTimeString();

                // Extraire RobotA & RobotB
                const robotA = data.robots[0];
                const robotB = data.robots[1];

                // 1) Mettre à jour le chart
                chartData.labels.push(timeLabel);
                chartData.datasets[0].data.push(robotA.latency);
                chartData.datasets[1].data.push(robotB.latency);
                if (chartData.labels.length > 20) {
                    chartData.labels.shift();
                    chartData.datasets[0].data.shift();
                    chartData.datasets[1].data.shift();
                }
                latencyChart.update();

                // 2) Mettre à jour le tableau
                const tbody = document.getElementById('metricsTableBody');
                tbody.innerHTML = '';

                data.robots.forEach(robot => {
                    const tr = document.createElement('tr');
                    let latencyClass = getLatencyClass(robot.latency);
                    tr.classList.add(latencyClass);
                    tr.innerHTML = `
                        <td>${robot.id}</td>
                        <td>${robot.latency.toFixed(2)}</td>
                        <td>${robot.bandwidth.toFixed(2)}</td>
                        <td>${robot.status}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => console.error(err));
    }
</script>



                <!-- OPERATIONS PAGE -->
                <?php elseif ($currentPage === 'operations'): ?>
                    <div class="container-fluid">
                        <?php if (isset($_GET['success'])): ?>
                            <?php if ($_GET['success'] == 1): ?>
                                <div class="alert alert-success" role="alert">
                                    Operation added successfully!
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger" role="alert">
                                    Failed to add the operation. Please try again.
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Plan an Operation</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="process_operations.php">
                                        <div class="form-group">
                                            <label for="patient">Select Patient</label>
                                            <select class="form-control" id="patient" name="patient" required>
                                                <?php
                                                require_once 'initialize_database.php';
                                                $stmt = $pdo->query("SELECT patient_id, name FROM Patients");
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['patient_id']}'>{$row['name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="doctor">Select Doctor</label>
                                            <select class="form-control" id="doctor" name="doctor" required>
                                                <?php
                                                $stmt = $pdo->prepare("SELECT id, name FROM Users WHERE role = 'doctor'");
                                                $stmt->execute();
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="operation_type">Operation Type</label>
                                            <select class="form-control" id="operation_type" name="operation_type" required>
                                                <option value="General Surgery">General Surgery</option>
                                                <option value="Cardiology">Cardiology</option>
                                                <option value="Neurology">Neurology</option>
                                                <option value="Orthopedics">Orthopedics</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="scheduled_date">Scheduled Date & Time</label>
                                            <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="comment">Comment</label>
                                            <textarea class="form-control" id="comment" name="comment"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Plan Operation</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exemple de script de refresh (facultatif) si vous aviez un autre tableau -->
                    <script>
                        function refreshMetrics() {
                            fetch("get_metrics.php")
                                .then(response => response.json())
                                .then(data => {
                                    let html = `
                                        <tr>
                                            <td>${data.robot_id || "N/A"}</td>
                                            <td>${data.latency !== null ? data.latency + " ms" : "N/A"}</td>
                                            <td>${data.packet_loss !== null ? data.packet_loss + " %" : "N/A"}</td>
                                            <td>${data.bandwidth_usage !== null ? data.bandwidth_usage + " Mbps" : "N/A"}</td>
                                            <td>${data.connection_status || "N/A"}</td>
                                        </tr>
                                    `;
                                    document.getElementById("metrics-body").innerHTML = html;
                                })
                                .catch(error => {
                                    console.error("Erreur lors de la récupération des métriques :", error);
                                    document.getElementById("metrics-body").innerHTML = `
                                        <tr><td colspan="6">Erreur de chargement des métriques.</td></tr>
                                    `;
                                });
                        }

                        // Exemple : rafraîchir toutes les 10 secondes
                        // setInterval(refreshMetrics, 10000);
                        // refreshMetrics();
                    </script>

                <?php else: ?>
                    <!-- Page Not Found / default fallback -->
                    <h1>404 - Page Not Found</h1>
                <?php endif; ?>
            </div>
            <!-- End of container-fluid -->
        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>&copy; MedicalApp 2025</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1"
     role="dialog" aria-labelledby="logoutModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to log out?
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Cancel
                </button>
                <a href="logout.php" class="btn btn-primary">
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Core JavaScript / Bootstrap -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Easing + Custom scripts -->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<!-- Activation/désactivation du champ "specialty" selon le rôle -->
<script>
    document.getElementById('role').addEventListener('change', function() {
        const specialtyField = document.getElementById('specialty');
        if (this.value === 'doctor') {
            specialtyField.disabled = false;
        } else {
            specialtyField.value = '';
            specialtyField.disabled = true;
        }
    });
</script>

<!-- Script pour afficher le FullCalendar (exemple sur le dashboard) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Récupération depuis PHP
    <?php if ($currentPage === 'dashboard' && !empty($operations)): ?>
    const operations = <?php echo json_encode($operations); ?>;

    // Préparation des events
    const events = operations.map(op => {
        const today = new Date();
        const operationDate = new Date(op.scheduled_date);
        const daysDifference = Math.floor((operationDate - today) / (1000 * 60 * 60 * 24));

        return {
            id: op.id,
            title: `${op.operation_type} - ${op.patient_name}`,
            start: op.scheduled_date,
            backgroundColor: daysDifference <= 3 ? 'red' : 'green',
            textColor: 'white',
            extendedProps: {
                doctorName: op.doctor_name,
                patientName: op.patient_name,
                operationType: op.operation_type,
                scheduledDate: op.scheduled_date
            }
        };
    });

    // Rendu du calendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: events,
        eventClick: function(info) {
            const { doctorName, patientName, operationType, scheduledDate } = info.event.extendedProps;
            alert(`Operation Details:
Doctor: ${doctorName}
Patient: ${patientName}
Type: ${operationType}
Scheduled Date: ${scheduledDate}`);
        }
    });

    calendar.render();
    <?php endif; ?>
});
</script>

</body>
</html>
