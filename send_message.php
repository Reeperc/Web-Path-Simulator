<?php
// Database configuration
$host = 'localhost';
$dbname = 'webapp';
$user = 'webuser';
$password = 'BARA@@.bara2020';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if data is sent via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sender_email = trim($_POST['email']); // Clean input
        $message = trim($_POST['message']);

        // Validate fields
        if (empty($sender_email) || empty($message)) {
            echo "<script>
                alert('Please fill in all fields.');
                window.location.href = 'index.php';
            </script>";
            exit();
        }

        // Validate email
        if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>
                alert('Invalid email address.');
                window.location.href = 'index.php';
            </script>";
            exit();
        }

        // Fetch receiver_id (Admin ID as default receiver)
        $stmt = $pdo->prepare("SELECT id FROM Users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            throw new Exception("No admin found in the database.");
        }

        $receiver_id = $admin['id'];

        // Define sender_id for guests (use 0 for guest ID)
        $sender_id = 0; // ID for "Guest" in the Users table

        // Insert into the Messages table
        $stmt = $pdo->prepare("INSERT INTO Messages (sender_id, sender_email, receiver_id, message) VALUES (:sender_id, :sender_email, :receiver_id, :message)");
        $stmt->bindParam(':sender_id', $sender_id);
        $stmt->bindParam(':sender_email', $sender_email);
        $stmt->bindParam(':receiver_id', $receiver_id);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            echo "<script>
                alert('Message sent successfully!');
                window.location.href = 'index.php';
            </script>";
        } else {
            echo "<script>
                alert('An error occurred while sending the message.');
                window.location.href = 'index.php';
            </script>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
