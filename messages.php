<?php
// Démarrer la session
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
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

    // Gérer les actions POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_message'])) {
            $message_id = $_POST['message_id'];
            $stmt = $pdo->prepare("DELETE FROM Messages WHERE id = ?");
            $stmt->execute([$message_id]);
        } elseif (isset($_POST['send_message'])) {
            $receiver_email = $_POST['receiver_email'];
            $message_content = $_POST['message_content'];

            $admin_id = $_SESSION['user_id'];
            $admin_email = $_SESSION['email'];

            $stmt = $pdo->prepare("SELECT id, role FROM Users WHERE email = ?");
            $stmt->execute([$receiver_email]);
            $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($receiver) {
                $receiver_id = $receiver['id'];
                $receiver_role = $receiver['role'];

                $stmt = $pdo->prepare("
                    INSERT INTO Messages (sender_id, sender_email, receiver_id, receiver_role, message, created_at, is_replied)
                    VALUES (?, ?, ?, ?, ?, NOW(), 0)
                ");
                $stmt->execute([$admin_id, $admin_email, $receiver_id, $receiver_role, $message_content]);
            } else {
                echo "<script>alert('Erreur : Destinataire introuvable.');</script>";
            }
        }
    }

    // Récupérer uniquement les messages reçus
    $stmt = $pdo->prepare("
        SELECT m.id, m.sender_email, m.message, m.created_at
        FROM Messages m
        WHERE m.receiver_id = ? AND m.sender_id != ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $received_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les messages envoyés
    $stmt = $pdo->prepare("
        SELECT m.id, u.email AS receiver_email, m.message, m.created_at
        FROM Messages m
        JOIN Users u ON m.receiver_id = u.id
        WHERE m.sender_id = ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $sent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $users = $pdo->query("SELECT email, role FROM Users WHERE role IN ('doctor', 'admin')")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}

// Inclure le fichier header.php pour le menu et la barre d'accueil
include 'header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Messages</h1>

    <!-- Formulaire pour envoyer un message -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Envoyer un message</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="receiver_email">Destinataire</label>
                    <select class="form-control" id="receiver_email" name="receiver_email" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?> (<?= htmlspecialchars($user['role']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message_content">Message</label>
                    <textarea class="form-control" id="message_content" name="message_content" rows="4" required></textarea>
                </div>
                <button type="submit" name="send_message" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>

    <!-- Messages reçus -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Messages reçus</h6>
        </div>
        <div class="card-body">
            <?php if (count($received_messages) > 0): ?>
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
                        <?php foreach ($received_messages as $message): ?>
                            <tr>
                                <td><?= htmlspecialchars($message['sender_email']) ?></td>
                                <td><?= htmlspecialchars($message['message']) ?></td>
                                <td><?= $message['created_at'] ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                        <button type="submit" name="delete_message" class="btn btn-danger btn-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun message trouvé.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Messages envoyés -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Messages envoyés</h6>
        </div>
        <div class="card-body">
            <?php if (count($sent_messages) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Destinataire</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sent_messages as $message): ?>
                            <tr>
                                <td><?= htmlspecialchars($message['receiver_email']) ?></td>
                                <td><?= htmlspecialchars($message['message']) ?></td>
                                <td><?= $message['created_at'] ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                        <button type="submit" name="delete_message" class="btn btn-danger btn-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun message trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Inclure le fichier footer.php
include 'footer.php';
?>
