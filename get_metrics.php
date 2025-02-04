<?php

header('Content-Type: application/json');
 
// Fichier o� stocker l'historique des latences

$history_file = "latency_history.json";
 
// Lire l'historique existant

$history = file_exists($history_file) ? json_decode(file_get_contents($history_file), true) : [];
 
// Ex�cuter le script Python pour r�cup�rer la latence actuelle

$command = "python3 /var/www/html/get_network_metrics.py"; // V�rifie le chemin correct

$output = shell_exec($command);

$data = json_decode($output, true);
 
// V�rifier si la latence est disponible

if (isset($data["latency"])) {

    $latency = $data["latency"];

    $timestamp = time(); // Ajoute un timestamp
 
    // Ajouter la nouvelle valeur � l'historique

    $history[] = ["time" => $timestamp, "latency" => $latency];
 
    // Garder uniquement les 10 derni�res valeurs

    if (count($history) > 10) {

        array_shift($history);

    }
 
    // Sauvegarder l'historique mis � jour

    file_put_contents($history_file, json_encode($history));

} else {

    $latency = null;

}
 
// V�rifier si connection_status est pr�sent

if (!isset($data["connection_status"])) {

    $data["connection_status"] = "Unknown"; // Valeur par d�faut si absent

}
 
// Fusionner les donn�es avant de les renvoyer

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

 