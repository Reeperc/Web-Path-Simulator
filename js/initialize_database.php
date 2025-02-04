<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'webapp'; // Nom de votre base de données
$user = 'webuser'; // Utilisateur MySQL
$password = 'BARA@@.bara2020'; // Mot de passe MySQL

try {
    // Connexion au serveur MySQL
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Base de données '$dbname' vérifiée ou créée avec succès !<br>";

    // Connexion à la base de données
    $pdo->exec("USE $dbname");

    // Création des tables si elles n'existent pas
    $queries = [
        "CREATE TABLE IF NOT EXISTS Users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'doctor', 'patient') DEFAULT 'patient',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS PerformanceReports (
            report_id INT PRIMARY KEY AUTO_INCREMENT,
            generated_by INT NOT NULL,
            report_title VARCHAR(100) NOT NULL,
            report_content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (generated_by) REFERENCES Users(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS Doctors (
            doctor_id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            specialization VARCHAR(50),
            last_login DATETIME
        )",
        "CREATE TABLE IF NOT EXISTS Patients (
            patient_id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            heart_rate INT,
            blood_pressure VARCHAR(20),
            status VARCHAR(20)
        )",
        "CREATE TABLE IF NOT EXISTS Robots (
            robot_id INT PRIMARY KEY AUTO_INCREMENT,
            location VARCHAR(100),
            status VARCHAR(20)
        )",
        "CREATE TABLE IF NOT EXISTS NetworkStatus (
            id INT PRIMARY KEY AUTO_INCREMENT,
            robot_id INT NOT NULL,
            latency INT,
            packet_loss FLOAT,
            connection_status VARCHAR(20),
            FOREIGN KEY (robot_id) REFERENCES Robots(robot_id)
        )",
        "CREATE TABLE IF NOT EXISTS Logs (
            log_id INT PRIMARY KEY AUTO_INCREMENT,
            admin_id INT,
            doctor_id INT,
            timestamp DATETIME,
            action VARCHAR(100),
            details TEXT,
            FOREIGN KEY (admin_id) REFERENCES Users(id),
            FOREIGN KEY (doctor_id) REFERENCES Doctors(doctor_id)
        )",
        "CREATE TABLE IF NOT EXISTS MediaData (
            id INT PRIMARY KEY AUTO_INCREMENT,
            robot_id INT NOT NULL,
            video_feed_url VARCHAR(200),
            snapshot_url VARCHAR(200),
            recorded_video_url VARCHAR(200),
            FOREIGN KEY (robot_id) REFERENCES Robots(robot_id)
        )",
        "CREATE TABLE IF NOT EXISTS AdminPermissions (
            id INT PRIMARY KEY AUTO_INCREMENT,
            admin_id INT NOT NULL,
            permission_name VARCHAR(100) NOT NULL,
            description TEXT,
            FOREIGN KEY (admin_id) REFERENCES Users(id)
        )",
        "CREATE TABLE IF NOT EXISTS Messages (
         id INT PRIMARY KEY AUTO_INCREMENT,
         sender_email VARCHAR(100) NOT NULL,
         message TEXT NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         is_replied BOOLEAN DEFAULT FALSE
        )",
        "CREATE TABLE IF NOT EXISTS NetworkRoutes (
            route_id INT PRIMARY KEY AUTO_INCREMENT,
            route_name VARCHAR(100) NOT NULL,
            source_ip VARCHAR(50) NOT NULL,
            destination_ip VARCHAR(50) NOT NULL,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES Users(id)
        )";        
        "CREATE TABLE IF NOT EXISTS Notifications (
            notification_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            title VARCHAR(100),
            message TEXT,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(id)
        )"
    ];

    foreach ($queries as $query) {
        $pdo->exec($query);
    }

    echo "Tables créées avec succès ou déjà existantes !<br>";

    // Ajouter des exemples de données avec mots de passe hachés
    $hashedPasswordAdmin = password_hash('admin123', PASSWORD_BCRYPT);
    $hashedPasswordDoctor = password_hash('doctor123', PASSWORD_BCRYPT);
    $hashedPasswordPatient = password_hash('patient123', PASSWORD_BCRYPT);

    // Ajouter un administrateur par défaut
    $checkAdmin = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'admin'")->fetchColumn();
    if ($checkAdmin == 0) {
        $pdo->exec("INSERT INTO Users (email, password, role) VALUES ('admin@example.com', '$hashedPasswordAdmin', 'admin')");
        echo "Administrateur par défaut ajouté : admin@example.com / admin123<br>";
    }

    // Ajouter un docteur par défaut
    $checkDoctor = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'doctor'")->fetchColumn();
    if ($checkDoctor == 0) {
        $pdo->exec("INSERT INTO Users (email, password, role) VALUES ('doctor@example.com', '$hashedPasswordDoctor', 'doctor')");
        $pdo->exec("INSERT INTO Doctors (name, email, specialization) VALUES ('Dr. John Doe', 'doctor@example.com', 'Cardiology')");
        echo "Docteur par défaut ajouté : doctor@example.com / doctor123<br>";
    }

    // Ajouter un patient par défaut
    $checkPatient = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'patient'")->fetchColumn();
    if ($checkPatient == 0) {
        $pdo->exec("INSERT INTO Users (email, password, role) VALUES ('patient@example.com', '$hashedPasswordPatient', 'patient')");
        $pdo->exec("INSERT INTO Patients (name, heart_rate, blood_pressure, status) VALUES ('Jane Doe', 75, '120/80', 'Stable')");
        echo "Patient par défaut ajouté : patient@example.com / patient123<br>";
    }

} catch (PDOException $e) {
    echo "Erreur lors de la connexion ou de la création des tables : " . $e->getMessage();
}
?>
