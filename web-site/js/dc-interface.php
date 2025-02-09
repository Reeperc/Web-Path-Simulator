<?php
// Start the session
session_start();

// Check if the user is logged in and has the doctor role
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'doctor') {
    // Redirect to login page if not logged in or not a doctor
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>

    <!-- Custom fonts and styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
        }

        .card-header {
            background-color: #1cc88a;
            color: white;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #1cc88a;
            border-color: #1cc88a;
        }

        .btn-primary:hover {
            background-color: #17a673;
            border-color: #17a673;
        }

        .btn-warning,
        .btn-danger {
            transition: transform 0.2s;
        }

        .btn-warning:hover,
        .btn-danger:hover {
            transform: scale(1.1);
        }

        .logs {
            font-size: 0.9rem;
            line-height: 1.6;
            color: #444;
        }

        .logs p {
            margin: 0 0 10px;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="doctor_interface.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Doctor Dashboard</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="doctor_interface.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Patient Data -->
            <li class="nav-item">
                <a class="nav-link" href="#patient-data">
                    <i class="fas fa-fw fa-user-injured"></i>
                    <span>View Patient Data</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Logs -->
            <li class="nav-item">
                <a class="nav-link" href="#logs">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>View Logs</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <!-- User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="modal" data-target="#logoutModal">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Doctor</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Robot Control Panel -->
                        <div class="col-xl-6 col-lg-6" data-aos="fade-up">
                            <div class="card shadow mb-4">
                                <div class="card-header">
                                    Robot Control
                                </div>
                                <div class="card-body">
                                    <p>Latency: <strong>50ms</strong></p>
                                    <p>Packet Loss: <strong>2%</strong></p>
                                    <p>Connection Status: <strong>Stable</strong></p>
                                    <button class="btn btn-primary">Start Procedure</button>
                                    <button class="btn btn-warning">Pause</button>
                                    <button class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Data -->
                        <div class="col-xl-6 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="card shadow mb-4">
                                <div class="card-header">
                                    Patient Data
                                </div>
                                <div class="card-body">
                                    <p>Heart Rate: <strong>75 bpm</strong></p>
                                    <p>Blood Pressure: <strong>120/80</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logs -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12" data-aos="fade-up" data-aos-delay="200">
                            <div class="card shadow mb-4">
                                <div class="card-header">
                                    Logs
                                </div>
                                <div class="card-body logs">
                                    <p>[2025-01-17] Procedure started</p>
                                    <p>[2025-01-17] Robot moved to position X</p>
                                    <p>[2025-01-17] Snapshot taken</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

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

    <!-- JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
