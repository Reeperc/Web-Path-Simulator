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

    // Vérification des données soumises
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Requête pour vérifier l'email
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérification du mot de passe
            if (password_verify($password, $user['password'])) {
                // Démarrer une session et stocker les informations de l'utilisateur
                session_start();
                $_SESSION['user_id'] = $user['id']; // Stocker l'ID de l'utilisateur
                $_SESSION['user_role'] = $user['role']; // Stocker le rôle de l'utilisateur
                $_SESSION['is_logged_in'] = true; // Confirmer que l'utilisateur est connecté

                // Redirection en fonction du rôle
                if ($user['role'] === 'admin') {
                    header("Location: indexadmin.php"); // Page admin
                } elseif ($user['role'] === 'doctor') {
                    header("Location: dc-interface.php"); // Page docteur
                } else {
                    // Si l'utilisateur a un autre rôle (par exemple 'patient'), affichez un message d'erreur ou redirigez
                    echo "<script>alert('Votre rôle ne permet pas d’accéder à ce système.'); window.location.href='login.html';</script>";
                }
                exit();
            } else {
                // Mot de passe incorrect
                echo "<script>alert('Mot de passe incorrect.'); window.location.href='login.html';</script>";
            }
        } else {
            // Email non trouvé
            echo "<script>alert('Utilisateur introuvable avec cet email.'); window.location.href='login.html';</script>";
        }
    }
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>
