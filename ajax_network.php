<?php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Dossiers/fichiers pour logs et PIDs (à adapter selon votre convenance)
$pingLogA = '/tmp/ping_10.8.3.3.log';
$pingPidA = '/tmp/ping_10.8.3.3.pid';

$pingLogB = '/tmp/ping_10.9.3.3.log';
$pingPidB = '/tmp/ping_10.9.3.3.pid';

$iperfLogA = '/tmp/iperf_10.8.3.3.log';
$iperfPidA = '/tmp/iperf_10.8.3.3.pid';

$iperfLogB = '/tmp/iperf_10.9.3.3.log';
$iperfPidB = '/tmp/iperf_10.9.3.3.pid';

if ($method === 'POST') {
    // On suppose qu'on reçoit du JSON (fetch POST)
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $action = $data['action'] ?? null;

    if ($action === 'start') {
        // -------------------------------------------------------------
        // Lancer ping en background pour 10.8.3.3
        // -i 1 => intervalle 1 seconde
        // & echo $! => récupère le PID du process
        // -------------------------------------------------------------
        // Vider les logs au démarrage (optionnel)
        file_put_contents($pingLogA, '');
        $cmdPingA = "ping -i 1 10.8.3.3 > $pingLogA 2>&1 & echo $!";
        $pidPingA = shell_exec($cmdPingA);
        file_put_contents($pingPidA, $pidPingA);

        // Pareil pour 10.9.3.3
        file_put_contents($pingLogB, '');
        $cmdPingB = "ping -i 1 10.9.3.3 > $pingLogB 2>&1 & echo $!";
        $pidPingB = shell_exec($cmdPingB);
        file_put_contents($pingPidB, $pidPingB);

        // -------------------------------------------------------------
        // Lancer iperf en background, ex: client vers 10.8.3.3
        // -u => mode UDP? ou -t => durée ? à adapter selon vos besoins
        // On redirige la sortie vers un log
        // -------------------------------------------------------------
        file_put_contents($iperfLogA, '');
        $cmdIperfA = "iperf -c 10.8.3.3 -t 9999 > $iperfLogA 2>&1 & echo $!";
        $pidIperfA = shell_exec($cmdIperfA);
        file_put_contents($iperfPidA, $pidIperfA);

        // idem pour 10.9.3.3
        file_put_contents($iperfLogB, '');
        $cmdIperfB = "iperf -c 10.9.3.3 -t 9999 > $iperfLogB 2>&1 & echo $!";
        $pidIperfB = shell_exec($cmdIperfB);
        file_put_contents($iperfPidB, $pidIperfB);

        echo json_encode(["status" => "ok", "message" => "Ping & iPerf started"]);
        exit;
    }
    elseif ($action === 'stop') {
        // -------------------------------------------------------------
        // Tuer les process dont on a sauvegardé les PID
        // -------------------------------------------------------------
        $pids = [$pingPidA, $pingPidB, $iperfPidA, $iperfPidB];
        foreach ($pids as $pidFile) {
            if (file_exists($pidFile)) {
                $pid = trim(file_get_contents($pidFile));
                if ($pid) {
                    // Tuer le process (SIGTERM)
                    exec("kill $pid");
                }
                // Supprimer le fichier .pid
                unlink($pidFile);
            }
        }

        echo json_encode(["status" => "ok", "message" => "Ping & iPerf stopped"]);
        exit;
    }

    // Autres actions... ?

} else if ($method === 'GET') {
    // Récupérer action depuis $_GET
    $action = $_GET['action'] ?? null;

    if ($action === 'get_data') {
        // -------------------------------------------------------------
        // 1) EXTRAIRE LATENCE DEPUIS LES LOGS PING
        // -------------------------------------------------------------
        // L’idée de base : lire la fin du fichier de log,
        // repérer la dernière ligne qui contient "time=XX ms"
        // Ex: "64 bytes from 10.8.3.3: icmp_seq=10 ttl=64 time=21.2 ms"
        //
        // On va faire simple : on lit tout le fichier, on prend la dernière
        // ligne contenant "time="...
        // Dans la vraie vie, pour éviter de relire tout le fichier à chaque fois,
        // on peut faire un tail -n 20 ou un parse plus efficace.

        $latencyA = parsePingLatency($pingLogA);
        $latencyB = parsePingLatency($pingLogB);

        // -------------------------------------------------------------
        // 2) EXTRAIRE THROUGHPUT / BANDE PASSANTE DEPUIS LES LOGS IPERF
        // -------------------------------------------------------------
        // Lancer iPerf en mode client sur la durée : le log contiendra
        // régulièrement des stats du type : "[ ID] 0.0- 1.0 sec  1.05 MBytes  8.81 Mbits/sec"
        // On va récupérer la dernière ligne qui contient "sec"
        // (là aussi, on fait un parse basique).
        // -------------------------------------------------------------
        $bandwidthA = parseIperfBandwidth($iperfLogA);
        $bandwidthB = parseIperfBandwidth($iperfLogB);

        // Statut de connexion (si on a une latence > 0, on le considère "Connected")
        $statusA = ($latencyA > 0) ? "Connected" : "Disconnected";
        $statusB = ($latencyB > 0) ? "Connected" : "Disconnected";

        $response = [
            "timestamp" => time(),
            "robots" => [
                [
                    "id" => "RobotA",
                    "latency" => $latencyA,
                    "bandwidth" => $bandwidthA,
                    "status" => $statusA
                ],
                [
                    "id" => "RobotB",
                    "latency" => $latencyB,
                    "bandwidth" => $bandwidthB,
                    "status" => $statusB
                ]
            ]
        ];
        echo json_encode($response);
        exit;
    }
}

// Sinon...
echo json_encode(["status" => "error", "message" => "Invalid request"]);

// ----------------------------------------------------------------
// FONCTIONS DE PARSE
// ----------------------------------------------------------------
function parsePingLatency($logFile)
{
    if (!file_exists($logFile)) return 0;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // On cherche la dernière ligne contenant "time="
    for ($i = count($lines) - 1; $i >= 0; $i--) {
        if (strpos($lines[$i], 'time=') !== false) {
            // exemple de ligne : "64 bytes from 10.8.3.3: icmp_seq=10 ttl=64 time=21.2 ms"
            preg_match('/time=([\d\.]+)/', $lines[$i], $matches);
            if (isset($matches[1])) {
                return floatval($matches[1]);
            }
        }
    }
    return 0;
}

function parseIperfBandwidth($logFile)
{
    if (!file_exists($logFile)) return 0;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // On cherche la dernière ligne qui ressemble à : "0.0- 1.0 sec  1.05 MBytes  8.81 Mbits/sec"
    // On va extraire le "8.81" par exemple.
    for ($i = count($lines) - 1; $i >= 0; $i--) {
        if (preg_match('/([\d\.]+)\s+Mbits\/sec/', $lines[$i], $matches)) {
            // $matches[1] devrait contenir la valeur en Mbits/s
            return floatval($matches[1]);
        }
    }
    return 0;
}
?>
