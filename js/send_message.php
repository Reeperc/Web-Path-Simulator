<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'webapp';
$user = 'webuser';
$password = 'BARA@@.bara2020';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si les données ont été envoyées via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sender_email = $_POST['email']; // Changer 'email' en 'sender_email' si nécessaire dans le formulaire HTML
        $message = $_POST['message'];

        // Insertion dans la table Messages
        $stmt = $pdo->prepare("INSERT INTO Messages (sender_email, message) VALUES (:sender_email, :message)");
        $stmt->bindParam(':sender_email', $sender_email);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            // En cas de succès, afficher une boîte de notification et rester sur la même page
            echo "<script>
                alert('Message envoyé avec succès !');
                window.location.href = 'index.php'; // Rediriger vers la même page (index.php)
            </script>";
        } else {
            // En cas d'erreur, afficher une notification d'échec
            echo "<script>
                alert('Une erreur s\\'est produite lors de l\\'envoi du message.');
                window.location.href = 'index.php'; // Rester sur la même page
            </script>";
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
