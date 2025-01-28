<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle docteur
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['user_role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Inclure le fichier headerDoctor
include 'headerDoctor.php';

// Obtenir l'ID du docteur connecté
$doctor_id = $_SESSION['user_id'];

// Connexion à la base de données
require_once 'initialize_database.php';

// Récupérer les opérations pour le docteur connecté
$stmt = $pdo->prepare("SELECT o.id, o.operation_type, o.scheduled_date, p.name AS patient_name, o.comment 
                       FROM Operations o
                       JOIN Patients p ON o.patient_id = p.patient_id
                       WHERE o.doctor_id = ?
                       ORDER BY o.scheduled_date ASC");
$stmt->execute([$doctor_id]);
$operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <!-- Calendrier -->
    <div id="calendar"></div>
</div>

<?php
// Inclure le fichier footerDoctor
include 'footerDoctor.php';
?>

<!-- Ajouter les styles et scripts FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const operations = <?php echo json_encode($operations); ?>;

        // Préparer les événements pour le calendrier
        const events = operations.map(op => {
            const today = new Date();
            const operationDate = new Date(op.scheduled_date);
            const daysDifference = (operationDate - today) / (1000 * 60 * 60 * 24);

            return {
                id: op.id,
                title: `${op.operation_type} - ${op.patient_name}`,
                start: op.scheduled_date,
                backgroundColor: daysDifference <= 3 ? 'red' : 'green', // Rouge si proche, vert sinon
                textColor: 'white',
                extendedProps: {
                    patientName: op.patient_name,
                    operationType: op.operation_type,
                    scheduledDate: op.scheduled_date,
                    comment: op.comment
                }
            };
        });

        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: events,
            eventClick: function (info) {
                const { patientName, operationType, scheduledDate, comment } = info.event.extendedProps;
                alert(`Operation Details:
Patient: ${patientName}
Type: ${operationType}
Date: ${scheduledDate}
Comment: ${comment || 'No comments available'}`);
            }
        });

        calendar.render();
    });
</script>
