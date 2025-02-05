<h1 class="h3 mb-4 text-gray-800">Network Metrics</h1>

                    <!-- Bouton de rafraîchissement -->
                    <button id="refreshBtn" class="btn btn-primary">Refresh</button>

                    <!-- Conteneur du tableau des performances -->
                    <div class="mt-4" id="performanceTableContainer"></div>

                    <!-- Canvas pour le graphe de latence -->
                    <canvas id="latencyChart" width="400" height="200"></canvas>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                      // Au clic sur le bouton, on envoie une requête POST pour lancer pings + iperf
                      document.getElementById('refreshBtn').addEventListener('click', function() {
                        fetch('indexadmin.php?page=network-metrics', {
                          method: 'POST',
                          headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                          },
                          body: 'action=refresh'
                        })
                        .then(response => response.json())
                        .then(data => {
                          // data contient { robots: [ {id, latency, bandwidth, status}, ... ] }
                          updatePerformanceTable(data.robots);
                          updateLatencyChart(data.robots);
                        })
                        .catch(err => console.error('Erreur lors de la récupération des métriques :', err));
                      });

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
                        labels: [],      // ex. "1", "2", "3", ...
                        dataRobotA: [],  // latences Robot A
                        dataRobotB: []   // latences Robot B
                      };

                      // Initialisation du graphe Chart.js
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

                      // Ajout d'un point de latence à chaque refresh
                      function updateLatencyChart(robots) {
                        // On incrémente un compteur pour l'axe X
                        let nextLabel = latencyHistory.labels.length + 1;
                        latencyHistory.labels.push(nextLabel.toString());

                        // Récupère latence Robot A et B
                        const robotA = robots.find(r => r.id.includes('10.8.3.3'));
                        const robotB = robots.find(r => r.id.includes('10.9.3.3'));

                        latencyHistory.dataRobotA.push(parseFloat(robotA.latency) || 0);
                        latencyHistory.dataRobotB.push(parseFloat(robotB.latency) || 0);

                        // Mise à jour du graphique
                        latencyChart.update();
                      }
                    </script>