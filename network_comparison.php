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

        let comparisonChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: "Time" } },
                    y: { title: { display: true, text: "Latency (ms)" }, beginAtZero: true }
                }
            }
        });

        function updateComparisonChart() {
            fetch("get_metrics.php")
                .then(response => response.json())
                .then(data => {
                    let colors = ["red", "blue", "green", "orange", "purple"];
                    let datasets = [];
                    let timestamps = data.timestamps || [];

                    Object.keys(data.servers).forEach((server, index) => {
                        let latencies = data.servers[server] || [];
                        datasets.push({
                            label: server,
                            data: latencies,
                            borderColor: colors[index % colors.length],
                            borderWidth: 2,
                            fill: false
                        });
                    });

                    comparisonChart.data.labels = timestamps;
                    comparisonChart.data.datasets = datasets;
                    comparisonChart.update();
                })
                .catch(error => console.error("Error fetching real-time data:", error));
        }

        setInterval(updateComparisonChart, 500); // Mise à jour toutes les 500 ms (0.5s)
        updateComparisonChart();
    });
</script>
</body>
</html>
