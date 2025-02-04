<?php
header('Content-Type: application/json');

// Liste des serveurs disponibles
$all_servers = ['UK', 'WestEurope', 'Paris', 'Korea', 'US'];

// Mode comparaison : si le paramètre compare=true est passé dans l'URL
$compare_mode = isset($_GET['compare']) && $_GET['compare'] === 'true';

if ($compare_mode) {
    $comparison_data = [];

    foreach ($all_servers as $server) {
        $history_file = "latency_history_{$server}.json";
        $history = file_exists($history_file) ? json_decode(file_get_contents($history_file), true) : [];

        // Si l'historique est vide, on initialise avec une entrée nulle
        if (empty($history)) {
            $history = [["time" => time(), "latency" => null]];
        }

        $last_entry = end($history);
        $comparison_data[$server] = [
            "latest_latency" => isset($last_entry["latency"]) ? $last_entry["latency"] : null,
            "latest_time"    => isset($last_entry["time"]) ? date("H:i:s", $last_entry["time"]) : "N/A",
            "history"        => $history
        ];
    }

    echo json_encode(["comparison" => $comparison_data]);
    exit(); // On s'arrête ici en mode comparaison
}

// Mode par défaut (pour un seul serveur)
$server = isset($_GET['server']) ? $_GET['server'] : 'UK';
$history_file = "latency_history_{$server}.json";

// Lire l'historique existant pour ce serveur
$history = file_exists($history_file) ? json_decode(file_get_contents($history_file), true) : [];

// Exécuter le script Python pour récupérer la latence actuelle
$command = "python3 /var/www/html/get_network_metrics.py"; // Assurez-vous que le chemin est correct
$output = shell_exec($command);
$data = json_decode($output, true);

// Si la latence est disponible, mettre à jour l'historique
if (isset($data["latency"])) {
    $latency = $data["latency"];
    $timestamp = time(); // Timestamp UNIX

    $history[] = ["time" => $timestamp, "latency" => $latency];

    // Garder uniquement les 10 dernières valeurs
    if (count($history) > 10) {
        array_shift($history);
    }

    // Si l'historique est vide (au cas improbable), on l'initialise
    if (empty($history)) {
        $history = [["time" => time(), "latency" => null]];
    }    

    // Sauvegarder l'historique mis à jour
    file_put_contents($history_file, json_encode($history));
} else {
    $latency = null;
}

if (!isset($data["connection_status"])) {
    $data["connection_status"] = "Unknown"; // Valeur par défaut si absente
}

// Préparer les données pour le graphique : formatage des timestamps et des valeurs
$timestamps = [];
$latencyValues = [];
foreach ($history as $entry) {
    $timestamps[] = date("H:i:s", $entry["time"]); // Format lisible
    $latencyValues[] = $entry["latency"];
}

// Fusionner les données du script Python et l'historique
$response = array_merge($data, [
    "latency"      => $latency,
    "history"      => $history,
    "timestamps"   => $timestamps,
    "latencyValues"=> $latencyValues
]);

echo json_encode($response);
?>
