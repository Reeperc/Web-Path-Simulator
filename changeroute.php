<?php
session_start();
// Vérifier le rôle admin, etc.
if (
    !isset($_SESSION['is_logged_in'])
    || $_SESSION['is_logged_in'] !== true
    || $_SESSION['user_role'] !== 'admin'
) {
    echo "Accès refusé";
    exit();
}

// On récupère la route (1, 2 ou 3)
$route = isset($_GET['route']) ? $_GET['route'] : '';

// Selon la route choisie, on construit la commande iptables
switch ($route) {
    case '1': //uk
        //-d = ip du robot ;  --to-dest : ip du serveur -ici uk-
        $command = 'sudo iptables -t nat -F && sudo iptables -t nat -A OUTPUT -d 10.8.0.3 -p icmp -j DNAT --to-destination 10.8.0.5';
        break;
    case '2': // we-----------------------
        $command = 'sudo iptables -t nat -F && sudo iptables -t nat -A OUTPUT -d 10.8.0.3 -p icmp -j DNAT --to-destination 10.8.0.4';
        break;
    case '3': //paris(france)----------------
        $command = 'sudo iptables -t nat -F && sudo iptables -t nat -A OUTPUT -d 10.8.0.3 -p icmp -j DNAT --to-destination 10.8.0.5';
        break;
    case '4': //korea
        $command = 'sudo iptables -t nat -F && sudo iptables -t nat -A OUTPUT -d 10.8.0.3 -p icmp -j DNAT --to-destination 10.8.0.6';
        break;
    case '5': //us
        $command = 'sudo iptables -t nat -F && sudo iptables -t nat -A OUTPUT -d 10.8.0.3 -p icmp -j DNAT --to-destination 10.8.0.7';
        break;
    default:
        echo "Route invalide. Usage : ?route=1|2|3|4|5";
        exit();
}

// On exécute et on récupère le code de retour
// `2>&1` pour capturer les erreurs également
$output = [];
$return_var = 0;
exec($command . ' 2>&1', $output, $return_var);

if ($return_var === 0) {
    echo "Succès : La route $route a été configurée !";
} else {
    echo "Erreur lors de l'exécution de la commande : " . implode("\n", $output);
}
