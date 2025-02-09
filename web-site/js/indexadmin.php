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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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

            <!-- Configure Network Routes -->
            <li class="nav-item <?= $currentPage === 'network-routes' ? 'active' : '' ?>">
                <a class="nav-link" href="indexadmin.php?page=network-routes">
                    <i class="fas fa-network-wired"></i>
                    <span>Configure Network Routes</span>
                </a>
            </li>

            <!-- View Network Performance -->
            <li class="nav-item <?= $currentPage === 'performance-monitoring' ? 'active' : '' ?>">
                <a class="nav-link" href="indexadmin.php?page=performance-monitoring">
                    <i class="fas fa-chart-line"></i>
                    <span>Monitor Network Performance</span>
                </a>
            </li>

            <!-- Notifications -->
            <li class="nav-item <?= $currentPage === 'notifications' ? 'active' : '' ?>">
                <a class="nav-link" href="indexadmin.php?page=notifications">
                    <i class="fas fa-bell"></i>
                    <span>Manage Notifications</span>
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
                                <!-- Counter - Messages -->
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
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <p>Welcome to the admin dashboard.</p>
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
                                            <button type="submit" class="btn btn-primary">Register</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($currentPage === 'network-routes'): ?>
                        <!-- Configure Network Routes Content -->
                        <h1 class="h3 mb-0 text-gray-800">Configure Network Routes</h1>
                        <p>Here you can configure network routing rules and settings.</p>
                    <?php elseif ($currentPage === 'performance-monitoring'): ?>
                        <!-- Monitor Network Performance Content -->
                        <h1 class="h3 mb-0 text-gray-800">Monitor Network Performance</h1>
                        <p>Monitor real-time network latency, jitter, and bandwidth usage.</p>
                    <?php elseif ($currentPage === 'notifications'): ?>
                        <!-- Notifications Management -->
                        <h1 class="h3 mb-0 text-gray-800">Manage Notifications</h1>
                        <p>Send notifications to doctors and manage notification settings.</p>
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

</body>

</html>
