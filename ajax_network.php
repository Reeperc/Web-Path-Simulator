<?php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$pingLogA = '/tmp/ping_10.8.3.3.log';
$pingPidA = '/tmp/ping_10.8.3.3.pid';

$pingLogB = '/tmp/ping_10.9.3.3.log';
$pingPidB = '/tmp/ping_10.9.3.3.pid';

if ($method === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $action = $data['action'] ?? '';

    if ($action === 'start') {
        // Lancer ping pour 10.8.3.3
        file_put_contents($pingLogA, '');
        $cmdPingA = "ping -i 1 10.8.3.3 > $pingLogA 2>&1 & echo $!";
        $pidPingA = shell_exec($cmdPingA);
        file_put_contents($pingPidA, $pidPingA);

        // Lancer ping pour 10.9.3.3
        file_put_contents($pingLogB, '');
        $cmdPingB = "ping -i 1 10.9.3.3 > $pingLogB 2>&1 & echo $!";
        $pidPingB = shell_exec($cmdPingB);
        file_put_contents($pingPidB, $pidPingB);

        echo json_encode(["status" => "ok", "message" => "Ping started"]);
        exit;
    }
    elseif ($action === 'stop') {
        $pids = [$pingPidA, $pingPidB];
        foreach ($pids as $pidFile) {
            if (file_exists($pidFile)) {
                $pid = trim(file_get_contents($pidFile));
                if ($pid) {
                    exec("kill $pid");
                }
                unlink($pidFile);
            }
        }
        echo json_encode(["status" => "ok", "message" => "Ping stopped"]);
        exit;
    }

} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'get_data') {
        // 1) Récup latence via logs ping
        $latA = parsePingLatency($pingLogA);
        $latB = parsePingLatency($pingLogB);

        // 2) Exécuter un test iPerf de courte durée (ex: 3s) pour la BP
        // On parse immédiatement la sortie
        $bwA = measureBandwidth("10.8.3.3");
        $bwB = measureBandwidth("10.9.3.3");

        // Connection status : latence > 0 => "Connected"
        $statusA = ($latA > 0) ? "Connected" : "Disconnected";
        $statusB = ($latB > 0) ? "Connected" : "Disconnected";

        $response = [
            "timestamp" => time(),
            "robots" => [
                [
                    "id" => "RobotA",
                    "latency" => $latA,
                    "bandwidth" => $bwA,
                    "status" => $statusA
                ],
                [
                    "id" => "RobotB",
                    "latency" => $latB,
                    "bandwidth" => $bwB,
                    "status" => $statusB
                ],
            ],
        ];
        echo json_encode($response);
        exit;
    }
}

echo json_encode(["status" => "error", "message" => "Invalid request"]);

// --------------------------------------------------------
// FONCTIONS
// --------------------------------------------------------
function parsePingLatency($logFile)
{
    if (!file_exists($logFile)) return 0;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    for ($i = count($lines) - 1; $i >= 0; $i--) {
        if (strpos($lines[$i], 'time=') !== false) {
            preg_match('/time=([\d\.]+)/', $lines[$i], $m);
            if (!empty($m[1])) {
                return floatval($m[1]);
            }
        }
    }
    return 0;
}

function measureBandwidth($ip)
{
    // Lance iPerf client vers $ip pendant 3s, parse la sortie
    // On suppose iPerf2, en TCP, sur le port par défaut, etc.
    // Mettez -u si besoin, ou ajustez selon votre usage.
    $cmd = "iperf -c $ip -t 3 -i 1 2>&1";
    $output = shell_exec($cmd);

    // On cherche la dernière ligne style "[  3]  0.0- 3.0 sec  4.00 MBytes  11.2 Mbits/sec"
    // On va capturer la valeur "11.2" + l'unité (K/M/G)
    if (preg_match_all('/([\d\.]+)\s?([KMG])?bits\/sec/', $output, $matches)) {
        // On prend la dernière occurrence
        $index = count($matches[0]) - 1;
        $value = floatval($matches[1][$index]);
        $unit  = strtoupper($matches[2][$index] ?? 'M');  // default M

        // Convertir en Mbits/s
        switch ($unit) {
            case 'K': $value *= 0.001; break;
            case 'G': $value *= 1000;  break;
        }
        return $value;  // Mbits/s
    }

    return 0;
}
?>
