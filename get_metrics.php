<?php

header('Content-Type: application/json');
 
// Fichier où stocker l'historique des latences

$history_file = "latency_history.json";
 
// Lire l'historique existant

$history = file_exists($history_file) ? json_decode(file_get_contents($history_file), true) : [];
 
// Exécuter le script Python pour récupérer la latence actuelle

$command = "python3 /var/www/html/get_network_metrics.py"; // Vérifie le chemin correct

$output = shell_exec($command);

$data = json_decode($output, true);
 
// Vérifier si la latence est disponible

if (isset($data["latency"])) {

    $latency = $data["latency"];

    $timestamp = time(); // Ajoute un timestamp
 
    // Ajouter la nouvelle valeur à l'historique

    $history[] = ["time" => $timestamp, "latency" => $latency];
 
    // Garder uniquement les 10 dernières valeurs

    if (count($history) > 10) {

        array_shift($history);

    }
 
    // Sauvegarder l'historique mis à jour

    file_put_contents($history_file, json_encode($history));

} else {

    $latency = null;

}
 
// Vérifier si connection_status est présent

if (!isset($data["connection_status"])) {

    $data["connection_status"] = "Unknown"; // Valeur par défaut si absent

}
 
// Fusionner les données avant de les renvoyer

$response = [

    "latency" => $latency,

    "history" => $history,

    "connection_status" => $data["connection_status"],

    "robot_id" => $data["robot_id"] ?? "Unknown",

    "bandwidth_usage" => $data["bandwidth_usage"] ?? null,

    "status_color" => $data["status_color"] ?? "gray"

];
 
// Retourner un JSON propre

echo json_encode($response);

?>

 