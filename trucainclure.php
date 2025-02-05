<?php
// --------------------------------------------------
// GESTION DU BACKEND QUAND on clique sur "Refresh" :
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'refresh') {

    // 1) Exécution des commandes ping
    // Note : '-c 2' envoie 2 requêtes ping
    $pingResult1 = shell_exec('ping -c 2 10.8.3.3');
    $pingResult2 = shell_exec('ping -c 2 10.9.3.3');

    // 2) Récupération de la latence moyenne (avg)
    // Les pings Linux renvoient souvent une ligne du type :
    // "rtt min/avg/max/mdev = 0.023/0.025/0.027/0.001 ms"
    // On peut donc parser l'avg via une expression régulière :
    preg_match('/min\/avg\/max\/mdev = [^\/]+\/([^\/]+)\//', $pingResult1, $matches1);
    $avgLatency1 = $matches1[1] ?? 'N/A';

    preg_match('/min\/avg\/max\/mdev = [^\/]+\/([^\/]+)\//', $pingResult2, $matches2);
    $avgLatency2 = $matches2[1] ?? 'N/A';

    // 3) Lancement iperf en mode client vers les deux IP
    // Note : la commande exacte dépend de vos besoins (TCP, UDP, durée du test, etc.)
    // Ici : "-u" = UDP, "-t 3" = 3 secondes, à adapter selon votre usage.
    $iperfResult1 = shell_exec('iperf -c 10.8.3.3 -u -t 3');
    $iperfResult2 = shell_exec('iperf -c 10.9.3.3 -u -t 3');

    // 4) Parsing simplifié de la bande passante dans la sortie iperf
    // Généralement, on a quelque chose comme :
    // "[  3]  0.0- 3.0 sec   4.00 MBytes  11.2 Mbits/sec"
    // On fait un preg_match sur "[\d\.]+ [KMG]bits/sec"
    preg_match('/([\d\.]+\s?[KMG]bits\/sec)/', $iperfResult1, $bwMatches1);
    $bandwidth1 = $bwMatches1[1] ?? 'N/A';

    preg_match('/([\d\.]+\s?[KMG]bits\/sec)/', $iperfResult2, $bwMatches2);
    $bandwidth2 = $bwMatches2[1] ?? 'N/A';

    // 5) Préparation de la structure de données
    // On renvoie un tableau "robots" avec les infos voulues.
    // Robot ID, Latency, Bandwidth, Connection Status (on suppose Connected si on arrive à pinger)
    $data = [
        'robots' => [
            [
                'id'       => 'Robot A (10.8.3.3)',
                'latency'  => $avgLatency1,
                'bandwidth'=> $bandwidth1,
                'status'   => (strpos($pingResult1, '0% packet loss') !== false) ? 'Connected' : 'Disconnected'
            ],
            [
                'id'       => 'Robot B (10.9.3.3)',
                'latency'  => $avgLatency2,
                'bandwidth'=> $bandwidth2,
                'status'   => (strpos($pingResult2, '0% packet loss') !== false) ? 'Connected' : 'Disconnected'
            ]
        ]
    ];

    // 6) On renvoie la réponse en JSON
    header('Content-Type: application/json');
    echo json_encode($data);
    exit(); // on arrête le script pour ne pas envoyer le HTML après
}
?>
