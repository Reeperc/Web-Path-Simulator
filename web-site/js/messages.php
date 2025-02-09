<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    // Rediriger vers la page de connexion si non connecté ou rôle non admin
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

    // Traitement des actions : répondre ou supprimer
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_message'])) {
            $message_id = $_POST['message_id'];
            $stmt = $pdo->prepare("DELETE FROM Messages WHERE id = ?");
            $stmt->execute([$message_id]);
        } elseif (isset($_POST['reply_message'])) {
            $message_id = $_POST['message_id'];
            $reply_email = $_POST['sender_email'];
            $reply_message = $_POST['reply_message'];

            // Simule l'envoi d'un email (vous pouvez configurer un système d'email ici)
            mail($reply_email, "Réponse à votre message", $reply_message);

            // Marquer comme répondu
            $stmt = $pdo->prepare("UPDATE Messages SET is_replied = 1 WHERE id = ?");
            $stmt->execute([$message_id]);
        }
    }

    // Récupérer tous les messages
    $stmt = $pdo->query("SELECT * FROM Messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="indexadmin.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Admin</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="indexadmin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Messages</h1>

                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (count($messages) > 0): ?>
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Messages reçus</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Expéditeur</th>
                                                    <th>Message</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($messages as $message): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($message['sender_email']) ?></td>
                                                        <td><?= htmlspecialchars($message['message']) ?></td>
                                                        <td><?= $message['created_at'] ?></td>
                                                        <td>
                                                            <form method="POST" style="display:inline;">
                                                                <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                                                <button type="submit" name="delete_message" class="btn btn-danger btn-sm">Supprimer</button>
                                                            </form>
                                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#replyModal<?= $message['id'] ?>">Répondre</button>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal de réponse -->
                                                    <div class="modal fade" id="replyModal<?= $message['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel<?= $message['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="replyModalLabel<?= $message['id'] ?>">Répondre à <?= htmlspecialchars($message['sender_email']) ?></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <form method="POST">
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                                                        <input type="hidden" name="sender_email" value="<?= htmlspecialchars($message['sender_email']) ?>">
                                                                        <div class="form-group">
                                                                            <label for="replyMessage<?= $message['id'] ?>">Message</label>
                                                                            <textarea name="reply_message" id="replyMessage<?= $message['id'] ?>" class="form-control" rows="4" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                                        <button type="submit" name="reply_message" class="btn btn-primary">Envoyer</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p>Aucun message trouvé.</p>
                            <?php endif; ?>
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

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
