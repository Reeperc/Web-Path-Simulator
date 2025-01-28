<?php
require_once 'vendor/autoload.php';

use \GameQ\GameQ;

$gq = new GameQ();
// Adding multiple servers
$gq->addServers([
    [
        'type' => 'quake3',
        'host' => '195.221.50.25:27960', // Rouen
    ],
    [
        'type' => 'quake3',
        'host' => '195.221.40.129:27960', // Paris - replace 'ip-paris:port' with actual IP and port
    ],
    [
        'type' => 'quake3',
        'host' => '195.221.20.27:27965', // Montcuq - replace 'ip-montcuq:port' with actual IP and port
    ],
    [
        'type' => 'quake3',
        'host' => '195.221.30.65:27960', // Monaco - replace 'ip-monaco:port' with actual IP and port
    ]
]);

$results = $gq->process();
echo json_encode($results);
?>
