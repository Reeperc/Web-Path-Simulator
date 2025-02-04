<?php
// Charger les données stockées pour chaque serveur
$servers = ['UK', 'WestEurope', 'Paris', 'Korea', 'US']; // Liste des serveurs
$comparison_data = [];

foreach ($servers as $server) {
    $history_file = "latency_history_{$server}.json";
    if (file_exists($history_file)) {
        $history = json_decode(file_get_contents($history_file), true);
        $comparison_data[$server] = $history;
    } else {
        $comparison_data[$server] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Network Latency Comparison</h2>
        <canvas id="comparisonChart"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let ctx = document.getElementById('comparisonChart').getContext('2d');

            let comparisonData = <?= json_encode($comparison_data); ?>;
            let datasets = [];
            let colors = ["red", "blue", "green", "orange", "purple"]; // Couleurs des serveurs

            Object.keys(comparisonData).forEach((server, index) => {
                let timestamps = comparisonData[server].map(entry => new Date(entry.time * 1000).toLocaleTimeString());
                let latencies = comparisonData[server].map(entry => entry.latency);

                datasets.push({
                    label: server,
                    data: latencies,
                    borderColor: colors[index % colors.length],
                    borderWidth: 2,
                    fill: false
                });
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: comparisonData[Object.keys(comparisonData)[0]].map(entry => new Date(entry.time * 1000).toLocaleTimeString()),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: "Time" } },
                        y: { title: { display: true, text: "Latency (ms)" }, beginAtZero: true }
                    }
                }
            });
        });
    </script>
</body>
</html>
