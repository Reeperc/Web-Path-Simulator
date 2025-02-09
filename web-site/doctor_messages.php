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
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Process actions: Delete, Reply, or Compose a new message
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_message'])) {
            $message_id = $_POST['message_id'];
            $stmt = $pdo->prepare("DELETE FROM Messages WHERE id = ?");
            $stmt->execute([$message_id]);
        } elseif (isset($_POST['reply_message'])) {
            $receiver_id = $_POST['receiver_id'];
            $reply_message = trim($_POST['reply_message']);

            if (empty($reply_message)) {
                echo "<script>alert('Erreur : Le message est vide. Veuillez remplir le champ avant de soumettre.');</script>";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO Messages (sender_id, sender_email, receiver_id, receiver_role, message, is_replied, created_at)
                    VALUES (?, ?, ?, 'admin', ?, 0, NOW())
                ");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['email'], $receiver_id, $reply_message]);
                echo "<script>alert('Message envoyé avec succès !');</script>";
            }
        } elseif (isset($_POST['send_new_message'])) {
            $receiver_role = $_POST['receiver_role'];
            $receiver_email = $_POST['receiver_email'];
            $new_message = trim($_POST['new_message']);

            if (empty($new_message)) {
                echo "<script>alert('Erreur : Le message est vide. Veuillez remplir le champ avant de soumettre.');</script>";
            } else {
                $stmt = $pdo->prepare("SELECT id FROM Users WHERE email = ? AND role = ?");
                $stmt->execute([$receiver_email, $receiver_role]);
                $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($receiver) {
                    $receiver_id = $receiver['id'];
                    $stmt = $pdo->prepare("
                        INSERT INTO Messages (sender_id, sender_email, receiver_id, receiver_role, message, is_replied, created_at)
                        VALUES (?, ?, ?, ?, ?, 0, NOW())
                    ");
                    $stmt->execute([$_SESSION['user_id'], $_SESSION['email'], $receiver_id, $receiver_role, $new_message]);
                    echo "<script>alert('Message envoyé avec succès !');</script>";
                } else {
                    echo "<script>alert('Erreur : Destinataire introuvable.');</script>";
                }
            }
        } elseif (isset($_POST['delete_sent_message'])) {
            $message_id = $_POST['message_id'];
            $stmt = $pdo->prepare("DELETE FROM Messages WHERE id = ? AND sender_id = ?");
            $stmt->execute([$message_id, $_SESSION['user_id']]);
            echo "<script>alert('Message supprimé avec succès !');</script>";
        }
    }

    // Fetch all received messages for the logged-in doctor
    $stmt = $pdo->prepare("
        SELECT m.id, m.sender_id, m.message, m.created_at, u.email AS sender_email
        FROM Messages m
        JOIN Users u ON m.sender_id = u.id
        WHERE m.receiver_id = ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $received_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all sent messages by the logged-in doctor
    $stmt = $pdo->prepare("
        SELECT m.id, m.receiver_id, m.message, m.created_at, u.email AS receiver_email, u.role AS receiver_role
        FROM Messages m
        JOIN Users u ON m.receiver_id = u.id
        WHERE m.sender_id = ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $sent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch users (admins and doctors)
    $users = $pdo->query("SELECT email, role FROM Users WHERE role IN ('admin', 'doctor')")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Include the Doctor header
include 'headerDoctor.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Messages</h1>

    <!-- Compose New Message -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Envoyer un nouveau message</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="receiver_role">Rôle du destinataire</label>
                    <select class="form-control" id="receiver_role" name="receiver_role" required>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="receiver_email">Email du destinataire</label>
                    <select class="form-control" id="receiver_email" name="receiver_email" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?> (<?= htmlspecialchars($user['role']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_message">Message</label>
                    <textarea class="form-control" id="new_message" name="new_message" rows="4" required></textarea>
                </div>
                <button type="submit" name="send_new_message" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>

    <!-- Sent Messages -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Messages envoyés</h6>
        </div>
        <div class="card-body">
            <?php if (count($sent_messages) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>À</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sent_messages as $message): ?>
                            <tr>
                                <td><?= htmlspecialchars($message['receiver_email']) ?> (<?= htmlspecialchars($message['receiver_role']) ?>)</td>
                                <td><?= htmlspecialchars($message['message']) ?></td>
                                <td><?= $message['created_at'] ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                        <button type="submit" name="delete_sent_message" class="btn btn-danger btn-sm">Supprimer</button>
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

    <!-- Received Messages -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Messages reçus</h6>
        </div>
        <div class="card-body">
            <?php if (count($received_messages) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>De</th>
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
</div>

<?php
// Include the Doctor footer
include 'footerDoctor.php';
?>
