# -*- coding: utf-8 -*-
import subprocess
import re
import json
 
# IP de destination pour tester le réseau
DEST_IP = "10.8.0.7"
 
# Fonction pour exécuter une commande et récupérer la sortie
def run_command(command):
    try:
        result = subprocess.run(command, capture_output=True, text=True, shell=True, timeout=5)
        return result.stdout.strip()
    except Exception as e:
        return None
 
# Fonction pour récupérer la latence depuis traceroute
def get_traceroute_latency():
    output = run_command(f"traceroute -I -n {DEST_IP}")
    if output:
        latencies = [float(x) for x in re.findall(r"(\d+\.\d+) ms", output)]
        return round(sum(latencies) / len(latencies), 2) if latencies else None
    return None
 
# Fonction pour récupérer les métriques depuis iPerf (ancienne version)
def get_iperf_metrics():
    output = run_command(f"iperf -c {DEST_IP}")
    if not output:  # Vérifier que la sortie n'est pas vide
        return None
    # Recherche uniquement du débit (Bandwidth)
    bandwidth_match = re.search(r"(\d+\.\d+|\d+)\s+Mbits/sec", output)
    # Extraction des valeurs
    bandwidth = float(bandwidth_match.group(1)) if bandwidth_match else None
    return bandwidth
 
# Fonction pour déterminer la couleur en fonction des métriques
def get_status_color(latency, bandwidth):
    if latency is None or bandwidth is None:
        return "gray"  # Si aucune donnée, mettre la ligne en gris
    latency_score = "green" if latency < 50 else "orange" if latency <= 150 else "red"
    bandwidth_score = "green" if bandwidth > 50 else "orange" if bandwidth >= 10 else "red"
 
    if latency_score == "green" and bandwidth_score == "green":
        return "green"
    elif "red" in [latency_score, bandwidth_score]:
        return "red"
    else:
        return "orange"
 
# Exécution des commandes
latency = get_traceroute_latency()
bandwidth = get_iperf_metrics()
 
# Si aucune métrique n'est disponible, définir le statut comme hors ligne
if latency is None and bandwidth is None:
    connection_status = "Offline"
    color_status = "gray"
else:
    connection_status = "Online"
    color_status = get_status_color(latency, bandwidth)
 
# Retourner les résultats sous forme de JSON
metrics = {
    "robot_id": "robot_1",
    "latency": latency,
    "bandwidth_usage": bandwidth,
    "connection_status": connection_status,
    "status_color": color_status
}
 
print(json.dumps(metrics))