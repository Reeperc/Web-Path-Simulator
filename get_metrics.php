<?php
header('Content-Type: application/json');

$command = "python3 /var/www/html/get_network_metrics.py"; // Modifier avec le bon chemin
$output = shell_exec($command);
echo $output;
