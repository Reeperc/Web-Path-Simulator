<?php
// Database Configuration
$host = 'localhost';
$dbname = 'webapp'; // Database name
$user = 'webuser'; // MySQL user
$password = 'BARA@@.bara2020'; // MySQL password

try {

    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");

    // Use the created database
    $pdo->exec("USE $dbname");

    // Queries to create tables
    $queries = [
        "CREATE TABLE IF NOT EXISTS Users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'doctor', 'patient') DEFAULT 'patient',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE Operations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        doctor_id INT NOT NULL,
        operation_type VARCHAR(100) NOT NULL,
        scheduled_date DATETIME NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES Patients(patient_id),
        FOREIGN KEY (doctor_id) REFERENCES Users(id)
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
            status VARCHAR(20),
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
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
        )",
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

    // Execute queries to create tables
    foreach ($queries as $query) {
        $pdo->exec($query);
    }


    // Check and add default admin
    $checkAdmin = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'admin'")->fetchColumn();
    if ($checkAdmin == 0) {
        $hashedPasswordAdmin = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->exec("INSERT INTO Users (name, email, password, role) VALUES ('Admin', 'admin@med-app.org', '$hashedPasswordAdmin', 'admin')");
    }

    // Check and add default doctor
    $checkDoctor = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'doctor'")->fetchColumn();
    if ($checkDoctor == 0) {
        $hashedPasswordDoctor = password_hash('doctor123', PASSWORD_BCRYPT);
        $pdo->exec("INSERT INTO Users (name, email, password, role) VALUES ('Dr. John Doe', 'doctor@med-app.org', '$hashedPasswordDoctor', 'doctor')");
        $pdo->exec("INSERT INTO Doctors (name, email, specialization) VALUES ('Dr. John Doe', 'doctor@med-app.org', 'Cardiology')");
    }

    // Check and add default patients
    $checkPatient = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'patient'")->fetchColumn();
    if ($checkPatient == 0) {
        $hashedPasswordPatient = password_hash('patient123', PASSWORD_BCRYPT);
        $pdo->exec("INSERT INTO Users (name, email, password, role) VALUES ('Jane Doe', 'patient@med-app.org', '$hashedPasswordPatient', 'patient')");
        $pdo->exec("INSERT INTO Patients (name, heart_rate, blood_pressure, status) VALUES 
            ('Jane Doe', 75, '120/80', 'Stable'),
            ('Noa Smith', 85, '130/85', 'Critical'),
            ('Jimmy Brown', 68, '128/90', 'Critical')");
        echo "Default patients added.<br>";
    }

    // Check and add sample logs
    $checkLogs = $pdo->query("SELECT COUNT(*) FROM Logs")->fetchColumn();
    if ($checkLogs == 0) {
        $pdo->exec("INSERT INTO Logs (admin_id, doctor_id, action, details) VALUES 
            (1, NULL, 'Database Setup', 'Created initial database and tables'),
            (NULL, 1, 'Patient Data Updated', 'Updated data for Jane Doe'),
            (1, 1, 'System Maintenance', 'Performed routine system maintenance')
        ");
    }

} catch (PDOException $e) {
}
?>
