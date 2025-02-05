<h1 class="h3 mb-4 text-gray-800">Network Metrics</h1>

<!-- Bouton Start Measuring -->
<button id="startMeasureBtn" class="btn btn-primary">Start Measuring</button>

<!-- Conteneur du tableau des performances -->
<div class="mt-4" id="performanceTableContainer"></div>

<!-- Canvas pour le graphe de latence -->
<canvas id="latencyChart" width="400" height="200"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Contiendra l'identifiant de l'interval
  let measureInterval = null;
  // Combien de fois on a fait la mesure
  let measureCount = 0;

  // Durée en secondes de la mesure
  const MEASURE_DURATION = 10; // 10 secondes
  // Intervalle entre chaque mesure, en millisecondes
  const MEASURE_INTERVAL_MS = 1000; // 1 seconde

  document.getElementById('startMeasureBtn').addEventListener('click', startMeasuring);

  function startMeasuring() {
    // Réinitialise l'historique du graphe si nécessaire
    latencyHistory.labels = [];
    latencyHistory.dataRobotA = [];
    latencyHistory.dataRobotB = [];
    latencyChart.update(); // mise à jour rapide pour repartir propre

    // Désactiver le bouton pendant la mesure
    document.getElementById('startMeasureBtn').disabled = true;

    measureCount = 0;

    // Lance un interval pour mesurer toutes les X ms
    measureInterval = setInterval(() => {
      measureCount++;
      doSingleMeasurement();

      // Si on a atteint 10 secondes (ou 10 itérations)
      if (measureCount >= MEASURE_DURATION) {
        clearInterval(measureInterval);
        measureInterval = null;
        document.getElementById('startMeasureBtn').disabled = false;
      }
    }, MEASURE_INTERVAL_MS);
  }

  // Effectue une seule mesure (envoie un POST AJAX)
  function doSingleMeasurement() {
    fetch('indexadmin.php?page=network-metrics', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'action=refresh'
    })
    .then(response => response.json())
    .then(data => {
      // data = { robots: [ {id, latency, bandwidth, status}, ... ] }
      updatePerformanceTable(data.robots);
      updateLatencyChart(data.robots);
    })
    .catch(err => console.error('Erreur lors de la récupération des métriques :', err));
  }

  // Fonction pour mettre à jour le tableau
  function updatePerformanceTable(robots) {
    const container = document.getElementById('performanceTableContainer');
    container.innerHTML = '';

    const table = document.createElement('table');
    table.classList.add('table', 'table-bordered');

    const thead = document.createElement('thead');
    thead.innerHTML = `
      <tr>
        <th>Robot ID</th>
        <th>Latency (ms)</th>
        <th>Bandwidth Usage</th>
        <th>Connection Status</th>
      </tr>
    `;
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    robots.forEach(robot => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${robot.id}</td>
        <td>${robot.latency}</td>
        <td>${robot.bandwidth}</td>
        <td>${robot.status}</td>
      `;
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    container.appendChild(table);
  }

  // Historique de latence pour Chart.js
  let latencyHistory = {
    labels: [],
    dataRobotA: [],
    dataRobotB: []
  };

  // Initialisation du graphe
  const ctx = document.getElementById('latencyChart').getContext('2d');
  const latencyChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: latencyHistory.labels,
      datasets: [
        {
          label: 'Latency Robot A (10.8.3.3)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          data: latencyHistory.dataRobotA,
          fill: false
        },
        {
          label: 'Latency Robot B (10.9.3.3)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          borderColor: 'rgba(255, 99, 132, 1)',
          data: latencyHistory.dataRobotB,
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        x: {
          title: {
            display: true,
            text: 'Refresh Count'
          }
        },
        y: {
          title: {
            display: true,
            text: 'Latency (ms)'
          },
          beginAtZero: true
        }
      }
    }
  });

  // Ajoute un point de latence à chaque mesure
  function updateLatencyChart(robots) {
    let nextLabel = latencyHistory.labels.length + 1;
    latencyHistory.labels.push(nextLabel.toString());

    // Cherche latences Robot A / Robot B
    const robotA = robots.find(r => r.id.includes('10.8.3.3'));
    const robotB = robots.find(r => r.id.includes('10.9.3.3'));

    latencyHistory.dataRobotA.push(parseFloat(robotA.latency) || 0);
    latencyHistory.dataRobotB.push(parseFloat(robotB.latency) || 0);

    // Mise à jour du graphe
    latencyChart.update();
  }
</script>
