<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MedicalApp - Home</title>

    <!-- Custom fonts and styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"> <!-- AOS animations -->

    <style>
        body {
            background-color: #f8f9fc;
        }

        .jumbotron {
            background: linear-gradient(to right, #4e73df, #1cc88a);
            color: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-orange {
            background-color: #f6c23e;
            color: white;
            transition: transform 0.3s, background-color 0.3s;
        }

        .btn-orange:hover {
            background-color: #dda20a;
            transform: scale(1.1);
        }

        .icon-container {
            transition: transform 0.3s;
        }

        .icon-container:hover {
            transform: translateY(-10px);
        }

        .about-section img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .testimonial-section {
            padding: 50px 0;
            background-color: #f4f4f4;
        }

        .testimonial-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .testimonial-card img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .testimonial-card h5 {
            color: #4e73df;
        }

        .testimonial-card p {
            font-size: 0.9em;
            color: #666;
        }

        .footer {
            background-color: #224abe;
            color: white;
            padding: 20px 0;
        }

        .footer a {
            color: #f6c23e;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body id="page-top">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <div class="container-fluid">
                    <h1 class="h3 mb-0 text-gray-800">
                        <a href="index.php" style="text-decoration: none; color: #4e73df;">MedicalApp</a>
                    </h1>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow mx-2">
                            <a class="nav-link dropdown-toggle" href="#" id="contactDropdown" role="button" data-toggle="modal" data-target="#contactModal">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Contact</span>
                                <i class="fas fa-envelope fa-fw"></i>
                            </a>
                        </li>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="login.html" id="userDropdown" role="button">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Login</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="container">
                <div class="jumbotron text-center" data-aos="fade-down">
                    <h1 class="display-4">Welcome to MedicalApp</h1>
                    <p class="lead">An innovative app to connect patients, doctors, and medical robots.</p>
                    <hr class="my-4">
                    <p>Explore how we are revolutionizing healthcare with modern technology.</p>
                    <a class="btn btn-orange btn-lg" href="login.html" role="button">Get Started</a>
                </div>
            </div>

            <!-- About Section -->
            <div class="container my-5">
                <h2 class="text-center mb-4" data-aos="fade-up">About MedicalApp</h2>
                <div class="row">
                    <div class="col-lg-4 text-center icon-container" data-aos="zoom-in">
                        <i class="fas fa-user-md fa-3x mb-3 text-primary"></i>
                        <h5>Doctors</h5>
                        <p>Streamlined access to patient data and care management.</p>
                    </div>
                    <div class="col-lg-4 text-center icon-container" data-aos="zoom-in" data-aos-delay="100">
                        <i class="fas fa-heartbeat fa-3x mb-3 text-danger"></i>
                        <h5>Patients</h5>
                        <p>Personalized follow-ups for better well-being.</p>
                    </div>
                    <div class="col-lg-4 text-center icon-container" data-aos="zoom-in" data-aos-delay="200">
                        <i class="fas fa-robot fa-3x mb-3 text-info"></i>
                        <h5>Medical Robots</h5>
                        <p>Advanced technology to simplify interventions.</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial Section -->
            <div class="testimonial-section">
                <div class="container">
                    <h2 class="text-center mb-5" data-aos="fade-up">What Our Users Say</h2>
                    <div class="row">
                        <div class="col-md-4" data-aos="flip-left">
                            <div class="testimonial-card">
                                <img src="img/undraw_profile_1.svg" alt="User 1">
                                <h5>Dr. John Doe</h5>
                                <p>MedicalApp has transformed how I work. It's amazing!</p>
                            </div>
                        </div>
                        <div class="col-md-4" data-aos="flip-left" data-aos-delay="100">
                            <div class="testimonial-card">
                                <img src="img/undraw_profile_2.svg" alt="User 2">
                                <h5>Jane Doe</h5>
                                <p>With MedicalApp, I feel safe and well-supported.</p>
                            </div>
                        </div>
                        <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
                            <div class="testimonial-card">
                                <img src="img/undraw_profile_3.svg" alt="User 3">
                                <h5>Robot RX-300</h5>
                                <p>MedicalApp maximizes my abilities to help doctors better.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer text-center">
            <div class="container">
                <span>&copy; MedicalApp 2025. All Rights Reserved. | <a href="#">Privacy Policy</a></span>
            </div>
        </footer>

    </div>

    <!-- Modal Contact -->
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contact the Administrator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="send_message.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
    </script>

</body>

</html>
