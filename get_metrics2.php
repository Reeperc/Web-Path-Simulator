<?php
header('Content-Type: application/json');

$servers = ['UK', 'WestEurope', 'Paris', 'Korea', 'US'];
$comparison_data = ["servers" => [], "timestamps" => []];


// Exécuter le script Python pour récupérer la latence actuelle
$command = "python3 /var/www/html/get_network_metrics.py"; // Modifier avec le bon chemin
$output = shell_exec($command);
$data = json_decode($output, true);

// Vérifier si la latence est disponible
if (isset($data["latency"])) {
    $latency = $data["latency"];
    $timestamp = time(); // Ajoute un timestamp

    $comparison_data["servers"][$server][] = $latency;

     if (!in_array(date("H:i:s", $timestamp), $comparison_data["timestamps"])) {
            $comparison_data["timestamps"][] = date("H:i:s", $timestamp);
    
     }
} else {
    $latency = null;
    $comparison_data["servers"][$server][] = null; // Valeur par défaut si erreur

}

if (!isset($data["connection_status"])) {
    $data["connection_status"] = "Unknown"; // Valeur par défaut si absent
}
// Retourner les données au format JSON
echo json_encode(["latency" => $latency, "history" => $history]);
echo json_encode($data);
?>
