<?php
// Database Configuration
$host = 'localhost';
$dbname = 'webapp';
$user = 'webuser';
$password = 'BARA@@.bara2020';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hash the password
    $plainPassword = 'admin123'; // Replace with desired admin password
    $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

    // Insert admin into the database
    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, role) VALUES (:name, :email, :password, :role)");
    $stmt->execute([
        ':name' => 'Default Admin',
        ':email' => 'admindef@med-app.org',
        ':password' => $hashedPassword,
        ':role' => 'admin'
    ]);

    echo "Admin added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
