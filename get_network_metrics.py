# -*- coding: utf-8 -*-

import subprocess

import re

import json
 
# IP de destination pour tester le réseau

DEST_IP = "10.8.0.7"
 
# Fonction pour exécuter une commande et récupérer la sortie

def run_command(command):

    try:

        result = subprocess.run(command, capture_output=True, text=True, shell=True)

        return result.stdout

    except Exception as e:

        return None
 
# Fonction pour récupérer la latence depuis traceroute

def get_traceroute_latency():

    output = run_command(f"traceroute -n {DEST_IP}")

    if output:

        latencies = [float(x) for x in re.findall(r"(\d+\.\d+) ms", output)]

        return round(sum(latencies) / len(latencies), 2) if latencies else None

    return None
 
# Fonction pour récupérer les métriques depuis iPerf

def get_iperf_metrics():

    output = run_command(f"iperf -c {DEST_IP}")

    if not output:  # Vérifier que la sortie n'est pas vide

        return None, None, None
 
    # Recherche uniquement du débit (Bandwidth)

    bandwidth_match = re.search(r"(\d+\.\d+|\d+)\s+Mbits/sec", output)
 
    # Extraction des valeurs

    bandwidth = float(bandwidth_match.group(1)) if bandwidth_match else None
 
    # Retourne le débit, et None pour jitter et packet_loss (non présents dans la sortie)

    return bandwidth
# Fonction pour déterminer la couleur en fonction de la latence uniquement
def get_status_color(latency):
    if latency is None:
        return "gray"  # Si aucune donnée, mettre la ligne en gris
    
    if latency < 50:
        return "green"
    elif latency <= 150:
        return "orange"
    else:
        return "red"
     
# Exécution des commandes

latency = get_traceroute_latency()

bandwidth = get_iperf_metrics()

connection_status = "Online" if latency is not None else "Offline"
status_color = get_status_color(latency)
 
# Retourner les résultats sous forme de JSON

metrics = {
    "robot_id": "robot_1",
    "latency": latency,
    "bandwidth_usage": bandwidth,
    "connection_status": connection_status,
    "status_color": status_color
}
 
print(json.dumps(metrics))

 