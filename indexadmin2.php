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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .green {
            background-color: #d4edda;
            color: #155724;
        }

        /* Vert */
        .orange {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Orange */
        .red {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Rouge */
        .gray {
            background-color: #e0e0e0;
            color: #6c757d;
        }

        /* Gris pour offline */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
    </style>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

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

            <!-- Notifications -->
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
                        <!-- Message Icon -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link" href="messages.php" role="button">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="modal" data-target="#logoutModal">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Main Content -->
                <div class="container-fluid">
                    <?php if ($currentPage === 'dashboard'): ?>
                        <!-- Dashboard Content -->
                        <?php
                        // Fetch operations for the calendar
                        require_once 'initialize_database.php';
                        $stmt = $pdo->prepare("SELECT o.id, o.operation_type, o.scheduled_date, p.name AS patient_name, u.name AS doctor_name 
                           FROM Operations o
                           JOIN Patients p ON o.patient_id = p.patient_id
                           JOIN Users u ON o.doctor_id = u.id
                           ORDER BY o.scheduled_date ASC");
                        $stmt->execute();
                        $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <!-- Dashboard Content -->
                        <p>Welcome to the admin dashboard.</p>

                        <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
                        <div id="calendar"></div>
                        <style>
                            #calendar {
                                max-width: 90%;
                                margin: 0 auto;
                                padding: 20px;
                            }
                        </style>

                    <?php elseif ($currentPage === 'register'): ?>
                        <!-- Register User Content -->
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


                    <?php elseif ($currentPage === 'network-metrics'): ?>
                        <h1 class="h3 mb-4 text-gray-800">Network Metrics</h1>

                        <!-- Server Selection Buttons -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-yellow-dark btn-lg btn-block server-btn" onclick="changeRoute(1)">UK</button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-yellow-dark btn-lg btn-block server-btn" onclick="changeRoute(2)">West Europe</button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-yellow-dark btn-lg btn-block server-btn" onclick="changeRoute(3)">Paris</button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-yellow-dark btn-lg btn-block server-btn" onclick="changeRoute(4)">Korea</button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-yellow-dark btn-lg btn-block server-btn" onclick="changeRoute(5)">US</button>
                            </div>

                        </div>

                        <script>
                            function changeRoute(routeNumber) {
                                fetch(`changeroute.php?route=${routeNumber}`)
                                    .then(response => response.text())
                                    .then(data => {
                                        alert(data); // On affiche la réponse (Succès ou Erreur)
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        alert("Une erreur s'est produite");
                                    });
                            }
                        </script>

                        <!-- Table for Metrics -->
                        <h1 class="h3 mb-0 text-gray-800">Network Metrics</h1>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Metrics Overview</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Robot ID</th>
                                                    <th>Latency</th>
                                                    <th>Bandwidth Usage</th>
                                                    <th>Connection Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="metrics-body">
                                                <tr>
                                                    <td colspan="4">Chargement des métriques...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Network Evolution</h6>
                            </div>
                            <div class="card-body">
                                <?php include "network_comparison.php"; ?>

                            </div>
                        </div>

                        <!-- Import Chart.js -->
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                        <script>
                            let selectedServer = null; // Store selected server

                            function configureRoute(routeNumber) {
                                fetch(`http://10.20.75.188:5000/configure?route=${routeNumber}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        alert(data.message); // Show response message
                                        selectedServer = routeNumber;
                                        refreshMetrics(); // Load metrics for the selected route
                                    })
                                    .catch(error => console.error("Error configuring route:", error));
                            }

                            // Attach click event to each button
                            document.querySelectorAll('.server-btn').forEach(button => {
                                button.addEventListener('click', () => {
                                    const routeNumber = button.getAttribute('data-route');
                                    configureRoute(routeNumber);
                                });
                            });

                            function refreshMetrics() {
                                fetch("get_metrics.php")
                                    .then(response => response.json())
                                    .then(data => {
                                        console.log(data); // Vérifie la sortie JSON dans la console

                                        let colorClass = data.status_color || "gray"; // Si pas de couleur définie, mettre en gris
                                        let latency = data.latency !== null ? data.latency + " ms" : "N/A";
                                        let bandwidth = data.bandwidth_usage !== null ? data.bandwidth_usage + " Mbps" : "N/A";
                                        let connection = data.connection_status || "Offline"; // Mettre "Offline" si pas de connexion
                                        let html = `
                        <tr class="${colorClass}">
                            <td>${data.robot_id || "N/A"}</td>
                            <td>${data.latency !== null ? data.latency + " ms" : "N/A"}</td>
                            <td>${data.bandwidth_usage !== null ? data.bandwidth_usage + " Mbps" : "N/A"}</td>
                            <td>${data.connection_status || "N/A"}</td>
                        </tr>
                    `;
                                        document.getElementById("metrics-body").innerHTML = html;
                                    })
                                    .catch(error => {
                                        console.error("Error fetching metrics:", error);
                                        document.getElementById("metrics-body").innerHTML = `<tr><td colspan="4">Error loading metrics.</td></tr>`;
                                    });
                            }


                            // Rafraîchir toutes les 10 secondes
                            setInterval(refreshMetrics, 10000);

                            // Charger les métriques au démarrage
                            refreshMetrics();

                            document.querySelectorAll('.server-btn').forEach(button => {
                                button.addEventListener('click', function() {
                                    const routeNumber = this.getAttribute('data-route');
                                    configureRoute(routeNumber);
                                });
                            });
                        </script>



                        </script>

                    <?php elseif ($currentPage === 'operations'): ?>
                        <!-- Operations Management -->
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

                            // Rafraîchir toutes les 10 secondes
                            setInterval(refreshMetrics, 10000);

                            // Charger les métriques au démarrage
                            refreshMetrics();
                        </script>
                    <?php endif; ?>

                </div>
                <!-- End of Container -->
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
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script>
        document.getElementById('role').addEventListener('change', function() {
            const specialtyField = document.getElementById('specialty');
            if (this.value === 'doctor') {
                specialtyField.disabled = false;
            } else {
                specialtyField.value = ''; // Réinitialiser la spécialité
                specialtyField.disabled = true;
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Convert PHP operations array to JavaScript
            const operations = <?php echo json_encode($operations); ?>;

            // Prepare events for FullCalendar
            const events = operations.map(op => {
                const today = new Date();
                const operationDate = new Date(op.scheduled_date);
                const daysDifference = Math.floor((operationDate - today) / (1000 * 60 * 60 * 24));

                return {
                    id: op.id,
                    title: `${op.operation_type} - ${op.patient_name}`,
                    start: op.scheduled_date,
                    backgroundColor: daysDifference <= 3 ? 'red' : 'green', // Close operations in red, others in green
                    textColor: 'white',
                    extendedProps: {
                        doctorName: op.doctor_name,
                        patientName: op.patient_name,
                        operationType: op.operation_type,
                        scheduledDate: op.scheduled_date
                    }
                };
            });

            // Render FullCalendar
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
                    const {
                        doctorName,
                        patientName,
                        operationType,
                        scheduledDate
                    } = info.event.extendedProps;
                    alert(`Operation Details:
Doctor: ${doctorName}
Patient: ${patientName}
Type: ${operationType}
Scheduled Date: ${scheduledDate}`);
                }
            });

            calendar.render();
        });
    </script>


</body>

</html>